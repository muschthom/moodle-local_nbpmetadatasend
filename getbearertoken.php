<?php

// Datei zur Speicherung des Token-Status
$tokenFile = "token_info.json";
require_once "config.php";
function get_new_token($tokenFile, $clientId, $clientSecret)
{

    //TODO: URL anpassen, passt derzeit nicht mit URL aus DB zusammen
    $url = "https://aai.demo.meinbildungsraum.de/realms/nbp-aai/protocol/openid-connect/token";
    $credentials = base64_encode("$clientId:$clientSecret");

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($statusCode == 200) {
        $data = json_decode($response, true);
        $expiresIn = $data["expires_in"];
        $accessToken = $data["access_token"];

        // Speichere den Token und den Ablaufzeitpunkt
        $tokenInfo = [
            "token" => $accessToken,
            "expires" => time() + $expiresIn - 30 // 30 Sekunden Sicherheitspuffer
        ];
        file_put_contents($tokenFile, json_encode($tokenInfo));
    } else {
        // Fehlerbehandlung
        echo "Fehler beim Abrufen des Tokens: HTTP-Status $statusCode\n";
    }

    curl_close($curl);
}

function get_token($tokenFile, $clientId, $clientSecret)
{
    global $tokenFile;

    if (!file_exists($tokenFile)) {
        get_new_token($tokenFile, $clientId, $clientSecret);
    }

    $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    if (time() >= $tokenInfo["expires"]) {
        get_new_token($tokenFile, $clientId, $clientSecret);
        $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    }

    return $tokenInfo["token"];
}


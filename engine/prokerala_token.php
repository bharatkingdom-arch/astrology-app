<?php

$client_id = "1b88a6d8-e904-4046-97f1-5537cfe9c827";
$client_secret = "7bx6ITreX95KG1pe6XmRJY3rzZOIngYidIMSsi3e";

$url = "https://api.prokerala.com/token";

$data = [
    "grant_type" => "client_credentials",
    "client_id" => $client_id,
    "client_secret" => $client_secret
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);

curl_close($ch);

echo $response;
<?php

/* STEP 1: GET TOKEN */

$client_id = "1b88a6d8-e904-4046-97f1-5537cfe9c827";
$client_secret = "t4ADts9eegyqYtSr29WzITkyViwKqq02TN9FUMoT";


$token_url = "https://api.prokerala.com/token";

$data = [
    "grant_type" => "client_credentials",
    "client_id" => $client_id,
    "client_secret" => $client_secret
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'];


/* STEP 2: CALL ASHTAKAVARGA API */

$url = "https://api.prokerala.com/v2/astrology/ashtakavarga";

$params = [
    "ayanamsa" => 1,
    "coordinates" => "17.3850,78.4867",
    "datetime" => "1990-01-01T10:30:00+05:30",
    "planet" => "Sun"
];

$ch = curl_init($url . "?" . http_build_query($params));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $access_token
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
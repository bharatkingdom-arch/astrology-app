<?php

$client_id = "1b88a6d8-e904-4046-97f1-5537cfe9c827";
$client_secret = "Ddv4lm4mqa5rHsahW3olQhvFeC8ZxUs60e7ekOkp";

$url = "https://api.prokerala.com/token";

$data = [
    "grant_type" => "client_credentials",
    "client_id" => $client_id,
    "client_secret" => $client_secret
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($data),
        "ignore_errors" => true
    ]
];

$context = stream_context_create($options);

$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo "Error requesting token.";
} else {
    echo $response;
}
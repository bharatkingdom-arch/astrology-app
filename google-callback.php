<?php
session_start();

$client_id = "720155568345-rpnbllbfe3vp6vlv821acvab7i742cti.apps.googleusercontent.com";
$client_secret = getenv("GOOGLE_CLIENT_SECRET");
$redirect_uri = "https://astrology-app-720155568345.asia-south1.run.app/google-callback.php";

$token_url = "https://oauth2.googleapis.com/token";

$data = [
    "code" => $_GET['code'],
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri" => $redirect_uri,
    "grant_type" => "authorization_code"
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "method" => "POST",
        "content" => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($token_url, false, $context);

$token = json_decode($response, true);

$access_token = $token['access_token'];

$userinfo = file_get_contents(
    "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $access_token
);

$user = json_decode($userinfo, true);

$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['name'];

header("Location: dashboard.php");
exit;
?>
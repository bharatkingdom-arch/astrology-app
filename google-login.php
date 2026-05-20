<?php
session_start();

$client_id = "720155568345-rpnbllbfe3vp6vlv821acvab7i742cti.apps.googleusercontent.com";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
}
$host = $_SERVER['HTTP_HOST'];
$redirect_uri = $protocol . "://" . $host . "/google-callback.php";

$scope = "email profile";

$google_auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" .
    "response_type=code" .
    "&client_id=" . urlencode($client_id) .
    "&redirect_uri=" . urlencode($redirect_uri) .
    "&scope=" . urlencode($scope) .
    "&access_type=online";

header("Location: " . $google_auth_url);
exit;
?>
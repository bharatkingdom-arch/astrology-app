<?php
session_start();

$client_id = "720155568345-rpnbllbfe3vp6vlv821acvab7i742cti.apps.googleusercontent.com";
$redirect_uri = "https://astrology-app-720155568345.asia-south1.run.app/google-callback.php";

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
<?php

$host = "34.93.xx.xx"; // Cloud SQL public IP
$user = "astro_user";
$password = "Astro@123456";
$database = "astrology";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
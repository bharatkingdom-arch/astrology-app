<?php

$host = "localhost";
$db   = "astrology";
$user = "root";
$pass = "password";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("DB connection failed");
}
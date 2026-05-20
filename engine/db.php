<?php

$host = "ep-silent-queen-ao0yz2oh.c-2.ap-southeast-1.aws.neon.tech";
$port = "5432";
$user = "neondb_owner";
$password = "npg_Mlb8CkEIcH6Z";
$database = "neondb";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;sslmode=require";
    $conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
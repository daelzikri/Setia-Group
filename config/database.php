<?php 
$host = 'localhost';
$dbname = 'u602243872_SetiaGroup';
$username = 'u602243872_Setia';
$password = '@Setia123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
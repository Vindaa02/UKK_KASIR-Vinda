<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "kasir_vinda";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Tidak dapat terhubung ke database. " . $e->getMessage());
}
?>

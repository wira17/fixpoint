<?php
$host = "localhost";      // atau 127.0.0.1
$user = "root";           // sesuaikan dengan user database kamu
$pass = "";               // sesuaikan dengan password database kamu
$dbname = "fixpoint_system"; // ganti dengan nama database kamu

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tiket_id = intval($_POST['tiket_id']);
  date_default_timezone_set('Asia/Jakarta');
  $waktu_validasi = date('Y-m-d H:i:s');

  if (isset($_POST['validasi'])) {
    $status = 'Diterima';
  } elseif (isset($_POST['tolak'])) {
    $status = 'Ditolak';
  }

  $update = mysqli_query($conn, "UPDATE tiket_it_software 
    SET status_validasi = '$status', waktu_validasi = '$waktu_validasi' 
    WHERE id = $tiket_id");

  header("Location: order_tiket_it_software.php");
  exit;
}
?>

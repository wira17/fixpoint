<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['laporan_id'])) {
  $laporan_id = intval($_POST['laporan_id']);
  $validator_id = $_SESSION['user_id'];
  $status = '';

  if (isset($_POST['validasi'])) {
    $status = 'Diterima';
  } elseif (isset($_POST['tolak'])) {
    $status = 'Ditolak';
  }

  $update = mysqli_query($conn, "UPDATE laporan_off_duty 
    SET status_validasi = '$status', 
        tanggal_validasi = NOW(),
        validator_id = '$validator_id'
    WHERE id = '$laporan_id'");

  header("Location: off_duty_approve.php"); // halaman admin
  exit;
}
?>

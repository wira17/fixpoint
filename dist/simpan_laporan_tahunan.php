<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id     = $_SESSION['user_id'];
$tahun       = $_POST['tahun'] ?? '';
$judul       = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
$keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
$file_name   = '';

if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
  $ext = pathinfo($_FILES['file_laporan']['name'], PATHINFO_EXTENSION);
  $file_name = 'LAPTAH_' . time() . '_' . rand(100,999) . '.' . $ext;
  $upload_dir = 'uploads/laporan_tahunan/';
  move_uploaded_file($_FILES['file_laporan']['tmp_name'], $upload_dir . $file_name);
}

$query = "INSERT INTO laporan_tahunan (user_id, tahun, judul, keterangan, file_laporan)
          VALUES ('$user_id', '$tahun', '$judul', '$keterangan', '$file_name')";

if (mysqli_query($conn, $query)) {
  header("Location: laporan_tahunan.php?notif=sukses");
} else {
  echo "Gagal menyimpan laporan: " . mysqli_error($conn);
}
?>

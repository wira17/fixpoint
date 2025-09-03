<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
  $_SESSION['flash_message'] = "❌ ID barang tidak ditemukan.";
  header("Location: data_barang_it.php");
  exit;
}

$id = intval($_GET['id']);

$query = "DELETE FROM data_barang_it WHERE id = $id";
if (mysqli_query($conn, $query)) {
  $_SESSION['flash_message'] = "✅ Data barang berhasil dihapus.";
} else {
  $_SESSION['flash_message'] = "❌ Gagal menghapus data.";
}
header("Location: data_barang_it.php");
exit;

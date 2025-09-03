<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');


$id_laporan  = $_POST['id'] ?? '';
$tahun       = $_POST['tahun'] ?? '';
$judul       = mysqli_real_escape_string($conn, $_POST['judul'] ?? '');
$keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');

$file_name = '';
$upload_dir = 'uploads/laporan_tahunan/';

// Ambil data lama
$cek = mysqli_query($conn, "SELECT file_laporan FROM laporan_tahunan WHERE id = '$id_laporan'");
$data_lama = mysqli_fetch_assoc($cek);
$file_lama = $data_lama['file_laporan'];

if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
  // Hapus file lama jika ada
  if (!empty($file_lama) && file_exists($upload_dir . $file_lama)) {
    unlink($upload_dir . $file_lama);
  }

  $ext = pathinfo($_FILES['file_laporan']['name'], PATHINFO_EXTENSION);
  $file_name = 'LAPTAH_' . time() . '_' . rand(100,999) . '.' . $ext;
  move_uploaded_file($_FILES['file_laporan']['tmp_name'], $upload_dir . $file_name);
} else {
  $file_name = $file_lama; // pakai file lama
}

$query = "UPDATE laporan_tahunan 
          SET tahun = '$tahun', judul = '$judul', keterangan = '$keterangan', file_laporan = '$file_name' 
          WHERE id = '$id_laporan'";

if (mysqli_query($conn, $query)) {
  header("Location: laporan_tahunan.php?notif=update");
} else {
  echo "Gagal mengupdate laporan: " . mysqli_error($conn);
}
?>

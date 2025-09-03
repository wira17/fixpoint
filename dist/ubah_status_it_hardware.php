<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_status'])) {
  $id = intval($_POST['tiket_id']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
  $catatan_it = mysqli_real_escape_string($conn, $_POST['catatan_it']); // ⬅️ Tambahan ini
  $teknisi_nama = mysqli_real_escape_string($conn, $_SESSION['nama']);
  $now = date('Y-m-d H:i:s');

  // Query dasar
  $query = "UPDATE tiket_it_hardware SET 
              status = '$status',
              teknisi_nama = '$teknisi_nama',
              catatan_it = '$catatan_it'"; // ⬅️ Tambahkan catatan_it ke query

  // Tambahkan waktu sesuai status
  if ($status === 'Ditolak') {
    $query .= ", waktu_ditolak = '$now'";
  } elseif ($status === 'Diproses') {
    $query .= ", waktu_diproses = '$now'";
  } elseif ($status === 'Selesai') {
    $query .= ", waktu_selesai = '$now'";
  } elseif ($status === 'Tidak Bisa Diperbaiki') {
    $query .= ", waktu_selesai = '$now', waktu_tidak_bisa_diperbaiki = '$now'";
  }

  $query .= " WHERE id = $id";

  if ($conn->query($query) === TRUE) {
    header("Location: data_tiket_it_hardware.php?notif=berhasil");
    exit;
  } else {
    echo "Gagal update status: " . $conn->error;
  }
}
?>

<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
  $id         = intval($_POST['id']);
  $bulan      = $_POST['bulan'];
  $tahun      = $_POST['tahun'];
  $judul      = mysqli_real_escape_string($conn, $_POST['judul']);
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

  // Ambil file lama dari database
  $q = mysqli_query($conn, "SELECT file_laporan FROM laporan_bulanan WHERE id = '$id'");
  $data = mysqli_fetch_assoc($q);
  $file_lama = $data['file_laporan'];

  // Penanganan file jika diupload ulang
  $file_baru = '';
  if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
    $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $file_name = $_FILES['file_laporan']['name'];
    $file_tmp = $_FILES['file_laporan']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
      $file_baru = uniqid() . '.' . $file_ext;
      $upload_dir = 'uploads/laporan_bulanan/';
      $upload_path = $upload_dir . $file_baru;

      if (move_uploaded_file($file_tmp, $upload_path)) {
        // Hapus file lama jika ada
        if (!empty($file_lama) && file_exists($upload_dir . $file_lama)) {
          unlink($upload_dir . $file_lama);
        }
      } else {
        $_SESSION['error'] = "Gagal mengunggah file baru.";
        header("Location: laporan_bulanan.php");
        exit;
      }
    } else {
      $_SESSION['error'] = "Format file tidak diizinkan.";
      header("Location: laporan_bulanan.php");
      exit;
    }
  } else {
    $file_baru = $file_lama; // Jika tidak upload baru, tetap pakai file lama
  }

  $update = mysqli_query($conn, "UPDATE laporan_bulanan SET 
    bulan = '$bulan',
    tahun = '$tahun',
    judul = '$judul',
    keterangan = '$keterangan',
    file_laporan = '$file_baru'
    WHERE id = '$id'
  ");

  if ($update) {
    $_SESSION['success'] = "Laporan berhasil diperbarui.";
  } else {
    $_SESSION['error'] = "Terjadi kesalahan saat memperbarui data.";
  }
}

header("Location: laporan_bulanan.php");
exit;

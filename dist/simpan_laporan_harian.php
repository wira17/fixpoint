<?php
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $uraian = mysqli_real_escape_string($conn, $_POST['uraian']);

  // Handle file upload
  $file_name = '';
  if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === 0) {
    $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $file_tmp = $_FILES['file_laporan']['tmp_name'];
    $file_ori = $_FILES['file_laporan']['name'];
    $ext = strtolower(pathinfo($file_ori, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_ext)) {
      $file_name = 'laporan_' . time() . '.' . $ext;
      move_uploaded_file($file_tmp, 'uploads/laporan_harian/' . $file_name);
    }
  }

  // Simpan ke database
  $query = "INSERT INTO laporan_harian (user_id, uraian, file_dokumen) 
            VALUES ('$user_id', '$uraian', '$file_name')";

  if (mysqli_query($conn, $query)) {
    header("Location: laporan_harian.php?success=1");
  } else {
    echo "Gagal menyimpan laporan.";
  }
} else {
  header("Location: login.php");
}
?>

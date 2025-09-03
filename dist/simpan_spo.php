<?php
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nomor_spo = $_POST['nomor_spo'];
  $judul = $_POST['judul'];
  $tanggal_upload = date('Y-m-d H:i:s');
  $petugas_upload = $_SESSION['nama'] ?? 'unknown';

  // Upload file
  $file_spo = '';
  if (isset($_FILES['file_spo']) && $_FILES['file_spo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/spo/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = basename($_FILES['file_spo']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;

    if (move_uploaded_file($_FILES['file_spo']['tmp_name'], $targetPath)) {
      $file_spo = $targetPath;
    }
  }

  // Simpan ke database
  $query = "INSERT INTO spo_it (nomor_spo, judul, file_spo, petugas_upload, tanggal_upload) 
            VALUES (?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "sssss", $nomor_spo, $judul, $file_spo, $petugas_upload, $tanggal_upload);
  $success = mysqli_stmt_execute($stmt);

  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
  if ($success) {
    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'SPO berhasil disimpan!',
        position: 'center',
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = 'data_spo_it.php';
      });
    </script>";
  } else {
    echo "<script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Gagal menyimpan SPO.',
        position: 'center',
        showConfirmButton: true
      }).then(() => {
        window.history.back();
      });
    </script>";
  }
}
?>

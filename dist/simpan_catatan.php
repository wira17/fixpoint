<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $judul   = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi     = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date("Y-m-d H:i:s");

    if ($user_id > 0 && !empty($judul) && !empty($isi)) {
        $sql = "INSERT INTO catatan_kerja (user_id, judul, isi, tanggal) 
        VALUES ('$user_id', '$judul', '$isi', NOW())";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Catatan berhasil disimpan'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan catatan'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Data tidak lengkap'); window.history.back();</script>";
    }
}
?>

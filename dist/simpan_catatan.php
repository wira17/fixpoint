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

    if ($user_id > 0 && !empty($judul) && !empty($isi)) {
        $sql = "INSERT INTO catatan_kerja (user_id, judul, isi, tanggal) 
                VALUES ('$user_id', '$judul', '$isi', NOW())";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['notif'] = ['type' => 'success', 'msg' => 'Catatan berhasil disimpan!'];
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'msg' => 'Gagal menyimpan catatan!'];
        }
    } else {
        $_SESSION['notif'] = ['type' => 'warning', 'msg' => 'Data tidak lengkap!'];
    }

    header("Location: dashboard.php");
    exit;
}

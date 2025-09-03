<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$id = $_GET['id'] ?? '';
$aksi = $_GET['aksi'] ?? '';
$status = $_GET['status'] ?? '';

if ($id && $aksi && $status) {
    $waktu = date('Y-m-d H:i:s');

    if ($aksi == 'atasan') {
        $query = "UPDATE izin_keluar SET acc_atasan='$status', waktu_acc_atasan='$waktu' WHERE id='$id'";
    } elseif ($aksi == 'sdm') {
        $query = "UPDATE izin_keluar SET acc_sdm='$status', waktu_acc_sdm='$waktu' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Status berhasil diperbarui'); window.location.href=document.referrer;</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status'); window.location.href=document.referrer;</script>";
    }
}
?>

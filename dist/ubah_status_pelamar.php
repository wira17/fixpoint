<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? 'Pending';
$page = $_GET['page'] ?? 1;
$keyword = $_GET['keyword'] ?? '';

if($id){
    $stmt = $conn->prepare("UPDATE pelamar_akun SET status_akun=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['flash_message'] = "Status akun berhasil diubah.";
}

header("Location: data_pelamar.php?page=$page&keyword=".urlencode($keyword));
exit;

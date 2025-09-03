<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nrp = trim($_POST['nrp']);
    $password = $_POST['password'];

    if (empty($nrp) || empty($password)) {
        echo "<script>alert('NRP dan Password harus diisi'); window.location.href='login.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nama, password FROM users WHERE nrp = ?");
    $stmt->bind_param("s", $nrp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['nrp'] = $nrp;

            // Arahkan ke halaman dashboard atau index
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password salah'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('NRP tidak ditemukan'); window.location.href='login.php';</script>";
    }
} else {
    header("Location: login.php");
    exit;
}

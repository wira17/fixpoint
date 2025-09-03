<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status_validasi = $_POST['status_validasi'];
    $catatan_it = mysqli_real_escape_string($conn, $_POST['catatan_it']);

    // Ambil user ID dari session sebagai validator
    $validator_id = $_SESSION['user_id'];
    $tanggal_validasi = date("Y-m-d H:i:s");

    $query = "UPDATE laporan_off_duty 
              SET status_validasi = '$status_validasi',
                  catatan_it = '$catatan_it',
                  tanggal_validasi = '$tanggal_validasi',
                  validator_id = '$validator_id'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Status berhasil diperbarui.');
            window.location.href='data_off_duty.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal memperbarui status.');
            window.history.back();
        </script>";
    }
} else {
    header("Location: data_off_duty.php");
    exit;
}

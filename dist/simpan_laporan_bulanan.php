<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $user_id = $_SESSION['user_id'];
    $tanggal_input = date('Y-m-d H:i:s');

    // Cek apakah file diupload
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $allowed_ext = ['pdf', 'doc', 'docx'];
        $file_name = $_FILES['file_laporan']['name'];
        $file_tmp = $_FILES['file_laporan']['tmp_name'];
        $file_size = $_FILES['file_laporan']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'laporan_bulanan_' . time() . '.' . $file_ext;
            $upload_dir = 'uploads/laporan_bulanan/';
            
            // Pastikan folder upload tersedia
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            move_uploaded_file($file_tmp, $upload_dir . $new_file_name);

            // Simpan ke database
            $query = "INSERT INTO laporan_bulanan (bulan, tahun, judul, keterangan, file_laporan, user_id, tanggal_input)
                      VALUES ('$bulan', '$tahun', '$judul', '$keterangan', '$new_file_name', '$user_id', '$tanggal_input')";

            if (mysqli_query($conn, $query)) {
                echo "<script>
                    alert('Laporan berhasil disimpan.');
                    window.location.href = 'laporan_bulanan.php';
                </script>";
            } else {
                echo "<script>alert('Gagal menyimpan laporan.');history.back();</script>";
            }
        } else {
            echo "<script>alert('Format file tidak diperbolehkan.');history.back();</script>";
        }
    } else {
        echo "<script>alert('Silakan upload file laporan.');history.back();</script>";
    }
} else {
    header("Location: laporan_bulanan.php");
    exit;
}
?>

<?php
require 'koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id         = $_POST['id'];
    $nama       = trim($_POST['nama_perusahaan']);
    $alamat     = trim($_POST['alamat']);
    $kota       = trim($_POST['kota']);
    $provinsi   = trim($_POST['provinsi']);
    $kontak     = trim($_POST['kontak']);
    $email      = trim($_POST['email']);

    $folder     = __DIR__ . "/images/logo/";
    $newLogoName = null;

    if (!empty($_FILES['logo']['name'])) {
        $logo = $_FILES['logo']['name'];
        $tmp = $_FILES['logo']['tmp_name'];

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($logo, PATHINFO_EXTENSION);
        $newLogoName = uniqid('logo_') . '.' . $ext;
        $uploadPath = $folder . $newLogoName;

        if (move_uploaded_file($tmp, $uploadPath)) {
            // Hapus logo lama
            $queryOld = mysqli_query($conn, "SELECT logo FROM perusahaan WHERE id = '$id'");
            $rowOld = mysqli_fetch_assoc($queryOld);
            if (!empty($rowOld['logo'])) {
                $oldLogoPath = $folder . $rowOld['logo'];
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
        } else {
            $_SESSION['flash_message'] = "Gagal mengupload file logo.";
            header("Location: perusahaan.php");
            exit;
        }
    }

    // Bangun query update
    $query = "UPDATE perusahaan SET 
              nama_perusahaan = '$nama',
              alamat = '$alamat',
              kota = '$kota',
              provinsi = '$provinsi',
              kontak = '$kontak',
              email = '$email'";

    if ($newLogoName) {
        $query .= ", logo = '$newLogoName'";
    }

    $query .= " WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = "Data perusahaan berhasil diperbarui.";
    } else {
        $_SESSION['flash_message'] = "Gagal memperbarui data: " . mysqli_error($conn);
    }

    header("Location: perusahaan.php");
    exit;
} else {
    $_SESSION['flash_message'] = "Permintaan tidak valid.";
    header("Location: perusahaan.php");
    exit;
}
?>

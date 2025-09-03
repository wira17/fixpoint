<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_POST['update'])) {
    header("Location: input_dokumen.php");
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$judul = isset($_POST['judul']) ? mysqli_real_escape_string($conn, trim($_POST['judul'])) : '';
$pokja_id = isset($_POST['pokja_id']) ? intval($_POST['pokja_id']) : 0;
$elemen_penilaian = isset($_POST['elemen_penilaian']) ? mysqli_real_escape_string($conn, trim($_POST['elemen_penilaian'])) : '';

if ($id <= 0 || $judul === '' || $pokja_id <= 0) {
    $_SESSION['flash_message'] = "❌ Data tidak valid.";
    header("Location: input_dokumen.php");
    exit;
}

// Ambil data dokumen lama
$qOld = mysqli_query($conn, "SELECT file_path FROM dokumen WHERE id = $id LIMIT 1");
$oldFile = '';
if ($qOld && mysqli_num_rows($qOld) === 1) {
    $rowOld = mysqli_fetch_assoc($qOld);
    $oldFile = $rowOld['file_path'];
}

// Proses upload file baru jika ada
$newFilePath = $oldFile;
$newFileNameOriginal = null;

if (isset($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] !== UPLOAD_ERR_NO_FILE) {
    $err = $_FILES['file_dokumen']['error'];
    if ($err !== UPLOAD_ERR_OK) {
        $_SESSION['flash_message'] = "❌ Gagal upload file (error code: $err).";
        header("Location: input_dokumen.php");
        exit;
    }

    $maxSize = 20*1024*1024;
    if ($_FILES['file_dokumen']['size'] > $maxSize) {
        $_SESSION['flash_message'] = "❌ Ukuran file melebihi 20MB.";
        header("Location: input_dokumen.php");
        exit;
    }

    $allowedExts = ['pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png'];
    $ext = strtolower(pathinfo($_FILES['file_dokumen']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        $_SESSION['flash_message'] = "❌ Ekstensi file tidak diizinkan.";
        header("Location: input_dokumen.php");
        exit;
    }

    $uploadDirAbs = __DIR__ . '/uploads/dokumen/';
    $uploadDirRel = 'uploads/dokumen/';
    if (!is_dir($uploadDirAbs)) @mkdir($uploadDirAbs, 0775, true);

    $rand = bin2hex(random_bytes(4));
    $newName = 'DOC_' . date('Ymd_His') . "_$rand.$ext";
    $destAbs = $uploadDirAbs . $newName;
    $destRel = $uploadDirRel . $newName;

    if (!move_uploaded_file($_FILES['file_dokumen']['tmp_name'], $destAbs)) {
        $_SESSION['flash_message'] = "❌ Gagal memindahkan file ke server.";
        header("Location: input_dokumen.php");
        exit;
    }

    // Hapus file lama jika ada
    if (!empty($oldFile) && file_exists(__DIR__ . '/' . $oldFile)) {
        @unlink(__DIR__ . '/' . $oldFile);
    }

    $newFilePath = mysqli_real_escape_string($conn, $destRel);
    $newFileNameOriginal = mysqli_real_escape_string($conn, $_FILES['file_dokumen']['name']);
}

// Update data
$sqlUpdate = "UPDATE dokumen SET 
                judul = '$judul', 
                pokja_id = $pokja_id, 
                elemen_penilaian = '$elemen_penilaian', 
                file_path = ".($newFilePath ? "'$newFilePath'" : "NULL").",
                file_name_original = ".($newFileNameOriginal ? "'$newFileNameOriginal'" : "file_name_original")."
              WHERE id = $id";

if (mysqli_query($conn, $sqlUpdate)) {
    $_SESSION['flash_message'] = "✅ Dokumen berhasil diperbarui.";
} else {
    $_SESSION['flash_message'] = "❌ Gagal update data: " . mysqli_error($conn);
}

header("Location: input_dokumen.php?tab=data");

exit;
?>

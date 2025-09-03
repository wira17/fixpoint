<?php
header("Content-Type: application/json; charset=UTF-8");

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "fixpoint_system");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB gagal: " . $conn->connect_error]);
    exit;
}

// Ambil data form
$nama   = $_POST['nama_lengkap'] ?? '';
$email  = $_POST['email'] ?? '';
$no_hp  = $_POST['no_hp'] ?? '';
$pendidikan = $_POST['pendidikan_terakhir'] ?? '';
$pengalaman = $_POST['pengalaman_kerja'] ?? '';
$file_cv = "";

// Upload file CV jika ada
if (!empty($_FILES['file_cv']['name'])) {
    $target_dir = __DIR__ . "/../uploads/cv/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["file_cv"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["file_cv"]["tmp_name"], $target_file)) {
        $file_cv = $file_name;
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload file CV"]);
        exit;
    }
}

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO pelamar 
    (nama_lengkap, email, no_hp, pendidikan_terakhir, pengalaman_kerja, file_cv) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nama, $email, $no_hp, $pendidikan, $pengalaman, $file_cv);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Lamaran berhasil dikirim"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
}

$stmt->close();
$conn->close();

<?php
session_start();
include 'koneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id   = $_SESSION['user_id'];
$judul_id  = $_POST['judul_id'] ?? 0;
$jawaban   = $_POST['jawaban'] ?? [];

if (!$judul_id) {
  header("Location: kuis.php?score=0");
  exit;
}

// Ambil semua id soal untuk judul ini
$stmt = $conn->prepare("SELECT id FROM kuis WHERE judul_id = ?");
$stmt->bind_param("i", $judul_id);
$stmt->execute();
$hasil = $stmt->get_result();

while ($soal = $hasil->fetch_assoc()) {
  $soal_id = $soal['id'];
  $opsi = $jawaban[$soal_id] ?? null; // jika kosong, nilai null tetap disimpan

  $stmt2 = $conn->prepare("INSERT INTO jawaban_kuis (user_id, judul_id, soal_id, jawaban) VALUES (?, ?, ?, ?)");
  $stmt2->bind_param("iiis", $user_id, $judul_id, $soal_id, $opsi);
  $stmt2->execute();
}

// Hitung nilai akhir
$sql = "SELECT k.jawaban AS kunci, j.jawaban
        FROM kuis k
        JOIN jawaban_kuis j ON k.id = j.soal_id
        WHERE j.user_id = ? AND j.judul_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $judul_id);
$stmt->execute();
$result = $stmt->get_result();

$total = $benar = 0;
while ($row = $result->fetch_assoc()) {
  if (strtoupper($row['jawaban']) === strtoupper($row['kunci'])) {
    $benar++;
  }
  $total++;
}

$nilai = $total > 0 ? round(($benar / $total) * 100, 2) : 0;

// Simpan hasil ke tabel hasil_kuis
$stmt = $conn->prepare("INSERT INTO hasil_kuis (user_id, judul_id, nilai) VALUES (?, ?, ?)");
$stmt->bind_param("iid", $user_id, $judul_id, $nilai);
$stmt->execute();

// Redirect ke halaman kuis dengan skor
header("Location: kuis.php?id=$judul_id&score=$nilai");
exit;
?>

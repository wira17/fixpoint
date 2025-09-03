<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json; charset=utf-8');

session_start();

$user_id  = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Sesi login tidak ditemukan."]);
    exit;
}

$id_indikator = (int) ($_POST['id_indikator'] ?? 0);
$tahun        = (int) ($_POST['tahun'] ?? 0);
$bulan_num    = (int) ($_POST['bulan'] ?? 0);
$target       = (float) ($_POST['target'] ?? 0);
$numerator    = (float) ($_POST['numerator'] ?? 0);
$denominator  = (float) ($_POST['denominator'] ?? 0);

if ($id_indikator <= 0 || $tahun <= 0 || $bulan_num <= 0) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap atau tidak valid."]);
    exit;
}

$bulanList = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$bulan_nama = $bulanList[$bulan_num] ?? $bulan_num;

$capaian = ($denominator > 0) ? round(($numerator / $denominator) * 100, 2) : 0;
<?php
// Escape string values
$user_id_esc = mysqli_real_escape_string($conn, $user_id);
$bulan_nama_esc = mysqli_real_escape_string($conn, $bulan_nama);

$stmt = $conn->prepare("INSERT INTO capaian_imut 
    (id_indikator, tahun, bulan, target, numerator, denominator, capaian, created_at, created_by) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Gagal menyiapkan query: " . $conn->error]);
    exit;
}

$stmt->bind_param(
    "iisdddds",
    $id_indikator,
    $tahun,
    $bulan_nama_esc,
    $target,
    $numerator,
    $denominator,
    $capaian,
    $user_id_esc
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Data capaian berhasil disimpan."]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $stmt->error]);
    exit;
}
?>

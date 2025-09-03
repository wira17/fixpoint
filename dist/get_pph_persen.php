<?php
include 'koneksi.php';
header('Content-Type: application/json');

$bruto = isset($_GET['bruto']) ? intval($_GET['bruto']) : 0;

$stmt = $conn->prepare("SELECT persentase FROM pph21 WHERE ? BETWEEN gaji_min AND gaji_max LIMIT 1");
$stmt->bind_param("i", $bruto);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode([
  'persentase' => $data ? (float)$data['persentase'] : 0
]);
?>

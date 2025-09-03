<?php
include 'koneksi.php';

if (!isset($_GET['tipe'])) {
  echo json_encode([]);
  exit;
}

$tipe = $_GET['tipe']; // hardware atau software
$keyword = $tipe === 'hardware' ? 'Hardware' : 'Software';

$result = mysqli_query($conn, "SELECT nama, jabatan FROM users WHERE jabatan LIKE '%$keyword%'");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = [
    'value' => $row['nama'],
    'label' => $row['nama'] . ' - ' . $row['jabatan']
  ];
}

echo json_encode($data);

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'koneksi.php';

header('Content-Type: application/json');

// Ambil data dari session
$pengirim_id = $_SESSION['user_id'] ?? 0;
$nama_pengirim = $_SESSION['nama'] ?? 'Pengirim';

// Ambil data dari POST
$penerima_id = isset($_POST['penerima_id']) ? (int) $_POST['penerima_id'] : 0;
$pesan = trim($_POST['pesan'] ?? '');



if (!isset($_SESSION['user_id']) || !isset($_SESSION['nama'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Anda belum login.'
    ]);
    exit;
}

// Validasi input
if ($pengirim_id <= 0 || $penerima_id <= 0 || empty($pesan)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap atau tidak valid.'
    ]);
    exit;
}

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO pesan (pengirim_id, penerima_id, isi, waktu_kirim) VALUES (?, ?, ?, NOW())");

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyiapkan query: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("iis", $pengirim_id, $penerima_id, $pesan);

if (!$stmt->execute()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan pesan: ' . $stmt->error
    ]);
    exit;
}

// Ambil waktu kirim sekarang
$waktu_kirim = date('Y-m-d H:i:s');

// Kirim ke WebSocket
$wsData = [
    'type' => 'chat',
    'dari_id' => $pengirim_id,
    'ke_id' => $penerima_id,
    'nama_pengirim' => $nama_pengirim,
    'pesan' => $pesan,
    'timestamp' => $waktu_kirim
];

$wsPayload = json_encode($wsData);

// Hubungi WebSocket server
$fp = @stream_socket_client("tcp://127.0.0.1:8081", $errno, $errstr, 1);
if ($fp) {
    fwrite($fp, $wsPayload);
    fclose($fp);
} else {
    error_log("WebSocket gagal terhubung: $errstr ($errno)");
    // Kamu bisa mengabaikan ini agar tetap berhasil meskipun WS offline
}

// Kirim respon sukses ke client
echo json_encode([
    'status' => 'success',
    'message' => 'Pesan berhasil dikirim.',
    'data' => [
        'pengirim_id' => $pengirim_id,
        'penerima_id' => $penerima_id,
        'nama_pengirim' => $nama_pengirim,
        'pesan' => htmlspecialchars($pesan),
        'waktu_kirim' => $waktu_kirim
    ]
]);

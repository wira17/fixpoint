<?php
include 'koneksi.php'; // koneksi ke DB
date_default_timezone_set('Asia/Jakarta');

/**
 * Kirim WhatsApp melalui gateway
 * @param string $nomor Format: 628xxxxxxxxx
 * @param string $pesan Pesan yang akan dikirim
 * @return bool true jika berhasil, false jika gagal
 */
function sendWA($nomor, $pesan) {
    global $conn;

    if (empty($nomor) || empty($pesan)) {
        error_log("WA gagal: nomor atau pesan kosong.");
        return false;
    }

    // Ambil URL gateway WA dari tabel wa_setting
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_gateway_url' LIMIT 1"));
    $wa_gateway = $row['nilai'] ?? '';

    // Pastikan URL lengkap
    if (!empty($wa_gateway) && !preg_match('/^https?:\/\//', $wa_gateway)) {
        $wa_gateway = 'http://' . $wa_gateway;
    }

    if (empty($wa_gateway)) {
        error_log("WA gagal: URL gateway kosong.");
        return false;
    }   

    // Siapkan data POST sesuai format gateway
    $data = http_build_query([
        'number' => $nomor,
        'text'   => $pesan  // pastikan gateway menerima field 'text'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $wa_gateway);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Logging untuk debugging
    if ($response === false) {
        error_log("WA gagal ke $nomor: $curl_error");
        return false;
    } else {
        error_log("WA berhasil ke $nomor, response: $response");
        return true;
    }
}

/**
 * Contoh pemanggilan fungsi:
 * include 'send_wa.php';
 * sendWA('6283199354543', 'Tes pesan WA dari PHP');
 */
?>

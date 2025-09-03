<?php
include 'koneksi.php'; // koneksi ke DB
date_default_timezone_set('Asia/Jakarta');

/**
 * Kirim WhatsApp ke nomor pribadi atau grup
 * @param string|null $nomor Nomor HP internasional atau ID grup (@g.us). Jika null, pakai default DB.
 * @param string $pesan Pesan yang akan dikirim
 * @return bool true jika berhasil, false jika gagal
 */
function sendWAGroup($nomor = null, $pesan) {
    global $conn;

    // Ambil nomor default dari DB jika $nomor kosong
    if (empty($nomor)) {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_number' LIMIT 1"));
        $nomor = $row['nilai'] ?? '';
        if (empty($nomor)) {
            error_log("WA gagal: nomor tujuan kosong dan default tidak tersedia.");
            return false;
        }
    }

    if (empty($pesan)) {
        error_log("WA gagal: pesan kosong.");
        return false;
    }

    // Ambil URL gateway WA
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
        'text'   => $pesan
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

    if ($response === false) {
        error_log("WA gagal ke $nomor: $curl_error");
        return false;
    } else {
        error_log("WA berhasil ke $nomor, response: $response");
        return true;
    }
}

/**
 * Contoh penggunaan:
 */

// Kirim ke grup
//$grup_id = '120363025091234567@g.us';
//sendWAGroup($grup_id, "Halo semua, ini pesan dari sistem.");

// Kirim ke nomor pribadi default DB
//sendWAGroup(null, "Halo, ini pesan japri otomatis.");

?>

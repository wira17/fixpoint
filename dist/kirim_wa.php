<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

/**
 * Fungsi kirim WhatsApp
 * @param string $number Nomor tujuan (format: 628xxxx)
 * @param string $message Isi pesan
 * @return bool true jika sukses, false jika gagal
 */
function kirimWA($number, $message) {
    global $conn;

    // Ambil gateway WA dari database
    $wa_gateway_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_gateway_url' LIMIT 1"));
    $wa_gateway = $wa_gateway_row['nilai'] ?? '';

    if (!$number || !$wa_gateway) {
        error_log("WA gagal: nomor atau gateway belum di-set");
        return false;
    }

    $wa_data = http_build_query([
        'number' => $number,
        'text'   => $message
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $wa_gateway);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $wa_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response !== false) {
        return true;
    } else {
        error_log("WA gagal: $curl_error");
        return false;
    }
}

// === Kirim manual dari ID agenda ===
if (!isset($_GET['id'])) {
    die("ID agenda tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil data agenda
$agenda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM agenda_direktur WHERE id=$id"));
if (!$agenda) die("Agenda tidak ditemukan.");

// Ambil nomor WA direktur dari tabel users
$wa_user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_hp FROM users WHERE jabatan='Direktur' LIMIT 1"));
$wa_number = $wa_user_row['no_hp'] ?? '';

// Buat isi pesan
$wa_text = "📝 *AGENDA DIREKTUR*\n";
$wa_text .= "Judul: {$agenda['judul']}\n";
$wa_text .= "Tanggal: {$agenda['tanggal']}\n";
$wa_text .= "Jam: {$agenda['jam']}\n";
$wa_text .= "Keterangan: {$agenda['keterangan']}";

if ($agenda['file_pendukung']) {
    $wa_text .= "\nFile: " . $_SERVER['HTTP_HOST'] . "/uploads/{$agenda['file_pendukung']}";
}

// Kirim WA
if (kirimWA($wa_number, $wa_text)) {
    echo "<script>alert('Pesan WA terkirim!'); window.close();</script>";
} else {
    echo "<script>alert('Gagal mengirim WA!'); window.close();</script>";
}

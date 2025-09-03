<?php
session_start();
include 'koneksi.php';
include 'send_wa.php'; // Fungsi sendWA()
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['simpan'])) {
    $nik        = $_POST['nik'];
    $nama       = $_POST['nama'];
    $jabatan    = $_POST['jabatan'];
    $unit_kerja = $_POST['unit_kerja'];
    $kategori   = $_POST['kategori'];
    $kendala    = $_POST['kendala'];
    $user_id    = $_SESSION['user_id'];
    $tanggal    = date('Y-m-d H:i:s');

    // Ambil nomor urut terakhir hari ini
    $today = date('Y-m-d');
    $cekNomor = mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_software WHERE DATE(tanggal_input) = '$today'");
    $dataNomor = mysqli_fetch_assoc($cekNomor);
    $noUrut = $dataNomor['total'] + 1;

    // Format nomor: TKT0001/IT-SOFT/DD/MM/YYYY
    $nomor_tiket = 'TKT' . str_pad($noUrut, 4, '0', STR_PAD_LEFT) . '/IT-SOFT/' . date('d') . '/' . date('m') . '/' . date('Y');

    $status = 'Menunggu';

    // Simpan ke database
    $query = "INSERT INTO tiket_it_software (
                user_id, nik, nama, jabatan, unit_kerja,
                kategori, kendala, nomor_tiket, tanggal_input, status
              ) VALUES (
                '$user_id', '$nik', '$nama', '$jabatan', '$unit_kerja',
                '$kategori', '$kendala', '$nomor_tiket', '$tanggal', '$status'
              )";

    if (mysqli_query($conn, $query)) {

        // --- Kirim Telegram ---
        $token_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama='telegram_bot_token' LIMIT 1"));
        $chatid_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama='telegram_chat_id' LIMIT 1"));
        $token = $token_row['nilai'] ?? '';
        $chat_id = $chatid_row['nilai'] ?? '';

        $pesan_telegram  = "<b>📢 TIKET IT SOFTWARE</b>\n\n";
        $pesan_telegram .= "🆔 <b>Nomor:</b> <code>$nomor_tiket</code>\n";
        $pesan_telegram .= "👤 <b>Nama:</b> $nama\n";
        $pesan_telegram .= "💼 <b>Jabatan:</b> $jabatan\n";
        $pesan_telegram .= "🏢 <b>Unit:</b> $unit_kerja\n";
        $pesan_telegram .= "📂 <b>Kategori:</b> $kategori\n";
        $pesan_telegram .= "🛠️ <b>Kendala:</b>\n<pre>$kendala</pre>\n";
        $pesan_telegram .= "📅 <b>Tanggal:</b> $tanggal\n";

        if ($token && $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
            $data = ['chat_id'=>$chat_id,'text'=>$pesan_telegram,'parse_mode'=>'HTML'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        // --- Kirim WhatsApp ke grup IT Software ---
        $row_grup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_group_it' LIMIT 1"));
        $id_grup = $row_grup['nilai'] ?? '';

        $pesan_wa  = "📝 *TIKET IT SOFTWARE*\n";
        $pesan_wa .= "Nomor Tiket: $nomor_tiket\n";
        $pesan_wa .= "Nama: $nama\nJabatan: $jabatan\nUnit Kerja: $unit_kerja\n";
        $pesan_wa .= "Kategori: $kategori\nKendala: $kendala\n";
        $pesan_wa .= "Tanggal: $tanggal";

        if (!empty($id_grup)) {
            sendWA($id_grup, $pesan_wa);
        }

        echo "<script>alert('Tiket berhasil disimpan & dikirim ke Telegram & WA. Nomor: $nomor_tiket'); window.location.href='order_tiket_it_software.php';</script>";
    } else {
        $error = addslashes(mysqli_error($conn));
        echo "<script>alert('Gagal menyimpan tiket: $error'); window.history.back();</script>";
    }
}
?>

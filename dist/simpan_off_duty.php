<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'koneksi.php';
include 'send_wa.php'; // <-- fungsi sendWA() ada di sini
include 'send_wa_grup.php'; // <-- fungsi kirim wa ke grup
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    if (
        empty($_POST['nik']) || empty($_POST['nama']) || empty($_POST['jabatan']) ||
        empty($_POST['unit_kerja']) || empty($_POST['kategori']) || empty($_POST['keterangan'])
    ) {
        echo "<script>alert('Harap lengkapi semua field!'); window.history.back();</script>";
        exit;
    }

    $nik        = $_POST['nik'];
    $nama       = $_POST['nama'];
    $jabatan    = $_POST['jabatan'];
    $unit_kerja = $_POST['unit_kerja'];
    $kategori   = $_POST['kategori'];
    $petugas    = $_POST['petugas'] ?? '-';
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tanggal    = date('Y-m-d H:i:s');

    // Nomor tiket
    $bulan = date('m');
    $tahun = date('Y');
    $cek_terakhir = mysqli_query($conn, 
        "SELECT no_tiket FROM laporan_off_duty 
         WHERE no_tiket LIKE 'TKT%/IT-OFFDUTY/$bulan/$tahun' 
         ORDER BY id DESC LIMIT 1"
    );
    $last = mysqli_fetch_assoc($cek_terakhir);
    if ($last) {
        preg_match('/TKT(\d+)\/IT-OFFDUTY\/\d+\/\d+/', $last['no_tiket'], $matches);
        $urutan = str_pad(intval($matches[1]) + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $urutan = '0001';
    }
    $no_tiket = "TKT{$urutan}/IT-OFFDUTY/{$bulan}/{$tahun}";

    $user_id = $_SESSION['user_id'];
    $query_user = mysqli_query($conn, "SELECT nama, atasan_id, no_hp FROM users WHERE id = $user_id");
    $data_user  = mysqli_fetch_assoc($query_user);
    $nama_input = $data_user['nama'] ?? 'Tidak Diketahui';

    // Simpan ke DB
    $query = "INSERT INTO laporan_off_duty 
        (no_tiket, nik, nama, jabatan, unit_kerja, kategori, petugas, keterangan, tanggal, user_id, nama_input)
        VALUES 
        ('$no_tiket', '$nik', '$nama', '$jabatan', '$unit_kerja', '$kategori', '$petugas', '$keterangan', '$tanggal', '$user_id', '$nama_input')";

    if (mysqli_query($conn, $query)) {

        // --- Kirim Telegram ---
        $token_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'telegram_bot_token' LIMIT 1"));
        $chatid_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'telegram_chat_id' LIMIT 1"));
        $token = $token_row['nilai'] ?? '';
        $chat_id = $chatid_row['nilai'] ?? '';
        $pesan_telegram  = "<b>📢 LAPORAN OFF DUTY (LUAR JAM KERJA)</b>\n\n";
        $pesan_telegram .= "🎫 <b>No Tiket:</b> $no_tiket\n";
        $pesan_telegram .= "👤 <b>Nama:</b> $nama\n";
        $pesan_telegram .= "🆔 <b>NIK:</b> $nik\n";
        $pesan_telegram .= "💼 <b>Jabatan:</b> $jabatan\n";
        $pesan_telegram .= "🏢 <b>Unit:</b> $unit_kerja\n";
        $pesan_telegram .= "📂 <b>Kategori:</b> $kategori\n";
        $pesan_telegram .= "🛠️ <b>Petugas dipilih:</b> $petugas\n";
        $pesan_telegram .= "📝 <b>Keterangan:</b>\n<pre>$keterangan</pre>\n";
        $pesan_telegram .= "📅 <b>Waktu:</b> $tanggal\n";

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

        // --- Kirim WA ---
        $pesan_wa  = "📝 *LAPORAN OFF-DUTY*\n";
        $pesan_wa .= "Nama: $nama\nJabatan: $jabatan\nUnit Kerja: $unit_kerja\n";
        $pesan_wa .= "Kategori: $kategori\nPetugas IT: $petugas\nKeterangan: $keterangan\n";
        $pesan_wa .= "Pengajuan oleh: $nama";

        // 1. Kirim ke atasan jika ada
        $atasan_id = $data_user['atasan_id'] ?? 0;
        if ($atasan_id) {
            $row_atasan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_hp FROM users WHERE id = $atasan_id"));
            if (!empty($row_atasan['no_hp'])) {
                sendWA($row_atasan['no_hp'], $pesan_wa);
            }
        }

        // 2. Kirim ke grup WA
        $row_grup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_group_it' LIMIT 1"));
        $id_grup = $row_grup['nilai'] ?? '';
        if ($id_grup) {
            sendWA($id_grup, $pesan_wa);
        }

        echo "<script>
            alert('Laporan Off-Duty berhasil disimpan, Telegram dan WA terkirim.');
            window.location.href = 'off_duty.php';
        </script>";
    } else {
        $error = addslashes(mysqli_error($conn));
        echo "<script>
            alert('Gagal menyimpan laporan: $error');
            window.history.back();
        </script>";
    }
}
?>

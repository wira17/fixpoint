<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tiket_id = intval($_POST['tiket_id']);
  date_default_timezone_set('Asia/Jakarta');
  $waktu_validasi = date('Y-m-d H:i:s');

  if (isset($_POST['validasi'])) {
    $status = 'Diterima';
  } elseif (isset($_POST['tolak'])) {
    $status = 'Ditolak';
  }

  // Proses update status validasi dan waktu validasi
  $update = mysqli_query($conn, "UPDATE tiket_it_hardware 
    SET status_validasi = '$status', waktu_validasi = '$waktu_validasi' 
    WHERE id = $tiket_id");

  // Kirim notifikasi ke Telegram jika update berhasil
  if ($update) {
    // Ambil data tiket yang divalidasi
    $queryTiket = mysqli_query($conn, "SELECT * FROM tiket_it_hardware WHERE id = $tiket_id");
    if ($dataTiket = mysqli_fetch_assoc($queryTiket)) {
      $nomor_tiket = $dataTiket['nomor_tiket'];
      $nama        = $dataTiket['nama'];
      $jabatan     = $dataTiket['jabatan'];
      $unit_kerja  = $dataTiket['unit_kerja'];
      $kategori    = $dataTiket['kategori'];
      $kendala     = $dataTiket['kendala'];
      $tanggal     = $dataTiket['tanggal'];

      // Ambil token dan chat_id dari tabel setting
      $resultSetting = mysqli_query($conn, "SELECT nama, nilai FROM setting WHERE nama IN ('telegram_bot_token', 'telegram_chat_id')");
      $settings = [];
      while ($row = mysqli_fetch_assoc($resultSetting)) {
        $settings[$row['nama']] = $row['nilai'];
      }

      $token = $settings['telegram_bot_token'] ?? '';
      $chat_id = $settings['telegram_chat_id'] ?? '';

      if ($token && $chat_id) {
        // Format pesan Telegram
    $pesan = "<b>✅ VALIDASI TIKET IT HARDWARE</b>\n";
$pesan .= "📄 Nomor: <b>$nomor_tiket</b>\n";
$pesan .= "👤 Nama: $nama\n";
$pesan .= "💼 Jabatan: $jabatan\n";
$pesan .= "🏢 Unit: $unit_kerja\n";
$pesan .= "📂 Kategori: $kategori\n";
$pesan .= "🛠️ Kendala:\n<pre>$kendala</pre>\n";
$pesan .= "🗓️ Tanggal: $tanggal\n";
$pesan .= "📌 Status Validasi: <b>$status</b>\n";
$pesan .= "⏰ Waktu: $waktu_validasi";


        // Kirim ke Telegram
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = [
          'chat_id' => $chat_id,
          'text' => $pesan,
          'parse_mode' => 'HTML'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
      }
    }
  }

  header("Location: order_tiket_it_hardware.php");
  exit;
}
?>

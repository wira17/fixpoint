<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'koneksi.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta');

// Ambil info akses ilegal
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$waktu = date('Y-m-d H:i:s');

// Ambil setting email dari database
$mail_setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mail_settings LIMIT 1"));
if (!$mail_setting) {
  die("<h2 style='color:red;text-align:center;margin-top:50px;'>❌ Gagal: Pengaturan email tidak ditemukan.</h2>");
}

$mail = new PHPMailer(true);

try {
  // Konfigurasi SMTP
  $mail->isSMTP();
  $mail->Host       = $mail_setting['mail_host'];
  $mail->SMTPAuth   = true;
  $mail->Username   = $mail_setting['mail_username'];
  $mail->Password   = $mail_setting['mail_password'];
  $mail->SMTPSecure = 'tls';
  $mail->Port       = $mail_setting['mail_port'];

  // Pengirim dan penerima
  $mail->setFrom($mail_setting['mail_from_email'], $mail_setting['mail_from_name']);
  $mail->addAddress($mail_setting['mail_from_email'], 'Pemilik Aplikasi'); // kirim ke diri sendiri

  // Konten email
  $mail->isHTML(true);
  $mail->Subject = '[ALERT] Upaya Generate Hash Terdeteksi';
  $mail->Body = "
    <h3>🚨 Peringatan Keamanan!</h3>
    <p>Seseorang mencoba mengakses <strong>generate_hash.php</strong> tanpa izin.</p>
    <p><strong>Waktu:</strong> $waktu<br>
    <strong>IP:</strong> $ip<br>
    <strong>Browser:</strong> $user_agent</p>
    <hr>
    <p>Segera periksa aplikasi Anda.</p>
  ";

  $mail->send();

} catch (Exception $e) {
  error_log("Gagal mengirim email peringatan hash: {$mail->ErrorInfo}");
}

// Output palsu agar pengguna tidak tahu
echo "<h2 style='color:red;text-align:center;margin-top:50px;'>😕 Akses ditolak. Anda tidak memiliki izin untuk menjalankan file ini.</h2>";

// Opsional: Auto hapus file setelah dipanggil
// unlink(__FILE__);

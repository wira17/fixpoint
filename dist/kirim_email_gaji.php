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

// Validasi ID gaji
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $_SESSION['flash_message'] = "ID gaji tidak valid.";
  header("Location: data_gaji.php");
  exit;
}

$id = intval($_GET['id']);

// Ambil data gaji dan email karyawan
$query = "
  SELECT g.*, u.email, u.nama 
  FROM input_gaji g 
  JOIN users u ON g.karyawan_id = u.id 
  WHERE g.id = $id
";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$data || empty($data['email'])) {
  $_SESSION['flash_message'] = 'Data karyawan atau email tidak ditemukan.';
  header("Location: data_gaji.php");
  exit;
}

// Ambil konfigurasi email dari mail_settings
$mail_setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mail_settings LIMIT 1"));
if (!$mail_setting) {
  $_SESSION['flash_message'] = 'Pengaturan email belum dikonfigurasi.';
  header("Location: data_gaji.php");
  exit;
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

  // Pengirim & penerima
  $mail->setFrom($mail_setting['mail_from_email'], $mail_setting['mail_from_name']);
  $mail->addAddress($data['email'], $data['nama']);

  // Konten email
  $mail->isHTML(true);
  $mail->Subject = 'Slip Gaji - ' . $data['periode'] . ' ' . $data['tahun'];
  $nama = htmlspecialchars($data['nama']);
  $mail->Body = "
    <p>Assalamualaikum warahmatullahi wabarakatuh,</p>
    <p>Dengan hormat,</p>
    <p>Bersama ini kami sampaikan informasi terkait slip gaji karyawan:</p>
    <p><strong>Nama:</strong> {$nama}<br>
    <strong>Periode:</strong> {$data['periode']} {$data['tahun']}<br>
    <strong>Gaji Bersih:</strong> Rp " . number_format($data['gaji_bersih'], 0, ',', '.') . "</p>
    <p>Untuk informasi lengkap mengenai detail gaji, silakan login ke sistem atau unduh slip gaji melalui menu cetak yang tersedia.</p>
    <p>Demikian informasi ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
    <p>Wassalamualaikum warahmatullahi wabarakatuh.</p>
    <br>
    <p>Hormat kami,<br><strong>Tim Keuangan</strong></p>
  ";

  // Kirim email
  $mail->send();

  // Tandai sebagai 'Terkirim' di database
  mysqli_query($conn, "UPDATE input_gaji SET email_status = 'Terkirim' WHERE id = $id");

  $_SESSION['flash_message'] = "Email berhasil dikirim.";
} catch (Exception $e) {
  $_SESSION['flash_message'] = "Gagal mengirim email: " . $mail->ErrorInfo;
}

header("Location: data_gaji.php");
exit;

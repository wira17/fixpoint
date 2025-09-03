<?php
session_start();
include 'koneksi.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id']) && isset($_GET['status'])) {
  $id = intval($_GET['id']);
  $status_input = strtolower(trim($_GET['status']));

  $allowed_status = ['active', 'pending'];
  if (in_array($status_input, $allowed_status)) {

    // Ambil email & nama user sebelum ubah status
    $user_query = "SELECT email, nama FROM users WHERE id = $id";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);

    $query = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
      $stmt->bind_param('si', $status_input, $id);
      if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Status berhasil diubah menjadi <strong>" . ucfirst($status_input) . "</strong>.";

        // ✅ Kirim email hanya jika status menjadi "active"
        if ($status_input === 'active' && !empty($user['email'])) {
          // Ambil konfigurasi email dari tabel
          $mail_setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mail_settings LIMIT 1"));
          if ($mail_setting) {
            $mail = new PHPMailer(true);
            try {
              // Setup SMTP
              $mail->isSMTP();
              $mail->Host       = $mail_setting['mail_host'];
              $mail->SMTPAuth   = true;
              $mail->Username   = $mail_setting['mail_username'];
              $mail->Password   = $mail_setting['mail_password'];
              $mail->SMTPSecure = 'tls';
              $mail->Port       = $mail_setting['mail_port'];

              // Pengirim dan Penerima
              $mail->setFrom($mail_setting['mail_from_email'], $mail_setting['mail_from_name']);
              $mail->addAddress($user['email'], $user['nama']);

              // Isi email
              $mail->isHTML(true);
             $mail->Subject = 'Aktivasi Akun Berhasil';

            $mail->Body = "
              <p>Assalamu’alaikum warahmatullahi wabarakatuh,</p>

              <p>Yth. <strong>" . htmlspecialchars($user['nama']) . "</strong>,</p>

              <p>Dengan hormat,</p>
              <p>Akun Anda telah berhasil diaktifkan. Silakan masuk ke sistem FixPoint untuk mulai menggunakan layanan kami.</p>

              <p>Apabila Anda tidak melakukan proses pendaftaran atau merasa ada aktivitas yang tidak sah, harap segera menghubungi administrator.</p>

              <br>
              <p>Terima kasih atas perhatian Anda.</p>

              <p>Wassalamu’alaikum warahmatullahi wabarakatuh.</p>
              <br>
              <p><strong>M. Wira Sb. S. Kom</strong><br>
              <strong>Admin FixPoint</strong><br>
              <strong>0821-7784-6209</strong><br>
              <strong>FixPoint - Smart Office Management System</strong></p>
            ";


              $mail->send();
              $_SESSION['flash_message'] .= "<br>Email notifikasi berhasil dikirim.";
            } catch (Exception $e) {
              $_SESSION['flash_message'] .= "<br><small>Email gagal dikirim: " . $mail->ErrorInfo . "</small>";
            }
          } else {
            $_SESSION['flash_message'] .= "<br><small>Pengaturan email belum dikonfigurasi.</small>";
          }
        }
      } else {
        $_SESSION['flash_message'] = "Gagal mengubah status.";
      }
      $stmt->close();
    } else {
      $_SESSION['flash_message'] = "Kesalahan query database.";
    }
  } else {
    $_SESSION['flash_message'] = "Status tidak valid.";
  }
} else {
  $_SESSION['flash_message'] = "Parameter tidak lengkap.";
}

// Ambil kembali page dan keyword
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$keyword = isset($_GET['keyword']) ? urlencode($_GET['keyword']) : '';

header("Location: pengguna.php?page=$page&keyword=$keyword");
exit;

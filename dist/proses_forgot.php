<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'koneksi.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // --- cek email user ---
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    if (!$stmt) die("Query error (cek user): " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        echo "<script>alert('Email tidak terdaftar'); window.location.href='login.php';</script>";
        exit;
    }

    // --- ambil setting mail dari DB ---
    $mailset_res = $conn->query("SELECT * FROM mail_settings LIMIT 1");
    if (!$mailset_res) die("Query error (mail_settings): " . $conn->error);
    $mailset = $mailset_res->fetch_assoc();

    // --- generate token ---
    $token = bin2hex(random_bytes(32));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));
    $url_reset = rtrim($mailset['base_url'], "/") . "/reset_password.php?token=" . $token;

    // --- hapus token lama ---
    $stmt_del = $conn->prepare("DELETE FROM password_resset WHERE email=?");
    if (!$stmt_del) die("Query error (hapus token lama): " . $conn->error);
    $stmt_del->bind_param("s", $email);
    $stmt_del->execute();

    // --- simpan token baru ---
    $stmt_ins = $conn->prepare("INSERT INTO password_resset (email, token, expires_at) VALUES (?, ?, ?)");
    if (!$stmt_ins) die("Query error (insert token): " . $conn->error);
    $stmt_ins->bind_param("sss", $email, $token, $expires_at);
    $stmt_ins->execute();

    // --- kirim email ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $mailset['mail_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailset['mail_username'];
        $mail->Password   = $mailset['mail_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailset['mail_port'];

        $mail->setFrom($mailset['mail_from_email'], $mailset['mail_from_name']);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Reset Password Anda";
   $mail->Body = "
<p>Halo,</p>

<p>Kami menerima permintaan reset password untuk akun Anda di <strong>FixPoint - Smart Office Management System</strong>.</p>

<p>Silakan klik link berikut untuk melakukan reset password Anda. Link ini berlaku selama 1 jam:</p>

<p><a href='{$url_reset}'>{$url_reset}</a></p>

<p>Jika Anda tidak meminta reset password, harap abaikan email ini. Tidak ada tindakan lebih lanjut yang diperlukan.</p>

<hr>
<p>Salam,</p>
<p><strong>M. Wira, Sb. S. Kom</strong><br>
FixPoint - Smart Office Management System<br>
Telp/WA: 0821 7784 6209</p>
";


        $mail->send();
        echo "<script>alert('Link reset password berhasil dikirim ke $email'); window.location.href='login.php';</script>";
    } catch (Exception $e) {
        echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
    }
}
?>

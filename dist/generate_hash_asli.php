<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Password akses ke halaman ini
$master_password = 'FIXPOINT2025';

// Ambil pengaturan email dari database
$mail_setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mail_settings LIMIT 1"));
if (!$mail_setting) {
  die("<h2 style='color:red;text-align:center;margin-top:50px;'>❌ Gagal: Pengaturan email tidak ditemukan.</h2>");
}

// Cek login
if (!isset($_SESSION['authorized'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $master_password) {
            $_SESSION['authorized'] = true;
            header("Location: generate_hash_asli.php");
            exit;
        } else {
            echo '<p style="color:red;text-align:center;">❌ Password salah.</p>';
        }
    } else {
        echo '
        <form method="POST" style="margin:100px auto;width:300px;text-align:center;">
            <h3>🔐 Akses Terbatas</h3>
            <input type="password" name="password" placeholder="Masukkan Password" style="padding:8px;width:100%;" required>
            <br><br>
            <button type="submit" style="padding:8px 20px;">Masuk</button>
        </form>
        ';
        exit;
    }
}

// Jika tombol generate ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_hash'])) {
    $file_path = 'sidebar.php';

    if (!file_exists($file_path)) {
        die('<p style="color:red;text-align:center;">❌ File sidebar.php tidak ditemukan.</p>');
    }

    $hash = sha1_file($file_path);
    $isi_file = htmlentities(file_get_contents($file_path));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mail_setting['mail_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_setting['mail_username'];
        $mail->Password   = $mail_setting['mail_password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $mail_setting['mail_port'];

        $mail->setFrom($mail_setting['mail_from_email'], $mail_setting['mail_from_name']);
        $mail->addAddress($mail_setting['mail_from_email'], 'Pemilik Aplikasi');

        $mail->isHTML(true);
        $mail->Subject = '🔐 SHA1 Hash sidebar.php Telah Dihasilkan';
        $mail->Body = "
            <h3>🔒 SHA1 Hash sidebar.php</h3>
            <p><strong>Hash:</strong> <code>$hash</code></p>
            <p><strong>Waktu:</strong> " . date('d-m-Y H:i:s') . "</p>
            <hr>
            <p><strong>Cuplikan isi file:</strong></p>
            <pre style='background:#f8f8f8;padding:10px;border:1px solid #ccc;max-height:300px;overflow:auto;'>$isi_file</pre>
        ";

        $mail->send();
        echo '<p style="color:green;text-align:center;">✅ Hash berhasil dikirim ke email!</p>';

    } catch (Exception $e) {
        echo '<p style="color:red;text-align:center;">❌ Gagal kirim email: ' . $mail->ErrorInfo . '</p>';
    }
}
?>

<!-- Tombol Generate -->
<div style="max-width:500px;margin:50px auto;text-align:center;">
    <h3>🔐 Generate SHA1 File sidebar.php</h3>
    <form method="POST">
        <input type="hidden" name="generate_hash" value="1">
        <button type="submit" style="padding:10px 20px;">🔁 Generate & Kirim Hash</button>
    </form>
    <br>
    <a href="?logout=true" style="color:red;">🔓 Logout</a>
</div>

<?php
// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: generate_hash_asli.php");
    exit;
}
?>

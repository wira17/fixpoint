<?php
session_start();

if ($_POST['captcha_input'] === $_SESSION['captcha']) {
    echo "✅ CAPTCHA benar. Nama kamu: " . htmlspecialchars($_POST['nama']);
} else {
    echo "❌ CAPTCHA salah. Silakan ulangi.";
}
?>

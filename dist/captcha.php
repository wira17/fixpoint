<?php
session_start();

// Generate karakter acak
$captcha_text = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $captcha_text;

// Buat gambar
header('Content-Type: image/png');
$image = imagecreate(120, 40);
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

// Tambahkan teks ke gambar
imagestring($image, 5, 30, 10, $captcha_text, $text_color);

// Tambahkan garis noise
$noise_color = imagecolorallocate($image, 100, 100, 100);
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $noise_color);
}

imagepng($image);
imagedestroy($image);
?>

<?php
// check_integrity.php

// Path file sidebar.php dan hash aslinya (saat masih utuh)
$sidebar_path = __DIR__ . '/sidebar.php';
$expected_hash = '3c7694504097439a543b88d651d91b18a94c6bc3'; 

// Cek file sidebar.php masih ada?
if (!file_exists($sidebar_path)) {
    die('<h2 style="color:red;text-align:center;margin-top:50px;">ERROR: File sidebar.php tidak ditemukan.</h2>');
}

// Hitung hash saat ini
$current_hash = sha1_file($sidebar_path);

// Bandingkan
if ($current_hash !== $expected_hash) {
    die('<h2 style="color:red;text-align:center;margin-top:50px;">
        🙏 Maaf, perubahan pada file ini tidak diizinkan.<br>
        Demi menjaga integritas sistem, mohon tidak memodifikasi aplikasi ini tanpa izin.<br><br>
        Silakan hubungi pengembang di <strong>0821 7784 6209</strong> untuk informasi lebih lanjut.
    </h2>');
}


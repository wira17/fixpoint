<?php
session_start();
session_unset();     // Hapus semua variabel session
session_destroy();   // Hancurkan session

// Arahkan kembali ke halaman login
header("Location: login.php");
exit;

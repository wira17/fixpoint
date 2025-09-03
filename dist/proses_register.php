<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik         = trim($_POST['nik']);
    $nama        = trim($_POST['nama']);
    $jabatan     = trim($_POST['jabatan']);
    $unit_kerja  = trim($_POST['unit_kerja']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $konfirmasi  = $_POST['konfirmasi_password'];
    $atasan_id   = !empty($_POST['atasan_id']) ? $_POST['atasan_id'] : null;

    // Validasi sederhana
    if ($password !== $konfirmasi) {
        echo "<script>alert('Konfirmasi password tidak cocok.');history.back();</script>";
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (nik, nama, jabatan, unit_kerja, email, password_hash, atasan_id, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssssssi", $nik, $nama, $jabatan, $unit_kerja, $email, $password_hash, $atasan_id);

    if ($stmt->execute()) {
        // --- Telegram notification ---
        $token_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'telegram_bot_token' LIMIT 1"));
        $chatid_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'telegram_chat_id' LIMIT 1"));

        if ($token_row && $chatid_row) {
            $token = $token_row['nilai'];
            $chat_id = $chatid_row['nilai'];

            $pesan  = "<b>🆕 PENDAFTARAN AKUN BARU</b>\n\n";
            $pesan .= "👤 <b>Nama:</b> $nama\n";
            $pesan .= "🆔 <b>NIK:</b> $nik\n";
            $pesan .= "💼 <b>Jabatan:</b> $jabatan\n";
            $pesan .= "🏢 <b>Unit:</b> $unit_kerja\n";
            $pesan .= "✉️ <b>Email:</b> $email\n";
            $pesan .= "⏳ <i>Menunggu aktivasi admin...</i>\n";

            $url = "https://api.telegram.org/bot$token/sendMessage";
            $data = [
                'chat_id' => $chat_id,
                'text' => $pesan,
                'parse_mode' => 'HTML'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        // --- WhatsApp notification via Gateway ---
        $wa_number_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_number' LIMIT 1"));
        $wa_url_row    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_gateway_url' LIMIT 1"));

        $wa_number = $wa_number_row ? $wa_number_row['nilai'] : '';
        $wa_url    = $wa_url_row ? $wa_url_row['nilai'] : '';

        if ($wa_number && $wa_url) {
            $wa_text = "🆕 PENDAFTARAN AKUN BARU DI APLIKASI FIXPOINT\n";
            $wa_text .= "Nama: $nama\n";
            $wa_text .= "NIK: $nik\n";
            $wa_text .= "Jabatan: $jabatan\n";
            $wa_text .= "Unit: $unit_kerja\n";
            $wa_text .= "Email: $email\n";
            $wa_text .= "Status: Menunggu aktivasi admin";

            $wa_data = http_build_query([
                'number' => $wa_number,
                'text'   => $wa_text
            ]);

            $wa_options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => $wa_data,
                    'timeout' => 5
                ]
            ];
            $wa_context = stream_context_create($wa_options);
            @file_get_contents($wa_url, false, $wa_context);
        }

        // Redirect kembali ke login
        echo "<script>alert('Pendaftaran berhasil. Tunggu aktivasi admin.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal: " . $stmt->error . "'); history.back();</script>";
    }
}
?>

<?php
session_start();
require 'koneksi.php';

$token = $_GET['token'] ?? '';
$notif = '';

// Proses form reset password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'], $_POST['konfirmasi'], $_POST['token'])) {
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $token = $_POST['token'];

    if ($password !== $konfirmasi) {
        $notif = "Password dan konfirmasi tidak sama.";
    } else {
        // --- cek token valid ---
        $stmt = $conn->prepare("SELECT email, expires_at FROM password_resset WHERE token=? LIMIT 1");
        if (!$stmt) {
            die("Query error (prepare failed): " . $conn->error);
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($email, $expires_at);

        if ($stmt->fetch()) {
            $stmt->close();

            // cek expired (1 jam)
            if (strtotime($expires_at) >= time()) {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // update password di tabel users
                $stmt2 = $conn->prepare("UPDATE users SET password_hash=? WHERE email=?");
                if (!$stmt2) {
                    die("Query error (update password): " . $conn->error);
                }
                $stmt2->bind_param("ss", $hash, $email);
                $stmt2->execute();
                $stmt2->close();

                // hapus token
                $stmt3 = $conn->prepare("DELETE FROM password_resset WHERE email=?");
                if (!$stmt3) {
                    die("Query error (hapus token lama): " . $conn->error);
                }
                $stmt3->bind_param("s", $email);
                $stmt3->execute();
                $stmt3->close();

                $_SESSION['notif'] = "Password berhasil direset. Silakan login.";
                header("Location: login.php");
                exit;
            } else {
                $notif = "Token sudah kadaluarsa.";
            }
        } else {
            $notif = "Token tidak valid.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Reset Password</h5>
                    <?php if ($notif): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($notif) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="konfirmasi" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="login.php">Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

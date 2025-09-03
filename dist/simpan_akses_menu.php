<?php
include 'koneksi.php';
session_start();

$user_id = $_POST['user_id'] ?? null;
$menu_ids = $_POST['menu_ids'] ?? [];

if ($user_id && is_array($menu_ids)) {
    foreach ($menu_ids as $menu_id) {
        // Cegah duplikat
        $cek = mysqli_query($conn, "SELECT * FROM akses_menu WHERE user_id='$user_id' AND menu_id='$menu_id'");
        if (mysqli_num_rows($cek) == 0) {
            mysqli_query($conn, "INSERT INTO akses_menu (user_id, menu_id) VALUES ('$user_id', '$menu_id')");
        }
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>

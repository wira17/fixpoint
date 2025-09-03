<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

include 'koneksi.php';

$login_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT id, nama FROM users WHERE id != '$login_id' ORDER BY nama ASC");

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);

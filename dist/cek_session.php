<?php
session_start();
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'BELUM LOGIN');
?>

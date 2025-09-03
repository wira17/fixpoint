<?php
$host_wa = "172.16.10.134";
$user_wa = "root";
$pass_wa = "";
$db_wa   = "wa_delphi";

$conn_wa = new mysqli($host_wa, $user_wa, $pass_wa, $db_wa);
if ($conn_wa->connect_error) {
    die("Koneksi WA gagal: " . $conn_wa->connect_error);
}
?>

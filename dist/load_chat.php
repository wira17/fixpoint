<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');




// Cek login
if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  exit('Unauthorized access');
}

$login_id = $_SESSION['user_id'];
$penerima_id = $_POST['penerima_id'] ?? null;

if (!$penerima_id) {
  exit('ID penerima tidak valid');
}

// Ambil data chat antara login user dan penerima
$query = mysqli_query($conn, "
  SELECT * FROM pesan 
  WHERE (pengirim_id = '$login_id' AND penerima_id = '$penerima_id') 
     OR (pengirim_id = '$penerima_id' AND penerima_id = '$login_id')
  ORDER BY waktu_kirim ASC
");

while ($chat = mysqli_fetch_assoc($query)) {
  $isMe = ($chat['pengirim_id'] == $login_id);
  $align = $isMe ? 'text-right' : 'text-left';
  $bubbleClass = $isMe ? 'bg-primary text-white' : 'bg-light text-dark';
  $roundedSide = $isMe ? 'rounded-start' : 'rounded-end';
  $time = date('d/m H:i', strtotime($chat['waktu_kirim']));
  
  echo '
  <div class="'.$align.'">
    <div class="d-inline-block px-3 py-2 rounded mb-1 '.$bubbleClass.' '.$roundedSide.'" style="max-width: 75%;">
      '.nl2br(htmlspecialchars($chat['isi'])).'
      <div class="text-muted small mt-1">'.$time.'</div>
    </div>
  </div>';
}
?>

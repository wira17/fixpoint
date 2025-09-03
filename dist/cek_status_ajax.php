<?php
include 'koneksi.php';
include 'send_wa.php';

function cekKoneksi($url) {
    if (!preg_match('~^https?://~i', $url)) $url = "https://$url";
    $parsed = parse_url($url);
    $host = $parsed['host'];
    $port = isset($parsed['scheme']) && $parsed['scheme']==='https'?443:80;
    $fp = @fsockopen($host,$port,$errno,$errstr,3);
    if ($fp) { fclose($fp); return true; }
    return false;
}

$id_grup_row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nilai FROM wa_setting WHERE nama='wa_group_it' LIMIT 1"));
$id_grup = $id_grup_row['nilai'] ?? '';

$urls = mysqli_query($conn,"SELECT * FROM master_url");
$response = [];

while($row=mysqli_fetch_assoc($urls)){
    $statusNow = cekKoneksi($row['base_url'])?'online':'offline';
    $statusLast = $row['status_last'] ?? '';

    if($statusNow!=$statusLast && !empty($id_grup)){
        $pesan_wa = "🔔 KONEKSI {$row['nama_koneksi']}\nStatus berubah: *$statusLast* → *$statusNow*\nURL: {$row['base_url']}\nWaktu: ".date('Y-m-d H:i:s');
        sendWA($id_grup,$pesan_wa);
        mysqli_query($conn,"UPDATE master_url SET status_last='$statusNow' WHERE id={$row['id']}");
    }

    $response[] = [
        'nama'=>$row['nama_koneksi'],
        'status'=>$statusNow
    ];
}

echo json_encode($response);

<?php
include 'security.php';
include 'koneksi.php';
include 'send_wa.php'; // Fungsi sendWA()
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek akses user
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Fungsi cek koneksi URL
function cekKoneksi($url) {
    if (!preg_match('~^https?://~i', $url)) $url = "https://" . $url;
    $parsed = parse_url($url);
    $host = $parsed['host'];
    $port = isset($parsed['scheme']) && $parsed['scheme'] === 'https' ? 443 : 80;
    $fp = @fsockopen($host, $port, $errno, $errstr, 3);
    if ($fp) { fclose($fp); return true; }
    return false;
}

// Ambil ID grup WA
$row_grup = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_group_it' LIMIT 1")
);
$id_grup = $row_grup['nilai'] ?? '';

// Pencarian
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$query_url = "SELECT * FROM master_url";
if (!empty($keyword)) {
    $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
    $query_url .= " WHERE nama_koneksi LIKE '%$keywordEscaped%' OR base_url LIKE '%$keywordEscaped%'";
}
$query_url .= " ORDER BY nama_koneksi ASC";
$result_url = mysqli_query($conn, $query_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Monitoring Koneksi Bridging</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="stylesheet" href="assets/css/components.css" />
</head>
<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div class="main-content">
<section class="section">
<div class="section-body">
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
<h4>Monitoring Koneksi Bridging</h4>
<form method="GET" class="form-inline">
<input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari URL / IP" />
<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
</form>
</div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-sm table-hover">
<thead class="thead-dark">
<tr class="text-center">
<th>No</th>
<th>Nama Koneksi</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
if (mysqli_num_rows($result_url) > 0) {
    while ($row = mysqli_fetch_assoc($result_url)) {
        $statusOnline = cekKoneksi($row['base_url']);
        $statusNow = $statusOnline ? 'online' : 'offline';
        $statusLast = $row['status_last'] ?? '';

        // Notifikasi WA jika status berubah
        if ($statusLast !== $statusNow && !empty($id_grup)) {
            $pesan_wa = "🔔 KONEKSI {$row['nama_koneksi']}\nStatus berubah: *$statusLast* → *$statusNow*\nURL: {$row['base_url']}\nWaktu: ".date('Y-m-d H:i:s');
            $waResult = sendWA($id_grup, $pesan_wa);
            if (!$waResult) {
                error_log("Gagal kirim WA ke grup $id_grup untuk {$row['nama_koneksi']}");
            }
            // Update status_last di DB
            mysqli_query($conn, "UPDATE master_url SET status_last='$statusNow' WHERE id={$row['id']}");
        }

        // Tampilkan tabel
        echo "<tr>";
        echo "<td class='text-center'>{$no}</td>";
        echo "<td>{$row['nama_koneksi']}</td>";
        echo "<td class='text-center'>";
        if ($statusOnline) echo "<span class='badge badge-success'><i class='fas fa-check-circle'></i> Online</span>";
        else echo "<span class='badge badge-danger'><i class='fas fa-times-circle'></i> Offline</span>";
        echo "</td></tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='3' class='text-center'>Tidak ada data ditemukan.</td></tr>";
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</section>
</div>
</div>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
function refreshStatus(){
    $.getJSON('cek_status_ajax.php', function(data){
        data.forEach(function(row, i){
            var badge = row.status==='online' ? 
                "<span class='badge badge-success'><i class='fas fa-check-circle'></i> Online</span>" :
                "<span class='badge badge-danger'><i class='fas fa-times-circle'></i> Offline</span>";
            $("table tbody tr").eq(i).find("td").eq(2).html(badge);
        });
    });
}

// Refresh tiap 30 detik
setInterval(refreshStatus, 30000);
$(document).ready(refreshStatus);
</script>

</body>
</html>

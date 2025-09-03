<?php
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<script>alert('Anda belum login.'); window.location.href='login.php';</script>";
    exit;
}

$current_file = basename(__FILE__);

// Cek akses menu
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = ? AND menu.file_menu = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $current_file);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Ambil data user SDM
$qUser = $conn->prepare("SELECT nik, nama FROM users WHERE id = ?");
$qUser->bind_param("i", $user_id);
$qUser->execute();
$resUser = $qUser->get_result();
$user = $resUser->fetch_assoc();

// Proses update ACC SDM
if (isset($_POST['status_sdm']) && isset($_POST['id_izin'])) {
    $id_izin = intval($_POST['id_izin']);
    $status_sdm = $_POST['status_sdm'];
    $waktu_acc_sdm = date('Y-m-d H:i:s');

    if (!in_array($status_sdm, ['disetujui','ditolak'])) {
        $_SESSION['flash_message'] = "Status tidak valid.";
        header("Location: acc_keluar_sdm.php");
        exit;
    }

    // Hanya bisa approve jika atasan sudah approve
    $qCheck = $conn->prepare("SELECT status_atasan FROM izin_keluar WHERE id = ?");
    $qCheck->bind_param("i", $id_izin);
    $qCheck->execute();
    $resCheck = $qCheck->get_result();
    $rowCheck = $resCheck->fetch_assoc();

    if($status_sdm == 'disetujui' && $rowCheck['status_atasan'] != 'disetujui') {
        $_SESSION['flash_message'] = "❌ Izin belum disetujui oleh atasan.";
        header("Location: acc_keluar_sdm.php");
        exit;
    }

    $qUpdate = $conn->prepare("UPDATE izin_keluar SET status_sdm = ?, waktu_acc_sdm = ?, acc_oleh_sdm = ? WHERE id = ?");
    $qUpdate->bind_param("ssii", $status_sdm, $waktu_acc_sdm, $user_id, $id_izin);
    $qUpdate->execute();

    $_SESSION['flash_message'] = $qUpdate->affected_rows > 0 ? "✅ Status ACC SDM berhasil diperbarui." : "❌ Gagal memperbarui status SDM.";
    header("Location: acc_keluar_sdm.php");
    exit;
}

// Filter pencarian & periode
$filterNama   = $_GET['nama'] ?? '';
$filterNik    = $_GET['nik'] ?? '';
$filterDari   = $_GET['dari'] ?? date('Y-m-d'); 
$filterSampai = $_GET['sampai'] ?? date('Y-m-d'); 

// Query semua data, tanpa filter status_atasan
$sql = "SELECT * FROM izin_keluar WHERE tanggal BETWEEN ? AND ?";
$params = [$filterDari, $filterSampai];
$types = "ss";

if (!empty($filterNama)) {
    $sql .= " AND nama LIKE ?";
    $params[] = "%$filterNama%";
    $types .= "s";
}

if (!empty($filterNik)) {
    $sql .= " AND nik LIKE ?";
    $params[] = "%$filterNik%";
    $types .= "s";
}

$sql .= " ORDER BY tanggal DESC, created_at DESC";

$qIzin = $conn->prepare($sql);
$qIzin->bind_param($types, ...$params);
$qIzin->execute();
$data_izin = $qIzin->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>ACC Izin Keluar SDM</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<style>
.flash-center { position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); z-index: 1050; min-width: 300px; max-width: 90%; text-align: center; padding: 15px; border-radius: 8px; font-weight: 500; box-shadow: 0 5px 15px rgba(0,0,0,0.3);}
.izin-table { font-size: 13px; white-space: nowrap; }
.izin-table th, .izin-table td { padding: 6px 10px; vertical-align: middle; }
</style>
</head>
<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
<section class="section">
<div class="section-body">

<?php if(isset($_SESSION['flash_message'])): ?>
<div class="alert alert-info flash-center" id="flashMsg">
<?= htmlspecialchars($_SESSION['flash_message']) ?>
</div>
<?php unset($_SESSION['flash_message']); endif; ?>

<div class="card">
<div class="card-header">
<h4 class="mb-0">Daftar Izin Keluar - Approval SDM</h4>
</div>
<div class="card-body">

<!-- Form Filter -->
<form class="form-inline mb-3" method="get">
<input type="text" class="form-control mr-2" name="nama" placeholder="Nama" value="<?= htmlspecialchars($filterNama) ?>">
<input type="text" class="form-control mr-2" name="nik" placeholder="NIK" value="<?= htmlspecialchars($filterNik) ?>">
<input type="date" class="form-control mr-2" name="dari" value="<?= htmlspecialchars($filterDari) ?>">
<input type="date" class="form-control mr-2" name="sampai" value="<?= htmlspecialchars($filterSampai) ?>">
<button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Cari</button>
<a href="acc_keluar_sdm.php" class="btn btn-secondary mr-2">Reset</a>
<a href="cetak_pdf_sdm.php?dari=<?= $filterDari ?>&sampai=<?= $filterSampai ?>&nama=<?= urlencode($filterNama) ?>&nik=<?= urlencode($filterNik) ?>" target="_blank" class="btn btn-success"><i class="fas fa-print"></i> Cetak PDF</a>
</form>

<div class="table-responsive">
<table class="table table-bordered izin-table">
<thead class="thead-dark text-center">
<tr>
<th>No</th><th>Nama</th><th>NIK</th><th>Jabatan</th><th>Tanggal</th><th>Jam Keluar</th><th>Jam Kembali</th>
<th>Keperluan</th><th>Status Atasan</th><th>Status SDM</th><th>Aksi</th>
</tr>
</thead>
<tbody>
<?php if($data_izin && $data_izin->num_rows>0):
$no=1;
while($izin=$data_izin->fetch_assoc()): ?>
<tr>
<td class="text-center"><?= $no++ ?></td>
<td><?= htmlspecialchars($izin['nama']) ?></td>
<td><?= htmlspecialchars($izin['nik']) ?></td>
<td><?= htmlspecialchars($izin['jabatan']) ?></td>
<td><?= date('d-m-Y', strtotime($izin['tanggal'])) ?></td>
<td><?= htmlspecialchars($izin['jam_keluar']) ?></td>
<td><?= htmlspecialchars($izin['jam_kembali']) ?></td>
<td><?= htmlspecialchars($izin['keperluan']) ?></td>
<td class="text-center">
<?php
$badgeAtasan='secondary';
if($izin['status_atasan']=='disetujui') $badgeAtasan='success';
elseif($izin['status_atasan']=='pending') $badgeAtasan='warning';
echo "<span class='badge badge-$badgeAtasan'>".ucfirst($izin['status_atasan'])."</span><br>";
echo "<small>".($izin['waktu_acc_atasan']?date('d-m-Y H:i',strtotime($izin['waktu_acc_atasan'])):'-')."</small>";
?>
</td>
<td class="text-center">
<?php
$badge='secondary';
if($izin['status_sdm']=='disetujui') $badge='success';
elseif($izin['status_sdm']=='ditolak') $badge='danger';
echo "<span class='badge badge-$badge'>".ucfirst($izin['status_sdm'])."</span><br>";
echo "<small>".($izin['waktu_acc_sdm']?date('d-m-Y H:i',strtotime($izin['waktu_acc_sdm'])):'-')."</small>";
?>
</td>
<td class="text-center">
<?php if($izin['status_sdm']=='pending'): ?>
<form method="POST" style="display:inline-block;">
<input type="hidden" name="id_izin" value="<?= $izin['id'] ?>">
<button type="submit" name="status_sdm" value="disetujui" class="btn btn-sm btn-success" onclick="return confirm('Setujui izin keluar ini?')"><i class="fas fa-check"></i></button>
</form>
<form method="POST" style="display:inline-block;">
<input type="hidden" name="id_izin" value="<?= $izin['id'] ?>">
<button type="submit" name="status_sdm" value="ditolak" class="btn btn-sm btn-danger" onclick="return confirm('Tolak izin keluar ini?')"><i class="fas fa-times"></i></button>
</form>
<?php else: ?><span>-</span><?php endif; ?>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="11" class="text-center">Tidak ada data izin keluar.</td></tr>
<?php endif; ?>
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
$(document).ready(function(){
setTimeout(function() { $("#flashMsg").fadeOut("slow"); }, 3000);
});
</script>
</body>
</html>

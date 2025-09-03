<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek akses
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// Pencarian
$keyword = $_GET['keyword'] ?? '';

// Pagination
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Filter pencarian
$where = "";
if(!empty($keyword)){
    $keywordEsc = mysqli_real_escape_string($conn, $keyword);
    $where = "WHERE pelamar_akun.nama_lengkap LIKE '%$keywordEsc%' OR lowongan.posisi LIKE '%$keywordEsc%'";
}

// Hitung total
$countQuery = "SELECT COUNT(*) as total 
               FROM pelamar_lamaran 
               LEFT JOIN pelamar_akun ON pelamar_lamaran.pelamar_id = pelamar_akun.id
               LEFT JOIN lowongan ON pelamar_lamaran.lowongan_id = lowongan.id
               $where";
$totalData = mysqli_fetch_assoc(mysqli_query($conn, $countQuery))['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data lamaran
$query = "SELECT pelamar_lamaran.*, pelamar_akun.nama_lengkap, pelamar_akun.email, lowongan.posisi
          FROM pelamar_lamaran
          LEFT JOIN pelamar_akun ON pelamar_lamaran.pelamar_id = pelamar_akun.id
          LEFT JOIN lowongan ON pelamar_lamaran.lowongan_id = lowongan.id
          $where
          ORDER BY pelamar_lamaran.tanggal_kirim DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1" name="viewport" />
<title>f.i.x.p.o.i.n.t - Data Lamaran</title>
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
<h4>Data Lamaran Pelamar</h4>
<form method="GET" class="form-inline">
    <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Nama / Posisi" />
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
</form>
</div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-sm table-hover">
<thead class="thead-dark">
<tr class="text-center">
<th>No</th>
<th>Nama Pelamar</th>
<th>Email</th>
<th>Posisi</th>
<th>File Lamaran</th>
<th>Tanggal Kirim</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php
$no = $offset + 1;
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<tr>";
        echo "<td class='text-center'>{$no}</td>";
        echo "<td>" . ($row['nama_lengkap'] ?? '-') . "</td>";
        echo "<td>" . ($row['email'] ?? '-') . "</td>";
        echo "<td>" . ($row['posisi'] ?? '-') . "</td>";

        echo "<td class='text-center'>";
        $filePath = "../fixpoint/uploads/cv/" . ($row['file_lamaran'] ?? '');
        if(!empty($row['file_lamaran']) && file_exists($filePath)){
            echo "<a href='$filePath' target='_blank' class='btn btn-info btn-sm'><i class='fas fa-file'></i> Download</a>";
        } else {
            echo "-";
        }
        echo "</td>";

        echo "<td class='text-center'>" . date('d M Y H:i', strtotime($row['tanggal_kirim'])) . "</td>";
        
        $status = $row['status'] ?? 'Pending';
        $badge = ($status=='Diterima') ? 'success' : (($status=='Ditolak') ? 'danger':'secondary');
        echo "<td class='text-center'><span class='badge badge-$badge'>$status</span></td>";

        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>Tidak ada lamaran ditemukan.</td></tr>";
}
?>
</tbody>
</table>
</div>

<!-- Pagination -->
<nav>
<ul class="pagination justify-content-center mt-3">
<?php if($page>1): ?>
<li class="page-item"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page-1 ?>">&laquo;</a></li>
<?php endif; ?>

<?php for($i=1;$i<=$totalPages;$i++): ?>
<li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a></li>
<?php endfor; ?>

<?php if($page<$totalPages): ?>
<li class="page-item"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page+1 ?>">&raquo;</a></li>
<?php endif; ?>
</ul>
</nav>

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
</body>
</html>

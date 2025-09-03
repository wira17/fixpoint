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
if ($result->num_rows == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Ambil filter
$tgl_dari   = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';
$search     = $_GET['search'] ?? '';

// Pagination
$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Build query
$where  = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($tgl_dari) && !empty($tgl_sampai)) {
    $where .= " AND DATE(c.tanggal) BETWEEN ? AND ?";
    $params[] = $tgl_dari;
    $params[] = $tgl_sampai;
    $types   .= "ss";
} elseif (!empty($tgl_dari)) {
    $where .= " AND DATE(c.tanggal) >= ?";
    $params[] = $tgl_dari;
    $types   .= "s";
} elseif (!empty($tgl_sampai)) {
    $where .= " AND DATE(c.tanggal) <= ?";
    $params[] = $tgl_sampai;
    $types   .= "s";
}

if (!empty($search)) {
    $where .= " AND (c.judul LIKE ? OR c.isi LIKE ? OR u.nama LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types   .= "sss";
}

// Hitung total data
$count_sql = "SELECT COUNT(*) as total 
              FROM catatan_kerja c 
              JOIN users u ON c.user_id = u.id $where";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_data = $count_stmt->get_result()->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_data / $limit);

// Ambil data catatan kerja
$sql = "SELECT c.*, u.nama 
        FROM catatan_kerja c 
        JOIN users u ON c.user_id = u.id
        $where ORDER BY c.tanggal DESC LIMIT ?, ?";
$params_page = $params;
$types_page  = $types . "ii";
$params_page[] = $offset;
$params_page[] = $limit;

$stmt_data = $conn->prepare($sql);
$stmt_data->bind_param($types_page, ...$params_page);
$stmt_data->execute();
$data_catatan = $stmt_data->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Rekap Catatan Kerja</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
    .catatan-table { font-size: 13px; white-space: nowrap; }
    .catatan-table th, .catatan-table td { padding: 8px 10px; vertical-align: middle; }
    .pagination { justify-content: center; }
    .table-responsive { overflow-x: auto; }
    /* Atur lebar minimal kolom biar rapi */
    .catatan-table th:nth-child(1), .catatan-table td:nth-child(1) { min-width: 50px; text-align: center; }
    .catatan-table th:nth-child(2), .catatan-table td:nth-child(2) { min-width: 150px; }
    .catatan-table th:nth-child(3), .catatan-table td:nth-child(3) { min-width: 200px; }
    .catatan-table th:nth-child(4), .catatan-table td:nth-child(4) { min-width: 300px; }
    .catatan-table th:nth-child(5), .catatan-table td:nth-child(5) { min-width: 150px; text-align: center; }
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

          <div class="card">
            <div class="card-header">
              <h4>Rekap Catatan Kerja</h4>
            </div>
            <div class="card-body">

  <!-- Filter Form -->
<form method="GET" class="form-inline mb-3">
  <label class="mr-2">Tanggal Dari:</label>
  <input type="date" name="tgl_dari" value="<?= htmlspecialchars($tgl_dari) ?>" class="form-control mr-2">
  <label class="mr-2">Tanggal Sampai:</label>
  <input type="date" name="tgl_sampai" value="<?= htmlspecialchars($tgl_sampai) ?>" class="form-control mr-2">
  <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama/judul/isi catatan" class="form-control mr-2">
  <button type="submit" class="btn btn-primary">Filter</button>
  <a href="rekap_catatan_kerja.php" class="btn btn-secondary ml-2">Reset</a>

  <!-- Tombol Cetak PDF -->
  <a href="cetak_catatan_kerja.php?tgl_dari=<?= urlencode($tgl_dari) ?>&tgl_sampai=<?= urlencode($tgl_sampai) ?>&search=<?= urlencode($search) ?>" 
     target="_blank" class="btn btn-danger ml-2">
     <i class="fa fa-file-pdf"></i> Cetak PDF
  </a>
</form>


              <div class="table-responsive">
                <table class="table table-bordered catatan-table">
                  <thead class="thead-dark">
                    <tr class="text-center">
                      <th>No</th>
                      <th>Nama Pengguna</th>
                      <th>Judul</th>
                      <th>Catatan Kerja</th>
                      <th>Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($data_catatan && $data_catatan->num_rows > 0): ?>
                      <?php $no = $offset + 1; while ($catatan = $data_catatan->fetch_assoc()) : ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($catatan['nama']) ?></td>
                          <td><?= htmlspecialchars($catatan['judul']) ?></td>
                          <td><?= htmlspecialchars($catatan['isi']) ?></td>
                          <td><?= date('d-m-Y H:i', strtotime($catatan['tanggal'])) ?></td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center">Tidak ada catatan kerja.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <nav>
                <ul class="pagination">
                  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>&tgl_dari=<?= urlencode($tgl_dari) ?>&tgl_sampai=<?= urlencode($tgl_sampai) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>

            </div>
          </div>

        </div>
      </section>
    </div>
  </div>
</div>

<!-- JS -->
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

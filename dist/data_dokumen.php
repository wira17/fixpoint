<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Cek akses user
$user_id = $_SESSION['user_id'] ?? 0;
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
           JOIN menu ON akses_menu.menu_id = menu.id 
           WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Ambil list pokja untuk filter
$list_pokja = mysqli_query($conn, "SELECT id, nama_pokja FROM master_pokja ORDER BY nama_pokja ASC");

// Tangkap filter jika ada
$filter_judul = $_GET['judul'] ?? '';
$filter_pokja = $_GET['pokja_id'] ?? '';

// Bangun query dengan filter
$sql = "SELECT d.*, mp.nama_pokja 
        FROM dokumen d 
        JOIN master_pokja mp ON d.pokja_id = mp.id 
        WHERE 1=1 ";

if (!empty($filter_judul)) {
    $judul_safe = mysqli_real_escape_string($conn, $filter_judul);
    $sql .= " AND d.judul LIKE '%$judul_safe%' ";
}
if (!empty($filter_pokja)) {
    $pokja_safe = intval($filter_pokja);
    $sql .= " AND d.pokja_id = $pokja_safe ";
}

$sql .= " ORDER BY d.waktu_input DESC";

$data_dokumen = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Dokumen Akreditasi</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<style>
  .dokumen-table { font-size: 13px; white-space: nowrap; }
  .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
</style>
</head>
<body>
<div class="main-wrapper main-wrapper-1">
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Dokumen Akreditasi</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form Filter -->
                        <form method="GET" class="form-inline mb-3">
                            <div class="form-group mr-2">
                                <input type="text" name="judul" class="form-control" placeholder="Cari Judul" value="<?= htmlspecialchars($filter_judul) ?>">
                            </div>
                            <div class="form-group mr-2">
                                <select name="pokja_id" class="form-control">
                                    <option value="">-- Semua Pokja --</option>
                                    <?php while($p = mysqli_fetch_assoc($list_pokja)): ?>
                                        <option value="<?= $p['id'] ?>" <?= ($filter_pokja == $p['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nama_pokja']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                            <a href="data_dokumen.php" class="btn btn-secondary ml-2"><i class="fas fa-sync"></i> Reset</a>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered dokumen-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Pokja</th>
                                        <th>Elemen Penilaian</th>
                                        <th>File</th>
                                        <th>Petugas</th>
                                        <th>Tanggal Input</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; while($dok = mysqli_fetch_assoc($data_dokumen)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($dok['judul']) ?></td>
                                        <td><?= htmlspecialchars($dok['nama_pokja']) ?></td>
                                        <td><?= htmlspecialchars($dok['elemen_penilaian'] ?? '') ?></td>
                                        <td>
                                            <?php if(!empty($dok['file_path'])): ?>
                                                <a href="<?= htmlspecialchars($dok['file_path']) ?>" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php else: ?>-
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($dok['petugas']) ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime($dok['waktu_input'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($data_dokumen) == 0): ?>
                                    <tr><td colspan="7" class="text-center">Data tidak ditemukan.</td></tr>
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

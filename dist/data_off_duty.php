<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

$current_file = basename(__FILE__); // 

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// ========== LOGIKA LANJUTAN HALAMAN INI =========

// Fungsi badge warna status
function statusColor($status) {
  $status = strtolower($status);
  return match ($status) {
    'menunggu' => 'secondary',
    'diproses' => 'info',
    'selesai' => 'success',
    'tidak bisa diperbaiki' => 'dark',
    'ditolak' => 'danger',
    default => 'light',
  };
}

// Ambil parameter pencarian
$keyword     = $_GET['keyword'] ?? '';
$tgl_dari    = $_GET['tgl_dari'] ?? '';
$tgl_sampai  = $_GET['tgl_sampai'] ?? '';

// Tampilkan halaman sesuai desain (lanjutkan HTML di bawah sini...)
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>f.i.x.p.o.i.n.t</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
 
  .modal-backdrop { z-index: 1040 !important; }
  .modal { z-index: 1050 !important; }
  .table-nowrap td, .table-nowrap th {
    white-space: nowrap;
    vertical-align: middle;
  }
</style>

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
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-user-clock text-primary"></i> Data Laporan Off Duty</h4>
              </div>
              <div class="card-body">
                <form method="GET" class="form-inline mb-3">
                  <div class="row w-100 align-items-end">
                    <div class="col-md-3">
                      <input type="text" name="keyword" class="form-control w-100" placeholder="Cari nama/kategori/petugas" value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <div class="col-md-2">
                      <input type="date" name="tgl_dari" class="form-control w-100" value="<?= $tgl_dari ?>">
                    </div>
                    <div class="col-md-2">
                      <input type="date" name="tgl_sampai" class="form-control w-100" value="<?= $tgl_sampai ?>">
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                    <div class="col-md-2">
                      <a href="data_off_duty.php" class="btn btn-secondary w-100">Reset</a>
                    </div>
                  </div>
                </form>

<?php
$limit = 6;
$page = (int) ($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$whereClauses = [];

if (!empty($tgl_dari) && !empty($tgl_sampai)) {
  $whereClauses[] = "DATE(tanggal) BETWEEN '$tgl_dari' AND '$tgl_sampai'";
} else {
  $whereClauses[] = "DATE(tanggal) = CURDATE()";
}

if (!empty($keyword)) {
  $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
  $whereClauses[] = "(nama LIKE '%$keywordEscaped%' OR kategori LIKE '%$keywordEscaped%' OR petugas LIKE '%$keywordEscaped%')";
}

$where = "WHERE " . implode(" AND ", $whereClauses);

$totalRows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_off_duty $where"))['total'];
$totalPages = ceil($totalRows / $limit);

$query = "SELECT * FROM laporan_off_duty $where ORDER BY tanggal DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
$no = $offset + 1;
?>

<div class="table-responsive">
  <table class="table table-bordered table-sm table-hover table-nowrap">

<thead class="thead-dark text-center">
  <tr>
    <th>No</th><th>No Tiket</th><th>Tanggal</th><th>Nama Pelapor</th><th>NIK</th><th>Jabatan</th>
    <th>Unit Kerja</th><th>Kategori</th><th>Petugas</th><th>Keterangan</th>
    <th>Catatan IT</th><th>Tgl Validasi</th><th>Validator</th><th>Aksi</th>
  </tr>
</thead>

    <tbody>
<?php if (mysqli_num_rows($result) > 0): while ($row = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td class="text-center"><?= $no++; ?></td>
<td class="text-center"><?= htmlspecialchars($row['no_tiket'] ?? '-'); ?></td>

    <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
    <td><?= htmlspecialchars($row['nama']); ?></td>
    <td><?= htmlspecialchars($row['nik']); ?></td>
    <td><?= htmlspecialchars($row['jabatan']); ?></td>
    <td><?= htmlspecialchars($row['unit_kerja']); ?></td>
    <td><?= htmlspecialchars($row['kategori']); ?></td>
    <td><?= htmlspecialchars($row['petugas']); ?></td>
    <td><?= htmlspecialchars($row['keterangan']); ?></td>
 
    <td><?= nl2br(htmlspecialchars($row['catatan_it'])); ?></td>
    <td><?= !empty($row['tanggal_validasi']) ? date('d-m-Y H:i', strtotime($row['tanggal_validasi'])) : '-'; ?></td>
    <td>
      <?php
        $validator = '-';
        if (!empty($row['validator_id'])) {
         $getValidator = mysqli_query($conn, "SELECT nama FROM users WHERE id = '{$row['validator_id']}'");

          if ($v = mysqli_fetch_assoc($getValidator)) {
            $validator = $v['nama'];
          }
        }
        echo $validator;
      ?>
    </td>
    <td class="text-center">
      <button class="btn btn-sm btn-warning" onclick="bukaModal(<?= $row['id']; ?>, <?= htmlspecialchars(json_encode($row['status_validasi'])); ?>, <?= htmlspecialchars(json_encode($row['catatan_it'])); ?>)">
        <i class="fa fa-edit"></i> Ubah
      </button>
    </td>
  </tr>
<?php endwhile; else: ?>
  <tr><td colspan="15" class="text-center">Tidak ada data ditemukan.</td></tr>
<?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
  <nav><ul class="pagination justify-content-center mt-3">
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
      <a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>&tgl_dari=<?= $tgl_dari; ?>&tgl_sampai=<?= $tgl_sampai; ?>"><?= $i; ?></a>
    </li>
  <?php endfor; ?>
  </ul></nav>
<?php endif; ?>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>

<!-- Modal Global -->
<div class="modal fade" id="ubahStatusModalGlobal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="update_status_off_duty.php" method="POST">
      <input type="hidden" name="id" id="modalInputId">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Ubah Status dan Catatan IT</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Status Validasi</label>
        <select name="status_validasi" id="modalStatus" class="form-control" required>
  <option value="Menunggu">Menunggu</option>
  <option value="Diproses">Diproses</option>
  <option value="Selesai">Selesai</option>
  <option value="Tidak Bisa Diperbaiki">Tidak Bisa Diperbaiki</option>
  <option value="Ditolak">Ditolak</option>
</select>

          </div>
          <div class="form-group">
            <label>Catatan IT</label>
            <textarea name="catatan_it" id="modalCatatan" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>

<script>
function bukaModal(id, status, catatan) {
  $('#modalInputId').val(id);
  $('#modalStatus').val(status);
  $('#modalCatatan').val(catatan);
  $('#ubahStatusModalGlobal').modal('show');
}
</script>
</body>
</html>

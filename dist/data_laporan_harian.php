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

$filter_tanggal = $_GET['tanggal'] ?? '';
$filter_unit = $_GET['unit_kerja'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$where = [];
if (!empty($filter_tanggal)) $where[] = "DATE(l.tanggal_input) = '" . mysqli_real_escape_string($conn, $filter_tanggal) . "'";
if (!empty($filter_unit)) $where[] = "u.unit_kerja = '" . mysqli_real_escape_string($conn, $filter_unit) . "'";
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_harian l JOIN users u ON l.user_id = u.id $where_sql");
$total_data = mysqli_fetch_assoc($q_total)['total'];
$total_pages = ceil($total_data / $limit);

$q = mysqli_query($conn, "SELECT l.*, u.nik, u.nama, u.jabatan, u.unit_kerja 
                          FROM laporan_harian l 
                          JOIN users u ON l.user_id = u.id 
                          $where_sql
                          ORDER BY l.tanggal_input DESC
                          LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Laporan Harian</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
    .table-nowrap td, .table-nowrap th {
      white-space: nowrap;
      vertical-align: middle;
    }
    thead th {
      background-color: #000 !important;
      color: #fff !important;
    }
    .table-responsive-custom {
      overflow-x: auto;
    }
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
              <h4><i class="fas fa-calendar-day text-primary mr-2"></i> Daftar Laporan Harian Seluruh Unit</h4>
            </div>

            <div class="card-body">
              <form method="GET" class="form-inline mb-4">
                <label class="mr-2">Tanggal:</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>" class="form-control mr-3">

                <label class="mr-2">Unit Kerja:</label>
                <select name="unit_kerja" class="form-control mr-3">
                  <option value="">Semua</option>
                  <?php
                  $q_unit = mysqli_query($conn, "SELECT DISTINCT unit_kerja FROM users WHERE unit_kerja IS NOT NULL ORDER BY unit_kerja ASC");
                  while ($u = mysqli_fetch_assoc($q_unit)) {
                    $sel = ($filter_unit == $u['unit_kerja']) ? 'selected' : '';
                    echo "<option value='{$u['unit_kerja']}' $sel>{$u['unit_kerja']}</option>";
                  }
                  ?>
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
              </form>

              <div class="table-responsive-custom mt-3">
                <table class="table table-bordered table-striped table-nowrap">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>NIK</th>
                      <th>Nama</th>
                      <th>Jabatan</th>
                      <th>Unit Kerja</th>
                      <th>Uraian</th>
                      <th>Dokumen</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = $offset + 1;
                    if (mysqli_num_rows($q) > 0) {
                      while ($d = mysqli_fetch_assoc($q)) {
                        echo "<tr>
                          <td>{$no}</td>
                          <td>" . date('Y-m-d', strtotime($d['tanggal_input'])) . "</td>
                          <td>{$d['nik']}</td>
                          <td>{$d['nama']}</td>
                          <td>{$d['jabatan']}</td>
                          <td>{$d['unit_kerja']}</td>
                          <td>" . nl2br(htmlspecialchars($d['uraian'])) . "</td>
                          <td>";
                        if (!empty($d['file_laporan'])) {
                          echo "<a href='uploads/laporan_harian/{$d['file_laporan']}' target='_blank' class='btn btn-sm btn-secondary'><i class='fas fa-file-download'></i></a>";
                        } else {
                          echo "<span class='text-muted'>-</span>";
                        }
                        echo "</td></tr>";
                        $no++;
                      }
                    } else {
                      echo "<tr><td colspan='8' class='text-center'>Belum ada laporan harian.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                  <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                      <?php
                      $query_params = $_GET;
                      if ($page > 1):
                        $query_params['page'] = $page - 1;
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Previous</a></li>';
                      endif;

                      for ($i = 1; $i <= $total_pages; $i++):
                        $query_params['page'] = $i;
                        $active = $i == $page ? 'active' : '';
                        echo "<li class='page-item $active'><a class='page-link' href='?" . http_build_query($query_params) . "'>$i</a></li>";
                      endfor;

                      if ($page < $total_pages):
                        $query_params['page'] = $page + 1;
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Next</a></li>';
                      endif;
                      ?>
                    </ul>
                  </nav>
                <?php endif; ?>
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
</body>
</html>
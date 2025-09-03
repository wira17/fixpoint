<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek akses menu
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// Proses pencarian
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t - Data Pelamar</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />

  <style>
    #notif-toast {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
      display: none;
      min-width: 300px;
    }
    .table-responsive { overflow-x: auto; }
    .table td, .table th { white-space: nowrap; }
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
              <h4>Data Pelamar</h4>
              <form method="GET" class="form-inline">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Nama / Email" />
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
              </form>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark">
                    <tr class="text-center">
                      <th>No</th>
                      <th>Nama Lengkap</th>
                      <th>Email</th>
                      <th>No HP</th>
                      <th>Pendidikan</th>
                      <th>Status Akun</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
<?php
if (isset($_SESSION['flash_message'])) {
  echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
  unset($_SESSION['flash_message']);
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Query
$where = "";
if (!empty($keyword)) {
  $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
  $where = "WHERE nama_lengkap LIKE '%$keywordEscaped%' OR email LIKE '%$keywordEscaped%'";
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM pelamar_akun $where";
$countResult = mysqli_query($conn, $countQuery);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalData / $limit);

// Fetch data
$query = "SELECT * FROM pelamar_akun $where ORDER BY nama_lengkap ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$no = $offset + 1;
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='text-center'>{$no}</td>";
    echo "<td>{$row['nama_lengkap']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['no_hp']}</td>";
    echo "<td>{$row['pendidikan_terakhir']}</td>";

    echo "<td class='text-center'>";
    echo $row['status_akun'] == 'Aktif'
      ? "<span class='badge badge-success'>Aktif</span>"
      : "<span class='badge badge-secondary'>Pending</span>";
    echo "</td>";

    echo "<td class='text-center'>";
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $keywordParam = urlencode($keyword);

    if ($row['status_akun'] == 'Aktif') {
      echo "<a href='ubah_status_pelamar.php?id={$row['id']}&status=Pending&page={$currentPage}&keyword={$keywordParam}' class='btn btn-danger btn-sm'>Nonaktifkan</a>";
    } else {
      echo "<a href='ubah_status_pelamar.php?id={$row['id']}&status=Aktif&page={$currentPage}&keyword={$keywordParam}' class='btn btn-success btn-sm'>Aktifkan</a>";
    }
    echo "</td>";

    echo "</tr>";
    $no++;
  }
} else {
  echo "<tr><td colspan='7' class='text-center'>Tidak ada data ditemukan.</td></tr>";
}
?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <nav>
                <ul class="pagination justify-content-center mt-3">
                  <?php if ($page > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">&laquo;</a>
                    </li>
                  <?php endif; ?>

                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                      <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                      <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">&raquo;</a>
                    </li>
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

<script>
  $(document).ready(function () {
    var toast = $('#notif-toast');
    if (toast.length) {
      toast.fadeIn(300).delay(2000).fadeOut(500);
    }
  });
</script>
</body>
</html>

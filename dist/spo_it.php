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

// Pencarian & Pagination
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM spo_it";
if (!empty($keyword)) {
  $kw = mysqli_real_escape_string($conn, $keyword);
  $totalQuery .= " WHERE nomor_spo LIKE '%$kw%' OR judul LIKE '%$kw%'";
}
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t</title>

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
                <h4><i class="fas fa-file-alt text-primary mr-2"></i>Data SPO IT</h4>
                <form method="GET" class="form-inline">
                  <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Nomor/ Judul SPO" />
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
                </form>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                      <tr class="text-center">
                        <th>No</th>
                        <th>Nomor SPO</th>
                        <th>Judul</th>
                        <th>File</th>
                        <th>Petugas Upload</th>
                        <th>Tanggal Upload</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['flash_message'])) {
                        echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
                        unset($_SESSION['flash_message']);
                      }

                      $no = $offset + 1;
                      $query = "SELECT * FROM spo_it";
                      if (!empty($keyword)) {
                        $kw = mysqli_real_escape_string($conn, $keyword);
                        $query .= " WHERE nomor_spo LIKE '%$kw%' OR judul LIKE '%$kw%'";
                      }
                      $query .= " ORDER BY tanggal_upload DESC LIMIT $limit OFFSET $offset";

                      $result = mysqli_query($conn, $query);
                      if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          echo "<td class='text-center'>{$no}</td>";
                          echo "<td>{$row['nomor_spo']}</td>";
                          echo "<td>{$row['judul']}</td>";
                          echo "<td class='text-center'><a href='uploads/{$row['file_spo']}' target='_blank' class='btn btn-info btn-sm'><i class='fas fa-file-download'></i> Unduh</a></td>";
                          echo "<td>{$row['petugas_upload']}</td>";
                          echo "<td class='text-center'>" . date('d-m-Y H:i', strtotime($row['tanggal_upload'])) . "</td>";
                          echo "</tr>";
                          $no++;
                        }
                      } else {
                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                  <nav>
                    <ul class="pagination justify-content-center mt-3">
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                          <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>
                    </ul>
                  </nav>
                <?php endif; ?>
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

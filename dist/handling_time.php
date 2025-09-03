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

// Pencarian & Filter tanggal
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$dari_tanggal = isset($_GET['dari_tanggal']) ? $_GET['dari_tanggal'] : '';
$sampai_tanggal = isset($_GET['sampai_tanggal']) ? $_GET['sampai_tanggal'] : '';

if ($dari_tanggal) $dari_tanggal = date('Y-m-d', strtotime($dari_tanggal));
if ($sampai_tanggal) $sampai_tanggal = date('Y-m-d', strtotime($sampai_tanggal));
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

    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive-custom table {
      min-width: 1500px;
      white-space: nowrap;
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
              <h4><i class="fas fa-clock me-2"></i> Data Handling Time Tiket</h4>
            </div>
            <div class="card-body">

              <!-- Tabs Menu -->
              <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                  <a class="nav-link active" href="handling_time.php">IT Hardware</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="handling_time_software.php">IT Software</a>
                </li>
              </ul>

              <!-- Form Filter -->
              <form method="GET" class="form-inline mb-3">
                <div class="form-group mr-2">
                  <label for="dari_tanggal" class="mr-2">Dari</label>
                  <input type="date" name="dari_tanggal" id="dari_tanggal" 
                         class="form-control" value="<?php echo isset($_GET['dari_tanggal']) ? $_GET['dari_tanggal'] : ''; ?>">
                </div>
                <div class="form-group mr-2">
                  <label for="sampai_tanggal" class="mr-2">Sampai</label>
                  <input type="date" name="sampai_tanggal" id="sampai_tanggal" 
                         class="form-control" value="<?php echo isset($_GET['sampai_tanggal']) ? $_GET['sampai_tanggal'] : ''; ?>">
                </div>
                <div class="form-group mr-2">
                  <input type="text" name="keyword" class="form-control" placeholder="Cari NIK / Nama / No Tiket"
                         value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="handling_time.php" class="btn btn-secondary ml-2">Reset</a>
              </form>

              <!-- Table -->
              <div class="table-responsive-custom">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark">
                    <tr class="text-center">
                      <th>No</th>
                      <th>Nomor Tiket</th>
                      <th>NIK</th>
                      <th>Nama</th>
                      <th>Jabatan</th>
                      <th>Unit Kerja</th>
                      <th>Kategori</th>
                      <th>Kendala</th>
                      <th>Status</th>
                      <th>Teknisi</th>
                      <th>Tgl Input</th>
                      <th>Diproses</th>
                      <th>Selesai</th>
                      <th>Validasi</th>
                      <th>Waktu Validasi</th>
                      <th>Respon Time</th>
                      <th>Selesai Time</th>
                      <th>Validasi Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($_SESSION['flash_message'])) {
                      echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
                      unset($_SESSION['flash_message']);
                    }

                    $no = 1;
                    $query = "SELECT * FROM tiket_it_hardware WHERE 1=1";

                    // Filter keyword
                    if (!empty($keyword)) {
                      $kw = mysqli_real_escape_string($conn, $keyword);
                      $query .= " AND (nik LIKE '%$kw%' OR nama LIKE '%$kw%' OR nomor_tiket LIKE '%$kw%')";
                    }

                    // Filter tanggal
                    if (!empty($dari_tanggal) && !empty($sampai_tanggal)) {
                      $query .= " AND DATE(tanggal_input) BETWEEN '$dari_tanggal' AND '$sampai_tanggal'";
                    }

                    $query .= " ORDER BY tanggal_input DESC";

                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td class='text-center'>{$no}</td>";
                        echo "<td>{$row['nomor_tiket']}</td>";
                        echo "<td>{$row['nik']}</td>";
                        echo "<td>{$row['nama']}</td>";
                        echo "<td>{$row['jabatan']}</td>";
                        echo "<td>{$row['unit_kerja']}</td>";
                        echo "<td>{$row['kategori']}</td>";
                        echo "<td>{$row['kendala']}</td>";

                        // Status
                        echo "<td class='text-center'>";
                        $status = $row['status'];
                        $badgeClass = match (strtolower($status)) {
                          'menunggu' => 'warning',
                          'diproses' => 'info',
                          'selesai' => 'success',
                          'tidak bisa diperbaiki' => 'danger',
                          default => 'secondary'
                        };
                        echo "<span class='badge badge-{$badgeClass}'>{$status}</span>";
                        echo "</td>";

                        echo "<td>{$row['teknisi_nama']}</td>";
                        echo "<td>" . formatTanggal($row['tanggal_input']) . "</td>";
                        echo "<td>" . formatTanggal($row['waktu_diproses']) . "</td>";
                        echo "<td>" . formatTanggal($row['waktu_selesai']) . "</td>";
                        echo "<td>{$row['status_validasi']}</td>";
                        echo "<td>" . formatTanggal($row['waktu_validasi']) . "</td>";
                        echo "<td>" . hitungDurasi($row['tanggal_input'], $row['waktu_diproses']) . "</td>";
                        echo "<td>" . hitungDurasi($row['tanggal_input'], $row['waktu_selesai']) . "</td>";
                        echo "<td>" . hitungDurasi($row['tanggal_input'], $row['waktu_validasi']) . "</td>";
                        echo "</tr>";
                        $no++;
                      }
                    } else {
                      echo "<tr><td colspan='18' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                    }

                    function formatTanggal($tanggal) {
                      return $tanggal ? date('d-m-Y H:i', strtotime($tanggal)) : '-';
                    }

                    function hitungDurasi($mulai, $selesai) {
                      if (!$mulai || !$selesai) return '-';
                      $start = new DateTime($mulai);
                      $end = new DateTime($selesai);
                      $interval = $start->diff($end);
                      $jam = $interval->h + ($interval->days * 24);
                      $menit = $interval->i;
                      return "{$jam}j {$menit}m";
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
  $(document).ready(function () {
    var toast = $('#notif-toast');
    if (toast.length) {
      toast.fadeIn(300).delay(2000).fadeOut(500);
    }
  });
</script>
</body>
</html>

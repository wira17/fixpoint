<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');


// Fungsi format tanggal Indonesia
function formatTanggalIndo($tanggal) {
  $bulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  $pecah = explode('-', $tanggal);
  return $pecah[2] . ' ' . $bulan[(int)$pecah[1] - 1] . ' ' . $pecah[0];
}

// Filter bulan dan tahun
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Pagination setup
$limit = 10;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$offset = ($halaman - 1) * $limit;

// Hitung total data
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM agenda_direktur 
  WHERE MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'");
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_halaman = ceil($total_data / $limit);

// Ambil data agenda (dengan limit dan offset)
$data_agenda = mysqli_query($conn, "SELECT a.*, u.nama AS user_nama 
  FROM agenda_direktur a 
  LEFT JOIN users u ON a.user_input = u.id 
  WHERE MONTH(a.tanggal) = '$filter_bulan' AND YEAR(a.tanggal) = '$filter_tahun'
  ORDER BY a.tanggal DESC
  LIMIT $limit OFFSET $offset");

$bulanIndo = [
  1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>f.i.x.p.o.i.n.t</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    .table-agenda {
      white-space: nowrap;
      min-width: 1000px;
    }
    .row-highlight {
      background-color: #d4edda !important;
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
              <h4>Agenda Direktur</h4>
            </div>
            <div class="card-body">

              <form method="GET" class="form-inline mb-3">
                <label class="mr-2">Filter Bulan:</label>
                <select name="bulan" class="form-control mr-2">
                  <?php
                    foreach ($bulanIndo as $num => $nama) {
                      $selected = ($filter_bulan == $num) ? 'selected' : '';
                      echo "<option value='$num' $selected>$nama</option>";
                    }
                  ?>
                </select>
                <select name="tahun" class="form-control mr-2">
                  <?php
                    $tahun_sekarang = date('Y');
                    for ($t = $tahun_sekarang - 2; $t <= $tahun_sekarang + 2; $t++) {
                      $selected = ($filter_tahun == $t) ? 'selected' : '';
                      echo "<option value='$t' $selected>$t</option>";
                    }
                  ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
              </form>

              <div class="table-responsive-custom">
                <table class="table table-bordered table-striped table-agenda">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Judul</th>
                      <th>Tanggal</th>
                      <th>Jam</th>
                      <th>Keterangan</th>
                      <th>File</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $no = $offset + 1; 
                      $today = date('Y-m-d');
                      while ($row = mysqli_fetch_assoc($data_agenda)) : 
                        $is_today_or_past = ($row['tanggal'] <= $today);
                    ?>
                      <tr class="<?= $is_today_or_past ? 'row-highlight' : '' ?>">
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= formatTanggalIndo($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars(substr($row['jam'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td>
                          <?php if ($row['file_pendukung']) : ?>
                            <a href="uploads/<?= $row['file_pendukung'] ?>" target="_blank" class="btn btn-sm btn-info">
                              <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                          <?php else : ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <nav>
                <ul class="pagination justify-content-center">
                  <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
                    <li class="page-item <?= ($halaman == $i) ? 'active' : '' ?>">
                      <a class="page-link" href="?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>&hal=<?= $i ?>"><?= $i ?></a>
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

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>

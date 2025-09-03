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

// Ambil filter tanggal dari GET
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Validasi dan format tanggal untuk query
$where_hardware = '';
$where_software = '';

if ($start_date && $end_date) {
    // Format ke Y-m-d untuk query, tambahkan waktu mulai dan akhir hari
    $start_datetime = date('Y-m-d 00:00:00', strtotime($start_date));
    $end_datetime = date('Y-m-d 23:59:59', strtotime($end_date));
    
    $where_hardware = " WHERE tanggal_ba BETWEEN '$start_datetime' AND '$end_datetime' ";
    $where_software = " WHERE tanggal_ba BETWEEN '$start_datetime' AND '$end_datetime' ";
}

// Query data hardware dengan filter
$query_hardware = "SELECT * FROM berita_acara $where_hardware ORDER BY tanggal_ba DESC";
$result_hardware = mysqli_query($conn, $query_hardware);

// Query data software dengan filter
$query_software = "SELECT * FROM berita_acara_software $where_software ORDER BY tanggal_ba DESC";
$result_software = mysqli_query($conn, $query_software);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t - Berita Acara IT</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />

  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive-custom table {
      width: 100%;
      min-width: 1300px; /* tambah lebar supaya muat kolom aksi */
      white-space: nowrap;
    }

    .table thead th {
      background-color: #000 !important;
      color: #fff !important;
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
              <h4><i class="fas fa-tools text-warning mr-2"></i>Data Berita Acara IT</h4>

             <form class="form-inline" method="GET" action="<?= $current_file ?>">
  <div class="form-group mr-2">
    <label for="start_date" class="mr-2 mb-0 font-weight-bold">Dari</label>
    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" required />
  </div>
  <div class="form-group mr-2">
    <label for="end_date" class="mr-2 mb-0 font-weight-bold">Sampai</label>
    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" required />
  </div>
  <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Filter</button>

  <?php if ($start_date && $end_date): ?>
    <a href="cetak_berita_acara_it.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" 
       target="_blank" 
       class="btn btn-success" 
       title="Cetak Laporan Periode">
      <i class="fas fa-print"></i> Cetak Laporan Periode
    </a>
  <?php endif; ?>
</form>

            </div>

            <div class="card-body">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="hardware-tab" data-toggle="tab" href="#hardware" role="tab" aria-controls="hardware" aria-selected="true">BA IT Hardware</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="software-tab" data-toggle="tab" href="#software" role="tab" aria-controls="software" aria-selected="false">BA IT Software</a>
                </li>
              </ul>

              <div class="tab-content mt-4" id="myTabContent">

                <!-- BA IT Hardware Tab -->
                <div class="tab-pane fade show active" id="hardware" role="tabpanel" aria-labelledby="hardware-tab">
                  <div class="table-responsive-custom">
                    <table class="table table-bordered table-striped table-hover">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nomor BA</th>
                          <th>Nomor Tiket</th>
                          <th>Tanggal</th>
                          <th>NIK</th>
                          <th>Nama Pelapor</th>
                          <th>Jabatan</th>
                          <th>Unit Kerja</th>
                          <th>Kategori</th>
                          <th>Kendala</th>
                          <th>Catatan Teknisi</th>
                          <th>Tanggal BA</th>
                          <th>Teknisi</th>
                          <th>Dibuat</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        if(mysqli_num_rows($result_hardware) > 0):
                          $no = 1;
                          while($row = mysqli_fetch_assoc($result_hardware)): 
                        ?>
                          <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nomor_ba']); ?></td>
                            <td><?= htmlspecialchars($row['nomor_tiket']); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['nik']); ?></td>
                            <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                            <td><?= htmlspecialchars($row['jabatan']); ?></td>
                            <td><?= htmlspecialchars($row['unit_kerja']); ?></td>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['kendala'])); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['catatan_teknisi'])); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal_ba'])); ?></td>
                            <td><?= htmlspecialchars($row['teknisi']); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                           
                          </tr>
                        <?php 
                          endwhile;
                        else: ?>
                          <tr>
                            <td colspan="15" class="text-center">Data berita acara hardware belum tersedia.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- BA IT Software Tab -->
                <div class="tab-pane fade" id="software" role="tabpanel" aria-labelledby="software-tab">
                  <div class="table-responsive-custom">
                    <table class="table table-bordered table-striped table-hover">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nomor BA</th>
                          <th>Nomor Tiket</th>
                          <th>Tanggal</th>
                          <th>NIK</th>
                          <th>Nama Pelapor</th>
                          <th>Jabatan</th>
                          <th>Unit Kerja</th>
                          <th>Kategori</th>
                          <th>Kendala</th>
                          <th>Catatan Teknisi</th>
                          <th>Tanggal BA</th>
                          <th>Teknisi</th>
                          <th>Dibuat</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        if(mysqli_num_rows($result_software) > 0):
                          $no = 1;
                          while($row = mysqli_fetch_assoc($result_software)): 
                        ?>
                          <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nomor_ba']); ?></td>
                            <td><?= htmlspecialchars($row['nomor_tiket']); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['nik']); ?></td>
                            <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                            <td><?= htmlspecialchars($row['jabatan']); ?></td>
                            <td><?= htmlspecialchars($row['unit_kerja']); ?></td>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['kendala'])); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['catatan_teknisi'])); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal_ba'])); ?></td>
                            <td><?= htmlspecialchars($row['teknisi']); ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                           
                          </tr>
                        <?php 
                          endwhile;
                        else: ?>
                          <tr>
                            <td colspan="15" class="text-center">Data berita acara software belum tersedia.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div> <!-- End Tab Content -->
            </div> <!-- End Card Body -->
          </div> <!-- End Card -->

        </div> <!-- End Section Body -->
      </section>
    </div> <!-- End Main Content -->
  </div> <!-- End Main Wrapper -->
</div> <!-- End App -->

<!-- Scripts -->
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

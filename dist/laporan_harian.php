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

$user_id = $_SESSION['user_id'];
$queryUser = mysqli_query($conn, "SELECT nik, nama, jabatan, unit_kerja FROM users WHERE id = '$user_id'");
$userData = mysqli_fetch_assoc($queryUser);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Laporan Harian</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />

  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    .table-responsive-custom table {
      width: 100%;
      min-width: 1000px;
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
            <div class="card-header">
              <h4><i class="fas fa-calendar-check text-primary mr-2"></i> Laporan Kerja Harian</h4>
            </div>

            <div class="card-body">
              <ul class="nav nav-tabs" id="laporanTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Laporan</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">Riwayat Laporan</a>
                </li>
              </ul>

              <div class="tab-content mt-4" id="laporanTabContent">
                <!-- Form Input -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="POST" action="simpan_laporan_harian.php" enctype="multipart/form-data">

                    <div class="row">
                      <div class="form-group col-md-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?= $userData['nik']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $userData['nama']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?= $userData['jabatan']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Unit Kerja</label>
                        <input type="text" name="unit_kerja" class="form-control" value="<?= $userData['unit_kerja']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-12">
                        <label>Uraian Tugas / Kegiatan</label>
                        <textarea name="uraian" class="form-control" rows="4" required></textarea>
                      </div>
                      <div class="form-group col-md-12">
                        <label>Unggah Dokumen (Opsional, PDF/Word/Excel)</label>
                        <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                      </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Laporan</button>
                  </form>
                </div>

                <!-- Riwayat -->
                <div class="tab-pane fade" id="riwayat" role="tabpanel">
                  <div class="table-responsive-custom mt-3">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Tanggal</th>
                          <th>Uraian</th>
                          <th>Dokumen</th>
                          <th>Oleh</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        $q = mysqli_query($conn, "SELECT * FROM laporan_harian WHERE user_id = '$user_id' ORDER BY tanggal_input DESC");
                        if (mysqli_num_rows($q) > 0) {
                          while ($d = mysqli_fetch_assoc($q)) {
                            echo "<tr>
                              <td>{$no}</td>
                              <td>" . date('d-m-Y', strtotime($d['tanggal_input'])) . "</td>
                              <td>" . nl2br(htmlspecialchars($d['uraian'])) . "</td>
                              <td>";
                              if (!empty($d['file_dokumen'])) {
                                echo "<a href='uploads/laporan_harian/{$d['file_dokumen']}' target='_blank' class='btn btn-sm btn-secondary'><i class='fas fa-file-download'></i></a>";
                              } else {
                                echo "<span class='text-muted'>-</span>";
                              }
                              echo "</td>
                              <td>{$userData['nama']}</td>
                              <td>
                                <a href='cetak_laporan_harian.php?id={$d['id']}' target='_blank' class='btn btn-sm btn-info'>
                                  <i class='fas fa-print'></i>
                                </a>
                              </td>
                            </tr>";
                            $no++;
                          }
                        } else {
                          echo "<tr><td colspan='6' class='text-center'>Belum ada laporan.</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div> <!-- End Riwayat -->
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>
  </div>
</div>

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

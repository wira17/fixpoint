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

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
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
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive-custom table {
      width: 100%;
      min-width: 1200px;
      white-space: nowrap;
    }

    .d-flex.gap-1 > form {
      margin-right: 5px;
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
              <h4>Order Tiket IT Software</h4>
            </div>
            <div class="card-body">

              <!-- Nav Tabs -->
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="order-tab" data-toggle="tab" href="#order" role="tab">Order Tiket</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tiket-saya-tab" data-toggle="tab" href="#tiket-saya" role="tab">Tiket Saya</a>
                </li>
              </ul>

              <!-- Tab Content -->
              <div class="tab-content mt-4" id="myTabContent">

                <!-- Order Tiket -->
                <div class="tab-pane fade show active" id="order" role="tabpanel">
                  <form method="POST" action="simpan_tiket_it_software.php">
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="nik">NIK</label>
                        <input type="text" name="nik" id="nik" class="form-control" value="<?= $userData['nik']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= $userData['nama']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?= $userData['jabatan']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="unit_kerja">Unit Kerja</label>
                        <input type="text" name="unit_kerja" id="unit_kerja" class="form-control" value="<?= $userData['unit_kerja']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="kategori">Kategori Software</label>
                        <select class="form-control" name="kategori" required>
                          <option value="">-- Pilih Kategori --</option>
                          <?php
                          $kategoriResult = mysqli_query($conn, "SELECT nama_kategori FROM kategori_software");
                          while ($k = mysqli_fetch_assoc($kategoriResult)) {
                            echo "<option value='{$k['nama_kategori']}'>{$k['nama_kategori']}</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div class="form-group col-md-12">
                        <label for="kendala">Kendala / Laporan</label>
                        <textarea name="kendala" class="form-control" rows="3" required></textarea>
                      </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Tiket</button>
                  </form>
                </div>

                <!-- Tiket Saya -->
                <div class="tab-pane fade" id="tiket-saya" role="tabpanel">
                  <div class="table-responsive-custom">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nomor Tiket</th>
                          <th>Tanggal</th>
                          <th>Kategori</th>
                          <th>Kendala</th>
                          <th>Catatan IT</th>
                          <th>Status</th>
                          <th>Validasi</th>
                          <th>Ticket</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        $queryTiket = mysqli_query($conn, "SELECT * FROM tiket_it_software WHERE user_id = '$user_id' ORDER BY tanggal_input DESC");
                        if (mysqli_num_rows($queryTiket) > 0) {
                          while ($row = mysqli_fetch_assoc($queryTiket)) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['nomor_tiket']}</td>
                                    <td>" . date('d-m-Y H:i', strtotime($row['tanggal_input'])) . "</td>
                                    <td>{$row['kategori']}</td>
                                    <td>{$row['kendala']}</td>
                                    <td>" . (!empty($row['catatan_it']) ? nl2br(htmlspecialchars($row['catatan_it'])) : '-') . "</td>
                                    <td><span class='badge badge-" . statusColor($row['status']) . "'>{$row['status']}</span></td>
                                    <td>" . renderValidasiButton($row['status_validasi'], $row['id']) . "</td>
                                     <td>
                                      <a href='cetak_tiket_it_software.php?id={$row['id']}' target='_blank' class='btn btn-sm btn-info' title='Lihat Tiket'>
                                        <i class='fas fa-print'></i>
                                      </a>
                                    </td>
                                  </tr>";
                            $no++;
                          }
                        } else {
                          echo "<tr><td colspan='8' class='text-center'>Belum ada tiket.</td></tr>";
                        }

                        function statusColor($status) {
                          switch (strtolower($status)) {
                            case 'menunggu': return 'warning';
                            case 'diproses': return 'info';
                            case 'selesai': return 'success';
                            case 'ditolak': return 'danger';
                            default: return 'secondary';
                          }
                        }

                        function renderValidasiButton($status_validasi, $id) {
                          switch ($status_validasi) {
                            case 'Belum Validasi':
                              return "
                                <div class='d-flex gap-1'>
                                  <form method='post' action='validasi_tiket_software.php' style='display:inline-block; margin-right: 5px;'>
                                    <input type='hidden' name='tiket_id' value='$id'>
                                    <button type='submit' name='validasi' class='btn btn-sm btn-success'>Terima</button>
                                  </form>
                                  <form method='post' action='validasi_tiket.php' style='display:inline-block;'>
                                    <input type='hidden' name='tiket_id' value='$id'>
                                    <button type='submit' name='tolak' class='btn btn-sm btn-danger'>Tolak</button>
                                  </form>
                                </div>";
                            case 'Diterima':
                              return "<span class='badge badge-success'>Diterima</span>";
                            case 'Ditolak':
                              return "<span class='badge badge-danger'>Ditolak</span>";
                            default:
                              return "<span class='badge badge-secondary'>Tidak Diketahui</span>";
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div> <!-- .tab-content -->
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

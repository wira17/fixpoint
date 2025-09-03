<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

$current_file = basename(__FILE__); 

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// === Proses Simpan Data ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
  $nama_cuti   = mysqli_real_escape_string($conn, $_POST['nama_cuti']);
  $lama_hari   = intval($_POST['lama_hari']);
  $keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan']);

  if ($nama_cuti == '' || $lama_hari <= 0) {
    $_SESSION['flash_message'] = "Nama cuti dan lama hari wajib diisi!";
  } else {
    $sql = "INSERT INTO master_cuti (nama_cuti, lama_hari, keterangan) 
            VALUES ('$nama_cuti', '$lama_hari', '$keterangan')";
    if (mysqli_query($conn, $sql)) {
      $_SESSION['flash_message'] = "Data cuti berhasil disimpan.";
    } else {
      $_SESSION['flash_message'] = "Gagal menyimpan data: " . mysqli_error($conn);
    }
  }
  header("Location: master_cuti.php");
  exit;
}

// === Ambil Data Cuti untuk Tabel ===
$dataCuti = mysqli_query($conn, "SELECT * FROM master_cuti ORDER BY id DESC");
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
    .cuti-table {
      font-size: 13px;
      white-space: nowrap;
    }
    .cuti-table th, .cuti-table td {
      padding: 6px 10px;
      vertical-align: middle;
    }
    .flash-center {
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1050;
      min-width: 300px;
      max-width: 90%;
      text-align: center;
      padding: 15px;
      border-radius: 8px;
      font-weight: 500;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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

          <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-info flash-center" id="flashMsg">
              <?= $_SESSION['flash_message'] ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
          <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Master Data Cuti</h4>
            </div>

            <div class="card-body">
              <!-- Tab menu -->
              <ul class="nav nav-tabs" id="cutiTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Cuti</a>
                </li>
              </ul>

              <!-- Tab Content -->
              <div class="tab-content mt-3">
                <!-- Form Input -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="post">
                    <div class="form-group">
                      <label for="nama_cuti">Nama Cuti</label>
                      <input type="text" name="nama_cuti" id="nama_cuti" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label for="lama_hari">Lama Hari</label>
                      <input type="number" name="lama_hari" id="lama_hari" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label for="keterangan">Keterangan</label>
                      <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">
                      <i class="fas fa-save"></i> Simpan
                    </button>
                  </form>
                </div>

                <!-- Tabel Data -->
                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered cuti-table">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Cuti</th>
                          <th>Lama Hari</th>
                          <th>Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $no=1;
                        while ($row = mysqli_fetch_assoc($dataCuti)): ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_cuti']) ?></td>
                            <td><?= $row['lama_hari'] ?> hari</td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div> <!-- End Tab Content -->
            </div>
          </div>

        </div>
      </section>
    </div>
  </div>
</div>

<!-- JS -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
  $(document).ready(function() {
    setTimeout(function() {
      $("#flashMsg").fadeOut("slow");
    }, 3000); // 3 detik
  });
</script>

</body>
</html>

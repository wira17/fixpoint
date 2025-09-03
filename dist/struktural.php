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

$notif = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  $nominal = str_replace(['Rp', '.', ','], '', $_POST['nominal']);
  if (!is_numeric($nominal) || $nominal <= 0) {
    $notif = "Nominal harus berupa angka dan lebih dari 0.";
  } else {
    $query = mysqli_query($conn, "INSERT INTO struktural (nominal) VALUES ('$nominal')");
    if ($query) {
      $_SESSION['flash_message'] = "Data berhasil disimpan.";
      header("Location: struktural.php");
      exit;
    } else {
      $notif = "Gagal menyimpan data.";
    }
  }
}

$data = mysqli_query($conn, "SELECT * FROM struktural ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
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
              <h4>Manajemen Struktural</h4>
            </div>
            <div class="card-body">

              <ul class="nav nav-tabs" id="spoTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data</a>
                </li>
              </ul>

              <div class="tab-content mt-4" id="spoTabContent">
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <?php if ($notif): ?>
                    <div class="alert alert-danger"> <?= $notif ?> </div>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['flash_message'])): ?>
                    <div id="notif-toast" class="alert alert-success text-center">
                      <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                    </div>
                  <?php endif; ?>

                  <form method="POST">
                    <div class="form-group">
                      <label>Nominal (Rp)</label>
                      <input type="text" name="nominal" class="form-control" placeholder="Contoh: 500000" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                  </form>
                </div>

                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nominal</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($data)) {
                          echo "<tr>
                                  <td>{$no}</td>
                                  <td>Rp " . number_format($row['nominal'], 0, ',', '.') . "</td>
                                </tr>";
                          $no++;
                        }
                        ?>
                        <?php if (mysqli_num_rows($data) == 0): ?>
                          <tr><td colspan="2" class="text-center">Belum ada data.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
  $(document).ready(function () {
    $('#notif-toast').fadeIn(300).delay(2000).fadeOut(500);
  });
</script>
</body>
</html>

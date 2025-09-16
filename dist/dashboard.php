<?php
include 'security.php';
include 'check_integrity.php';
include 'koneksi.php';

// Tiket IT Hardware
$jumlah_hardware     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_hardware"));
$hardware_menunggu   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_hardware WHERE status = 'Menunggu'"));
$hardware_diproses   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_hardware WHERE status = 'Diproses'"));
$hardware_selesai    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_hardware WHERE status = 'Selesai'"));

// Tiket IT Software
$jumlah_software     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_software"));
$software_menunggu   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_software WHERE status = 'Menunggu'"));
$software_diproses   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_software WHERE status = 'Diproses'"));
$software_selesai    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_software WHERE status = 'Selesai'"));

// Laporan Off Duty
$laporan_total       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_off_duty"));
$laporan_menunggu    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_off_duty WHERE status_validasi = 'Menunggu'"));
$laporan_diproses    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_off_duty WHERE status_validasi = 'Diproses'"));
$laporan_selesai     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_off_duty WHERE status_validasi = 'Selesai'"));

// Agenda Direktur
$agenda_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda_direktur"));

// Arsip Digital
$arsip_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM arsip_digital"));

// Berita Acara Hardware
$ba_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM berita_acara"));

// Berita Acara Software
$ba_software_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM berita_acara_software"));

// Data Barang IT
$barang_it_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM data_barang_it"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Dashboard</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .card-statistic-1 { padding: 5px; margin-bottom: 5px; font-size: 13px; }
    .card-statistic-1 .card-icon { font-size: 14px; padding: 4px; width: 30px; height: 30px; }
    .card-statistic-1 .card-header h4 { font-size: 11px; margin-bottom: 2px; }
    .card-statistic-1 .card-body { font-size: 14px; font-weight: bold; }
    .card-statistic-1 .card-wrap { padding-left: 8px; }
    .row > [class*='col-'] { padding-right: 5px; padding-left: 5px; margin-bottom: 5px; }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">

      <?php include 'navbar.php'; ?>
      <?php if (isset($_SESSION['notif'])): ?>
  <div class="container mt-3">
    <div class="alert alert-<?= $_SESSION['notif']['type']; ?> alert-dismissible fade show" role="alert">
      <i class="fas <?= $_SESSION['notif']['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
      <?= htmlspecialchars($_SESSION['notif']['msg']); ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
  <?php unset($_SESSION['notif']); ?>
<?php endif; ?>

      <?php include 'sidebar.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard</h1>
          </div>

          <!-- Row Gabungan: Hardware & Software -->
          <h5 class="mb-2">Tiket IT</h5>
          <div class="row">
            <?php
            $it_cards = [
            ['Hardware Total', 'primary', 'fas fa-microchip', $jumlah_hardware['total']],

              ['Hardware Menunggu', 'warning', 'fas fa-hourglass-start', $hardware_menunggu['total']],
              ['Hardware Diproses', 'info', 'fas fa-spinner', $hardware_diproses['total']],
              ['Hardware Selesai', 'success', 'fas fa-check-circle', $hardware_selesai['total']],
              ['Software Total', 'primary', 'fas fa-laptop-code', $jumlah_software['total']],
              ['Software Menunggu', 'warning', 'fas fa-hourglass-start', $software_menunggu['total']],
              ['Software Diproses', 'info', 'fas fa-spinner', $software_diproses['total']],
              ['Software Selesai', 'success', 'fas fa-check-circle', $software_selesai['total']],
            ];
            foreach($it_cards as $card): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card card-statistic-1">
                <div class="card-icon bg-<?= $card[1]; ?>"><i class="<?= $card[2]; ?>"></i></div>
                <div class="card-wrap">
                  <div class="card-header"><h4><?= $card[0]; ?></h4></div>
                  <div class="card-body"><?= $card[3]; ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="row">
            <?php
            $other_cards = [
              ['Off Duty Total', 'dark', 'fas fa-user-clock', $laporan_total['total']],
              ['Off Duty Menunggu', 'warning', 'fas fa-hourglass-start', $laporan_menunggu['total']],
              ['Off Duty Diproses', 'info', 'fas fa-spinner', $laporan_diproses['total']],
              ['Off Duty Selesai', 'success', 'fas fa-check-double', $laporan_selesai['total']],
              ['Agenda Direktur', 'secondary', 'fas fa-calendar-alt', $agenda_total['total']],
              ['Arsip Digital', 'success', 'fas fa-folder-open', $arsip_total['total']],
              ['Berita Acara Hardware', 'primary', 'fas fa-file-alt', $ba_total['total']],
              ['Berita Acara Software', 'info', 'fas fa-file-alt', $ba_software_total['total']],
              ['Data Barang IT', 'warning', 'fas fa-box', $barang_it_total['total']],
            ];
            foreach($other_cards as $card): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card card-statistic-1">
                <div class="card-icon bg-<?= $card[1]; ?>"><i class="<?= $card[2]; ?>"></i></div>
                <div class="card-wrap">
                  <div class="card-header"><h4><?= $card[0]; ?></h4></div>
                  <div class="card-body"><?= $card[3]; ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

        </section>
      </div>

    </div>
  </div>

  <!-- JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['notif'])): ?>
<script>
Swal.fire({
  icon: '<?= $_SESSION['notif']['type']; ?>',
  title: '<?= $_SESSION['notif']['msg']; ?>',
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true,
  position: 'center'
});
</script>
<?php unset($_SESSION['notif']); endif; ?>



</body>
</html>

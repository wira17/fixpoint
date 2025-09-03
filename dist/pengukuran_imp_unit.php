<?php
// indikator_harian.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;

// ambil info user login
$qUser = mysqli_query($conn, "SELECT unit_kerja, nama FROM users WHERE id = '$user_id' LIMIT 1");
$userData = mysqli_fetch_assoc($qUser);
$nama_user = $userData['nama'] ?? 'User #' . $user_id;
$unit_name = $userData['unit_kerja'] ?? null;

// aktifkan tab
$activeTab = $_GET['tab'] ?? 'data';

// proses simpan
if (isset($_POST['simpan'])) {
    $indikator_id = intval($_POST['indikator_id']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $numerator = intval($_POST['numerator']);
    $denominator = intval($_POST['denominator']);

    // hitung persen
    $capaian = ($denominator > 0) ? round(($numerator / $denominator) * 100, 2) : 0;

    // ambil unit_id berdasarkan unit_kerja
    $qUnit = mysqli_query($conn, "SELECT id FROM unit_kerja WHERE nama_unit = '".mysqli_real_escape_string($conn,$unit_name)."' LIMIT 1");
    $unit = mysqli_fetch_assoc($qUnit);
    $unit_id = $unit['id'] ?? 0;

    // periode dalam format YYYY-MM
    $periode = date('Y-m', strtotime($tanggal));

    $sql = "INSERT INTO pengukuran_indikator 
            (indikator_id, unit_id, periode, numerator, denominator, capaian, dibuat_oleh)
            VALUES ('$indikator_id','$unit_id','$periode', '$numerator','$denominator','$capaian','$nama_user')";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['flash_message'] = "Data harian berhasil disimpan.";
    } else {
        $_SESSION['flash_message'] = "Gagal menyimpan data: " . mysqli_error($conn);
    }
    header("Location: pengukuran_imp_unit.php?tab=data");
    exit;
}

// ambil data indikator utk dropdown beserta kategori
$qIndikator = mysqli_query($conn, "SELECT id, nama_indikator, kategori FROM master_indikator WHERE aktif=1 ORDER BY nama_indikator ASC");

// ambil data pengukuran indikator dengan nama unit dan kategori indikator
$qData = mysqli_query($conn, "SELECT pi.*, mi.nama_indikator, mi.kategori, uk.nama_unit
                              FROM pengukuran_indikator pi
                              JOIN master_indikator mi ON pi.indikator_id = mi.id
                              LEFT JOIN unit_kerja uk ON pi.unit_id = uk.id
                              ORDER BY pi.periode DESC");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Indikator Harian</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .dokumen-table { font-size: 13px; white-space: nowrap; }
    .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
    .flash-center {
      position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%);
      z-index: 1050; min-width: 300px; max-width: 90%; text-align: center;
      padding: 15px; border-radius: 8px; font-weight: 500;
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

        <?php if(isset($_SESSION['flash_message'])): ?>
          <div class="alert alert-info flash-center" id="flashMsg">
            <?= htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
          </div>
        <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Input Indikator Harian</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" data-toggle="tab" href="#input">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" data-toggle="tab" href="#data">Data Harian</a>
                </li>
              </ul>

              <div class="tab-content mt-3">

<!-- FORM INPUT -->
<div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>" id="input">
  <form method="POST">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Indikator</label>
          <select name="indikator_id" class="form-control" required>
            <option value="">-- Pilih Indikator --</option>
            <?php while($i=mysqli_fetch_assoc($qIndikator)): ?>
              <option value="<?= $i['id']; ?>">
                <?= htmlspecialchars($i['nama_indikator']); ?> (<?= $i['kategori']; ?>)
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Unit</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($unit_name); ?>" readonly>
        </div>
        <div class="form-group">
          <label>Numerator</label>
          <input type="number" name="numerator" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Denominator</label>
          <input type="number" name="denominator" class="form-control" required>
        </div>
      </div>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
  </form>
</div>

<!-- DATA -->
<div class="tab-pane fade <?= ($activeTab=='data')?'show active':'' ?>" id="data">
  <div class="table-responsive">
    <table class="table table-bordered table-striped dokumen-table">
      <thead class="thead-light">
        <tr>
          <th>#</th>
          <th>Indikator</th>
          <th>Kategori</th>
          <th>Unit</th>
          <th>Periode</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Capaian (%)</th>
          <th>Dibuat Oleh</th>
          <th>Dibuat Pada</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($d=mysqli_fetch_assoc($qData)): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($d['nama_indikator']); ?></td>
            <td><?= htmlspecialchars($d['kategori']); ?></td>
            <td><?= htmlspecialchars($d['nama_unit'] ?? '-'); ?></td>
            <td><?= $d['periode']; ?></td>
            <td><?= $d['numerator']; ?></td>
            <td><?= $d['denominator']; ?></td>
            <td><?= $d['capaian']; ?>%</td>
            <td><?= htmlspecialchars($d['dibuat_oleh']); ?></td>
            <td><?= $d['dibuat_pada']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

              </div> <!-- end tab-content -->
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
  $(function(){
    setTimeout(function(){ $("#flashMsg").fadeOut("slow"); }, 2500);
  });
</script>
</body>
</html>

<?php
// master_indikator_rs.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
$nama_user = $_SESSION['nama_user'] ?? '';
$activeTab = 'data';
$modals = [];

// akses menu
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
            JOIN menu ON akses_menu.menu_id = menu.id 
            WHERE akses_menu.user_id = '".intval($user_id)."' 
            AND menu.file_menu = '".mysqli_real_escape_string($conn,$current_file)."'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// proses simpan
if (isset($_POST['simpan'])) {
    $id_nasional      = intval($_POST['id_nasional']);
    $kategori         = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nama_indikator   = mysqli_real_escape_string($conn, $_POST['nama_indikator']);
    $definisi         = mysqli_real_escape_string($conn, $_POST['definisi']);
    $numerator        = mysqli_real_escape_string($conn, $_POST['numerator']);
    $denominator      = mysqli_real_escape_string($conn, $_POST['denominator']);
    $standar          = mysqli_real_escape_string($conn, $_POST['standar']);
    $sumber_data      = mysqli_real_escape_string($conn, $_POST['sumber_data']);
    $frekuensi        = mysqli_real_escape_string($conn, $_POST['frekuensi']);
    $penanggung_jawab = intval($_POST['penanggung_jawab']); // ambil id user

    if ($nama_indikator && $standar && $kategori) {
        $q = "INSERT INTO indikator_rs 
                (id_nasional, kategori, nama_indikator, definisi, numerator, denominator, standar, sumber_data, frekuensi, penanggung_jawab) 
              VALUES 
                (" . ($id_nasional ? $id_nasional : "NULL") . ", '$kategori', '$nama_indikator', '$definisi', '$numerator', '$denominator', '$standar', '$sumber_data', '$frekuensi', '$penanggung_jawab')";
        if (mysqli_query($conn, $q)) {
            $_SESSION['flash_message'] = "Data berhasil disimpan.";
            $activeTab = 'data';
        } else {
            $_SESSION['flash_message'] = "Gagal menyimpan data: " . mysqli_error($conn);
            $activeTab = 'input';
        }
    } else {
        $_SESSION['flash_message'] = "Lengkapi semua field!";
        $activeTab = 'input';
    }
}

// hapus data
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM indikator_rs WHERE id_rs='$id'");
    $_SESSION['flash_message'] = "Data berhasil dihapus.";
    header("Location: master_indikator_rs.php");
    exit;
}

// ambil indikator nasional
$indikatorNasional = mysqli_query($conn, "SELECT id_nasional, nama_indikator FROM indikator_nasional ORDER BY nama_indikator");
// ambil users untuk penanggung jawab
$users = mysqli_query($conn, "SELECT id, nama FROM users ORDER BY nama ASC");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Master Indikator RS</title>
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
              <h4 class="mb-0">Master Indikator RS</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" id="indikatorTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Indikator</a>
                </li>
              </ul>

              <div class="tab-content mt-3">
               <!-- FORM INPUT -->
<div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>" id="input" role="tabpanel">
  <form method="POST">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Indikator Nasional (Opsional)</label>
          <select name="id_nasional" class="form-control">
            <option value="">-- Tidak terkait --</option>
            <?php while($n = mysqli_fetch_assoc($indikatorNasional)): ?>
              <option value="<?= $n['id_nasional'] ?>"><?= htmlspecialchars($n['nama_indikator']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="SKP">Indikator Sasaran Keselamatan Pasien</option>
            <option value="Pelayanan Klinis">Indikator Pelayanan Klinis</option>
            <option value="Strategis">Indikator Sesuai Tujuan Strategis Rumah Sakit</option>
            <option value="Sistem">Indikator Terkait Perbaikan Sistem</option>
            <option value="Risiko">Indikator Terkait Manajemen Resiko</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Indikator</label>
          <input type="text" name="nama_indikator" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Definisi Operasional</label>
          <textarea name="definisi" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Numerator</label>
          <textarea name="numerator" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Denominator</label>
          <textarea name="denominator" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Standar/Target (%)</label>
          <input type="number" step="0.01" name="standar" class="form-control" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Sumber Data</label>
          <input type="text" name="sumber_data" class="form-control">
        </div>
        <div class="form-group">
          <label>Frekuensi Pelaporan</label>
          <select name="frekuensi" class="form-control">
            <option value="">-- Pilih --</option>
            <option value="Harian">Harian</option>
            <option value="Mingguan">Mingguan</option>
            <option value="Bulanan">Bulanan</option>
            <option value="Triwulan">Triwulan</option>
            <option value="Tahunan">Tahunan</option>
          </select>
        </div>
        <div class="form-group">
          <label>Penanggung Jawab</label>
          <select name="penanggung_jawab" class="form-control" required>
            <option value="">-- Pilih Penanggung Jawab --</option>
            <?php while($u = mysqli_fetch_assoc($users)): ?>
              <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
  </form>
</div>

<!-- DATA -->
<div class="tab-pane fade <?= ($activeTab=='data')?'show active':'' ?>" id="data" role="tabpanel">
  <div class="table-responsive">
    <table class="table table-bordered table-striped dokumen-table">
      <thead class="thead-light">
        <tr>
          <th>No</th>
          <th>Kategori</th>
          <th>Nama Indikator</th>
          <th>Standar</th>
          <th>Frekuensi</th>
          <th>Penanggung Jawab</th>
          <th>Indikator Nasional</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $qInd = mysqli_query($conn, "SELECT rs.id_rs, rs.kategori, rs.nama_indikator, rs.standar, rs.frekuensi, u.nama AS penanggung_jawab, n.nama_indikator AS indikator_nasional
                                   FROM indikator_rs rs 
                                   LEFT JOIN indikator_nasional n ON rs.id_nasional=n.id_nasional
                                   LEFT JOIN users u ON rs.penanggung_jawab=u.id
                                   ORDER BY rs.id_rs DESC");
      $no=1;
      while($row = mysqli_fetch_assoc($qInd)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['kategori']) ?></td>
          <td><?= htmlspecialchars($row['nama_indikator']) ?></td>
          <td><?= htmlspecialchars($row['standar']) ?></td>
          <td><?= htmlspecialchars($row['frekuensi']) ?></td>
          <td><?= htmlspecialchars($row['penanggung_jawab']) ?></td>
          <td><?= htmlspecialchars($row['indikator_nasional'] ?? '-') ?></td>
          <td>
            <a href="?hapus=<?= $row['id_rs'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
          </td>
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

<?php
foreach ($modals as $m) echo $m;
?>

</body>
</html>

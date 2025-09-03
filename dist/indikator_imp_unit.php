<?php
// master_indikator.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// ambil nama user (safe)
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['nama'] ?? $_SESSION['username'] ?? '';
if ($nama_user === '' && $user_id > 0) {
    $qUser = mysqli_query($conn, "SELECT nama FROM users WHERE id = ".intval($user_id)." LIMIT 1");
    if ($qUser && mysqli_num_rows($qUser) === 1) {
        $nama_user = mysqli_fetch_assoc($qUser)['nama'];
    }
}
if ($nama_user === '') $nama_user = 'User ID #' . $user_id;

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

// aktifkan tab
$activeTab = $_GET['tab'] ?? 'data';

// proses simpan
if (isset($_POST['simpan'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nama_indikator = mysqli_real_escape_string($conn, $_POST['nama_indikator']);
    $definisi = mysqli_real_escape_string($conn, $_POST['definisi']);
    $numerator = mysqli_real_escape_string($conn, $_POST['numerator']);
    $denominator = mysqli_real_escape_string($conn, $_POST['denominator']);
    $target = floatval($_POST['target']);
    $unit_id = ($_POST['unit_id'] !== '') ? intval($_POST['unit_id']) : 'NULL';

    $sql = "INSERT INTO master_indikator 
            (kategori, nama_indikator, definisi_operasional, numerator, denominator, target, unit_id, dibuat_oleh) 
            VALUES ('$kategori','$nama_indikator','$definisi','$numerator','$denominator','$target',$unit_id,'$nama_user')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['flash_message'] = "Data indikator berhasil disimpan.";
    } else {
        $_SESSION['flash_message'] = "Gagal menyimpan data: ".mysqli_error($conn);
    }
    header("Location: indikator_imp_unit.php?tab=data");
    exit;
}

// ambil data indikator
$qData = mysqli_query($conn, "SELECT mi.*, uk.nama_unit 
            FROM master_indikator mi 
            LEFT JOIN unit_kerja uk ON mi.unit_id = uk.id 
            ORDER BY mi.id DESC");

// ambil unit untuk dropdown
$qUnit = mysqli_query($conn, "SELECT id, nama_unit FROM unit_kerja ORDER BY nama_unit ASC");

// siapkan modals edit (opsional, bisa ditambahkan nanti)
$modals = [];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Master Indikator Mutu</title>
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
              <h4 class="mb-0">Master Indikator Mutu</h4>
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
      <!-- Kiri -->
      <div class="col-md-6">
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori" class="form-control" required>
            <option value="">-- Pilih --</option>
            <option value="Nasional">Indikator Nasional Mutu</option>
            <option value="RS">Indikator Mutu Prioritas RS</option>
            <option value="Unit">Indikator Mutu Prioritas Unit</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Indikator</label>
          <input type="text" name="nama_indikator" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Definisi Operasional</label>
          <textarea name="definisi" class="form-control" rows="3"></textarea>
        </div>
      </div>

      <!-- Kanan -->
      <div class="col-md-6">
        <div class="form-group">
          <label>Numerator</label>
          <textarea name="numerator" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group">
          <label>Denominator</label>
          <textarea name="denominator" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group">
          <label>Target (%)</label>
          <input type="number" step="0.01" name="target" class="form-control" placeholder="Contoh: 95.00">
        </div>
        <div class="form-group">
          <label>Unit Terkait</label>
          <select name="unit_id" class="form-control">
            <option value="">-- Tidak Ada --</option>
            <?php while($u=mysqli_fetch_assoc($qUnit)): ?>
              <option value="<?= $u['id']; ?>"><?= htmlspecialchars($u['nama_unit']); ?></option>
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
          <th>#</th>
          <th>Kategori</th>
          <th>Nama Indikator</th>
          <th>Definisi</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Target</th>
          <th>Unit</th>
          <th>Dibuat Oleh</th>
          <th>Dibuat Pada</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($d=mysqli_fetch_assoc($qData)): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($d['kategori']); ?></td>
            <td><?= htmlspecialchars($d['nama_indikator']); ?></td>
            <td><?= htmlspecialchars($d['definisi_operasional']); ?></td>
            <td><?= htmlspecialchars($d['numerator']); ?></td>
            <td><?= htmlspecialchars($d['denominator']); ?></td>
            <td><?= $d['target']; ?>%</td>
            <td><?= htmlspecialchars($d['nama_unit']); ?></td>
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

<?php
// render edit modals (belum diimplementasi, bisa ditambahkan nanti)
foreach ($modals as $m) echo $m;
?>

</body>
</html>

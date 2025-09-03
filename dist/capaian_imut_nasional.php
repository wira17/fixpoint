<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['nama'] ?? $_SESSION['username'] ?? 'User ID #'.$user_id;

// === Cek Akses ===
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
  JOIN menu ON akses_menu.menu_id = menu.id 
  WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
  echo "<script>alert('Tidak ada akses!'); window.location.href='dashboard.php';</script>";
  exit;
}

// === Simpan Data ===
if(isset($_POST['simpan'])){
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $indikator   = intval($_POST['id_indikator']);
    $unit        = mysqli_real_escape_string($conn, $_POST['unit']);
    $numerator   = intval($_POST['numerator']);
    $denominator = intval($_POST['denominator']);
    $hasil       = ($denominator > 0) ? ($numerator / $denominator) * 100 : 0;
    $petugas     = $nama_user;
    $waktu_input = date('Y-m-d H:i:s');

    $sql = "INSERT INTO capaian_imut (tanggal, id_indikator, unit, numerator, denominator, hasil, petugas, waktu_input) 
            VALUES ('$tanggal', $indikator, '$unit', $numerator, $denominator, $hasil, '$petugas', '$waktu_input')";
    if(mysqli_query($conn,$sql)){
        $_SESSION['flash_message'] = "Capaian berhasil disimpan";
    } else {
        $_SESSION['flash_message'] = "Error: ".mysqli_error($conn);
    }
    header("Location: capaian_imut_nasional.php?tab=input");
    exit;
}

// === Tab aktif ===
$activeTab = $_GET['tab'] ?? 'input';

// === Data indikator ===
$list_indikator = mysqli_query($conn, "SELECT id, nama_indikator FROM master_imut_nasional ORDER BY nama_indikator ASC");

// === Filter Data ===
$filter_unit = $_GET['unit'] ?? '';
$filter_indikator = $_GET['indikator'] ?? '';

// === Pagination ===
$limit = 10;
$page = max(1,intval($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

// Hitung total
$sqlCount = "SELECT COUNT(*) AS total FROM capaian_imut ci
             JOIN master_imut_nasional mi ON ci.id_indikator = mi.id WHERE 1=1";
if($filter_unit!='') $sqlCount .= " AND ci.unit LIKE '%".mysqli_real_escape_string($conn,$filter_unit)."%'";
if($filter_indikator!='') $sqlCount .= " AND ci.id_indikator=".intval($filter_indikator);
$totalData = mysqli_fetch_assoc(mysqli_query($conn,$sqlCount))['total'];
$totalPages = ceil($totalData/$limit);

// Ambil data
$sqlData = "SELECT ci.*, mi.nama_indikator FROM capaian_imut ci
            JOIN master_imut_nasional mi ON ci.id_indikator = mi.id WHERE 1=1";
if($filter_unit!='') $sqlData .= " AND ci.unit LIKE '%".mysqli_real_escape_string($conn,$filter_unit)."%'";
if($filter_indikator!='') $sqlData .= " AND ci.id_indikator=".intval($filter_indikator);
$sqlData .= " ORDER BY ci.waktu_input DESC LIMIT $limit OFFSET $offset";
$data = mysqli_query($conn,$sqlData);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Capaian Indikator Mutu</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<style>
  .dokumen-table { font-size: 13px; white-space: nowrap; }
  .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
  .flash-center {position:fixed;top:20%;left:50%;transform:translate(-50%,-50%);
    z-index:1050;min-width:300px;max-width:90%;text-align:center;
    padding:15px;border-radius:8px;font-weight:500;
    box-shadow:0 5px 15px rgba(0,0,0,.3);}
</style>
<script>
function hitungHasil(){
  let num = parseFloat(document.getElementById('numerator').value)||0;
  let den = parseFloat(document.getElementById('denominator').value)||0;
  let hasil = (den>0)?(num/den)*100:0;
  document.getElementById('hasil').value = hasil.toFixed(2)+' %';
}
</script>
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
            <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="card-header"><h4 class="mb-0">Input Capaian Indikator Mutu</h4></div>
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" href="?tab=input">Input Data</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" href="?tab=data">Data Capaian</a>
              </li>
            </ul>

            <div class="tab-content mt-3">
              <!-- Input -->
              <div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group"><label>Tanggal / Periode</label>
                        <input type="month" name="tanggal" class="form-control" required></div>
                      <div class="form-group"><label>Indikator Mutu Nasional</label>
                        <select name="id_indikator" class="form-control" required>
                          <option value="">-- Pilih Indikator --</option>
                          <?php mysqli_data_seek($list_indikator,0);
                          while($ind=mysqli_fetch_assoc($list_indikator)): ?>
                            <option value="<?= $ind['id'] ?>"><?= htmlspecialchars($ind['nama_indikator']) ?></option>
                          <?php endwhile; ?>
                        </select></div>
                      <div class="form-group"><label>Unit / Ruangan</label>
                        <input type="text" name="unit" class="form-control" required></div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group"><label>Numerator</label>
                        <input type="number" id="numerator" name="numerator" class="form-control" oninput="hitungHasil()" required></div>
                      <div class="form-group"><label>Denominator</label>
                        <input type="number" id="denominator" name="denominator" class="form-control" oninput="hitungHasil()" required></div>
                      <div class="form-group"><label>Hasil (%)</label>
                        <input type="text" id="hasil" class="form-control" readonly></div>
                      <div class="form-group"><label>Petugas</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nama_user) ?>" readonly></div>
                    </div>
                  </div>
                  <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
              </div>

              <!-- Data -->
              <div class="tab-pane fade <?= ($activeTab=='data')?'show active':'' ?>">
                <form method="GET" class="form-inline mb-2">
                  <input type="hidden" name="tab" value="data">
                  <input type="text" name="unit" class="form-control mr-2" placeholder="Cari Unit" value="<?= htmlspecialchars($filter_unit) ?>">
                  <select name="indikator" class="form-control mr-2">
                    <option value="">-- Semua Indikator --</option>
                    <?php mysqli_data_seek($list_indikator,0);
                    while($ind=mysqli_fetch_assoc($list_indikator)): ?>
                      <option value="<?= $ind['id'] ?>" <?= ($filter_indikator==$ind['id'])?'selected':'' ?>><?= htmlspecialchars($ind['nama_indikator']) ?></option>
                    <?php endwhile; ?>
                  </select>
                  <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
                </form>

                <div class="table-responsive">
                  <table class="table table-bordered dokumen-table">
                    <thead class="thead-dark">
                      <tr>
                        <th>No</th><th>Tanggal</th><th>Indikator</th><th>Unit</th>
                        <th>Numerator</th><th>Denominator</th><th>Hasil (%)</th><th>Petugas</th><th>Waktu Input</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
                      <?php else: $no=$offset+1; while($row=mysqli_fetch_assoc($data)): ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($row['tanggal']) ?></td>
                          <td><?= htmlspecialchars($row['nama_indikator']) ?></td>
                          <td><?= htmlspecialchars($row['unit']) ?></td>
                          <td><?= $row['numerator'] ?></td>
                          <td><?= $row['denominator'] ?></td>
                          <td><?= number_format($row['hasil'],2) ?>%</td>
                          <td><?= htmlspecialchars($row['petugas']) ?></td>
                          <td><?= date('d-m-Y H:i',strtotime($row['waktu_input'])) ?></td>
                        </tr>
                      <?php endwhile; endif; ?>
                    </tbody>
                  </table>
                </div>

                <?php if($totalPages>1): ?>
                <nav><ul class="pagination">
                  <?php for($i=1;$i<=$totalPages;$i++): ?>
                    <li class="page-item <?= ($i==$page)?'active':'' ?>">
                      <a class="page-link" href="?tab=data&page=<?= $i ?>&unit=<?= urlencode($filter_unit) ?>&indikator=<?= urlencode($filter_indikator) ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                </ul></nav>
                <?php endif; ?>
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
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script>$(function(){ setTimeout(()=>$("#flashMsg").fadeOut("slow"),2500); });</script>
</body>
</html>

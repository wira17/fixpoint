<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['nama'] ?? $_SESSION['username'] ?? 'User ID #'.$user_id;

// Cek akses
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
  JOIN menu ON akses_menu.menu_id = menu.id 
  WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
  echo "<script>alert('Tidak ada akses!');window.location.href='dashboard.php';</script>";
  exit;
}

// === HANDLE SIMPAN ===
if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_indikator']);
    $definisi = mysqli_real_escape_string($conn, $_POST['definisi']);
    $numerator = mysqli_real_escape_string($conn, $_POST['numerator']);
    $denominator = mysqli_real_escape_string($conn, $_POST['denominator']);
    $target = floatval($_POST['target']);
    $periode = mysqli_real_escape_string($conn, $_POST['periode']);
    $petugas = $nama_user;
    $waktu_input = date('Y-m-d H:i:s');

    $sql = "INSERT INTO master_imut_nasional 
        (nama_indikator, definisi, numerator, denominator, target, periode, petugas, waktu_input) 
        VALUES ('$nama','$definisi','$numerator','$denominator',$target,'$periode','$petugas','$waktu_input')";
    if(mysqli_query($conn,$sql)){
        $_SESSION['flash_message'] = "Indikator berhasil disimpan";
    } else {
        $_SESSION['flash_message'] = "Error: ".mysqli_error($conn);
    }
    header("Location: master_imut_nasional.php?tab=input");
    exit;
}

// Tab aktif
$activeTab = $_GET['tab'] ?? 'input';

// Filter
$filter_nama = $_GET['nama'] ?? '';

// Pagination
$limit = 10;
$page = max(1,intval($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

// Hitung total
$sqlCount = "SELECT COUNT(*) AS total FROM master_imut_nasional WHERE 1=1";
if($filter_nama!='') $sqlCount .= " AND nama_indikator LIKE '%".mysqli_real_escape_string($conn,$filter_nama)."%'";
$totalData = mysqli_fetch_assoc(mysqli_query($conn,$sqlCount))['total'];
$totalPages = ceil($totalData/$limit);

// Ambil data
$sqlData = "SELECT * FROM master_imut_nasional WHERE 1=1";
if($filter_nama!='') $sqlData .= " AND nama_indikator LIKE '%".mysqli_real_escape_string($conn,$filter_nama)."%'";
$sqlData .= " ORDER BY waktu_input DESC LIMIT $limit OFFSET $offset";
$data = mysqli_query($conn,$sqlData);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Master Indikator Mutu Nasional</title>
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
          <div class="card-header"><h4 class="mb-0">Master Indikator Mutu Nasional</h4></div>
          <div class="card-body">
            <ul class="nav nav-tabs" id="dokumenTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" href="?tab=input">Input Data</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" href="?tab=data">Data Indikator</a>
              </li>
            </ul>

            <div class="tab-content mt-3">
              <!-- Input -->
              <div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group"><label>Nama Indikator</label>
                        <input type="text" name="nama_indikator" class="form-control" required></div>
                      <div class="form-group"><label>Definisi</label>
                        <textarea name="definisi" class="form-control"></textarea></div>
                      <div class="form-group"><label>Numerator</label>
                        <input type="text" name="numerator" class="form-control"></div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group"><label>Denominator</label>
                        <input type="text" name="denominator" class="form-control"></div>
                      <div class="form-group"><label>Target (%)</label>
                        <input type="number" name="target" step="0.01" class="form-control"></div>
                      <div class="form-group"><label>Periode</label>
                        <select name="periode" class="form-control">
                          <option value="Bulanan">Bulanan</option>
                          <option value="Triwulan">Triwulan</option>
                          <option value="Semester">Semester</option>
                          <option value="Tahunan">Tahunan</option>
                        </select></div>
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
                  <input type="text" name="nama" class="form-control mr-2" placeholder="Cari nama indikator" value="<?= htmlspecialchars($filter_nama) ?>">
                  <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
                </form>

                <div class="table-responsive">
                  <table class="table table-bordered dokumen-table">
                    <thead class="thead-dark">
                      <tr>
                        <th>No</th><th>Nama</th><th>Definisi</th>
                        <th>Numerator</th><th>Denominator</th>
                        <th>Target</th><th>Periode</th><th>Petugas</th><th>Tanggal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
                      <?php else: $no=$offset+1; while($row=mysqli_fetch_assoc($data)): ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($row['nama_indikator']) ?></td>
                          <td><?= htmlspecialchars($row['definisi']) ?></td>
                          <td><?= htmlspecialchars($row['numerator']) ?></td>
                          <td><?= htmlspecialchars($row['denominator']) ?></td>
                          <td><?= $row['target'] ?>%</td>
                          <td><?= htmlspecialchars($row['periode']) ?></td>
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
                      <a class="page-link" href="?tab=data&page=<?= $i ?>&nama=<?= urlencode($filter_nama) ?>"><?= $i ?></a>
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
<script>
$(function(){ setTimeout(()=>$("#flashMsg").fadeOut("slow"),2500); });
</script>
</body>
</html>

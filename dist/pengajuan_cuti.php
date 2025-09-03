<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
session_start();

// Helper escape output
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } // cegah XSS [7]

// Ambil context dasar
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$current_file = basename(__FILE__);

// Siapkan CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // token kuat kriptografis [9]
}
$csrf_token = $_SESSION['csrf_token'];

// Cek akses menu (prepared)
$stmtAkses = $conn->prepare("
  SELECT 1 
  FROM akses_menu am 
  JOIN menu m ON am.menu_id = m.id 
  WHERE am.user_id = ? AND m.file_menu = ?
"); // gunakan prepared untuk cegah injeksi [1]
$stmtAkses->bind_param("is", $user_id, $current_file);
$stmtAkses->execute();
$resAkses = $stmtAkses->get_result();
if ($resAkses->num_rows === 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}
$stmtAkses->close();

// Ambil data user login
$stmtUser = $conn->prepare("SELECT id, nik, nama, jabatan, unit_kerja, no_hp, email FROM users WHERE id=?"); // prepared [1]
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

// Ambil data master cuti (aktif semua karena kolom aktif tidak ada di skema Anda)
$cutiList = $conn->query("SELECT id, nama_cuti, lama_hari FROM master_cuti ORDER BY nama_cuti"); // aman tanpa input [5]

// Proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verifikasi CSRF
  $post_token = $_POST['csrf_token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'], $post_token)) { // verifikasi token [9]
    $_SESSION['flash_message'] = "CSRF token tidak valid.";
    header("Location: pengajuan_cuti.php");
    exit;
  }

  // Validasi input
  $cuti_id   = filter_input(INPUT_POST, 'cuti_id', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]); // validasi integer [1]
  $lama_hari = filter_input(INPUT_POST, 'lama_hari', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]); // >=1 [1]
  $tahun     = (int)date("Y");

  if ($cuti_id && $lama_hari) {
    // Ambil batas maksimal lama_hari dari master_cuti
    $stmtMC = $conn->prepare("SELECT lama_hari FROM master_cuti WHERE id=?"); // prepared [1]
    $stmtMC->bind_param("i", $cuti_id);
    $stmtMC->execute();
    $resMC = $stmtMC->get_result();
    $rowMC = $resMC->fetch_assoc();
    $stmtMC->close();

    if (!$rowMC) {
      $_SESSION['flash_message'] = "Jenis cuti tidak ditemukan.";
    } else {
      $maxHari = (int)$rowMC['lama_hari'];
      if ($lama_hari > $maxHari) {
        $_SESSION['flash_message'] = "Lama hari melebihi kuota ($maxHari hari) untuk jenis cuti ini.";
      } else {
        // Simpan pengajuan ke jatah_cuti
        $stmtIns = $conn->prepare("
          INSERT INTO jatah_cuti (karyawan_id, cuti_id, lama_hari, tahun) 
          VALUES (?, ?, ?, ?)
        "); // gunakan bind untuk tipe iiii [4]
        $stmtIns->bind_param("iiii", $user_id, $cuti_id, $lama_hari, $tahun);
        if ($stmtIns->execute()) {
          $_SESSION['flash_message'] = "Pengajuan cuti berhasil disimpan.";
        } else {
          $_SESSION['flash_message'] = "Gagal menyimpan pengajuan cuti.";
        }
        $stmtIns->close();
      }
    }
  } else {
    $_SESSION['flash_message'] = "Harap pilih jenis cuti dan isi lama hari dengan benar.";
  }
  header("Location: pengajuan_cuti.php");
  exit;
}

// Ambil riwayat pengajuan user
$stmtPeng = $conn->prepare("
  SELECT jc.id, jc.lama_hari, jc.tahun, jc.created_at, mc.nama_cuti
  FROM jatah_cuti jc
  JOIN master_cuti mc ON jc.cuti_id = mc.id
  WHERE jc.karyawan_id = ?
  ORDER BY jc.created_at DESC, jc.id DESC
"); // prepared untuk filter by user [1]
$stmtPeng->bind_param("i", $user_id);
$stmtPeng->execute();
$qPengajuan = $stmtPeng->get_result();
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
    .cuti-table { font-size:13px; white-space:nowrap; }
    .cuti-table th, .cuti-table td { padding:6px 10px; vertical-align:middle; }
    .flash-center { position:fixed; top:20%; left:50%; transform:translate(-50%,-50%); z-index:1050; min-width:300px; max-width:90%; text-align:center; padding:15px; border-radius:8px; font-weight:500; box-shadow:0 5px 15px rgba(0,0,0,0.3);}
    .hint { font-size:12px; color:#6c757d; margin-top:4px; }
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
              <?= e($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
          <?php endif; ?>
          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Pengajuan Cuti</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" id="cutiTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#input" role="tab">Form Input</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#data" role="tab">Data Pengajuan</a></li>
              </ul>
              <div class="tab-content mt-3">
                <!-- Form Input -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="post" autocomplete="off" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>"><!-- CSRF hidden field [9] -->
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group"><label>NIK</label>
                          <input type="text" class="form-control" value="<?= e($user['nik'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group"><label>Nama</label>
                          <input type="text" class="form-control" value="<?= e($user['nama'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group"><label>Jabatan</label>
                          <input type="text" class="form-control" value="<?= e($user['jabatan'] ?? '') ?>" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group"><label>Unit Kerja</label>
                          <input type="text" class="form-control" value="<?= e($user['unit_kerja'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group"><label>No HP</label>
                          <input type="text" class="form-control" value="<?= e($user['no_hp'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group"><label>Email</label>
                          <input type="text" class="form-control" value="<?= e($user['email'] ?? '') ?>" readonly>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="form-group">
                      <label>Jenis Cuti</label>
                      <select name="cuti_id" class="form-control" required>
                        <option value="">-- Pilih Jenis Cuti --</option>
                        <?php if ($cutiList && $cutiList instanceof mysqli_result): ?>
                          <?php $cutiList->data_seek(0); while($c = $cutiList->fetch_assoc()): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= e($c['nama_cuti']) ?> (<?= (int)$c['lama_hari'] ?> hari)</option>
                          <?php endwhile; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Lama Hari</label>
                      <input type="number" name="lama_hari" class="form-control" min="1" required>
                      <div class="hint">Isi sesuai jumlah hari yang diajukan.</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ajukan</button>
                  </form>
                </div>
                <!-- Tabel Data -->
                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered cuti-table">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Jenis Cuti</th>
                          <th>Lama Hari</th>
                          <th>Tahun</th>
                          <th>Tanggal Pengajuan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $no=1; if ($qPengajuan && $qPengajuan->num_rows > 0): ?>
                          <?php while($row = $qPengajuan->fetch_assoc()): ?>
                            <tr>
                              <td><?= $no++ ?></td>
                              <td><?= e($row['nama_cuti']) ?></td>
                              <td><?= (int)$row['lama_hari'] ?> hari</td>
                              <td><?= e($row['tahun']) ?></td>
                              <td><?= e($row['created_at']) ?></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="5" class="text-center">Belum ada pengajuan cuti.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div><!-- End Tab Content -->
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
setTimeout(()=>{ 
  let msg=document.getElementById('flashMsg'); 
  if(msg){ msg.style.display='none'; } 
}, 3000);
</script>
</body>
</html>
<?php
// Bebaskan resource
if (isset($stmtPeng)) { $stmtPeng->close(); }
if (isset($cutiList) && $cutiList instanceof mysqli_result) { $cutiList->free(); }
$conn->close();

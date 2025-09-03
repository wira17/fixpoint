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

$success = '';

// Proses update jika form dikirim
if (isset($_POST['update'])) {
  $nama         = mysqli_real_escape_string($conn, $_POST['nama']);
  $email        = mysqli_real_escape_string($conn, $_POST['email']);
  $no_hp        = mysqli_real_escape_string($conn, $_POST['no_hp']);

  $jabatan_id   = $_POST['jabatan'];
  $unit_kerja_id= $_POST['unit_kerja'];
  $atasan_id    = intval($_POST['atasan_id']);
  $password_baru = trim($_POST['password_baru'] ?? '');

  // Ambil nama jabatan dari ID
  $jabatan_nama = '';
  if (!empty($jabatan_id)) {
    $res_jabatan = mysqli_query($conn, "SELECT nama_jabatan FROM jabatan WHERE id = '$jabatan_id' LIMIT 1");
    if ($row_jabatan = mysqli_fetch_assoc($res_jabatan)) {
      $jabatan_nama = mysqli_real_escape_string($conn, $row_jabatan['nama_jabatan']);
    }
  }

  // Ambil nama unit kerja dari ID
  $unit_nama = '';
  if (!empty($unit_kerja_id)) {
    $res_unit = mysqli_query($conn, "SELECT nama_unit FROM unit_kerja WHERE id = '$unit_kerja_id' LIMIT 1");
    if ($row_unit = mysqli_fetch_assoc($res_unit)) {
      $unit_nama = mysqli_real_escape_string($conn, $row_unit['nama_unit']);
    }
  }

  // Buat query update
  $query_update = "UPDATE users SET 
    nama = '$nama',
    email = '$email',
    no_hp = '$no_hp',
    jabatan = '$jabatan_nama',
    unit_kerja = '$unit_nama',
    atasan_id = $atasan_id";

  if (!empty($password_baru)) {
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $query_update .= ", password_hash = '$password_hash'";
  }

  $query_update .= " WHERE id = $user_id";

  $update = mysqli_query($conn, $query_update);

  if ($update) {
    $success = !empty($password_baru) ? 
      "Profil dan password berhasil diperbarui." : 
      "Profil berhasil diperbarui.";
  } else {
    $success = "Terjadi kesalahan saat memperbarui data.";
  }
}

// Ambil data user
$query = mysqli_query($conn, "SELECT nik, nama, jabatan, unit_kerja, email, no_hp, status, created_at, atasan_id FROM users WHERE id = $user_id");
$row = mysqli_fetch_assoc($query);
$nik         = $row['nik'];
$nama        = $row['nama'];
$jabatan     = $row['jabatan'];
$unit_kerja  = $row['unit_kerja'];
$email       = $row['email'];
$no_hp       = $row['no_hp'];
$status      = $row['status'];
$created_at  = $row['created_at'];
$atasan_id   = $row['atasan_id'];

// Ambil daftar jabatan
$daftar_jabatan_arr = [];
$res = mysqli_query($conn, "SELECT id, nama_jabatan FROM jabatan");
while($r = mysqli_fetch_assoc($res)) $daftar_jabatan_arr[] = $r;

// Ambil daftar unit kerja
$daftar_unit_arr = [];
$res = mysqli_query($conn, "SELECT id, nama_unit FROM unit_kerja");
while($r = mysqli_fetch_assoc($res)) $daftar_unit_arr[] = $r;

// Ambil daftar atasan (kecuali dirinya sendiri)
$daftar_atasan_arr = [];
$res = mysqli_query($conn, "SELECT id, nama FROM users WHERE id != $user_id");
while($r = mysqli_fetch_assoc($res)) $daftar_atasan_arr[] = $r;

// Ambil nama atasan
$nama_atasan = '-';
if (!empty($atasan_id)) {
  $q_atasan = mysqli_query($conn, "SELECT nama FROM users WHERE id = '$atasan_id' LIMIT 1");
  $r_atasan = mysqli_fetch_assoc($q_atasan);
  $nama_atasan = $r_atasan['nama'] ?? '-';
}

$nama_jabatan = $jabatan ?: '-';
$nama_unit = $unit_kerja ?: '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>f.i.x.p.o.i.n.t</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.form-inline label { width: 120px; }
.list-group-item strong { min-width: 150px; display: inline-block; }
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
<?php if ($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="card">
<div class="card-header"><h4>Informasi Akun</h4></div>
<div class="card-body">

<form method="POST" id="formEdit" style="display: none;">
  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Nama</label>
    <div class="col-sm-9">
      <input type="text" name="nama" value="<?= htmlspecialchars($nama); ?>" class="form-control" required>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Email</label>
    <div class="col-sm-9">
      <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" class="form-control" required>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">No. HP</label>
    <div class="col-sm-9">
      <input type="text" name="no_hp" value="<?= htmlspecialchars($no_hp); ?>" class="form-control" required>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Jabatan</label>
    <div class="col-sm-9">
      <select name="jabatan" class="form-control select2" style="width: 100%;">
        <option value="">- Pilih Jabatan -</option>
        <?php foreach($daftar_jabatan_arr as $j): ?>
          <option value="<?= $j['id']; ?>" <?= ($jabatan == $j['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($j['nama_jabatan']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Unit Kerja</label>
    <div class="col-sm-9">
      <select name="unit_kerja" class="form-control select2" style="width: 100%;">
        <option value="">- Pilih Unit Kerja -</option>
        <?php foreach($daftar_unit_arr as $u): ?>
          <option value="<?= $u['id']; ?>" <?= ($unit_kerja == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nama_unit']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Atasan</label>
    <div class="col-sm-9">
      <select name="atasan_id" class="form-control select2" style="width: 100%;">
        <option value="">- Tidak Ada -</option>
        <?php foreach($daftar_atasan_arr as $atasan): ?>
          <option value="<?= $atasan['id']; ?>" <?= ($atasan_id == $atasan['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($atasan['nama']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-3 col-form-label">Password Baru</label>
    <div class="col-sm-9">
      <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti">
    </div>
  </div>

  <div class="form-group row text-right">
    <div class="col-sm-12 text-right">
      <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
      <button type="button" class="btn btn-secondary" onclick="toggleForm()">Batal</button>
    </div>
  </div>
</form>

<div id="dataView">
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>NIK</strong> : <?= htmlspecialchars($nik); ?></li>
    <li class="list-group-item"><strong>Nama</strong> : <?= htmlspecialchars($nama); ?></li>
    <li class="list-group-item"><strong>Email</strong> : <?= htmlspecialchars($email); ?></li>
    <li class="list-group-item"><strong>No. HP</strong> : <?= htmlspecialchars($no_hp); ?></li>
    <li class="list-group-item"><strong>Jabatan</strong> : <?= htmlspecialchars($nama_jabatan); ?></li>
    <li class="list-group-item"><strong>Unit Kerja</strong> : <?= htmlspecialchars($nama_unit); ?></li>
    <li class="list-group-item"><strong>Atasan</strong> : <?= htmlspecialchars($nama_atasan); ?></li>
    <li class="list-group-item"><strong>Status Akun</strong> : <?= htmlspecialchars($status); ?></li>
    <li class="list-group-item"><strong>Daftar Akun</strong> : <?= date('d-m-Y H:i', strtotime($created_at)); ?></li>
  </ul>
</div>

<div class="card-footer text-right" id="editButton">
  <button class="btn btn-primary" onclick="toggleForm()">Edit Profil</button>
</div>

</div></div>
</section></div></div></div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function toggleForm() {
  const form = document.getElementById('formEdit');
  const view = document.getElementById('dataView');
  const editBtn = document.getElementById('editButton');
  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}

$(document).ready(function() {
  $('select[name="jabatan"]').select2({ placeholder: "Pilih Jabatan" });
  $('select[name="unit_kerja"]').select2({ placeholder: "Pilih Unit Kerja" });
  $('select[name="atasan_id"]').select2({ placeholder: "Pilih Atasan" });
});
</script>

</body>
</html>

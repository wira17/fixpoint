<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Ambil data user + relasi jabatan, unit kerja, dan atasan
$sql = "
    SELECT u.nik, u.nama, u.email, u.no_hp, u.status, u.created_at, u.password,
           j.nama_jabatan, uk.nama_unit, a.nama AS nama_atasan, u.atasan_id, j.id AS jabatan_id, uk.id AS unit_id
    FROM users u
    LEFT JOIN jabatan j ON u.jabatan = j.id
    LEFT JOIN unit_kerja uk ON u.unit_kerja = uk.id
    LEFT JOIN users a ON u.atasan_id = a.id
    WHERE u.id = $user_id
";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $nik          = $user['nik'];
    $nama         = $user['nama'];
    $email        = $user['email'];
    $no_hp        = $user['no_hp'];
    $status       = $user['status'];
    $created_at   = $user['created_at'];
    $nama_jabatan = $user['nama_jabatan'] ?: '-';
    $nama_unit    = $user['nama_unit'] ?: '-';
    $nama_atasan  = $user['nama_atasan'] ?: '-';
    $jabatan_id   = $user['jabatan_id'];
    $unit_id      = $user['unit_id'];
    $atasan_id    = $user['atasan_id'];
} else {
    echo "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
    exit;
}

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $jabatan  = (int)$_POST['jabatan'];
    $unit     = (int)$_POST['unit_kerja'];
    $atasan   = (int)$_POST['atasan'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET nik='$nik', nama='$nama', email='$email', no_hp='$no_hp', 
                   jabatan='$jabatan', unit_kerja='$unit', atasan_id='$atasan', password='$password' 
                   WHERE id='$user_id'";
    } else {
        $update = "UPDATE users SET nik='$nik', nama='$nama', email='$email', no_hp='$no_hp', 
                   jabatan='$jabatan', unit_kerja='$unit', atasan_id='$atasan' 
                   WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location.href='$current_file';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal update: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="card">
<div class="card-body">

<!-- Form Edit Profil -->
<form method="POST" id="formEdit" style="display: none;">
  <div class="form-group">
    <label>NIK</label>
    <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($nik); ?>" required>
  </div>
  <div class="form-group">
    <label>Nama</label>
    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama); ?>" required>
  </div>
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>
  </div>
  <div class="form-group">
    <label>No. HP</label>
    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($no_hp); ?>" required>
  </div>
  <div class="form-group">
    <label>Jabatan</label>
    <select name="jabatan" class="form-control">
      <option value="">-- Pilih Jabatan --</option>
      <?php
      $qJab = mysqli_query($conn, "SELECT * FROM jabatan");
      while ($j = mysqli_fetch_assoc($qJab)) {
          $sel = ($jabatan_id == $j['id']) ? "selected" : "";
          echo "<option value='{$j['id']}' $sel>{$j['nama_jabatan']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label>Unit Kerja</label>
    <select name="unit_kerja" class="form-control">
      <option value="">-- Pilih Unit --</option>
      <?php
      $qUnit = mysqli_query($conn, "SELECT * FROM unit_kerja");
      while ($u = mysqli_fetch_assoc($qUnit)) {
          $sel = ($unit_id == $u['id']) ? "selected" : "";
          echo "<option value='{$u['id']}' $sel>{$u['nama_unit']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label>Atasan</label>
    <select name="atasan" class="form-control">
      <option value="">-- Pilih Atasan --</option>
      <?php
      $qAtasan = mysqli_query($conn, "SELECT id, nama FROM users");
      while ($a = mysqli_fetch_assoc($qAtasan)) {
          $sel = ($atasan_id == $a['id']) ? "selected" : "";
          echo "<option value='{$a['id']}' $sel>{$a['nama']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label>Password Baru</label>
    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
  </div>
  <button type="submit" name="update" class="btn btn-success">Simpan</button>
  <button type="button" class="btn btn-secondary" onclick="toggleForm()">Batal</button>
</form>

<!-- Tampilan Data -->
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
  <button class="btn btn-primary" onclick="toggleForm()">Edit Akun</button>
</div>

</div>
</div>

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
</script>

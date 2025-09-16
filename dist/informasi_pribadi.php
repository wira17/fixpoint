<?php
if (!isset($_SESSION)) session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<div class='alert alert-danger'>User belum login atau tidak valid.</div>";
    return;
}

// Ambil nama dari tabel users
$user_sql = "SELECT nama FROM users WHERE id = $user_id LIMIT 1";
$user_result = mysqli_query($conn, $user_sql);
$user_data = mysqli_fetch_assoc($user_result);
$nama_user = $user_data['nama'] ?? '';

// Ambil data informasi pribadi
$sql = "SELECT * FROM informasi_pribadi WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$info = mysqli_fetch_assoc($result);

// Definisi field & icon
$fields = [
    'nama' => ['label' => 'Nama', 'icon' => 'bi-person', 'readonly' => true],
    'jenis_kelamin' => ['label' => 'Jenis Kelamin', 'icon' => 'bi-gender-ambiguous'],
    'tempat_lahir' => ['label' => 'Tempat Lahir', 'icon' => 'bi-geo-alt'],
    'tanggal_lahir' => ['label' => 'Tanggal Lahir', 'icon' => 'bi-calendar-event'],
    'alamat' => ['label' => 'Alamat', 'icon' => 'bi-house'],
    'kota' => ['label' => 'Kota', 'icon' => 'bi-building'],
    'no_ktp' => ['label' => 'No. KTP', 'icon' => 'bi-card-text'],
    'hubungan_keluarga' => ['label' => 'Hubungan Keluarga', 'icon' => 'bi-people-fill'],
    'no_hp' => ['label' => 'No. HP', 'icon' => 'bi-telephone']
];

// Ambil data field
$data = [];
foreach ($fields as $key => $info_field) {
    if($key == 'nama'){
        $data[$key] = $nama_user;
    } else {
        $data[$key] = $info[$key] ?? '';
    }
}

// Flash message
$notif = "";

// Proses simpan/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    foreach ($fields as $key => $info_field) {
        if($key != 'nama') { // nama tidak boleh diubah
            $$key = mysqli_real_escape_string($conn, $_POST[$key] ?? '');
        }
    }

    if ($info) {
        // Update
        $update = "UPDATE informasi_pribadi SET 
                    jenis_kelamin='$jenis_kelamin', tempat_lahir='$tempat_lahir', 
                    tanggal_lahir='$tanggal_lahir', alamat='$alamat', kota='$kota', no_ktp='$no_ktp',
                    hubungan_keluarga='$hubungan_keluarga', no_hp='$no_hp'
                   WHERE user_id='$user_id'";
        $notif = mysqli_query($conn, $update) ? "Data informasi pribadi berhasil diperbarui" : "Error update: ".mysqli_error($conn);
    } else {
        // Insert, sertakan nama dari tabel users
        $insert = "INSERT INTO informasi_pribadi 
                   (user_id, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, kota, no_ktp, hubungan_keluarga, no_hp)
                   VALUES ('$user_id','$nama_user','$jenis_kelamin','$tempat_lahir','$tanggal_lahir','$alamat','$kota','$no_ktp','$hubungan_keluarga','$no_hp')";
        $notif = mysqli_query($conn, $insert) ? "Data informasi pribadi berhasil disimpan" : "Error insert: ".mysqli_error($conn);
    }

    // Refresh data
    $sql = "SELECT * FROM informasi_pribadi WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $info = mysqli_fetch_assoc($result);
    foreach ($fields as $key => $info_field) {
        if($key == 'nama'){
            $data[$key] = $nama_user;
        } else {
            $data[$key] = $info[$key] ?? '';
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="card">
  <div class="card-header"><h4>Informasi Pribadi</h4></div>
  <div class="card-body">

    <!-- Form Edit Informasi Pribadi -->
    <form method="POST" id="formEditInfo" style="display: none;">
      <div class="row">
        <?php foreach ($fields as $key => $info_field): ?>
        <div class="col-md-6">
          <div class="form-group mb-2">
            <label><i class="bi <?= $info_field['icon'] ?>"></i> <?= $info_field['label'] ?></label>
            <?php if(isset($info_field['readonly']) && $info_field['readonly']): ?>
              <input type="text" class="form-control" value="<?= htmlspecialchars($data[$key]) ?>" readonly>
            <?php elseif($key == 'jenis_kelamin'): ?>
              <select name="<?= $key ?>" class="form-control" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <?php
                $opsi = ['Laki-laki', 'Perempuan'];
                foreach ($opsi as $o){
                    $sel = ($data[$key] == $o) ? "selected" : "";
                    echo "<option value='$o' $sel>$o</option>";
                }
                ?>
              </select>
            <?php elseif($key == 'hubungan_keluarga'): ?>
              <select name="<?= $key ?>" class="form-control" required>
                <option value="">-- Pilih Hubungan --</option>
                <?php
                $opsi = ['Suami','Istri','Ayah','Ibu','Saudara'];
                foreach ($opsi as $o){
                    $sel = ($data[$key] == $o) ? "selected" : "";
                    echo "<option value='$o' $sel>$o</option>";
                }
                ?>
              </select>
            <?php elseif(strpos($key,'tanggal')!==false): ?>
              <input type="date" name="<?= $key ?>" class="form-control" value="<?= $data[$key] ?>">
            <?php else: ?>
              <input type="text" name="<?= $key ?>" class="form-control" value="<?= htmlspecialchars($data[$key]) ?>">
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-right mt-2">
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="toggleFormInfo()">Batal</button>
      </div>
    </form>

    <!-- Tampilan Data -->
    <div id="dataViewInfo" class="row">
      <?php foreach ($fields as $key => $info_field): ?>
      <div class="col-md-6">
        <ul class="list-group list-group-flush mb-2">
          <li class="list-group-item">
            <i class="bi <?= $info_field['icon'] ?>"></i> <strong><?= $info_field['label'] ?></strong> : 
            <?php 
              if(strpos($key,'tanggal')!==false){
                  echo $data[$key] ? date('d-m-Y', strtotime($data[$key])) : '-';
              } else {
                  echo $data[$key] ?: '-';
              }
            ?>
          </li>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
  <div class="card-footer text-right" id="editButtonInfo">
    <button type="button" class="btn btn-primary" onclick="toggleFormInfo()">Edit Data Pribadi</button>
  </div>
</div>

<?php if (!empty($notif)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= $notif ?>',
    showConfirmButton: false,
    timer: 2000,
    position: 'center'
});
</script>
<?php endif; ?>

<script>
function toggleFormInfo() {
  const form = document.getElementById('formEditInfo');
  const view = document.getElementById('dataViewInfo');
  const editBtn = document.getElementById('editButtonInfo');
  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}
</script>

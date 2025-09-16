<?php
if (!isset($_SESSION)) session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<div class='alert alert-danger'>User belum login atau tidak valid.</div>";
    return;
}

// Ambil data kesehatan & asuransi
$sql = "SELECT * FROM riwayat_kesehatan WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$kesehatan = mysqli_fetch_assoc($result);

// Definisi data dan icon
$fields = [
    'gol_darah'=>['label'=>'Golongan Darah','icon'=>'bi-droplet'],
    'riwayat_penyakit'=>['label'=>'Riwayat Penyakit Penting','icon'=>'bi-heart-pulse'],
    'status_vaksinasi'=>['label'=>'Status Vaksinasi','icon'=>'bi-shield-check'],
    'no_bpjs_kesehatan'=>['label'=>'No. BPJS Kesehatan','icon'=>'bi-card-heading'],
    'no_bpjs_kerja'=>['label'=>'No. BPJS Ketenagakerjaan','icon'=>'bi-file-earmark-person'],
    'asuransi_tambahan'=>['label'=>'Asuransi Tambahan','icon'=>'bi-wallet2']
];

// Ambil data field
$data = [];
foreach($fields as $key=>$info){
    $data[$key] = $kesehatan[$key] ?? '';
}

$notif = "";

// Proses simpan/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_kesehatan'])) {
    foreach($fields as $key=>$info){
        $$key = mysqli_real_escape_string($conn, $_POST[$key] ?? '');
    }

    if ($kesehatan) {
        $update = "UPDATE riwayat_kesehatan SET 
                    gol_darah='$gol_darah', riwayat_penyakit='$riwayat_penyakit', status_vaksinasi='$status_vaksinasi', 
                    no_bpjs_kesehatan='$no_bpjs_kesehatan', no_bpjs_kerja='$no_bpjs_kerja', asuransi_tambahan='$asuransi_tambahan'
                   WHERE user_id='$user_id'";
        $notif = mysqli_query($conn, $update) ? "Data kesehatan & asuransi berhasil diperbarui" : "Error update: ".mysqli_error($conn);
    } else {
        $insert = "INSERT INTO riwayat_kesehatan 
                   (user_id, gol_darah, riwayat_penyakit, status_vaksinasi, no_bpjs_kesehatan, no_bpjs_kerja, asuransi_tambahan) 
                   VALUES ('$user_id','$gol_darah','$riwayat_penyakit','$status_vaksinasi','$no_bpjs_kesehatan','$no_bpjs_kerja','$asuransi_tambahan')";
        $notif = mysqli_query($conn, $insert) ? "Data kesehatan & asuransi berhasil disimpan" : "Error insert: ".mysqli_error($conn);
    }

    // Refresh data
    $sql = "SELECT * FROM riwayat_kesehatan WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $kesehatan = mysqli_fetch_assoc($result);
    foreach($fields as $key=>$info){
        $data[$key] = $kesehatan[$key] ?? '';
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="card">
  <div class="card-header"><h4>Data Kesehatan & Asuransi</h4></div>
  <div class="card-body">
    <!-- Form Edit -->
    <form method="POST" id="formEditKesehatan" style="display: none;">
      <div class="row">
        <?php
        $fields_left = array_slice($fields, 0, 3, true);
        $fields_right = array_slice($fields, 3, 3, true);
        foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
        <div class="col-md-6">
          <?php foreach($fields_col as $key=>$info): ?>
          <div class="form-group">
            <label><i class="bi <?= $info['icon'] ?>"></i> <?= $info['label'] ?></label>
            <?php if($key == 'gol_darah'): ?>
              <select name="<?= $key ?>" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php foreach(['A','B','AB','O'] as $g): ?>
                  <option value="<?= $g ?>" <?= ($data[$key]==$g) ? 'selected':'' ?>><?= $g ?></option>
                <?php endforeach; ?>
              </select>
            <?php elseif($key == 'riwayat_penyakit'): ?>
              <textarea name="<?= $key ?>" class="form-control"><?= htmlspecialchars($data[$key]) ?></textarea>
            <?php else: ?>
              <input type="text" name="<?= $key ?>" class="form-control" value="<?= htmlspecialchars($data[$key]) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-right">
        <button type="submit" name="simpan_kesehatan" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="toggleFormKesehatan()">Batal</button>
      </div>
    </form>

    <!-- Tampilan Data Kiri-Kanan -->
    <div id="dataViewKesehatan" class="row">
      <?php foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
      <div class="col-md-6">
        <ul class="list-group list-group-flush">
        <?php foreach($fields_col as $key=>$info): ?>
          <li class="list-group-item">
            <i class="bi <?= $info['icon'] ?>"></i> <strong><?= $info['label'] ?></strong> : <?= $data[$key] ?: '-' ?>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card-footer text-right" id="editButtonKesehatan">
    <button type="button" class="btn btn-primary" onclick="toggleFormKesehatan()">Edit Data Kesehatan & Asuransi</button>
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
function toggleFormKesehatan() {
  const form = document.getElementById('formEditKesehatan');
  const view = document.getElementById('dataViewKesehatan');
  const editBtn = document.getElementById('editButtonKesehatan');

  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}
</script>

<?php
if (!isset($_SESSION)) session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<div class='alert alert-danger'>User belum login atau tidak valid.</div>";
    return;
}

// Ambil pendidikan terakhir
$sql = "SELECT * FROM riwayat_pendidikan WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$pendidikan = mysqli_fetch_assoc($result);

// Definisi field & icon
$fields = [
    'pendidikan_terakhir'=>['label'=>'Pendidikan Terakhir','icon'=>'bi-mortarboard'],
    'jurusan'=>['label'=>'Jurusan / Program Studi','icon'=>'bi-book'],
    'kampus'=>['label'=>'Kampus / Institusi','icon'=>'bi-building'],
    'tgl_lulus'=>['label'=>'Tanggal Lulus','icon'=>'bi-calendar-event'],
    'no_ijazah'=>['label'=>'Nomor Ijazah','icon'=>'bi-card-text'],
    'ipk'=>['label'=>'IPK / Nilai Akhir','icon'=>'bi-bar-chart']
];

// Ambil data field
$data = [];
foreach($fields as $key=>$info){
    $data[$key] = $pendidikan[$key] ?? '';
}

// Flash message
$notif = "";

// Proses simpan/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    foreach($fields as $key=>$info){
        $$key = mysqli_real_escape_string($conn, $_POST[$key] ?? '');
    }

    if ($pendidikan) {
        $update = "UPDATE riwayat_pendidikan SET 
                    pendidikan_terakhir='$pendidikan_terakhir', jurusan='$jurusan', kampus='$kampus', 
                    tgl_lulus='$tgl_lulus', no_ijazah='$no_ijazah', ipk='$ipk' 
                   WHERE user_id='$user_id'";
        $notif = mysqli_query($conn, $update) ? "Data pendidikan berhasil diperbarui" : "Error update: ".mysqli_error($conn);
    } else {
        $insert = "INSERT INTO riwayat_pendidikan 
                   (user_id, pendidikan_terakhir, jurusan, kampus, tgl_lulus, no_ijazah, ipk) 
                   VALUES ('$user_id','$pendidikan_terakhir','$jurusan','$kampus','$tgl_lulus','$no_ijazah','$ipk')";
        $notif = mysqli_query($conn, $insert) ? "Data pendidikan berhasil disimpan" : "Error insert: ".mysqli_error($conn);
    }

    // Refresh data
    $sql = "SELECT * FROM riwayat_pendidikan WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $pendidikan = mysqli_fetch_assoc($result);
    foreach($fields as $key=>$info){
        $data[$key] = $pendidikan[$key] ?? '';
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="card">
  <div class="card-header"><h4>Riwayat Pendidikan</h4></div>
  <div class="card-body">

    <!-- Form Edit Pendidikan -->
    <form method="POST" id="formEditPendidikan" style="display: none;">
      <div class="row">
        <?php foreach($fields as $key=>$info): ?>
        <div class="col-md-6">
          <div class="form-group mb-2">
            <label><i class="bi <?= $info['icon'] ?>"></i> <?= $info['label'] ?></label>
            <?php if($key=='pendidikan_terakhir'): ?>
              <select name="<?= $key ?>" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php
                $opsi = ['SMA Sederajat','D3','S1','S2','S3'];
                foreach ($opsi as $o){
                    $sel = ($data[$key] == $o) ? "selected" : "";
                    echo "<option value='$o' $sel>$o</option>";
                }
                ?>
              </select>
            <?php elseif(strpos($key,'tgl')!==false): ?>
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
        <button type="button" class="btn btn-secondary" onclick="toggleFormPendidikan()">Batal</button>
      </div>
    </form>

    <!-- Tampilan Data -->
    <div id="dataViewPendidikan" class="row">
      <?php foreach($fields as $key=>$info): ?>
      <div class="col-md-6">
        <ul class="list-group list-group-flush mb-2">
          <li class="list-group-item">
            <i class="bi <?= $info['icon'] ?>"></i> <strong><?= $info['label'] ?></strong> : 
            <?php 
              if(strpos($key,'tgl')!==false){
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
  <div class="card-footer text-right" id="editButtonPendidikan">
    <button type="button" class="btn btn-primary" onclick="toggleFormPendidikan()">Edit Data Pendidikan</button>
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
function toggleFormPendidikan() {
  const form = document.getElementById('formEditPendidikan');
  const view = document.getElementById('dataViewPendidikan');
  const editBtn = document.getElementById('editButtonPendidikan');
  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}
</script>

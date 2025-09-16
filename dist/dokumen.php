<?php
if (!isset($_SESSION)) session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<div class='alert alert-danger'>User belum login atau tidak valid.</div>";
    return;
}

// Ambil data dokumen user
$sql = "SELECT * FROM dokumen_pendukung WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$dokumen = mysqli_fetch_assoc($result);

// Flash message
$notif = "";

// Definisi dokumen dan icon
$labels = [
    'ktp'=>['label'=>'Scan KTP','icon'=>'bi-person-badge'],
    'ijazah'=>['label'=>'Ijazah & Transkrip','icon'=>'bi-journal-text'],
    'str'=>['label'=>'STR','icon'=>'bi-file-medical'],
    'sip'=>['label'=>'SIP','icon'=>'bi-file-medical'],
    'vaksin'=>['label'=>'Sertifikat Vaksin','icon'=>'bi-capsule'],
    'pelatihan'=>['label'=>'Sertifikat Pelatihan','icon'=>'bi-award'],
    'surat_kerja'=>['label'=>'Surat Pengalaman Kerja','icon'=>'bi-file-earmark-text'],
    'pas_foto'=>['label'=>'Pas Foto','icon'=>'bi-image']
];

// Proses upload / update dokumen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_dokumen'])) {
    $uploads = [];
    foreach ($labels as $f=>$info) {
        if(isset($_FILES[$f]) && $_FILES[$f]['error'] == 0){
            $ext = pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION);
            $filename = $user_id . '_' . str_replace(' ','',$info['label']) . '.' . $ext;
            $target_dir = "uploads/";
            if(!is_dir($target_dir)) mkdir($target_dir,0777,true);
            move_uploaded_file($_FILES[$f]["tmp_name"], $target_dir.$filename);
            $uploads[$f] = $filename;
        } else {
            $uploads[$f] = $dokumen[$f] ?? null;
        }
    }

    if($dokumen){
        $set = [];
        foreach ($uploads as $k=>$v) $set[] = "$k='".mysqli_real_escape_string($conn,$v)."'";
        $update = "UPDATE dokumen_pendukung SET ".implode(", ",$set)." WHERE user_id='$user_id'";
        $notif = mysqli_query($conn,$update) ? "Dokumen berhasil diperbarui" : "Error update: ".mysqli_error($conn);
    } else {
        $cols = "user_id,".implode(", ",array_keys($uploads));
        $vals = "'$user_id','".implode("','",array_map(function($v) use ($conn){return mysqli_real_escape_string($conn,$v);},$uploads))."'";
        $insert = "INSERT INTO dokumen_pendukung ($cols) VALUES ($vals)";
        $notif = mysqli_query($conn,$insert) ? "Dokumen berhasil disimpan" : "Error insert: ".mysqli_error($conn);
    }

    // Refresh data
    $sql = "SELECT * FROM dokumen_pendukung WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $dokumen = mysqli_fetch_assoc($result);
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="card">
  <div class="card-header"><h4>Dokumen Pendukung</h4></div>
  <div class="card-body">
    <!-- Form Upload Dokumen -->
    <form method="POST" enctype="multipart/form-data" id="formEditDokumen" style="display: none;">
      <div class="row">
        <?php
        $fields_left = array_slice($labels, 0, 4, true);
        $fields_right = array_slice($labels, 4, 4, true);
        foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
        <div class="col-md-6">
          <?php foreach($fields_col as $key=>$info): ?>
          <div class="form-group">
            <label><i class="bi <?= $info['icon'] ?>"></i> <?= $info['label'] ?></label>
            <?php if(!empty($dokumen[$key])): ?>
              <p>File saat ini: <a href="uploads/<?= htmlspecialchars($dokumen[$key]) ?>" target="_blank"><?= htmlspecialchars($dokumen[$key]) ?></a></p>
            <?php endif; ?>
            <input type="file" name="<?= $key ?>" class="form-control">
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-right">
        <button type="submit" name="simpan_dokumen" class="btn btn-success">Simpan Dokumen</button>
        <button type="button" class="btn btn-secondary" onclick="toggleFormDokumen()">Batal</button>
      </div>
    </form>

    <!-- Tampilan Data Dokumen Kiri-Kanan -->
    <div id="dataViewDokumen" class="row">
      <?php foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
      <div class="col-md-6">
        <ul class="list-group list-group-flush">
        <?php foreach($fields_col as $key=>$info): ?>
          <li class="list-group-item">
            <i class="bi <?= $info['icon'] ?>"></i> <strong><?= $info['label'] ?></strong> :
            <?php if(!empty($dokumen[$key])): ?>
              <a href="uploads/<?= htmlspecialchars($dokumen[$key]) ?>" target="_blank"><?= htmlspecialchars($dokumen[$key]) ?></a>
            <?php else: ?> - <?php endif; ?>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card-footer text-right" id="editButtonDokumen">
    <button type="button" class="btn btn-primary" onclick="toggleFormDokumen()">Upload / Edit Dokumen</button>
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
function toggleFormDokumen() {
  const form = document.getElementById('formEditDokumen');
  const view = document.getElementById('dataViewDokumen');
  const editBtn = document.getElementById('editButtonDokumen');

  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}
</script>

<?php
if (!isset($_SESSION)) session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<div class='alert alert-danger'>User belum login atau tidak valid.</div>";
    return;
}

// Ambil pekerjaan terakhir
$sql = "SELECT * FROM riwayat_pekerjaan WHERE user_id = $user_id ORDER BY tanggal_mulai DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$pekerjaan = mysqli_fetch_assoc($result);

// Definisi field & icon
$fields = [
    'nama_perusahaan'=>['label'=>'Nama Perusahaan','icon'=>'bi-building'],
    'posisi'=>['label'=>'Posisi / Jabatan','icon'=>'bi-person-badge'],
    'tanggal_mulai'=>['label'=>'Tanggal Mulai','icon'=>'bi-calendar-event'],
    'tanggal_selesai'=>['label'=>'Tanggal Selesai','icon'=>'bi-calendar-check'],
    'alasan_keluar'=>['label'=>'Alasan Keluar','icon'=>'bi-box-arrow-right']
];

// Ambil data field
$data = [];
foreach($fields as $key=>$info){
    $data[$key] = $pekerjaan[$key] ?? '';
}

$id_pekerjaan = $pekerjaan['id'] ?? 0;

// Flash message
$notif = "";

// Proses simpan/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_pekerjaan'])) {
    foreach($fields as $key=>$info){
        $$key = mysqli_real_escape_string($conn, $_POST[$key] ?? '');
    }

    if ($id_pekerjaan > 0) {
        $update = "UPDATE riwayat_pekerjaan 
                   SET nama_perusahaan='$nama_perusahaan', posisi='$posisi', tanggal_mulai='$tanggal_mulai', 
                       tanggal_selesai='$tanggal_selesai', alasan_keluar='$alasan_keluar' 
                   WHERE id='$id_pekerjaan' AND user_id='$user_id'";
        $notif = mysqli_query($conn, $update) ? "Data riwayat pekerjaan berhasil diperbarui" : "Error update: " . mysqli_error($conn);
    } else {
        $insert = "INSERT INTO riwayat_pekerjaan 
                   (user_id, nama_perusahaan, posisi, tanggal_mulai, tanggal_selesai, alasan_keluar) 
                   VALUES ('$user_id','$nama_perusahaan','$posisi','$tanggal_mulai','$tanggal_selesai','$alasan_keluar')";
        $notif = mysqli_query($conn, $insert) ? "Data riwayat pekerjaan berhasil disimpan" : "Error insert: " . mysqli_error($conn);
    }

    // Refresh data
    $sql = "SELECT * FROM riwayat_pekerjaan WHERE user_id = $user_id ORDER BY tanggal_mulai DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $pekerjaan = mysqli_fetch_assoc($result);
    $id_pekerjaan = $pekerjaan['id'] ?? 0;
    foreach($fields as $key=>$info){
        $data[$key] = $pekerjaan[$key] ?? '';
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="card">
  <div class="card-header"><h4>Riwayat Pekerjaan Terakhir</h4></div>
  <div class="card-body">
    <!-- Form Edit -->
    <form method="POST" id="formEditPekerjaan" style="display: none;">
      <input type="hidden" name="id_pekerjaan" value="<?= $id_pekerjaan ?>">
      <div class="row">
        <?php
        $fields_left = array_slice($fields,0,3,true);
        $fields_right = array_slice($fields,3,2,true); // sisa 2
        foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
        <div class="col-md-6">
          <?php foreach($fields_col as $key=>$info): ?>
          <div class="form-group">
            <label><i class="bi <?= $info['icon'] ?>"></i> <?= $info['label'] ?></label>
            <?php if(strpos($key,'tanggal')!==false): ?>
              <input type="date" name="<?= $key ?>" class="form-control" value="<?= $data[$key] ?>">
            <?php else: ?>
              <input type="text" name="<?= $key ?>" class="form-control" value="<?= htmlspecialchars($data[$key]) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-right">
        <button type="submit" name="simpan_pekerjaan" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="toggleFormPekerjaan()">Batal</button>
      </div>
    </form>

    <!-- Tampilan Data Kiri-Kanan -->
    <div id="dataViewPekerjaan" class="row">
      <?php foreach(['left'=>$fields_left,'right'=>$fields_right] as $side=>$fields_col): ?>
      <div class="col-md-6">
        <ul class="list-group list-group-flush">
        <?php foreach($fields_col as $key=>$info): ?>
          <li class="list-group-item">
            <i class="bi <?= $info['icon'] ?>"></i> <strong><?= $info['label'] ?></strong> : 
            <?php 
              if(strpos($key,'tanggal')!==false) {
                  echo $data[$key] ? date('d-m-Y', strtotime($data[$key])) : '-';
              } else {
                  echo $data[$key] ?: '-';
              }
            ?>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card-footer text-right" id="editButtonPekerjaan">
    <button type="button" class="btn btn-primary" onclick="toggleFormPekerjaan()">Edit Data Pekerjaan</button>
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
function toggleFormPekerjaan() {
  const form = document.getElementById('formEditPekerjaan');
  const view = document.getElementById('dataViewPekerjaan');
  const editBtn = document.getElementById('editButtonPekerjaan');
  const isEditing = form.style.display === 'block';
  form.style.display = isEditing ? 'none' : 'block';
  view.style.display = isEditing ? 'block' : 'none';
  editBtn.style.display = isEditing ? 'block' : 'none';
}
</script>

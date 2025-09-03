<?php
// master_indikator.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
$nama_user = $_SESSION['nama_user'] ?? '';

// default tab aktif
$activeTab = 'data';
$modals = [];

// akses menu
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
            JOIN menu ON akses_menu.menu_id = menu.id 
            WHERE akses_menu.user_id = '".intval($user_id)."' 
            AND menu.file_menu = '".mysqli_real_escape_string($conn,$current_file)."'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// proses simpan
if (isset($_POST['simpan'])) {
    $nama_indikator = mysqli_real_escape_string($conn, $_POST['nama_indikator']);
    $definisi = mysqli_real_escape_string($conn, $_POST['definisi']);
    $numerator = mysqli_real_escape_string($conn, $_POST['numerator']);
    $denominator = mysqli_real_escape_string($conn, $_POST['denominator']);
    $standar = mysqli_real_escape_string($conn, $_POST['standar']);
    $sumber_data = mysqli_real_escape_string($conn, $_POST['sumber_data']);
    $frekuensi = mysqli_real_escape_string($conn, $_POST['frekuensi']);
    $penanggung_jawab = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
    $unit_id = intval($_POST['unit_id']);

    if ($nama_indikator && $standar) {
        $q = "INSERT INTO indikator_nasional 
                (nama_indikator, definisi, numerator, denominator, standar, sumber_data, frekuensi, penanggung_jawab, unit_id) 
              VALUES 
                ('$nama_indikator','$definisi','$numerator','$denominator','$standar','$sumber_data','$frekuensi','$penanggung_jawab','$unit_id')";
        if (mysqli_query($conn, $q)) {
            $_SESSION['flash_message'] = "Data berhasil disimpan.";
            $activeTab = 'data';
        } else {
            $_SESSION['flash_message'] = "Gagal menyimpan data: " . mysqli_error($conn);
            $activeTab = 'input';
        }
    } else {
        $_SESSION['flash_message'] = "Lengkapi semua field!";
        $activeTab = 'input';
    }
}

// proses update
if (isset($_POST['update'])) {
    $id_nasional = intval($_POST['id_nasional']);
    $nama_indikator = mysqli_real_escape_string($conn, $_POST['nama_indikator']);
    $definisi = mysqli_real_escape_string($conn, $_POST['definisi']);
    $numerator = mysqli_real_escape_string($conn, $_POST['numerator']);
    $denominator = mysqli_real_escape_string($conn, $_POST['denominator']);
    $standar = mysqli_real_escape_string($conn, $_POST['standar']);
    $sumber_data = mysqli_real_escape_string($conn, $_POST['sumber_data']);
    $frekuensi = mysqli_real_escape_string($conn, $_POST['frekuensi']);
    $penanggung_jawab = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
    $unit_id = intval($_POST['unit_id']);

    $q = "UPDATE indikator_nasional SET 
            nama_indikator='$nama_indikator',
            definisi='$definisi',
            numerator='$numerator',
            denominator='$denominator',
            standar='$standar',
            sumber_data='$sumber_data',
            frekuensi='$frekuensi',
            penanggung_jawab='$penanggung_jawab',
            unit_id='$unit_id'
          WHERE id_nasional='$id_nasional'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['flash_message'] = "Data berhasil diperbarui.";
    } else {
        $_SESSION['flash_message'] = "Gagal update data: " . mysqli_error($conn);
    }
    header("Location: master_indikator.php");
    exit;
}

// hapus data
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM indikator_nasional WHERE id_nasional='$id'");
    $_SESSION['flash_message'] = "Data berhasil dihapus.";
    header("Location: master_indikator.php");
    exit;
}

// ambil data unit kerja
$units = mysqli_query($conn, "SELECT id, nama_unit FROM unit_kerja ORDER BY nama_unit");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Master Indikator Nasional</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .aksi-btn { display: flex; gap: 5px; justify-content: center; }
    .dokumen-table { white-space: nowrap; font-size: 13px; }
    .table-responsive { overflow-x: auto; }
    .flash-center {
      position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%);
      z-index: 1050; min-width: 300px; max-width: 90%; text-align: center;
      padding: 15px; border-radius: 8px; font-weight: 500;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
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
            <?= htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
          </div>
        <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Master Indikator Nasional</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" id="indikatorTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" data-toggle="tab" href="#input" role="tab">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" data-toggle="tab" href="#data" role="tab">Data Indikator</a>
                </li>
              </ul>

              <div class="tab-content mt-3">
                <!-- FORM INPUT -->

               <!-- FORM INPUT -->
<div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>" id="input" role="tabpanel">
  <form method="POST">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Nama Indikator</label>
          <input type="text" name="nama_indikator" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Definisi Operasional</label>
          <textarea name="definisi" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Numerator</label>
          <textarea name="numerator" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Denominator</label>
          <textarea name="denominator" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label>Standar/Target (%)</label>
          <input type="number" name="standar" step="0.01" class="form-control" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Sumber Data</label>
          <input type="text" name="sumber_data" class="form-control">
        </div>
        <div class="form-group">
          <label>Frekuensi Pelaporan</label>
          <select name="frekuensi" class="form-control">
            <option value="">-- Pilih --</option>
            <option value="Harian">Harian</option>
            <option value="Mingguan">Mingguan</option>
            <option value="Bulanan">Bulanan</option>
            <option value="Triwulan">Triwulan</option>
            <option value="Tahunan">Tahunan</option>
          </select>
        </div>
       <div class="form-group">
  <label>Penanggung Jawab</label>
  <select name="penanggung_jawab" id="penanggung_jawab" class="form-control select2" required>
    <option value="">-- Pilih Penanggung Jawab --</option>
    <?php
    $qUsers = mysqli_query($conn, "SELECT id, nama FROM users ORDER BY nama ASC");
    while($usr = mysqli_fetch_assoc($qUsers)): ?>
      <option value="<?= htmlspecialchars($usr['nama']) ?>">
        <?= htmlspecialchars($usr['nama']) ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

        <div class="form-group">
          <label>Unit Kerja</label>
          <select name="unit_id" class="form-control" required>
            <option value="">-- Pilih Unit --</option>
            <?php while($u = mysqli_fetch_assoc($units)): ?>
              <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama_unit']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
  </form>
</div>

<!-- DATA -->
<div class="tab-pane fade <?= ($activeTab=='data')?'show active':'' ?>" id="data" role="tabpanel">
  <div class="table-responsive">
    <table class="table table-bordered table-striped dokumen-table">
      <thead class="thead-light">
        <tr>
          <th>No</th>
          <th>Nama Indikator</th>
          <th>Standar</th>
          <th>Unit</th>
          <th>Frekuensi</th>
          <th>Penanggung Jawab</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $qInd = mysqli_query($conn, "SELECT i.*, u.nama_unit 
                                   FROM indikator_nasional i 
                                   LEFT JOIN unit_kerja u ON i.unit_id=u.id 
                                   ORDER BY i.id_nasional DESC");
      $no=1;
      while($row = mysqli_fetch_assoc($qInd)): 
          ob_start(); ?>
          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $row['id_nasional'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <form method="POST">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Indikator</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id_nasional" value="<?= $row['id_nasional'] ?>">
                    <div class="form-group">
                      <label>Nama Indikator</label>
                      <input type="text" name="nama_indikator" class="form-control" value="<?= htmlspecialchars($row['nama_indikator']) ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Definisi</label>
                      <textarea name="definisi" class="form-control"><?= htmlspecialchars($row['definisi']) ?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Numerator</label>
                      <textarea name="numerator" class="form-control"><?= htmlspecialchars($row['numerator']) ?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Denominator</label>
                      <textarea name="denominator" class="form-control"><?= htmlspecialchars($row['denominator']) ?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Standar</label>
                      <input type="number" name="standar" class="form-control" value="<?= htmlspecialchars($row['standar']) ?>">
                    </div>
                    <div class="form-group">
                      <label>Sumber Data</label>
                      <input type="text" name="sumber_data" class="form-control" value="<?= htmlspecialchars($row['sumber_data']) ?>">
                    </div>
                    <div class="form-group">
                      <label>Frekuensi</label>
                      <select name="frekuensi" class="form-control">
                        <option <?= ($row['frekuensi']=='Harian')?'selected':'' ?>>Harian</option>
                        <option <?= ($row['frekuensi']=='Mingguan')?'selected':'' ?>>Mingguan</option>
                        <option <?= ($row['frekuensi']=='Bulanan')?'selected':'' ?>>Bulanan</option>
                        <option <?= ($row['frekuensi']=='Triwulan')?'selected':'' ?>>Triwulan</option>
                        <option <?= ($row['frekuensi']=='Tahunan')?'selected':'' ?>>Tahunan</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Penanggung Jawab</label>
                      <input type="text" name="penanggung_jawab" class="form-control" value="<?= htmlspecialchars($row['penanggung_jawab']) ?>">
                    </div>
                    <div class="form-group">
                      <label>Unit Kerja</label>
                      <select name="unit_id" class="form-control">
                        <?php
                        $qU = mysqli_query($conn, "SELECT id, nama_unit FROM unit_kerja ORDER BY nama_unit");
                        while($u = mysqli_fetch_assoc($qU)): ?>
                          <option value="<?= $u['id'] ?>" <?= ($u['id']==$row['unit_id'])?'selected':'' ?>>
                            <?= htmlspecialchars($u['nama_unit']) ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php $modals[] = ob_get_clean(); ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_indikator']) ?></td>
          <td><?= htmlspecialchars($row['standar']) ?></td>
          <td><?= htmlspecialchars($row['nama_unit']) ?></td>
          <td><?= htmlspecialchars($row['frekuensi']) ?></td>
          <td><?= htmlspecialchars($row['penanggung_jawab']) ?></td>
          <td>
            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $row['id_nasional'] ?>"><i class="fas fa-edit"></i></button>
            <a href="?hapus=<?= $row['id_nasional'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

              </div> <!-- end tab-content -->
            </div>
          </div>

        </div>
      </section>
    </div>
  </div>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
  $(document).ready(function() {
    $('#penanggung_jawab').select2({
      placeholder: "Cari nama penanggung jawab...",
      allowClear: true,
      width: '100%'
    });
    setTimeout(function(){ $("#flashMsg").fadeOut("slow"); }, 2500);
  });
</script>

<?php
foreach ($modals as $m) echo $m;
?>
</body>
</html>
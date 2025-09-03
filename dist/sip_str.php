<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__); // 

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// Proses Simpan
if (isset($_POST['simpan'])) {
  $user_id     = $_SESSION['user_id'];
  $selected_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = " . intval($_POST['user_ref'])));

  $nama        = $selected_user['nama'];
  $nik         = $selected_user['nik'];
  $email       = $selected_user['email'];
  $jabatan     = $selected_user['jabatan'];
  $unit_kerja  = $selected_user['unit_kerja'];
  $no_sip      = mysqli_real_escape_string($conn, $_POST['no_sip']);
  $masa_berlaku= mysqli_real_escape_string($conn, $_POST['masa_berlaku']);
  $waktu_input = date('Y-m-d H:i:s');

  // Upload File
  $uploadDir = "uploads/sip/";
  $allowedTypes = ['pdf', 'png', 'jpg', 'jpeg'];
  $fileName = $_FILES['file_upload']['name'];
  $fileTmp = $_FILES['file_upload']['tmp_name'];
  $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

  if (in_array($fileExt, $allowedTypes)) {
    $newName = uniqid() . '.' . $fileExt;
    move_uploaded_file($fileTmp, $uploadDir . $newName);

 $query = "INSERT INTO data_sip_str (nama, nik, email, jabatan, unit_kerja, no_sip, masa_berlaku, file_upload, waktu_input)
          VALUES ('$nama', '$nik', '$email', '$jabatan', '$unit_kerja', '$no_sip', '$masa_berlaku', '$newName', '$waktu_input')";


    if (mysqli_query($conn, $query)) {
      $_SESSION['flash_message'] = "✅ Data SIP berhasil disimpan.";
      echo "<script>location.href='sip_str.php';</script>";
      exit;
    } else {
      $_SESSION['flash_message'] = "❌ Gagal menyimpan data: " . mysqli_error($conn);
    }
  } else {
    $_SESSION['flash_message'] = "❌ Format file tidak didukung.";
  }
}

// Ambil data user untuk dropdown
$users = mysqli_query($conn, "SELECT * FROM users WHERE status = 'active' ORDER BY nama ASC");

// Ambil data SIP/STR
$data_sip = mysqli_query($conn, "SELECT * FROM data_sip_str ORDER BY waktu_input DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>f.i.x.p.o.i.n.t</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
 table.sip-table {
  font-size: 13px;
  white-space: nowrap;
  table-layout: auto; /* Penting agar lebar kolom menyesuaikan isi */
  width: 100%;
}

table.sip-table th,
table.sip-table td {
  padding: 4px 8px;
  vertical-align: middle;
  white-space: nowrap; /* Supaya teks tidak membungkus */
  max-width: 1px;       /* Ini akan membuat kolom sekecil mungkin */
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

          <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-info text-center">
              <?= $_SESSION['flash_message'] ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
          <?php endif; ?>

          <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
  <h4 class="mb-0">
    Manajemen Data SIP / STR
    <button type="button" class="btn btn-sm btn-light ml-2" data-toggle="modal" data-target="#infoWarna" title="Penjelasan Warna">
      <i class="fas fa-info-circle text-primary"></i>
    </button>
  </h4>
</div>

            <div class="card-body">
              <!-- Tab menu -->
              <ul class="nav nav-tabs" id="sipTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data SIP / STR</a>
                </li>
              </ul>

              <!-- Tab Content -->
              <div class="tab-content mt-3">
                <!-- Form Input -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                      <label>Pilih Pegawai</label>
                      <select name="user_ref" class="form-control" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php while ($user = mysqli_fetch_assoc($users)) : ?>
                          <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['nama']) ?> (<?= $user['nik'] ?>)
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label>No. SIP</label>
                        <input type="text" name="no_sip" class="form-control" required>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Masa Berlaku SIP</label>
                        <input type="date" name="masa_berlaku" class="form-control" required>
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Upload File (pdf, jpg, png)</label>
                      <input type="file" name="file_upload" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

         <!-- Data SIP / STR -->
<div class="tab-pane fade" id="data" role="tabpanel">
  <div class="table-responsive">
    <table class="table table-bordered table-striped sip-table">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>Email</th>
          <th>Jabatan</th>
          <th>Unit Kerja</th>
          <th>No SIP</th>
          <th>Masa Berlaku</th>
          <th>File</th>
          <th>Tanggal Input</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $today = date('Y-m-d');
        while($row = mysqli_fetch_assoc($data_sip)) :
          $masa_berlaku = $row['masa_berlaku'];
          $tanggal_input = date('d-m-Y H:i', strtotime($row['waktu_input']));
          $exp_date = strtotime($masa_berlaku);
          $now = strtotime($today);
          $selisih_hari = floor(($exp_date - $now) / (60 * 60 * 24));

          if ($selisih_hari > 60) {
            $row_class = 'table-success';
            $style_text = '';
          } elseif ($selisih_hari > 7) {
            $row_class = 'table-warning';
            $style_text = '';
          } elseif ($selisih_hari >= 0) {
            $row_class = 'table-danger';
            $style_text = '';
          } else {
            $row_class = 'table-secondary';
            $style_text = 'text-decoration: line-through;';
          }
        ?>
        <tr class="<?= $row_class ?>">
          <td style="<?= $style_text ?>"><?= $no++ ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['nama']) ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['nik']) ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['email']) ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['jabatan']) ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['unit_kerja']) ?></td>
          <td style="<?= $style_text ?>"><?= htmlspecialchars($row['no_sip']) ?></td>
          <td style="<?= $style_text ?>"><?= date('d-m-Y', strtotime($row['masa_berlaku'])) ?></td>
          <td style="<?= $style_text ?>"><a href="uploads/sip/<?= $row['file_upload'] ?>" target="_blank">Lihat</a></td>
          <td style="<?= $style_text ?>"><?= $tanggal_input ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

              </div> <!-- tab-content -->
            </div>
          </div>

        </div>
      </section>
    </div>
  </div>
</div>

<!-- Script Template Stisla -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
  $(document).ready(function(){
    console.log('Bootstrap modal test:', typeof $.fn.modal); // Harus "function"
  });
</script>


<script>
  $(document).ready(function(){
    // Jika ada modal terbuka tapi tidak bisa ditutup
    $('#infoWarna').on('hidden.bs.modal', function () {
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();
    });

    // Debug status modal
    $('#infoWarna').on('shown.bs.modal', function () {
      console.log('Modal infoWarna terbuka');
    });
  });
</script>

<!-- Modal Penjelasan Warna -->
<div class="modal fade" id="infoWarna" tabindex="-1" role="dialog" aria-labelledby="infoWarnaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infoWarnaLabel">Penjelasan Warna Tabel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="list-unstyled">
          <li><span class="badge badge-success">Hijau</span> - Masa berlaku &gt; 60 hari</li><br>
          <li><span class="badge badge-warning text-dark">Kuning</span> - Masa berlaku antara 7 sampai 60 hari</li><br>
          <li><span class="badge badge-danger">Merah</span> - Masa berlaku kurang dari 7 hari</li><br>
          <li><span class="badge badge-secondary">Abu-abu</span> - Sudah kadaluarsa</li><br>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


</body>
</html>

<?php
include 'security.php'; // sudah handle session_start + cek login + timeout
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];


// Tambahkan sebelum HTML
$data_kategori = mysqli_query($conn, "SELECT * FROM kategori_arsip ORDER BY nama_kategori ASC");


// Proses simpan arsip digital
if (isset($_POST['simpan'])) {
  $judul       = $_POST['judul'];
  $deskripsi   = $_POST['deskripsi'];
  $kategori    = $_POST['kategori'];
  $file_arsip  = '';

  if ($_FILES['file_arsip']['name']) {
    $ext = pathinfo($_FILES['file_arsip']['name'], PATHINFO_EXTENSION);
    $file_arsip = 'arsip_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['file_arsip']['tmp_name'], 'uploads/' . $file_arsip);
  }

  mysqli_query($conn, "INSERT INTO arsip_digital (
    judul, deskripsi, kategori, file_arsip, tgl_upload, user_input
  ) VALUES (
    '$judul', '$deskripsi', '$kategori', '$file_arsip', NOW(), '$user_id'
  )");

  header("Location: arsip_digital.php#data");
  exit;
}

$data_arsip = mysqli_query($conn, "SELECT a.*, u.nama AS user_nama 
  FROM arsip_digital a 
  LEFT JOIN users u ON a.user_input = u.id 
  ORDER BY a.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Arsip Digital</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    .table-arsip {
      white-space: nowrap;
      min-width: 1200px;
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

          <div class="card">
            <div class="card-header">
              <h4>Manajemen Arsip Digital</h4>
            </div>
            <div class="card-body">

              <ul class="nav nav-tabs" id="arsipTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab">Input Arsip</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Arsip</a>
                </li>
              </ul>

              <div class="tab-content mt-3" id="arsipTabContent">
                <div class="tab-pane fade show active" id="form" role="tabpanel">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group"><label>Judul Arsip</label><input name="judul" class="form-control" required></div>
                       <!-- Di bagian <select name="kategori"> -->
<select name="kategori" class="form-control" required>
  <option value="">-- Pilih Kategori --</option>
  <?php while ($k = mysqli_fetch_assoc($data_kategori)) : ?>
    <option value="<?= htmlspecialchars($k['nama_kategori']) ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
  <?php endwhile; ?>
</select>

                      </div>
                      <div class="col-md-6">
                        <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" required></textarea></div>
                        <div class="form-group"><label>File Arsip (PDF)</label><input type="file" name="file_arsip" accept=".pdf" class="form-control"></div>
                      </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive-custom">
                    <table class="table table-bordered table-striped table-arsip">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Judul</th>
                          <th>Kategori</th>
                          <th>Deskripsi</th>
                          <th>File</th>
                          <th>Tanggal Upload</th>
                          <th>Petugas</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data_arsip)) : ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>
                              <?php if ($row['file_arsip']) : ?>
                                <a href="uploads/<?= $row['file_arsip'] ?>" target="_blank" class="btn btn-sm btn-info">
                                  <i class="fas fa-file-pdf"></i> Lihat
                                </a>
                              <?php else : ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['tgl_upload']) ?></td>
                            <td><?= htmlspecialchars($row['user_nama'] ?? '-') ?></td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div> <!-- end tab content -->
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
  $(document).ready(function () {
    var hash = window.location.hash;
    if (hash) {
      $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
  });
</script>

</body>
</html>

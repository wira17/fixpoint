<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');



$id = $_GET['id'] ?? 0;

// Ambil data surat masuk
$query = mysqli_query($conn, "SELECT * FROM surat_masuk WHERE id = '$id' LIMIT 1");
$data = mysqli_fetch_assoc($query);

if (!$data) {
  echo "Data tidak ditemukan";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>f.i.x.p.o.i.n.t</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php include 'navbar.php'; ?>
      <?php include 'sidebar.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1><i class="fas fa-envelope"></i> Detail Surat Masuk</h1>
          </div>

          <div class="section-body">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Informasi Surat Masuk</h4>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tr><th>No Surat</th><td><?= htmlspecialchars($data['no_surat']) ?></td></tr>
                  <tr><th>Tanggal</th><td><?= htmlspecialchars($data['tgl_surat']) ?></td></tr>
                  <tr><th>Asal</th><td><?= htmlspecialchars($data['asal_surat']) ?></td></tr>
                  <tr><th>Perihal</th><td><?= htmlspecialchars($data['perihal']) ?></td></tr>
                  <tr><th>Keterangan</th><td><?= htmlspecialchars($data['keterangan']) ?></td></tr>
                  <tr><th>File</th>
                    <td>
                      <?php if ($data['file_surat']) : ?>
                        <a href="uploads/<?= $data['file_surat'] ?>" class="btn btn-sm btn-info" target="_blank">
                          <i class="fas fa-file-pdf"></i> Lihat File
                        </a>
                      <?php else : ?>
                        <span class="text-muted">Tidak ada file</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                </table>
               <a href="surat_keluar.php#data" class="btn btn-secondary">

  <i class="fas fa-arrow-left"></i> Kembali ke Surat Keluar
</a>

              </div>
            </div>
          </div>
        </section>
      </div>

    </div>
  </div>

  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>

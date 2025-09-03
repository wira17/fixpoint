<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');




$id = $_GET['id'] ?? 0;

// Ambil detail surat keluar
$query = mysqli_query($conn, "
  SELECT sk.*, sm.no_surat AS no_surat_masuk
  FROM surat_keluar sk
  LEFT JOIN surat_masuk sm ON sm.id = sk.balasan_untuk_id
  WHERE sk.id = '$id' LIMIT 1
");
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
            <h1><i class="fas fa-envelope-open-text"></i> Detail Surat Keluar</h1>
          </div>

          <div class="section-body">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-paper-plane"></i> Informasi Surat Keluar</h4>
              </div>

              <div class="card-body">
                <table class="table table-bordered">
                  <tr>
                    <th width="25%">No Surat</th>
                    <td><?= htmlspecialchars($data['no_surat']) ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Surat</th>
                    <td><?= htmlspecialchars($data['tgl_surat']) ?></td>
                  </tr>
                  <tr>
                    <th>Tujuan</th>
                    <td><?= htmlspecialchars($data['tujuan']) ?></td>
                  </tr>
                  <tr>
                    <th>Perihal</th>
                    <td><?= htmlspecialchars($data['perihal']) ?></td>
                  </tr>
                  <tr>
                    <th>Keterangan</th>
                    <td><?= htmlspecialchars($data['keterangan']) ?></td>
                  </tr>
                  <tr>
                    <th>Balasan Untuk Surat</th>
                    <td><?= $data['no_surat_masuk'] ?? '-' ?></td>
                  </tr>
                  <tr>
                    <th>File Surat</th>
                    <td>
                      <?php if ($data['file_surat']) : ?>
                        <a href="uploads/<?= $data['file_surat'] ?>" target="_blank" class="btn btn-sm btn-info">
                          <i class="fas fa-file-pdf"></i> Lihat File
                        </a>
                      <?php else : ?>
                        <span class="text-muted">Tidak ada file</span>
                      <?php endif ?>
                    </td>
                  </tr>
                </table>
 <a href="surat_masuk.php?tab=data" class="btn btn-secondary">
  <i class="fas fa-arrow-left"></i> Kembali
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
  <script>
  $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');

    if (tab === 'data') {
      $('#data-tab').tab('show');
    } else {
      $('#form-tab').tab('show'); // default ke tab form jika tidak ada parameter
    }
  });
</script>

</body>
</html>

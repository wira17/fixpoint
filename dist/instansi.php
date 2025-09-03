<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];


// Proses simpan jika form disubmit
$notif = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama_instansi'];
  $alamat = $_POST['alamat'];
  $kota = $_POST['kota'];
  $kabupaten = $_POST['kabupaten'];
  $provinsi = $_POST['provinsi'];
  $telp = $_POST['telp'];
  $email = $_POST['email'];

  $logo = $_FILES['logo']['name'];
  $tmp_logo = $_FILES['logo']['tmp_name'];

  if ($logo) {
    $upload_dir = "uploads/logo/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    move_uploaded_file($tmp_logo, $upload_dir . $logo);
  }

  $stmt = $conn->prepare("INSERT INTO instansi (nama_instansi, alamat, kota, kabupaten, provinsi, telp, email, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $nama, $alamat, $kota, $kabupaten, $provinsi, $telp, $email, $logo);
  if ($stmt->execute()) {
    $notif = "Data berhasil disimpan!";
  } else {
    $notif = "Gagal menyimpan data.";
  }
}

$query = "SELECT * FROM instansi ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php include 'navbar.php'; ?>
      <?php include 'sidebar.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Data Instansi</h1>
          </div>

          <div class="section-body">
            <?php if ($notif): ?>
              <div class="alert alert-info"><?= $notif; ?></div>
            <?php endif; ?>

            <div class="card">
              <div class="card-header">
                <h4>Form Tambah Instansi</h4>
              </div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Nama Instansi</label>
                        <input type="text" name="nama_instansi" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                      </div>
                      <div class="form-group">
                        <label>Kota</label>
                        <input type="text" name="kota" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Kabupaten</label>
                        <input type="text" name="kabupaten" class="form-control">
                      </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Telp</label>
                        <input type="text" name="telp" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Logo</label>
                        <input type="file" name="logo" class="form-control-file" accept="image/*">
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Simpan</button>
                </form>
              </div>
            </div>

            <!-- Tabel Data -->
            <div class="card mt-4">
              <div class="card-header">
                <h4>Daftar Instansi</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                      <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Telp</th>
                        <th>Email</th>
                        <th>Logo</th>
                        <th>Waktu Input</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                          <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_instansi']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td><?= htmlspecialchars($row['kota']); ?></td>
                            <td><?= htmlspecialchars($row['kabupaten']); ?></td>
                            <td><?= htmlspecialchars($row['provinsi']); ?></td>
                            <td><?= htmlspecialchars($row['telp']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td>
                              <?php if (!empty($row['logo'])): ?>
                                <img src="uploads/logo/<?= htmlspecialchars($row['logo']); ?>" alt="Logo" width="40">
                              <?php else: ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="10" class="text-center text-muted">Belum ada data instansi.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- End Table -->
          </div>
        </section>
      </div>

      <?php include 'footer.php'; ?>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>

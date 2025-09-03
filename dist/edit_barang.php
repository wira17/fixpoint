<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: data_barang_it.php");
  exit;
}

$result = mysqli_query($conn, "SELECT * FROM data_barang_it WHERE id = '$id'");
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
  $_SESSION['flash_message'] = "❌ Data tidak ditemukan.";
  header("Location: data_barang_it.php");
  exit;
}

$lokasi_query = mysqli_query($conn, "SELECT nama_unit FROM unit_kerja ORDER BY nama_unit ASC");

if (isset($_POST['update'])) {
  $no_barang    = mysqli_real_escape_string($conn, $_POST['no_barang']);
  $nama_barang  = mysqli_real_escape_string($conn, $_POST['nama_barang']);
  $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
  $merk         = mysqli_real_escape_string($conn, $_POST['merk']);
  $spesifikasi  = mysqli_real_escape_string($conn, $_POST['spesifikasi']);
  $ip_address   = mysqli_real_escape_string($conn, $_POST['ip_address']);
  $lokasi       = mysqli_real_escape_string($conn, $_POST['lokasi']);
  $kondisi      = mysqli_real_escape_string($conn, $_POST['kondisi']);

  $query = "UPDATE data_barang_it SET 
    no_barang = '$no_barang',
    nama_barang = '$nama_barang',
    kategori = '$kategori',
    merk = '$merk',
    spesifikasi = '$spesifikasi',
    ip_address = '$ip_address',
    lokasi = '$lokasi',
    kondisi = '$kondisi'
    WHERE id = '$id'";

  if (mysqli_query($conn, $query)) {
    $_SESSION['flash_message'] = "✅ Data barang berhasil diperbarui.";
    echo "<script>location.href='data_barang_it.php';</script>";
    exit;
  } else {
    $error_message = mysqli_error($conn);
    $_SESSION['flash_message'] = "❌ Gagal update data: $error_message";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <div class="section-body">
          <div class="card">
            <div class="card-header">
              <h4>Edit Data Barang IT</h4>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label>No. Barang</label>
                    <input type="text" name="no_barang" class="form-control" value="<?= htmlspecialchars($barang['no_barang']) ?>" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control">
                      <option value="">-- Pilih Kategori --</option>
                      <option value="Printer" <?= $barang['kategori'] == 'Printer' ? 'selected' : '' ?>>Printer</option>
                      <option value="Komputer" <?= $barang['kategori'] == 'Komputer' ? 'selected' : '' ?>>Komputer</option>
                      <option value="Aset IT" <?= $barang['kategori'] == 'Aset IT' ? 'selected' : '' ?>>Aset IT</option>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" value="<?= htmlspecialchars($barang['merk']) ?>">
                  </div>
                  <div class="form-group col-md-4">
                    <label>Spesifikasi</label>
                    <input type="text" name="spesifikasi" class="form-control" value="<?= htmlspecialchars($barang['spesifikasi']) ?>">
                  </div>
                  <div class="form-group col-md-4">
                    <label>IP Address</label>
                    <input type="text" name="ip_address" class="form-control" value="<?= htmlspecialchars($barang['ip_address']) ?>">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label>Lokasi</label>
                    <select name="lokasi" class="form-control" required>
                      <option value="">-- Pilih Lokasi --</option>
                      <?php while ($row = mysqli_fetch_assoc($lokasi_query)): ?>
                        <option value="<?= htmlspecialchars($row['nama_unit']) ?>" <?= $barang['lokasi'] == $row['nama_unit'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($row['nama_unit']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Kondisi</label>
                    <select name="kondisi" class="form-control" required>
                      <option value="">-- Pilih Kondisi --</option>
                      <option value="Baik" <?= $barang['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                      <option value="Rusak Ringan" <?= $barang['kondisi'] == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                      <option value="Rusak Berat" <?= $barang['kondisi'] == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                  </div>
                </div>

                <button type="submit" name="update" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                <a href="data_barang_it.php" class="btn btn-secondary">Batal</a>
              </form>
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
</body>
</html>
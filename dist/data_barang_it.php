<?php
include 'security.php'; // sudah handle session_start + cek login + timeout
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

// ✅ Proses Simpan
if (isset($_POST['simpan'])) {
  $no_barang    = mysqli_real_escape_string($conn, $_POST['no_barang']);
  $nama_barang  = mysqli_real_escape_string($conn, $_POST['nama_barang']);
  $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
  $merk         = mysqli_real_escape_string($conn, $_POST['merk']);
  $spesifikasi  = mysqli_real_escape_string($conn, $_POST['spesifikasi']);
  $ip_address   = mysqli_real_escape_string($conn, $_POST['ip_address']);
  $lokasi       = mysqli_real_escape_string($conn, $_POST['lokasi']);
  $kondisi      = mysqli_real_escape_string($conn, $_POST['kondisi']);
  $tgl_input    = date('Y-m-d H:i:s');

  $query = "INSERT INTO data_barang_it (
    user_id, no_barang, nama_barang, kategori, merk, spesifikasi, ip_address, lokasi, kondisi
  ) VALUES (
    '$user_id', '$no_barang', '$nama_barang', '$kategori', '$merk', '$spesifikasi', '$ip_address', '$lokasi', '$kondisi'
  )";

  if (mysqli_query($conn, $query)) {
    $_SESSION['flash_message'] = "✅ Data barang berhasil disimpan.";
    echo "<script>location.href='data_barang_it.php';</script>";
    exit;
  } else {
    $error_message = mysqli_error($conn);
    $_SESSION['flash_message'] = "❌ Gagal menyimpan data: $error_message";
  }
}

// ✅ Ambil lokasi
$lokasi_query = mysqli_query($conn, "SELECT nama_unit FROM unit_kerja ORDER BY nama_unit ASC");

// ✅ Ambil data barang
$data_barang = mysqli_query($conn, "SELECT * FROM data_barang_it ORDER BY waktu_input DESC");

// ✅ Rekap jumlah per kategori
$rekap_kategori = mysqli_query($conn, "
  SELECT kategori, COUNT(*) AS jumlah 
  FROM data_barang_it 
  GROUP BY kategori
");

// ✅ Rekap jumlah per kondisi
$rekap_kondisi = mysqli_query($conn, "
  SELECT kondisi, COUNT(*) AS jumlah 
  FROM data_barang_it 
  GROUP BY kondisi
");
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
  
<style>
  #notif-toast {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    display: none;
    min-width: 300px;
  }

  .btn-icon-white {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.1rem;
  }

  .modal-lg-custom {
    max-width: 90%;
  }

  .modal-body .btn-outline-secondary:hover {
    background-color: #343a40;
    color: #fff;
    border-color: #343a40;
  }

  /* Biar semua isi kolom tidak pindah ke baris bawah */
  .table-nowrap td,
  .table-nowrap th {
    white-space: nowrap;
  }

      .table thead th {
  background-color: #000 !important;
  color: #fff !important;
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
            <div id="notif-toast" class="alert alert-info text-center">
              <?= $_SESSION['flash_message'] ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
          <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <h4>Manajemen Data Barang IT</h4>
            </div>
            <div class="card-body">
              <!-- Nav tabs -->
           <ul class="nav nav-tabs" id="dataTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Barang</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Barang</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="laporan-tab" data-toggle="tab" href="#laporan" role="tab">Laporan</a>
  </li>
</ul>


              <!-- Tab panes -->
              <div class="tab-content mt-3">
                <!-- Input Barang -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="POST">
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label>No. Barang</label>
                        <input type="text" name="no_barang" class="form-control" required>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                          <option value="">-- Pilih Kategori --</option>
                          <option value="Printer">Printer</option>
                          <option value="Komputer">Komputer</option>
                          <option value="Aset IT">Aset IT</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label>Merk</label>
                        <input type="text" name="merk" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>Spesifikasi</label>
                        <input type="text" name="spesifikasi" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" class="form-control">
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label>Lokasi</label>
                        <select name="lokasi" class="form-control" required>
                          <option value="">-- Pilih Lokasi --</option>
                          <?php while ($row = mysqli_fetch_assoc($lokasi_query)): ?>
                            <option value="<?= htmlspecialchars($row['nama_unit']) ?>"><?= htmlspecialchars($row['nama_unit']) ?></option>
                          <?php endwhile; ?>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-control" required>
                          <option value="">-- Pilih Kondisi --</option>
                          <option value="Baik">Baik</option>
                          <option value="Rusak Ringan">Rusak Ringan</option>
                          <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                      </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

                <!-- Data Barang -->
                <div class="tab-pane fade" id="data" role="tabpanel">
                  <a href="cetak_barang_it.php" target="_blank" class="btn btn-secondary mb-3">
  <i class="fas fa-print"></i> Cetak Data
</a>

                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-nowrap">

                  <thead class="thead-dark">
                          <tr>
                            <th>No</th>
                            <th>No. Barang</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Spesifikasi</th>
                            <th>IP</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Tanggal Input</th>
                            <th>Aksi</th> <!-- Tambahkan ini -->
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        while ($barang = mysqli_fetch_assoc($data_barang)) :
                        ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($barang['no_barang']) ?></td>
                            <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($barang['kategori']) ?></td>
                            <td><?= htmlspecialchars($barang['merk']) ?></td>
                            <td><?= htmlspecialchars($barang['spesifikasi']) ?></td>
                            <td><?= htmlspecialchars($barang['ip_address']) ?></td>
                            <td><?= htmlspecialchars($barang['lokasi']) ?></td>
                            <td><?= htmlspecialchars($barang['kondisi']) ?></td>
                           <td><?= date('d-m-Y H:i', strtotime($barang['waktu_input'])) ?></td>
                           <td>
                            <a href="edit_barang.php?id=<?= $barang['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="hapus_barang.php?id=<?= $barang['id'] ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash-alt"></i></a>
                          </td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>



                <!-- Tab Laporan -->
<div class="tab-pane fade" id="laporan" role="tabpanel">
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Rekap per Kategori</h5>
        </div>
        <div class="card-body">
          <ul class="list-group">
            <?php while ($kat = mysqli_fetch_assoc($rekap_kategori)) : ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($kat['kategori']) ?>
                <span class="badge badge-primary badge-pill"><?= $kat['jumlah'] ?></span>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0">Rekap per Kondisi</h5>
        </div>
        <div class="card-body">
          <ul class="list-group">
            <?php while ($kon = mysqli_fetch_assoc($rekap_kondisi)) : ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($kon['kondisi']) ?>
                <span class="badge badge-success badge-pill"><?= $kon['jumlah'] ?></span>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>


                    </table>
                  </div>
                </div>
              </div> <!-- End tab-content -->
            </div> <!-- End card-body -->
          </div> <!-- End card -->
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
    var toast = $('#notif-toast');
    if (toast.length) {
      toast.fadeIn(300).delay(2000).fadeOut(500);
    }

    // Otomatis aktifkan tab "Data Barang" jika ada notifikasi sukses
    <?php if (isset($_SESSION['flash_message'])): ?>
      $('#data-tab').tab('show');
    <?php endif; ?>
  });
</script>

</body>
</html>

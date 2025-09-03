<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');




// Simpan kategori
if (isset($_POST['simpan'])) {
  $nama_kategori = trim($_POST['nama_kategori']);
  if (!empty($nama_kategori)) {
    mysqli_query($conn, "INSERT INTO kategori_arsip (nama_kategori) VALUES ('$nama_kategori')");
    header("Location: kategori_arsip.php");
    exit;
  }
}

// Hapus kategori
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  mysqli_query($conn, "DELETE FROM kategori_arsip WHERE id = $id");
  header("Location: kategori_arsip.php");
  exit;
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_data = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kategori_arsip"));
$total_pages = ceil($total_data / $limit);

// Ambil data kategori sesuai halaman
$data_kategori = mysqli_query($conn, "SELECT * FROM kategori_arsip ORDER BY nama_kategori ASC LIMIT $limit OFFSET $offset");
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

        <div class="section-body">

          <!-- Form Input -->
          <div class="card">
            <div class="card-header">
              <h4>Tambah Kategori Arsip</h4>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-group">
                  <label>Nama Kategori</label>
                  <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Surat Keluar, Laporan Tahunan, dll" required>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">
                  <i class="fas fa-save"></i> Simpan
                </button>
              </form>
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="card">
            <div class="card-header">
              <h4>Daftar Kategori Arsip</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark">
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Nama Kategori</th>
                      <th style="width: 100px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = $offset + 1;
                    while ($row = mysqli_fetch_assoc($data_kategori)) :
                    ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td>
                          <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($data_kategori) == 0): ?>
                      <tr>
                        <td colspan="3" class="text-center text-muted">Belum ada data.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <nav>
                <ul class="pagination justify-content-center">
                  <?php if ($page > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                    </li>
                  <?php endif; ?>

                  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>

            </div>
          </div>

        </div> <!-- end section-body -->
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

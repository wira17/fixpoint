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

$keyword    = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$tgl_dari   = isset($_GET['tgl_dari']) ? $_GET['tgl_dari'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" />
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
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  table.table {
    min-width: 1200px;
  }

  /* Modal z-index fix */
  .modal-backdrop {
    z-index: 1040;
  }

  .modal {
    z-index: 1050;
  }

  .table td, .table th {
  white-space: nowrap;
  vertical-align: middle;
}

.table td.kategori-col,
.table td.kendala-col,
.table td.perangkat-col {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

#notif-toast {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999;
  display: none;
  min-width: 300px;
  padding: 20px;
  font-size: 16px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  border-radius: 10px;
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
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-desktop me-2 text-primary"></i>  Data Tiket IT Hardware</h4>

              </div>
             
              <div class="card-body">
                <form method="GET" class="form-inline mb-3">
                  <div class="row w-100 align-items-end">
                    <div class="col-md-3">
                      <input type="text" name="keyword" class="form-control w-100" placeholder="Cari nama/kategori/kendala" value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <div class="col-md-2">
                      <input type="date" name="tgl_dari" class="form-control w-100" value="<?= $tgl_dari ?>">
                    </div>
                    <div class="col-md-2">
                      <input type="date" name="tgl_sampai" class="form-control w-100" value="<?= $tgl_sampai ?>">
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                    <div class="col-md-2">
                      <a href="data_tiket_it_hardware.php" class="btn btn-secondary w-100">Reset</a>
                    </div>
                  </div>
                </form>

                <?php
                $limit = 6;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;
                $whereClauses = [];

                if (!empty($tgl_dari) && !empty($tgl_sampai)) {
                  $whereClauses[] = "DATE(t.tanggal_input) BETWEEN '$tgl_dari' AND '$tgl_sampai'";
                } else {
                  $whereClauses[] = "DATE(t.tanggal_input) = CURDATE()";
                }

                if (!empty($keyword)) {
                  $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
                  $whereClauses[] = "(u.nama LIKE '%$keywordEscaped%' OR t.kategori LIKE '%$keywordEscaped%' OR t.kendala LIKE '%$keywordEscaped%')";
                }

                $where = "WHERE " . implode(" AND ", $whereClauses);

                $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tiket_it_hardware t JOIN users u ON t.user_id = u.id $where");
                $totalRows = mysqli_fetch_assoc($totalQuery)['total'];
                $totalPages = ceil($totalRows / $limit);

             $query = "SELECT t.*, u.nik, u.nama, u.jabatan, u.unit_kerja 
              FROM tiket_it_hardware t 
              JOIN users u ON t.user_id = u.id 
              $where 
              ORDER BY t.tanggal_input DESC 
              LIMIT $offset, $limit";


                $result = mysqli_query($conn, $query);
                $no = $offset + 1;
                $modals = [];
                ?>

                <div class="table-responsive">
                  <table class="table table-bordered table-sm table-hover">
                 <thead class="thead-dark text-center">
  <tr>
    <th>No</th>
    <th>Nomor Tiket</th>
    <th>Tanggal</th>
    <th>NIK</th>
    <th>Nama Order</th>
    <th>Teknisi</th>
    <th>Jabatan</th>
    <th>Unit Kerja</th>
    <th>Kategori</th>
    <th>Kendala</th>
    <th>Status</th>
    <th>Validasi</th> <!-- ✅ Tambahkan ini -->
    <th>Aksi</th>
    <th>Terbit BA</th>

  </tr>
</thead>

                  <tbody>
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php 
        $tgl = date('d-m-Y H:i', strtotime($row['tanggal_input'])); 
        ob_start();
      ?>
    <tr>
  <td class="text-center"><?= $no++; ?></td>
  <td><strong><?= $row['nomor_tiket']; ?></strong></td>
  <td><?= $tgl; ?></td>
  <td><?= $row['nik']; ?></td>
  <td><?= $row['nama']; ?></td>
  <td><?= htmlspecialchars($row['teknisi_nama'] ?? '-'); ?></td>
  <td><?= $row['jabatan']; ?></td>
  <td><?= $row['unit_kerja']; ?></td>
  <td><?= $row['kategori']; ?></td>
  <td><?= $row['kendala']; ?></td>
  <td><strong><?= ucwords($row['status']); ?></strong></td>
  <td class="text-center">
    <?= !empty($row['waktu_validasi']) ? '<span class="badge badge-success">Sudah</span>' : '<span class="badge badge-warning">Belum</span>'; ?>
  </td>
  <td class="text-center">
    <a href="berita_acara.php?tiket_id=<?= $row['id']; ?>" target="_blank" class="btn btn-sm btn-success">
      <i class="fas fa-file-alt"></i> BA
    </a>
  </td>
  <td class="text-center">
    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalStatus<?= $row['id']; ?>">
      <i class="fas fa-edit"></i> Ubah Status
    </button>
  </td>
</tr>




      <?php
        $modals[] = '
        <div class="modal fade" id="modalStatus'.$row['id'].'" tabindex="-1" role="dialog" aria-labelledby="modalStatusLabel'.$row['id'].'" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <form action="ubah_status_it_hardware.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalStatusLabel'.$row['id'].'">Ubah Status Tiket</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <div class="modal-body">
  <input type="hidden" name="tiket_id" value="'.$row['id'].'">
  <input type="hidden" name="teknisi" value="'.$_SESSION['nama'].'">
  
  <div class="form-group">
    <label for="statusSelect'.$row['id'].'">Status Baru:</label>
    <select class="form-control" id="statusSelect'.$row['id'].'" name="status" required>
      <option value="">-- Pilih Status --</option>
      <option value="Menunggu" '.($row['status'] === 'Menunggu' ? 'selected' : '').'>Menunggu</option>
      <option value="Diproses" '.($row['status'] === 'Diproses' ? 'selected' : '').'>Diproses</option>
      <option value="Selesai" '.($row['status'] === 'Selesai' ? 'selected' : '').'>Selesai</option>
      <option value="Tidak Bisa Diperbaiki" '.($row['status'] === 'Tidak Bisa Diperbaiki' ? 'selected' : '').'>Tidak Bisa Diperbaiki</option>
      <option value="Ditolak" '.($row['status'] === 'Ditolak' ? 'selected' : '').'>Ditolak</option>
    </select>
  </div>

  <div class="form-group">
    <label for="catatanIt'.$row['id'].'">Catatan IT:</label>
    <textarea class="form-control" id="catatanIt'.$row['id'].'" name="catatan_it" rows="3" placeholder="Masukkan catatan atau detail tambahan..."></textarea>
  </div>
</div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                  <button type="submit" name="ubah_status" class="btn btn-primary btn-sm">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>';
      ?>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="13" class="text-center">Tidak ada data ditemukan.</td></tr>
  <?php endif; ?>
</tbody>


                    <?php if (isset($_GET['notif']) && $_GET['notif'] == 'berhasil'): ?>
  <div id="notif-toast" class="alert alert-success text-center">
    <i class="fas fa-check-circle fa-2x mb-1"></i><br>
    Status berhasil diperbarui.
  </div>
<?php endif; ?>

                  </table>
                </div>

                

                <?php if ($totalPages > 1): ?>
                  <nav>
                    <ul class="pagination justify-content-center mt-3">
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                          <a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>&tgl_dari=<?= $tgl_dari; ?>&tgl_sampai=<?= $tgl_sampai; ?>"><?= $i; ?></a>
                        </li>
                      <?php endfor; ?>
                    </ul>
                  </nav>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
<!-- SCRIPTS -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>

<?php foreach ($modals as $modal) echo $modal; ?> <!-- 🟢 MODAL DIPINDAH KE SINI -->



<script>
  $(document).ready(function () {
    var toast = $('#notif-toast');
    if (toast.length) {
      toast.fadeIn(300).delay(2500).fadeOut(500);
    }
  });
</script>

</body>
</html>
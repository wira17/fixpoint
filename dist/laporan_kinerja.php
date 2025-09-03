<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    table th, table td {
      white-space: nowrap;
    }
    .form-inline-custom {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
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
          <div class="section-header">
            <h1>Laporan Kinerja Semua Pengguna</h1>
          </div>

          <div class="section-body">
          <div class="card-header">
  <div class="row w-100 align-items-center">
    <div class="col-md-4">
      <h4 class="mb-0">Data Kinerja</h4>
    </div>
    <div class="col-md-8">
      <form method="GET" class="form-inline justify-content-end">
        <input type="text" name="cari" class="form-control mr-2" placeholder="Cari nama..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit" class="btn btn-info mr-2">
          <i class="fas fa-search"></i> Cari
        </button>
        <a href="laporan_kinerja_print.php<?= isset($_GET['cari']) ? '?cari=' . urlencode($_GET['cari']) : ''; ?>" class="btn btn-success">
          <i class="fas fa-print"></i> Cetak
        </a>
      </form>
    </div>
  </div>
</div>


              <div class="card-body table-responsive-custom">
                <?php
                $cari = isset($_GET['cari']) ? $_GET['cari'] : '';

                $query = "
                  SELECT k.*, u.nama,
                    (SELECT AVG(rating) FROM feedback_kinerja f WHERE f.kinerja_id = k.id) AS avg_rating,
                    (SELECT komentar FROM feedback_kinerja f WHERE f.kinerja_id = k.id ORDER BY tanggal_feedback DESC LIMIT 1) AS last_comment
                  FROM kinerja_petugas k
                  LEFT JOIN users u ON k.user_input = u.id
                ";

                if (!empty($cari)) {
                  $safe_cari = $conn->real_escape_string($cari);
                  $query .= " WHERE u.nama LIKE '%$safe_cari%'";
                }

                $query .= " ORDER BY k.tanggal DESC, u.nama ASC";
                $result = $conn->query($query);
                ?>

                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark text-center">
                    <tr>
                      <th>No</th>
                      <th>Nama Pengguna</th>
                      <th>Tanggal</th>
                      <th>Kegiatan</th>
                      <th>Progress</th>
                      <th>Catatan</th>
                      <th>Rating Rata-rata</th>
                      <th>Feedback Kinerja</th>
                      <th>Bukti</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($result->num_rows > 0): ?>
                      <?php $no = 1; ?>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= $no++; ?></td>
                          <td><?= htmlspecialchars($row['nama']); ?></td>
                          <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                          <td><?= htmlspecialchars($row['kegiatan']); ?></td>
                          <td><span class="badge badge-info"><?= htmlspecialchars($row['progres']); ?></span></td>
                          <td><?= htmlspecialchars($row['catatan']); ?></td>
                          <td>
                            <?php
                              $avg = round($row['avg_rating'], 1);
                              if ($avg > 0) {
                                if ($avg >= 4.5) $ket = "Sangat Baik";
                                elseif ($avg >= 3.5) $ket = "Baik";
                                elseif ($avg >= 2.5) $ket = "Cukup";
                                elseif ($avg >= 1.5) $ket = "Kurang";
                                else $ket = "Sangat Kurang";
                                echo "$avg / 5 ($ket)";
                              } else {
                                echo "-";
                              }
                            ?>
                          </td>
                          <td><?= htmlspecialchars($row['last_comment'] ?? '-'); ?></td>
                          <td>
                            <?php if (!empty($row['bukti'])): ?>
                              <a href="uploads/<?= htmlspecialchars($row['bukti']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-file"></i> Lihat
                              </a>
                            <?php else: ?>
                              <span class="text-muted">-</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-center text-muted">Belum ada data kinerja.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div> <!-- .card-body -->
            </div> <!-- .card -->
          </div>
        </section>
      </div>

      <?php include 'footer.php'; ?>
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

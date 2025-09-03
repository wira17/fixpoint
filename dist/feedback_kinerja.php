<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Query ambil data kinerja + nama user + rating avg + komentar terakhir
$query = "
  SELECT k.*, u.nama AS nama_user,
    (SELECT AVG(rating) FROM feedback_kinerja f WHERE f.kinerja_id = k.id) AS avg_rating,
    (SELECT komentar FROM feedback_kinerja f WHERE f.kinerja_id = k.id ORDER BY tanggal_feedback DESC LIMIT 1) AS last_comment
  FROM kinerja_petugas k
  JOIN users u ON k.user_input = u.id
  ORDER BY k.tanggal DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
    .table-wrapper-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    table.table th, table.table td {
      white-space: nowrap;
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
            <h1>Feedback dan Penilaian Kinerja</h1>
          </div>

          <div class="section-body">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Kinerja</h4>
              </div>
              <div class="card-body">
                <div class="table-wrapper-scroll">
                  <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                      <tr>
                        <th>No</th>
                        <th>Nama Personel</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Progress</th>
                        <th>Rating Rata-rata</th>
                        <th>Komentar Terakhir</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                          <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_user']); ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['kegiatan']); ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($row['progres']); ?></span></td>
                            <td>
                              <?php
                                $avg = round($row['avg_rating'], 1);
                                if ($avg > 0) {
                                  if ($avg >= 4.5) $label = "Sangat Baik";
                                  elseif ($avg >= 3.5) $label = "Baik";
                                  elseif ($avg >= 2.5) $label = "Cukup";
                                  elseif ($avg >= 1.5) $label = "Kurang";
                                  else $label = "Sangat Kurang";
                                  echo "$avg / 5 ($label)";
                                } else {
                                  echo "-";
                                }
                              ?>
                            </td>
                            <td><?= htmlspecialchars($row['last_comment'] ?? '-'); ?></td>
                            <td>
                              <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalFeedback" 
                                data-kinerja-id="<?= $row['id']; ?>" 
                                data-kegiatan="<?= htmlspecialchars($row['kegiatan']); ?>">
                                <i class="fas fa-comment-dots"></i> Beri Feedback
                              </button>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="8" class="text-center text-muted">Tidak ada data kinerja.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div> <!-- end scroll wrapper -->
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include 'footer.php'; ?>
    </div>
  </div>

  <!-- Modal Feedback -->
  <div class="modal fade" id="modalFeedback" tabindex="-1" role="dialog" aria-labelledby="modalFeedbackLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="proses_feedback.php" method="POST">
        <input type="hidden" name="mentor_id" value="<?= $_SESSION['user_id']; ?>">
        <input type="hidden" name="kinerja_id" id="kinerja_id" value="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Berikan Feedback</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><strong>Kegiatan:</strong> <span id="modal_kegiatan"></span></p>
            <div class="form-group">
              <label for="rating">Rating (1 - 5)</label>
              <select name="rating" id="rating" class="form-control" required>
                <option value="">-- Pilih Rating --</option>
                <option value="1">1 - Sangat Kurang</option>
                <option value="2">2 - Kurang</option>
                <option value="3">3 - Cukup</option>
                <option value="4">4 - Baik</option>
                <option value="5">5 - Sangat Baik</option>
              </select>
            </div>
            <div class="form-group">
              <label for="komentar">Komentar (opsional)</label>
              <textarea name="komentar" id="komentar" class="form-control" rows="3" placeholder="Masukkan komentar..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Kirim Feedback</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
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
    $('#modalFeedback').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var kinerjaId = button.data('kinerja-id');
      var kegiatan = button.data('kegiatan');

      var modal = $(this);
      modal.find('#kinerja_id').val(kinerjaId);
      modal.find('#modal_kegiatan').text(kegiatan);
      modal.find('#rating').val('');
      modal.find('#komentar').val('');
    });
  </script>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

$judul_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT k.soal, k.jawaban AS jawaban_benar, j.jawaban
        FROM kuis k
        JOIN jawaban_kuis j ON k.id = j.soal_id
        WHERE j.user_id = ? AND j.judul_id = ?";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $judul_id);
$stmt->execute();
$result = $stmt->get_result();

$total = $benar = 0;
$detail = [];

while ($row = $result->fetch_assoc()) {
  $total++;
  $jawaban = strtoupper($row['jawaban']);
  $kunci = strtoupper($row['jawaban_benar']);
  if ($jawaban === $kunci) {
    $benar++;
  }
  $detail[] = $row;
}

$nilai = $total > 0 ? round(($benar / $total) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
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
            <h1>Hasil Kuis</h1>
          </div>

          <div class="section-body">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> Rekap Jawaban Kuis</h4>
              </div>
              <div class="card-body">
                <p><strong>Total Soal:</strong> <?= $total ?></p>
                <p><strong>Jawaban Benar:</strong> <?= $benar ?></p>
                <p><strong>Nilai Anda:</strong> <span class="badge badge-info p-2"><?= $nilai ?></span></p>

                <div class="table-responsive mt-4">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                      <tr>
                        <th>No</th>
                        <th>Soal</th>
                        <th>Kunci</th>
                        <th>Jawaban Anda</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($detail as $i => $row): ?>
                        <tr>
                          <td><?= $i + 1 ?></td>
                          <td><?= htmlspecialchars($row['soal']) ?></td>
                          <td><strong><?= $row['jawaban_benar'] ?></strong></td>
                          <td><?= $row['jawaban'] ?></td>
                          <td>
                            <?php if (strtoupper($row['jawaban']) === strtoupper($row['jawaban_benar'])): ?>
                              <span class="badge badge-success">Benar</span>
                            <?php else: ?>
                              <span class="badge badge-danger">Salah</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="text-right mt-4">
                  <a href="judul_kuis.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
              </div>
            </div>
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
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
      icon: 'success',
      title: 'Jawaban Anda telah disimpan!',
      html: '<strong>Nilai Anda: <?= $nilai ?></strong>',
      showConfirmButton: false,
      timer: 3000
    });
  });
</script>

</body>
</html>

<?php
include 'security.php'; 
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


$user_id = $_SESSION['user_id'];
$queryUser = mysqli_query($conn, "SELECT nama FROM users WHERE id = '$user_id'");
$userData = mysqli_fetch_assoc($queryUser);
$user_nama = $userData['nama'] ?? 'unknown';

$notif = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  $nomor_spo = mysqli_real_escape_string($conn, trim($_POST['nomor_spo']));
  $judul = mysqli_real_escape_string($conn, trim($_POST['judul']));
  $petugas = mysqli_real_escape_string($conn, $user_nama);

  if (empty($nomor_spo) || empty($judul)) {
    $notif = "Nomor SPO dan Judul wajib diisi.";
  } else {
    $cek = mysqli_query($conn, "SELECT id FROM spo_it WHERE nomor_spo = '$nomor_spo'");
    if (mysqli_num_rows($cek) > 0) {
      $notif = "Nomor SPO sudah ada. Gunakan nomor lain.";
    } else {
      $file = $_FILES['file_spo'];
      $namaFile = $file['name'];
      $tmpName = $file['tmp_name'];
      $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
      $allowed = ['pdf', 'png'];

      if (in_array($ext, $allowed)) {
        $newName = uniqid('spo_', true) . '.' . $ext;
        $uploadPath = 'uploads/spo/' . $newName;

        if (!is_dir('uploads/spo')) {
          mkdir('uploads/spo', 0755, true);
        }

        if (move_uploaded_file($tmpName, $uploadPath)) {
          $insert = mysqli_query($conn, "INSERT INTO spo_it (nomor_spo, judul, file_spo, petugas_upload, tanggal_upload) VALUES ('$nomor_spo', '$judul', '$newName', '$petugas', NOW())");

          if ($insert) {
            $_SESSION['flash_message'] = "SPO berhasil disimpan.";
            header("Location: input_spo_it.php");
            exit;
          } else {
            $notif = "Gagal menyimpan ke database.";
          }
        } else {
          $notif = "Gagal mengunggah file.";
        }
      } else {
        $notif = "Format file tidak valid. Hanya PDF dan PNG yang diperbolehkan.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
    <style>

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
          <div class="card">
            <div class="card-header">
              <h4>Manajemen SPO IT</h4>
            </div>
            <div class="card-body">

              <ul class="nav nav-tabs" id="spoTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input SPO</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data SPO</a>
                </li>
              </ul>

              <div class="tab-content mt-4" id="spoTabContent">
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <?php if ($notif): ?>
                    <div class="alert alert-danger"> <?= $notif ?> </div>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['flash_message'])): ?>
                    <div id="notif-toast" class="alert alert-success text-center">
                      <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                    </div>
                  <?php endif; ?>

                  <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                      <label>Nomor SPO</label>
                      <input type="text" name="nomor_spo" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Petugas Upload</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($user_nama); ?>" readonly>
                    </div>

                    <div class="form-group">
                      <label>Judul</label>
                      <input type="text" name="judul" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Upload File (PDF / PNG)</label>
                      <input type="file" name="file_spo" class="form-control-file" accept=".pdf,.png" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                  </form>
                </div>

                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nomor SPO</th>
                          <th>Judul</th>
                          <th>File</th>
                          <th>Petugas Upload</th>
                          <th>Tanggal Upload</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        $spoData = mysqli_query($conn, "SELECT * FROM spo_it ORDER BY tanggal_upload DESC");
                        while ($row = mysqli_fetch_assoc($spoData)) {
                          echo "<tr>
                                  <td>{$no}</td>
                                  <td>{$row['nomor_spo']}</td>
                                  <td>{$row['judul']}</td>
                                  <td><a href='uploads/spo/{$row['file_spo']}' target='_blank'>Lihat File</a></td>
                                  <td>{$row['petugas_upload']}</td>
                                  <td>" . date('d-m-Y H:i', strtotime($row['tanggal_upload'])) . "</td>
                                </tr>";
                          $no++;
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
  $(document).ready(function () {
    $('#notif-toast').fadeIn(300).delay(2000).fadeOut(500);
  });
</script>
</body>
</html>

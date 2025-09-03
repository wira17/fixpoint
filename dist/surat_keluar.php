<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename($_SERVER['PHP_SELF']);

// ✅ Cek hak akses pengguna untuk halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";

$cek = mysqli_query($conn, $query);

// ✅ Jika tidak punya akses, tampilkan notifikasi profesional dan redirect
if (!$cek || mysqli_num_rows($cek) === 0) {
  echo '
  <html>
  <head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      body {
        font-family: "Poppins", sans-serif;
        background: linear-gradient(to right, #ffecd2, #fcb69f);
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }
    </style>
  </head>
  <body>
    <script>
      Swal.fire({
        icon: "error",
        title: "Akses Ditolak!",
        html: "<b>Oops...</b> Anda tidak memiliki izin untuk membuka halaman ini.",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "<i class=\'fa fa-home\'></i> Kembali ke FixPoint",
        background: "#fff url(\'https://www.transparenttextures.com/patterns/paper-fibers.png\')",
        customClass: {
          popup: "animated fadeInDown"
        }
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = "dashboard.php";
        }
      });
    </script>
  </body>
  </html>
  ';
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data surat masuk yang perlu dibalas
$surat_masuk = mysqli_query($conn, "SELECT id, no_surat, perihal FROM surat_masuk WHERE perlu_balasan = 'Ya' AND status_balasan = 'Belum Dibalas'");

// Simpan surat keluar
if (isset($_POST['simpan'])) {
  $no_surat     = $_POST['no_surat'];
  $tgl_surat    = $_POST['tgl_surat'];
  $tujuan       = $_POST['tujuan'];
  $perihal      = $_POST['perihal'];
  $isi_ringkas  = $_POST['isi'];

  $surat_masuk_id = $_POST['surat_masuk_id'] ?: null;
  $file_surat   = '';

  if ($_FILES['file_surat']['name']) {
    $ext = pathinfo($_FILES['file_surat']['name'], PATHINFO_EXTENSION);
    $file_surat = 'keluar_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['file_surat']['tmp_name'], 'uploads/' . $file_surat);
  }

mysqli_query($conn, "INSERT INTO surat_keluar (
  no_surat, tgl_surat, tgl_kirim, tujuan, perihal, lampiran, isi_ringkas, file_surat, balasan_untuk_id, user_input
) VALUES (
  '$no_surat', '$tgl_surat', NOW(), '$tujuan', '$perihal', '', '$isi_ringkas', '$file_surat', " . ($surat_masuk_id ?: "NULL") . ", '$user_id'
)");


  $id_keluar = mysqli_insert_id($conn);

  if ($surat_masuk_id) {
    mysqli_query($conn, "UPDATE surat_masuk SET status_balasan = 'Sudah Dibalas', balasan_surat_id = '$id_keluar' WHERE id = '$surat_masuk_id'");
  }

  header("Location: surat_keluar.php");
  exit;
}

$data_surat = mysqli_query($conn, "SELECT sk.*, sm.no_surat AS no_masuk, u.nama AS user_nama 
  FROM surat_keluar sk 
  LEFT JOIN surat_masuk sm ON sk.balasan_untuk_id = sm.id 
  LEFT JOIN users u ON sk.user_input = u.id 
  ORDER BY sk.id DESC");

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
  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    .table-surat {
      white-space: nowrap;
      min-width: 1500px;
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
              <h4>Manajemen Surat Keluar</h4>
            </div>
            <div class="card-body">

              <ul class="nav nav-tabs" id="keluarTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab">Input Surat Keluar</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Surat Keluar</a>
                </li>
              </ul>

              <div class="tab-content mt-3" id="keluarTabContent">
                <div class="tab-pane fade show active" id="form" role="tabpanel">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group"><label>No Surat</label><input name="no_surat" class="form-control" required></div>
                        <div class="form-group"><label>Tanggal Surat</label><input type="date" name="tgl_surat" class="form-control" required></div>
                        <div class="form-group"><label>Tujuan</label><input name="tujuan" class="form-control" required></div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group"><label>Perihal</label><input name="perihal" class="form-control" required></div>
                        <div class="form-group"><label>Isi</label><textarea name="isi" class="form-control" rows="3" required></textarea></div>
                        <div class="form-group"><label>File Surat (PDF)</label><input type="file" name="file_surat" accept=".pdf" class="form-control"></div>
                      </div>
                    </div>

                    <div class="form-group"><label>Balasan untuk Surat Masuk</label>
                      <select name="surat_masuk_id" class="form-control">
                        <option value="">-- Bukan Balasan --</option>
                        <?php while ($sm = mysqli_fetch_assoc($surat_masuk)) : ?>
                          <option value="<?= $sm['id'] ?>">[<?= $sm['no_surat'] ?>] <?= $sm['perihal'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

                <div class="tab-pane fade" id="data" role="tabpanel">
                  <div class="table-responsive-custom">
                    <table class="table table-bordered table-striped table-surat">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>No Surat</th>
                          <th>Tanggal</th>
                          <th>Tujuan</th>
                          <th>Perihal</th>
                          <th>Balasan Untuk</th>
                          <th>Lihat Surat Masuk</th>

                          <th>File</th>
                          <th>Petugas</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data_surat)) : ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['no_surat']) ?></td>
                            <td><?= htmlspecialchars($row['tgl_surat']) ?></td>
                            <td><?= htmlspecialchars($row['tujuan']) ?></td>
                            <td><?= htmlspecialchars($row['perihal']) ?></td>
                            <td><?= $row['no_masuk'] ? '[ ' . $row['no_masuk'] . ' ]' : '-' ?></td>
                          <td>
                          <?php if ($row['balasan_untuk_id']) : ?>
                            <a href="surat_masuk_detail.php?id=<?= $row['balasan_untuk_id'] ?>" class="btn btn-sm btn-success" target="_blank">
                              <i class="fas fa-reply"></i> Lihat Balasan
                            </a>
                          <?php else : ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>



                            <td>
                              <?php if ($row['file_surat']) : ?>
                                <a href="uploads/<?= $row['file_surat'] ?>" target="_blank" class="btn btn-sm btn-info">
                                  <i class="fas fa-file-pdf"></i> Lihat
                                </a>
                              <?php else : ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['user_nama'] ?? '-') ?></td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div> <!-- end tab content -->
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
<script>
  $(document).ready(function () {
    var hash = window.location.hash;
    if (hash) {
      $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
  });
</script>

</body>
</html>
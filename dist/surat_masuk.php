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

$user_input = $_SESSION['user_id'];

// Ambil data user untuk dropdown Disposisi Ke
$data_user = mysqli_query($conn, "SELECT id, nama FROM users ORDER BY nama ASC");


// Proses Simpan Surat Masuk
if (isset($_POST['simpan'])) {
  $no_surat       = $_POST['no_surat'];
  $tgl_surat      = $_POST['tgl_surat'];
  $tgl_terima     = $_POST['tgl_terima'];
  $pengirim       = $_POST['pengirim'];
  $asal_surat     = $_POST['asal_surat'];
  $perihal        = $_POST['perihal'];
  $lampiran       = $_POST['lampiran'];
  $jenis_surat    = $_POST['jenis_surat'];
  $sifat_surat    = $_POST['sifat_surat'];
  $perlu_balasan  = $_POST['perlu_balasan'];
  $status_balasan = $_POST['status_balasan'];
  $disposisi_ke   = $_POST['disposisi_ke'];
  $catatan        = $_POST['catatan'];
  $file_surat     = '';

  // Upload file
  if ($_FILES['file_surat']['name']) {
    $ext = pathinfo($_FILES['file_surat']['name'], PATHINFO_EXTENSION);
    $file_surat = 'file_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['file_surat']['tmp_name'], 'uploads/' . $file_surat);
  }

  mysqli_query($conn, "INSERT INTO surat_masuk (
    no_surat, tgl_surat, tgl_terima, pengirim, asal_surat, perihal, lampiran,
    jenis_surat, sifat_surat, perlu_balasan, status_balasan, disposisi_ke, catatan,
    file_surat, user_input
  ) VALUES (
    '$no_surat', '$tgl_surat', '$tgl_terima', '$pengirim', '$asal_surat', '$perihal', '$lampiran',
    '$jenis_surat', '$sifat_surat', '$perlu_balasan', '$status_balasan', '$disposisi_ke', '$catatan',
    '$file_surat', '$user_input'
  )");
  header("Location: surat_masuk.php");
  exit;
}



$data_surat = mysqli_query($conn, "
  SELECT sm.*, 
         sk.id AS id_surat_keluar,
         sk.tgl_surat AS tgl_balasan
  FROM surat_masuk sm
  LEFT JOIN surat_keluar sk ON sk.balasan_untuk_id = sm.id
  ORDER BY sm.id DESC
");

// Filter tanggal laporan
$tanggal_dari = $_GET['dari'] ?? '';
$tanggal_sampai = $_GET['sampai'] ?? '';

$whereFilter = "";
if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
  $whereFilter = "WHERE tgl_terima BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
}

// Jumlah surat per bulan
$data_laporan = mysqli_query($conn, "
  SELECT DATE_FORMAT(tgl_terima, '%Y-%m') AS bulan, COUNT(*) AS jumlah
  FROM surat_masuk
  $whereFilter
  GROUP BY bulan
  ORDER BY bulan DESC
");

// Jumlah surat berdasarkan sifat
$data_sifat = mysqli_query($conn, "
  SELECT sifat_surat, COUNT(*) AS jumlah
  FROM surat_masuk
  $whereFilter
  GROUP BY sifat_surat
");


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
    min-width: 2000px;
  }
  .table-surat th, .table-surat td {
    vertical-align: top;
  }

  .highlight-balasan {
  background-color: #fff3cd !important; /* kuning lembut */
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

            <!-- FORM -->
            <div class="card">
  <div class="card-header">
    <h4>Manajemen Surat Masuk</h4>
  </div>
  <div class="card-body">

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="suratTab" role="tablist">

      <li class="nav-item">
        <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab" aria-controls="form" aria-selected="true">
          <i class="fas fa-edit"></i> Input Surat Masuk
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="false">
          <i class="fas fa-table"></i> Data Surat Masuk
        </a>
      </li>
      <li class="nav-item">
  <a class="nav-link" id="laporan-tab" data-toggle="tab" href="#laporan" role="tab" aria-controls="laporan" aria-selected="false">
    <i class="fas fa-chart-bar"></i> Laporan
  </a>
</li>

    </ul>

    <!-- Tab Contents -->
    <div class="tab-content mt-3" id="suratTabContent">
      
      <!-- TAB 1: FORM -->
      <div class="tab-pane fade show active" id="form" role="tabpanel" aria-labelledby="form-tab">
        <form method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group"><label>No Surat</label><input name="no_surat" class="form-control" required></div>
              <div class="form-group"><label>Tanggal Surat</label><input type="date" name="tgl_surat" class="form-control" required></div>
              <div class="form-group"><label>Tanggal Terima</label><input type="date" name="tgl_terima" class="form-control" required></div>
              <div class="form-group"><label>Pengirim</label><input name="pengirim" class="form-control" required></div>
              <div class="form-group"><label>Asal Surat</label><input name="asal_surat" class="form-control"></div>
            </div>

            <div class="col-md-6">
              <div class="form-group"><label>Perihal</label><input name="perihal" class="form-control"></div>
              <div class="form-group"><label>Lampiran</label><input name="lampiran" class="form-control"></div>
              <div class="form-group"><label>Jenis Surat</label>
                <select name="jenis_surat" class="form-control">
                  <option>Undangan</option>
                  <option>Pemberitahuan</option>
                  <option>Penting</option>
                </select>
              </div>
              <div class="form-group"><label>Sifat Surat</label>
                <select name="sifat_surat" class="form-control">
                  <option>Biasa</option>
                  <option>Penting</option>
                  <option>Rahasia</option>
                </select>
              </div>
              <div class="form-group"><label>Perlu Balasan?</label>
                <select name="perlu_balasan" class="form-control">
                  <option value="Tidak">Tidak</option>
                  <option value="Ya">Ya</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group"><label>Status Balasan</label>
            <select name="status_balasan" class="form-control">
              <option value="Belum Dibalas">Belum Dibalas</option>
              <option value="Sudah Dibalas">Sudah Dibalas</option>
              <option value="Tidak Perlu Dibalas">Tidak Perlu Dibalas</option>
            </select>
          </div>
          <div class="form-group"><label>Disposisi Ke</label>
            <select name="disposisi_ke" class="form-control">
              <option value="">-- Pilih User --</option>
              <?php while ($u = mysqli_fetch_assoc($data_user)) : ?>
                <option value="<?= $u['nama'] ?>"><?= $u['nama'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group"><label>Catatan</label><textarea name="catatan" class="form-control"></textarea></div>
          <div class="form-group"><label>File Surat (PDF)</label><input type="file" name="file_surat" accept=".pdf" class="form-control"></div>

          <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </form>
      </div>

   <div class="tab-pane fade" id="data" role="tabpanel" aria-labelledby="data-tab">
  <div class="table-responsive-custom">
    <table class="table table-bordered table-striped table-surat">
      <thead>
        <tr>
          <th>No</th>
          <th>No Surat</th>
          <th>Tanggal Surat</th>
          <th>Tanggal Terima</th>
          <th>Pengirim</th>
          <th>Asal Surat</th>
          <th>Perihal</th>
          <th>Lampiran</th>
          <th>Jenis Surat</th>
          <th>Sifat Surat</th>
          <th>Perlu Balasan</th>
         <th>Status Balasan</th>
<th>Tgl Balas</th>
<th>Balasan</th>

          <th>Disposisi Ke</th>
          <th>Catatan</th>
          <th>File</th>
          <th>Input Oleh</th>
        </tr>
      </thead>
      <tbody>
     <?php $no = 1; while ($row = mysqli_fetch_assoc($data_surat)) : ?>
  <tr class="<?= ($row['perlu_balasan'] === 'Ya' && !$row['id_surat_keluar']) ? 'table-warning' : '' ?>">

    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['no_surat']) ?></td>
            <td><?= htmlspecialchars($row['tgl_surat']) ?></td>
            <td><?= htmlspecialchars($row['tgl_terima']) ?></td>
            <td><?= htmlspecialchars($row['pengirim']) ?></td>
            <td><?= htmlspecialchars($row['asal_surat']) ?></td>
            <td><?= htmlspecialchars($row['perihal']) ?></td>
            <td><?= htmlspecialchars($row['lampiran']) ?></td>
            <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
            <td><?= htmlspecialchars($row['sifat_surat']) ?></td>
            <td><?= htmlspecialchars($row['perlu_balasan']) ?></td>
            <td>
  <?php
    $status = $row['status_balasan'];
    if ($row['id_surat_keluar']) {
      $status = 'Sudah Dibalas';
    }
    echo htmlspecialchars($status);
  ?>
</td>

            <td>
  <?= $row['tgl_balasan'] ? htmlspecialchars($row['tgl_balasan']) : '-' ?>
</td>
<td>
  <?php if ($row['id_surat_keluar']) : ?>
    <a href="surat_keluar_detail.php?id=<?= $row['id_surat_keluar'] ?>" class="btn btn-sm btn-success" target="_blank">
      <i class="fas fa-reply"></i> Lihat Balasan
    </a>
  <?php else : ?>
    <span class="text-muted">-</span>
  <?php endif; ?>
</td>

            <td><?= htmlspecialchars($row['disposisi_ke']) ?></td>
            <td><?= htmlspecialchars($row['catatan']) ?></td>
            <td>
              <?php if ($row['file_surat']) : ?>
                <a href="uploads/<?= $row['file_surat'] ?>" target="_blank" class="btn btn-sm btn-info">
                  <i class="fas fa-file-pdf"></i> Lihat
                </a>
              <?php else : ?>
                <span class="text-muted">-</span>
              <?php endif ?>
            </td>
            <td>
              <?php
                $uid = $row['user_input'];
                $u = mysqli_query($conn, "SELECT nama FROM users WHERE id = '$uid'");
                $nama_input = mysqli_fetch_assoc($u);
                echo htmlspecialchars($nama_input['nama'] ?? '-');
              ?>
            </td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>
</div>

<!-- TAB 3: LAPORAN -->
<div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
  <h5 class="mb-3">Laporan Surat Masuk</h5>

  <form method="GET" class="form-inline mb-3">
    <input type="hidden" name="tab" value="laporan">
    
    <label class="mr-2">Dari:</label>
    <input type="date" name="dari" value="<?= htmlspecialchars($tanggal_dari) ?>" class="form-control mr-3">

    <label class="mr-2">Sampai:</label>
    <input type="date" name="sampai" value="<?= htmlspecialchars($tanggal_sampai) ?>" class="form-control mr-3">

    <button type="submit" class="btn btn-primary mr-2">
      <i class="fas fa-filter"></i> Filter
    </button>

    <?php if (!empty($tanggal_dari) && !empty($tanggal_sampai)) : ?>
      <a href="laporan_surat_masuk.php?dari=<?= urlencode($tanggal_dari) ?>&sampai=<?= urlencode($tanggal_sampai) ?>" target="_blank" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Cetak PDF
      </a>
    <?php endif; ?>
  </form>

  <!-- Jumlah per bulan -->
  <div class="table-responsive mb-4">
    <h5 class="mt-4">Jumlah Surat Masuk per Bulan</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Bulan</th>
          <th>Jumlah Surat</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($lap = mysqli_fetch_assoc($data_laporan)) : ?>
          <tr>
            <td><?= date('F Y', strtotime($lap['bulan'] . '-01')) ?></td>
            <td><?= $lap['jumlah'] ?></td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>

  <!-- Jumlah berdasarkan sifat -->
  <div class="table-responsive">
    <h5 class="mt-4">Jumlah Surat Berdasarkan Sifat Surat</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Sifat Surat</th>
          <th>Jumlah</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($sifat = mysqli_fetch_assoc($data_sifat)) : ?>
          <tr>
            <td><?= htmlspecialchars($sifat['sifat_surat']) ?></td>
            <td><?= $sifat['jumlah'] ?></td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>
</div>



    </div> <!-- end of tab content -->
  </div> <!-- end of card body -->
</div> <!-- end of card -->


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
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');

  if (tab === 'data') {
    $('#data-tab').tab('show');
  } else if (tab === 'laporan') {
    $('#laporan-tab').tab('show');
  } else {
    $('#form-tab').tab('show');
  }
});

</script>

</body>
</html>

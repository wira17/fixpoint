<?php
include 'security.php'; 
include 'koneksi.php';
include 'koneksi_wa.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

// Fungsi format tanggal Indonesia
function formatTanggalIndo($tanggal) {
  $bulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  $pecah = explode('-', $tanggal);
  return $pecah[2] . ' ' . $bulan[(int)$pecah[1] - 1] . ' ' . $pecah[0];
}

// Filter berdasarkan bulan dan tahun
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Proses simpan agenda
if (isset($_POST['simpan'])) {
  $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
  $keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan']);
  $tanggal     = $_POST['tanggal'];
  $jam         = $_POST['jam'];

  $file_pendukung = '';
  if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['file_pendukung']['name'], PATHINFO_EXTENSION));
    if ($ext == 'pdf') {
      $file_pendukung = 'agenda_' . time() . '.' . $ext;
      move_uploaded_file($_FILES['file_pendukung']['tmp_name'], 'uploads/' . $file_pendukung);
    }
  }

  mysqli_query($conn, "INSERT INTO agenda_direktur (
    judul, keterangan, tanggal, jam, file_pendukung, tgl_input, user_input
  ) VALUES (
    '$judul', '$keterangan', '$tanggal', '$jam', '$file_pendukung', NOW(), '$user_id'
  )");

  // Kirim ke WA
  // Kirim ke WA
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, jabatan FROM users WHERE id = $user_id"));
$nomor_hp_tujuan = '082177846209';
$nowa = '62' . preg_replace('/[^0-9]/', '', substr($nomor_hp_tujuan, 1)) . '@c.us';

$pesan_wa = "*AGENDA BARU DIREKTUR/PIMPINAN!*\n"
          . "*Judul     :* $judul\n"
          . "*Tanggal   :* " . formatTanggalIndo($tanggal) . "\n"
          . "*Jam       :* $jam WIB\n"
          . "*Keterangan:*\n$keterangan\n\n";

// Siapkan data WA
$tanggal_jam = date('Y-m-d H:i:s'); // gunakan timestamp sekarang
$status_wa   = 'ANTRIAN';
$source      = 'KHANZA';
$sender      = 'NODEJS'; 
$success     = null;
$response    = null;
$request     = null;
$type        = 'TEXT';
$file        = null;

// Simpan ke wa_outbox
$stmt_wa = $conn_wa->prepare("INSERT INTO wa_outbox 
    (NOWA, PESAN, TANGGAL_JAM, STATUS, SOURCE, SENDER, SUCCESS, RESPONSE, REQUEST, TYPE, FILE) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt_wa->bind_param(
    "sssssssssss",
    $nowa,
    $pesan_wa,
    $tanggal_jam,
    $status_wa,
    $source,
    $sender,
    $success,
    $response,
    $request,
    $type,
    $file
);

$stmt_wa->execute();
$stmt_wa->close();


  header("Location: agenda_direktur.php?sukses=1#data");

  exit;
}


// Hitung total data & halaman
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM agenda_direktur WHERE MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'");
$total_result = mysqli_fetch_assoc($total_query);
$total_data = $total_result['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data agenda
$data_agenda = mysqli_query($conn, "SELECT a.*, u.nama AS user_nama 
  FROM agenda_direktur a 
  LEFT JOIN users u ON a.user_input = u.id 
  WHERE MONTH(a.tanggal) = '$filter_bulan' AND YEAR(a.tanggal) = '$filter_tahun'
  ORDER BY a.tanggal DESC
  LIMIT $limit OFFSET $offset");

$bulanIndo = [
  1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Agenda Direktur</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <style>
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
    }
    .table-agenda {
      white-space: nowrap;
      min-width: 1000px;
    }

    .table-agenda tr[style] {
  font-weight: bold;
  color: #155724;
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
              <h4>Agenda Direktur</h4>
            </div>
            <div class="card-body">

              <ul class="nav nav-tabs" id="agendaTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link <?= (isset($_GET['bulan']) ? '' : 'active') ?>" id="form-tab" data-toggle="tab" href="#form" role="tab">Input Agenda</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= (isset($_GET['bulan']) ? 'active' : '') ?>" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Agenda</a>
                </li>
              </ul>

              <div class="tab-content mt-3" id="agendaTabContent">
                <div class="tab-pane fade <?= (isset($_GET['bulan']) ? '' : 'show active') ?>" id="form" role="tabpanel">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group"><label>Judul Agenda</label><input name="judul" class="form-control" required></div>
                        <div class="form-group"><label>Tanggal Agenda</label><input type="date" name="tanggal" class="form-control" required></div>
                        <div class="form-group"><label>Jam Agenda</label><input type="time" name="jam" class="form-control" required></div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3" required></textarea></div>
                        <div class="form-group"><label>File Pendukung (PDF)</label><input type="file" name="file_pendukung" accept=".pdf" class="form-control"></div>
                      </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

                <div class="tab-pane fade <?= (isset($_GET['bulan']) ? 'show active' : '') ?>" id="data" role="tabpanel">
                  <div class="table-responsive-custom">
                    <form method="GET" class="form-inline mb-3">
                      <label class="mr-2">Filter Bulan:</label>
                      <select name="bulan" class="form-control mr-2">
                        <?php
                          foreach ($bulanIndo as $num => $nama) {
                            $selected = ($filter_bulan == $num) ? 'selected' : '';
                            echo "<option value='$num' $selected>$nama</option>";
                          }
                        ?>
                      </select>
                      <select name="tahun" class="form-control mr-2">
                        <?php
                          $tahun_sekarang = date('Y');
                          for ($t = $tahun_sekarang - 2; $t <= $tahun_sekarang + 2; $t++) {
                            $selected = ($filter_tahun == $t) ? 'selected' : '';
                            echo "<option value='$t' $selected>$t</option>";
                          }
                        ?>
                      </select>
                      <button type="submit" class="btn btn-primary mr-2">
  <i class="fas fa-filter"></i> Tampilkan
</button>
<a href="cetak_agenda.php?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>" target="_blank" class="btn btn-success">
  <i class="fas fa-print"></i> Cetak
</a>

                    </form>

                    <table class="table table-bordered table-striped table-agenda">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Judul</th>
                          <th>Tanggal</th>
                          <th>Jam</th>
                          <th>Keterangan</th>
                          <th>File</th>
                        </tr>
                      </thead>
                     <tbody>
                      <?php 
                        $no = 1;
                        $today = date('Y-m-d');
                        while ($row = mysqli_fetch_assoc($data_agenda)) :
                          $highlight = ($row['tanggal'] == $today) ? 'style="background-color: #d4edda;"' : '';
                      ?>
                        <tr <?= $highlight ?>>

                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= formatTanggalIndo($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars(substr($row['jam'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                              <?php if ($row['file_pendukung']) : ?>
                                <a href="uploads/<?= $row['file_pendukung'] ?>" target="_blank" class="btn btn-sm btn-info">
                                  <i class="fas fa-file-pdf"></i> Lihat
                                </a>
                              <?php else : ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>

   <nav>
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
      <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
        <a class="page-link" href="?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>&page=<?= $i ?>#data"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>


                  </div>
                </div> <!-- End tab data -->
              </div> <!-- End tab content -->

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function () {
      const urlParams = new URLSearchParams(window.location.search);
  const sukses = urlParams.get('sukses');
  if (sukses === '1') {
    Swal.fire({
      title: 'Alhamdulillah!',
      text: 'Berhasil disimpan dan pesan WhatsApp terkirim.',
      icon: 'success',
      imageUrl: 'https://img.icons8.com/color/96/000000/whatsapp--v1.png',
      imageWidth: 60,
      imageHeight: 60,
      confirmButtonText: 'OK',
      timer: 4000,
      timerProgressBar: true,
      position: 'center',
    }).then(() => {
      window.history.replaceState(null, null, window.location.pathname + '#data'); // Hapus ?sukses
    });
  }

    var hash = window.location.hash;
    if (hash) {
      $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
  });
</script>

</body>
</html>

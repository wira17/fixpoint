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


$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
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
    width: 100%;
    overflow-x: auto;
  }

  table th,
  table td {
    white-space: nowrap !important;
    vertical-align: middle !important;
  }

  /* Agar font dalam tabel tetap proporsional dan kompak */
  .table td, .table th {
    font-size: 13px;
    padding: 6px 8px;
  }



  .text-green-dark {
    color: #0f5132 !important; /* Hijau tua, Bootstrap's dark green used in alerts */
    font-weight: bold;
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
              <h4>Data Gaji</h4>
            </div>
            <div class="card-body">



     <ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="input_gaji.php">Input Gaji</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="data_gaji.php">Data Gaji</a>
  </li>
</ul>



            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark text-center">
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Periode</th>
                      <th>Tahun</th>
                      <th>Gaji Pokok</th>
                      <th>Struktural</th>
                      <th>Fungsional</th>
                      <th>Fungsional 2</th>
                      <th>Kesehatan</th>
                      <th>Masa Kerja</th>
                      <th>Lembur</th>
                      <th>Lainnya</th>
                      <th>Bruto</th>
                      <th>PPH21</th>
                      <th>Potongan</th>
                      <th>BPJS Kes</th>
                      <th>BPJS JHT</th>
                      <th>BPJS JP</th>
                      <th>Dana Sosial</th>
                      <th>Absensi</th>
                      <th>Angsuran</th>
                      <th>Gaji Bersih</th>
                      <th>Petugas</th>
                       <th>Waktu Input</th>
                      <th>Status Email</th>
                      <th>Aksi</th>

                    </tr>
                  </thead>
            
<tbody>
<?php
if (isset($_SESSION['flash_message'])) {
  echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
  unset($_SESSION['flash_message']);
}

$no = 1;
$query = "
  SELECT g.*, u.nama AS nama_karyawan, pet.nama AS petugas
  FROM input_gaji g
  LEFT JOIN users u ON g.karyawan_id = u.id
  LEFT JOIN users pet ON g.user_input = pet.id
";

if (!empty($keyword)) {
  $esc = mysqli_real_escape_string($conn, $keyword);
  $query .= " WHERE u.nama LIKE '%$esc%' OR g.periode LIKE '%$esc%'";
}

$query .= " ORDER BY g.id DESC";
$result = mysqli_query($conn, $query);

$total_gaji_pokok = $total_struktural = $total_fungsional = $total_fungsional2 = $total_kesehatan = 0;
$total_masa_kerja = $total_lembur = $total_lainya = $total_bruto = $total_pph21 = 0;
$total_potongan_total = $total_bpjs_kes = $total_bpjs_jht = $total_bpjs_jp = 0;
$total_dana_sosial = $total_absensi = $total_angsuran = $total_gaji_bersih = 0;

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='text-center'>{$no}</td>";
    echo "<td>{$row['nama_karyawan']}</td>";
    echo "<td class='text-center'>{$row['periode']}</td>";
    echo "<td class='text-center'>{$row['tahun']}</td>";
    echo "<td class='text-right'>Rp " . number_format($row['gaji_pokok'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['struktural'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['fungsional'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['fungsional2'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['kesehatan'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['masa_kerja'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['lembur'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['lainya'], 0, ',', '.') . "</td>";
    echo "<td class='text-right font-weight-bold'>Rp " . number_format($row['bruto'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['pph21'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['potongan_total'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['bpjs_kes'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['bpjs_jht'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['bpjs_jp'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['dana_sosial'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['absensi'], 0, ',', '.') . "</td>";
    echo "<td class='text-right'>Rp " . number_format($row['angsuran'], 0, ',', '.') . "</td>";
    echo "<td class='text-right text-green-dark'>Rp " . number_format($row['gaji_bersih'], 0, ',', '.') . "</td>";

    echo "<td>{$row['petugas']}</td>";
   echo "<td class='text-center'>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";

// Perbaikan variabel status_email
$status_email = strtolower($row['email_status']) === 'terkirim' ? 
  "<span class='badge badge-success'>Terkirim</span>" : 
  "<span class='badge badge-warning'>Belum</span>";

echo "<td class='text-center'>{$status_email}</td>";

echo "<td class='text-center'>
  <a href='cetak_gaji.php?id={$row['id']}' target='_blank' class='btn btn-success btn-sm' title='Cetak'>
    <i class='fas fa-print'></i>
  </a>
  <a href='kirim_email_gaji.php?id={$row['id']}' class='btn btn-primary btn-sm' title='Kirim Email'>
    <i class='fas fa-paper-plane'></i>
  </a>
  <a href='hapus_gaji.php?id={$row['id']}' onclick='return confirm(\"Hapus data ini?\")' class='btn btn-danger btn-sm' title='Hapus'>
    <i class='fas fa-trash'></i>
  </a>
</td>";


    echo "</tr>";

    // Penjumlahan
    $total_gaji_pokok += $row['gaji_pokok'];
    $total_struktural += $row['struktural'];
    $total_fungsional += $row['fungsional'];
    $total_fungsional2 += $row['fungsional2'];
    $total_kesehatan += $row['kesehatan'];
    $total_masa_kerja += $row['masa_kerja'];
    $total_lembur += $row['lembur'];
    $total_lainya += $row['lainya'];
    $total_bruto += $row['bruto'];
    $total_pph21 += $row['pph21'];
    $total_potongan_total += $row['potongan_total'];
    $total_bpjs_kes += $row['bpjs_kes'];
    $total_bpjs_jht += $row['bpjs_jht'];
    $total_bpjs_jp += $row['bpjs_jp'];
    $total_dana_sosial += $row['dana_sosial'];
    $total_absensi += $row['absensi'];
    $total_angsuran += $row['angsuran'];
    $total_gaji_bersih += $row['gaji_bersih'];

    $no++;
  }

  // Baris total ditambahkan setelah semua data
  echo "<tr class='bg-light font-weight-bold text-right'>";
  echo "<td colspan='4' class='text-center'>Total</td>";
  echo "<td>Rp " . number_format($total_gaji_pokok, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_struktural, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_fungsional, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_fungsional2, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_kesehatan, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_masa_kerja, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_lembur, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_lainya, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_bruto, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_pph21, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_potongan_total, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_bpjs_kes, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_bpjs_jht, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_bpjs_jp, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_dana_sosial, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_absensi, 0, ',', '.') . "</td>";
  echo "<td>Rp " . number_format($total_angsuran, 0, ',', '.') . "</td>";
 echo "<td class='text-green-dark'>Rp " . number_format($total_gaji_bersih, 0, ',', '.') . "</td>";

  echo "</tr>";
} else {
  echo "<tr><td colspan='25' class='text-center'>Tidak ada data ditemukan.</td></tr>";
}
?>

<!-- Overlay Loading -->
<div id="loading-overlay" style="
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 9999;
  display: none;
  align-items: center;
  justify-content: center;
">
  <div>
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <p class="mt-2 text-center text-dark">Mengirim email...</p>
  </div>
</div>

</tbody>

                </table>
              </div>
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

<!-- Overlay Loading -->
<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; 
  width: 100%; height: 100%; background: rgba(255,255,255,0.8); 
  z-index: 9999; text-align: center;">
  <div style="position: absolute; top: 50%; left: 50%; 
    transform: translate(-50%, -50%);">
    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p style="margin-top: 10px;">Mengirim email...</p>
  </div>
</div>


<script>
  $(document).ready(function () {
    // Tampilkan toast flash message jika ada
    var toast = $('#notif-toast');
    if (toast.length) {
      toast.fadeIn(300).delay(2000).fadeOut(500);
    }

    // Saat tombol Kirim Email diklik, tampilkan loading overlay
    $('a[href^="kirim_email_gaji.php"]').on('click', function () {
      $('#loading-overlay').fadeIn(200);
    });
  });
</script>

</body>
</html>

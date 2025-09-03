<?php
// laporan_indikator.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;

// ambil info user login
$qUser = mysqli_query($conn, "SELECT unit_kerja, nama FROM users WHERE id = '$user_id' LIMIT 1");
$userData = mysqli_fetch_assoc($qUser);
$nama_user = $userData['nama'] ?? 'User #' . $user_id;

// ambil filter
$filter_kategori = $_GET['kategori'] ?? '';
$filter_indikator = $_GET['indikator'] ?? '';
$filter_tgl_awal = $_GET['tgl_awal'] ?? '';
$filter_tgl_akhir = $_GET['tgl_akhir'] ?? '';

// ambil list kategori dan indikator untuk dropdown
$qKategori = mysqli_query($conn, "SELECT DISTINCT kategori FROM master_indikator WHERE aktif=1 ORDER BY kategori ASC");
$qIndikator = mysqli_query($conn, "SELECT id, nama_indikator FROM master_indikator WHERE aktif=1 ORDER BY nama_indikator ASC");

// bangun query data
$where = [];
if($filter_kategori) $where[] = "mi.kategori = '".mysqli_real_escape_string($conn,$filter_kategori)."'";
if($filter_indikator) $where[] = "pi.indikator_id = '".intval($filter_indikator)."'";
if($filter_tgl_awal) $where[] = "pi.periode >= '".date('Y-m', strtotime($filter_tgl_awal))."'";
if($filter_tgl_akhir) $where[] = "pi.periode <= '".date('Y-m', strtotime($filter_tgl_akhir))."'";

$whereSQL = '';
if(count($where) > 0){
    $whereSQL = 'WHERE '.implode(' AND ',$where);
}

// Ambil data untuk tabel
$qData = mysqli_query($conn, "SELECT pi.*, mi.nama_indikator, mi.kategori, uk.nama_unit
                              FROM pengukuran_indikator pi
                              JOIN master_indikator mi ON pi.indikator_id = mi.id
                              LEFT JOIN unit_kerja uk ON pi.unit_id = uk.id
                              $whereSQL
                              ORDER BY pi.periode ASC");

// Siapkan data untuk Chart.js (hanya jika ada filter tanggal)
$chart_labels = [];
$chart_data = [];
$grafik_aktif = false;
if($filter_tgl_awal && $filter_tgl_akhir){
    $grafik_aktif = true;
    while($row=mysqli_fetch_assoc($qData)){
        $chart_labels[] = $row['periode'];
        $chart_data[] = number_format($row['capaian'],2);
    }
    // reset pointer untuk tabel
    mysqli_data_seek($qData, 0);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Indikator</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .dokumen-table { font-size: 13px; white-space: nowrap; }
    .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
    .chart-container { width: 100%; max-width: 900px; margin: auto; margin-bottom: 30px; display: none; }
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
              <h4 class="mb-0">Laporan Pengukuran Indikator</h4>
            </div>
            <div class="card-body">

              <!-- Filter -->
              <form method="GET" class="form-inline mb-3">
                <div class="form-group mr-2">
                  <label>Kategori &nbsp;</label>
                  <select name="kategori" class="form-control">
                    <option value="">Semua</option>
                    <?php while($k=mysqli_fetch_assoc($qKategori)): ?>
                      <option value="<?= $k['kategori']; ?>" <?= ($filter_kategori==$k['kategori'])?'selected':'' ?>><?= $k['kategori']; ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <label>Indikator &nbsp;</label>
                  <select name="indikator" class="form-control">
                    <option value="">Semua</option>
                    <?php while($i=mysqli_fetch_assoc($qIndikator)): ?>
                      <option value="<?= $i['id']; ?>" <?= ($filter_indikator==$i['id'])?'selected':'' ?>><?= htmlspecialchars($i['nama_indikator']); ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <label>Dari &nbsp;</label>
                  <input type="date" name="tgl_awal" value="<?= $filter_tgl_awal; ?>" class="form-control">
                </div>
                <div class="form-group mr-2">
                  <label>Sampai &nbsp;</label>
                  <input type="date" name="tgl_akhir" value="<?= $filter_tgl_akhir; ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                <button type="button" id="btnGrafik" class="btn btn-success" <?= $grafik_aktif?'':'disabled'; ?>><i class="fas fa-chart-line"></i> Tampilkan Grafik</button>
              </form>

              <!-- Grafik -->
              <div class="chart-container" id="grafikContainer">
                <canvas id="capaianChart"></canvas>
              </div>

              <!-- Tabel -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped dokumen-table">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Indikator</th>
                      <th>Kategori</th>
                      <th>Unit</th>
                      <th>Periode</th>
                      <th>Numerator</th>
                      <th>Denominator</th>
                      <th>Capaian (%)</th>
                      <th>Dibuat Oleh</th>
                      <th>Dibuat Pada</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no=1; while($d=mysqli_fetch_assoc($qData)): ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($d['nama_indikator']); ?></td>
                        <td><?= htmlspecialchars($d['kategori']); ?></td>
                        <td><?= htmlspecialchars($d['nama_unit'] ?? '-'); ?></td>
                        <td><?= $d['periode']; ?></td>
                        <td><?= $d['numerator']; ?></td>
                        <td><?= $d['denominator']; ?></td>
                        <td><?= number_format($d['capaian'], 2); ?>%</td>
                        <td><?= htmlspecialchars($d['dibuat_oleh']); ?></td>
                        <td><?= $d['dibuat_pada']; ?></td>
                      </tr>
                    <?php endwhile; ?>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function(){
    let grafikAktif = <?= $grafik_aktif ? 'true' : 'false'; ?>;
    let chartData = <?= json_encode($chart_data); ?>;
    let chartLabels = <?= json_encode($chart_labels); ?>;

    let capaianChart;

    $('#btnGrafik').click(function(){
        $('#grafikContainer').slideDown();

        if(!capaianChart){
            const ctx = document.getElementById('capaianChart').getContext('2d');
            capaianChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Capaian (%)',
                        data: chartData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.2,
                        pointRadius: 4,
                        pointHoverRadius:6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>

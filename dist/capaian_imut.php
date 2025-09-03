<?php
// capaian_imut.php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id   = $_SESSION['user_id'] ?? 0;
$nama_user = $_SESSION['nama_user'] ?? '';
$modals = [];

// akses menu
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1
            FROM akses_menu
            JOIN menu ON akses_menu.menu_id = menu.id
            WHERE akses_menu.user_id = '".intval($user_id)."'
              AND menu.file_menu = '".mysqli_real_escape_string($conn,$current_file)."'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

/* =========================
   FILTER INPUT (GET)
   ========================= */
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date']   ?? date('Y-m-t');
$jenis      = $_GET['jenis']      ?? 'all'; // all | nasional | rs | unit
$id_selected= isset($_GET['id_indikator']) ? intval($_GET['id_indikator']) : 0;

// sanitasi
$start_date = mysqli_real_escape_string($conn, $start_date);
$end_date   = mysqli_real_escape_string($conn, $end_date);
$jenis      = mysqli_real_escape_string($conn, $jenis);

/* =========================
   REFERENSI INDIKATOR
   ========================= */
function getIndikatorList($conn) {
    $data = [
        'nasional' => [],
        'rs' => [],
        'unit' => []
    ];

    $q1 = mysqli_query($conn, "SELECT id_nasional AS id, nama_indikator, CAST(standar AS DECIMAL(10,2)) AS standar, 'nasional' AS jenis 
                               FROM indikator_nasional ORDER BY nama_indikator");
    while($r=mysqli_fetch_assoc($q1)) $data['nasional'][] = $r;

    $q2 = mysqli_query($conn, "SELECT id_rs AS id, nama_indikator, CAST(standar AS DECIMAL(10,2)) AS standar, 'rs' AS jenis 
                               FROM indikator_rs ORDER BY nama_indikator");
    while($r=mysqli_fetch_assoc($q2)) $data['rs'][] = $r;

    $q3 = mysqli_query($conn, "SELECT iu.id_unit AS id, 
                                      CONCAT(u.nama_unit,' - ', iu.nama_indikator) AS nama_indikator,
                                      CAST(iu.standar AS DECIMAL(10,2)) AS standar, 
                                      'unit' AS jenis
                               FROM indikator_unit iu
                               LEFT JOIN unit_kerja u ON iu.unit_id=u.id
                               ORDER BY u.nama_unit, iu.nama_indikator");
    while($r=mysqli_fetch_assoc($q3)) $data['unit'][] = $r;

    return $data;
}
$refIndikator = getIndikatorList($conn);

/* helper untuk dapatkan nama & standar indikator */
function getNamaDanStandar($conn, $jenis, $id) {
    if($jenis==='nasional'){
        $q = mysqli_query($conn,"SELECT nama_indikator AS nama, CAST(standar AS DECIMAL(10,2)) AS standar 
                                 FROM indikator_nasional WHERE id_nasional=".intval($id)." LIMIT 1");
    } elseif($jenis==='rs'){
        $q = mysqli_query($conn,"SELECT nama_indikator AS nama, CAST(standar AS DECIMAL(10,2)) AS standar 
                                 FROM indikator_rs WHERE id_rs=".intval($id)." LIMIT 1");
    } else { // unit
        $q = mysqli_query($conn,"SELECT CONCAT(u.nama_unit,' - ', iu.nama_indikator) AS nama, 
                                        CAST(iu.standar AS DECIMAL(10,2)) AS standar
                                 FROM indikator_unit iu
                                 LEFT JOIN unit_kerja u ON iu.unit_id=u.id
                                 WHERE iu.id_unit=".intval($id)." LIMIT 1");
    }
    $r = $q? mysqli_fetch_assoc($q) : null;
    return [$r['nama'] ?? '-', $r['standar'] ?? 0];
}

/* =========================
   WHERE CLAUSE
   ========================= */
$where = " WHERE h.tanggal BETWEEN '$start_date' AND '$end_date' ";
if ($jenis !== 'all') {
    $where .= " AND h.jenis_indikator = '$jenis' ";
}
if ($id_selected > 0 && $jenis !== 'all') {
    $where .= " AND h.id_indikator = ".intval($id_selected)." ";
}

/* =========================
   REKAP PER INDIKATOR
   ========================= */
$sqlRekap = "
    SELECT h.jenis_indikator, h.id_indikator,
           (CASE WHEN SUM(h.denominator) > 0 
                 THEN ROUND( (SUM(h.numerator)/SUM(h.denominator))*100 , 2)
                 ELSE 0 END) AS capaian
    FROM indikator_harian h
    $where
    GROUP BY h.jenis_indikator, h.id_indikator
    ORDER BY h.jenis_indikator, h.id_indikator
";
$qRekap = mysqli_query($conn, $sqlRekap);
$rowsRekap = [];
$barLabels=[];$barCapaian=[];$barStandar=[];
while($row = mysqli_fetch_assoc($qRekap)) {
    list($nama, $standar) = getNamaDanStandar($conn, $row['jenis_indikator'], $row['id_indikator']);
    $rowsRekap[] = [
        'jenis'   => strtoupper($row['jenis_indikator']),
        'jenis_raw' => $row['jenis_indikator'],
        'id'      => (int)$row['id_indikator'],
        'nama'    => $nama,
        'standar' => (float)$standar,
        'capaian' => (float)$row['capaian'],
        'status'  => ((float)$row['capaian'] >= (float)$standar) ? 'Tercapai' : 'Tidak Tercapai'
    ];
    $barLabels[]=$nama;
    $barCapaian[]=(float)$row['capaian'];
    $barStandar[]=(float)$standar;
}

/* =========================
   KPI RINGKAS
   ========================= */
$totalIndikator = count($rowsRekap);
$tercapai = 0;
foreach($rowsRekap as $r){ if($r['status']==='Tercapai') $tercapai++; }
$rateTercapai = $totalIndikator>0 ? round($tercapai/$totalIndikator*100,2) : 0;

/* =========================
   DATA GRAFIK TREN HARIAN
   ========================= */
$trendLabels = [];
$trendValues = [];

if ($id_selected > 0 && $jenis !== 'all') {
    $sqlTrend = "
        SELECT DATE(h.tanggal) as tgl,
               (CASE WHEN SUM(h.denominator)>0 
                     THEN ROUND((SUM(h.numerator)/SUM(h.denominator))*100,2)
                     ELSE 0 END) AS persen
        FROM indikator_harian h
        $where
        GROUP BY DATE(h.tanggal)
        ORDER BY tgl
    ";
    $qTrend = mysqli_query($conn, $sqlTrend);
    while($r=mysqli_fetch_assoc($qTrend)){
        $trendLabels[] = $r['tgl'];
        $trendValues[] = (float)$r['persen'];
    }
}

/* =========================
   Pilihan dropdown indikator
   ========================= */
function optionsIndikator($data, $selectedId){
    $opt = '<option value="">-- Pilih Indikator --</option>';
    foreach($data as $r){
        $sel = ($selectedId==$r['id']) ? 'selected' : '';
        $opt .= '<option value="'.$r['id'].'" '.$sel.'>'.htmlspecialchars($r['nama_indikator']).'</option>';
    }
    return $opt;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Capaian IMUT</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    .dokumen-table { font-size: 13px; white-space: nowrap; }
    .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
    .kpi-card { border-left:5px solid #4e73df; }
    .kpi-card.green { border-left-color:#1cc88a; }
    .kpi-card.orange{ border-left-color:#f6c23e; }
  </style>
</head>
<body>
<div id="app">
  <div class="main-wrapper main-wrapper-1">
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <section class="section">
          <div class="card">
            <div class="card-header"><h4 class="mb-0">Filter & Ringkasan</h4></div>
            <div class="card-body">
              <!-- FILTER -->
              <form method="GET" class="mb-3">
                <div class="form-row align-items-end">
                  <div class="form-group col-md-3">
                    <label>Periode Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label>Periode Selesai</label>
                    <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label>Jenis Indikator</label>
                    <select name="jenis" id="jenis" class="form-control">
                      <option value="all"      <?= $jenis==='all'?'selected':'' ?>>Semua</option>
                      <option value="nasional" <?= $jenis==='nasional'?'selected':'' ?>>Nasional</option>
                      <option value="rs"       <?= $jenis==='rs'?'selected':'' ?>>RS</option>
                      <option value="unit"     <?= $jenis==='unit'?'selected':'' ?>>Unit</option>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label>Indikator</label>
                    <select name="id_indikator" id="id_indikator" class="form-control" <?= $jenis==='all'?'disabled':'' ?>>
                      <?php
                        if($jenis==='nasional')      echo optionsIndikator($refIndikator['nasional'], $id_selected);
                        elseif($jenis==='rs')        echo optionsIndikator($refIndikator['rs'], $id_selected);
                        elseif($jenis==='unit')      echo optionsIndikator($refIndikator['unit'], $id_selected);
                        else                         echo '<option value="">-- Pilih Indikator --</option>';
                      ?>
                    </select>
                  </div>
                </div>
                <div>
                  <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                  <a href="capaian_imut.php" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                </div>
              </form>

              <!-- KPI RINGKAS -->
              <div class="row mb-4">
                <div class="col-md-4"><div class="card kpi-card"><div class="card-body"><div class="d-flex justify-content-between"><div><div class="text-muted">Total Indikator</div><div class="h4 mb-0"><?= $totalIndikator ?></div></div><i class="fas fa-list fa-2x text-primary"></i></div></div></div></div>
                <div class="col-md-4"><div class="card kpi-card green"><div class="card-body"><div class="d-flex justify-content-between"><div><div class="text-muted">Tercapai</div><div class="h4 mb-0"><?= $tercapai ?></div></div><i class="fas fa-check-circle fa-2x text-success"></i></div></div></div></div>
                <div class="col-md-4"><div class="card kpi-card orange"><div class="card-body"><div class="d-flex justify-content-between"><div><div class="text-muted">Rate Tercapai</div><div class="h4 mb-0"><?= number_format($rateTercapai,2) ?>%</div></div><i class="fas fa-percentage fa-2x text-warning"></i></div></div></div></div>
              </div>

              <!-- GRAFIK BAR -->
              <div class="card mb-4">
                <div class="card-header"><h4 class="mb-0">Capaian vs Standar (Per Indikator)</h4></div>
                <div class="card-body"><div style="height:300px;"><canvas id="barChart"></canvas></div></div>
              </div>

              <!-- GRAFIK LINE -->
              <?php if(!empty($trendLabels)): ?>
              <div class="card mb-4">
                <div class="card-header"><h4 class="mb-0">Tren Harian Indikator Terpilih</h4></div>
                <div class="card-body"><div style="height:250px;"><canvas id="lineChart"></canvas></div></div>
              </div>
              <?php endif; ?>

              <!-- TABEL REKAP -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped dokumen-table">
                  <thead class="thead-light"><tr><th>No</th><th>Jenis</th><th>Nama Indikator</th><th>Standar</th><th>Capaian</th><th>Status</th></tr></thead>
                  <tbody>
                  <?php if(empty($rowsRekap)): ?>
                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                  <?php else: $no=1; foreach($rowsRekap as $d): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= $d['jenis'] ?></td>
                      <td><?= htmlspecialchars($d['nama']) ?></td>
                      <td><?= number_format($d['standar'],2) ?>%</td>
                      <td><?= number_format($d['capaian'],2) ?>%</td>
                      <td><?= $d['status']==='Tercapai' ? '<span class="badge badge-success">Tercapai</span>' : '<span class="badge badge-danger">Tidak Tercapai</span>' ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
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


<!-- JQUERY dulu -->
<script src="assets/modules/jquery.min.js"></script>

<!-- LIBRARY DEPENDENCY -->
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>

<!-- FRAMEWORK SCRIPT -->
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>

<!-- LIBRARY TAMBAHAN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const barLabels  = <?= json_encode($barLabels) ?>;
const barCapaian = <?= json_encode($barCapaian) ?>;
const barStandar = <?= json_encode($barStandar) ?>;
const trendLabels= <?= json_encode($trendLabels) ?>;
const trendValues= <?= json_encode($trendValues) ?>;

// Bar Chart
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {labels: barLabels, datasets:[
    {label:'Capaian (%)', data: barCapaian, backgroundColor:'rgba(54,162,235,0.7)'},
    {label:'Standar (%)', data: barStandar, backgroundColor:'rgba(255,99,132,0.7)'}
  ]},
  options: {responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100}}}
});

// Line Chart
if(trendLabels.length>0){
  new Chart(document.getElementById('lineChart'), {
    type:'line',
    data:{labels:trendLabels,datasets:[{label:'Persentase Harian (%)',data:trendValues,borderColor:'rgba(75,192,192,1)',backgroundColor:'rgba(75,192,192,0.2)',tension:0.3,fill:true}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100}}}
  });
}

// Dropdown dinamis
const refInd = <?= json_encode($refIndikator) ?>;
$('#jenis').on('change', function(){
  const jenis = $(this).val();
  const $idInd = $('#id_indikator');
  $idInd.empty();
  if (jenis==='all'){ $idInd.prop('disabled',true).append('<option value="">-- Pilih Indikator --</option>'); }
  else{
    $idInd.prop('disabled',false).append('<option value="">-- Pilih Indikator --</option>');
    (refInd[jenis]||[]).forEach(it=>{ $idInd.append('<option value="'+it.id+'">'+it.nama_indikator+'</option>'); });
  }
});
</script>
</body>
</html>

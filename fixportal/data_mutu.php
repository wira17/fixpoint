<?php
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';

// Ambil filter bulan & tahun dari request, default bulan & tahun sekarang
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Query tabel capaian_imp
$sql_imp = "SELECT * FROM capaian_imp WHERE bulan = $bulan AND tahun = $tahun ORDER BY id DESC";
$data_imp = $conn->query($sql_imp);

// Query tabel capaian_imut
$sql_imut = "SELECT * FROM capaian_imut WHERE bulan = $bulan AND tahun = $tahun ORDER BY id DESC";
$data_imut = $conn->query($sql_imut);

// Query tabel capaian_imut_rs
$sql_imut_rs = "SELECT * FROM capaian_imut_rs WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun ORDER BY id DESC";
$data_imut_rs = $conn->query($sql_imut_rs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Data Mutu - FixPoint</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { padding-top: 140px; }
        .section-title { font-size: 18px; font-weight: bold; margin-top: 40px; }
        .table th, .table td { font-size: 13px; }
  

        /* Navbar */
        .navbar-custom { background-color: #343a40; }
        .navbar-custom .navbar-brand { font-weight: 800; color: #fff; font-size: 28px; }
        .navbar-custom .navbar-brand:hover { color: #FFD700; }
        .navbar-custom .nav-link { color: #fff; font-weight: 600; margin: 0 10px; transition: 0.3s; }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active { color: #FFD700; }
        .btn-apply {
            background: #FFD700; color: #000; font-weight: 600;
            border-radius: 50px; transition: 0.3s;
        }
        .btn-apply:hover { background: #FFC107; color: #000; }

        /* Lowongan Kerja Styling */
        .lowongan-section { max-width: 1320px; }
        .lowongan-card { border-radius: 10px; font-size: 13px; overflow: hidden; }
        .lowongan-card .card-img-top {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        .lowongan-card .card-body { padding: 12px; }
        .lowongan-card .card-title {
            font-size: 14px; font-weight: 700; color: #343a40; margin-bottom: 6px;
        }
        .lowongan-card .tanggal {
            font-size: 12px; color: #6c757d; margin: 0;
        }
        .lowongan-card .status-tutup {
            font-size: 12px;
            font-weight: bold;
            color: red;
        }
        .lowongan-card .card-footer {
            font-size: 11px; background-color: #fff; border-top: 1px solid #dee2e6;
        }

        /* Modal styling */
        .modal-job-img {
            width: 100%; height: 260px; object-fit: cover; border-radius: 8px;
        }
        .modal-body {
            font-size: 13px; /* perkecil isi modal */
        }
        .modal-body h6 {
            font-size: 14px;
        }

        /* Grid 4 per baris di desktop */
        @media (min-width: 992px) {
            .lowongan-section .col-lg-3 { flex: 0 0 25%; max-width: 25%; }
        }
    </style>
</head>
<body>


<?php include 'navbar.php'; ?>


<div class="container py-4">
    <h2 class="text-center mb-4">Data Mutu Rumah Sakit</h2>

    <!-- Filter Bulan dan Tahun -->
    <form method="get" class="row g-2 mb-4">
        <div class="col-auto">
            <select name="bulan" class="form-select form-select-sm">
                <?php
                $bulanArr = [1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                foreach($bulanArr as $num=>$nama){
                    echo "<option value='$num' ".($bulan==$num?'selected':'').">$nama</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="tahun" class="form-select form-select-sm">
                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= ($tahun==$y?'selected':'') ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </div>
    </form>

    <!-- Data capaian_imp -->
    <div>
        <div class="section-title">Capaian IMP</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>IMP ID</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Target</th>
                        <th>Numerator</th>
                        <th>Denominator</th>
                        <th>Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data_imp->num_rows > 0): while($row = $data_imp->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['imp_id'] ?></td>
                            <td><?= $row['bulan'] ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td><?= $row['target'] ?></td>
                            <td><?= $row['numerator'] ?></td>
                            <td><?= $row['denominator'] ?></td>
                            <td><?= $row['capaian'] ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Data capaian_imut -->
    <div>
        <div class="section-title">Capaian IMUT</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Indikator ID</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Target</th>
                        <th>Numerator</th>
                        <th>Denominator</th>
                        <th>Capaian</th>
                        <th>User ID</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data_imut->num_rows > 0): while($row = $data_imut->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['indikator_id'] ?></td>
                            <td><?= $row['bulan'] ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td><?= $row['target'] ?></td>
                            <td><?= $row['numerator'] ?></td>
                            <td><?= $row['denominator'] ?></td>
                            <td><?= $row['capaian'] ?></td>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['updated_at'] ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="11" class="text-center">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Data capaian_imut_rs -->
    <div>
        <div class="section-title">Capaian IMUT RS</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>IMUT ID</th>
                        <th>Unit ID</th>
                        <th>Tanggal</th>
                        <th>Nilai</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data_imut_rs->num_rows > 0): while($row = $data_imut_rs->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['imut_id'] ?></td>
                            <td><?= $row['unit_id'] ?></td>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= $row['nilai'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['updated_at'] ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>

<!-- Copyright Start -->
<div class="container-fluid copyright bg-dark py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>FixPoint</a>, All right reserved.</span>
            </div>
            <div class="col-md-6 my-auto text-center text-md-end text-white">
            </div>
        </div>
    </div>
</div>
<!-- Copyright End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

<!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

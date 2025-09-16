<?php
// Sertakan koneksi
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';

// Ambil data lowongan
$result = $conn->query("SELECT * FROM lowongan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FixPoint - Lowongan Kerja</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        body { padding-top: 140px; }

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

<!-- Navbar Start -->

<?php include 'navbar.php'; ?>

<!-- Navbar End -->

<!-- Lowongan Kerja -->
<div class="container py-5 lowongan-section">
    <h2 class="text-center mb-4">Lowongan Kerja</h2>
    <div class="row g-3">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                    $foto = isset($row['foto']) ? trim($row['foto']) : '';
                    if ($foto === '' ) {
                        $fotoSrc = 'img/default-job.jpg';
                    } elseif (preg_match('/^(https?:\/\/|\/)/', $foto)) {
                        $fotoSrc = $foto;
                    } else {
                        $fotoSrc = '../fixpoint/dist/uploads/lowongan/' . $foto;
                    }

                    // Status tutup jika lewat tanggal akhir
                    $tanggal_akhir = strtotime($row['tanggal_akhir']);
                    $sekarang = strtotime(date('Y-m-d'));
                    $isTutup = $tanggal_akhir < $sekarang;
                ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card lowongan-card h-100 border-secondary shadow-sm">
                        <img src="<?= htmlspecialchars($fotoSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['posisi']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($row['posisi']) ?></h5>
                            <?php if ($isTutup): ?>
                                <p class="status-tutup"><i class="fas fa-lock me-1"></i> Tutup</p>
                            <?php else: ?>
                                <p class="tanggal">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    Tanggal Akhir: <?= date('d-m-Y', $tanggal_akhir) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-sm btn-apply" data-bs-toggle="modal" data-bs-target="#modalLoker<?= $row['id'] ?>" <?= $isTutup ? 'disabled' : '' ?>>
                                Lamar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalLoker<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($row['posisi']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <img src="<?= htmlspecialchars($fotoSrc) ?>" class="modal-job-img mb-3" alt="<?= htmlspecialchars($row['posisi']) ?>">
                                <?php if ($isTutup): ?>
                                    <p class="status-tutup"><i class="fas fa-lock me-1"></i> Lowongan ini sudah ditutup.</p>
                                <?php else: ?>
                                    <p class="tanggal">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        Tanggal Akhir: <?= date('d-m-Y', $tanggal_akhir) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($row['deskripsi'])): ?>
                                    <h6>Deskripsi</h6>
                                    <p><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($row['kualifikasi'])): ?>
                                    <h6>Kualifikasi</h6>
                                    <p><?= nl2br(htmlspecialchars($row['kualifikasi'])) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($row['lokasi'])): ?>
                                    <h6>Lokasi</h6>
                                    <p><?= nl2br(htmlspecialchars($row['lokasi'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <?php if (!$isTutup): ?>
                                    <a href="apply.php?id=<?= (int)$row['id'] ?>" class="btn btn-apply">Lamar Sekarang</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Tidak ada lowongan saat ini.</p>
        <?php endif; ?>
    </div>
</div>
<!-- Lowongan Kerja End -->

<!-- Footer Start -->
<?php include 'footer.php'; ?>
<!-- Footer End -->

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

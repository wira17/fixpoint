<?php
// Sertakan koneksi
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';

// Ambil data dokter
$result = $conn->query("SELECT * FROM dokter ORDER BY nama_dokter ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FixPoint - Dokter</title>
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

        /* Dokter Styling */
        .dokter-section { max-width: 1320px; }
        .dokter-card { border-radius: 10px; font-size: 13px; overflow: hidden; }
        .dokter-card .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .dokter-card .card-body { padding: 12px; }
        .dokter-card .card-title {
            font-size: 14px; font-weight: 700; color: #343a40; margin-bottom: 6px;
        }
        .dokter-card .info { font-size: 12px; color: #6c757d; margin: 0; }
        .dokter-card .modal-body { font-size: 13px; }

        /* Modal image */
        .modal-doctor-img {
            width: 100%; height: 260px; object-fit: cover; border-radius: 8px;
        }

        @media (min-width: 992px) {
            .dokter-section .col-lg-3 { flex: 0 0 25%; max-width: 25%; }
        }
    </style>
</head>
<body>

<!-- Navbar Start -->
<?php include 'navbar.php'; ?>
<!-- Navbar End -->

<!-- Dokter Section -->
<div class="container py-5 dokter-section">
    <h2 class="text-center mb-4">Daftar Dokter</h2>
    <div class="row g-3">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                    $foto = isset($row['foto']) ? trim($row['foto']) : '';
                    if ($foto === '') {
                        $fotoSrc = 'img/default-doctor.jpg';
                    } elseif (preg_match('/^(https?:\/\/|\/)/', $foto)) {
                        $fotoSrc = $foto;
                    } else {
                        $fotoSrc = '../fixpoint/dist/uploads/dokter/' . $foto;
                    }

                    $hariPraktek = !empty($row['hari_praktek']) ? $row['hari_praktek'] : '-';
                    $jamPraktek = !empty($row['jam_praktek']) ? $row['jam_praktek'] : '-';
                ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card dokter-card h-100 border-secondary shadow-sm">
                        <img src="<?= htmlspecialchars($fotoSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_dokter']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($row['nama_dokter']) ?></h5>
                            <p class="info"><i class="fas fa-clinic-medical me-1"></i> <?= htmlspecialchars($row['poliklinik']) ?></p>
                            <p class="info"><i class="far fa-calendar-alt me-1"></i> <?= htmlspecialchars($hariPraktek) ?></p>
                            <p class="info"><i class="far fa-clock me-1"></i> <?= htmlspecialchars($jamPraktek) ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDokter<?= $row['id'] ?>">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalDokter<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($row['nama_dokter']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <img src="<?= htmlspecialchars($fotoSrc) ?>" class="modal-doctor-img mb-3" alt="<?= htmlspecialchars($row['nama_dokter']) ?>">
                                <p><strong>Poliklinik:</strong> <?= htmlspecialchars($row['poliklinik']) ?></p>
                                <p><strong>Hari Praktek:</strong> <?= htmlspecialchars($hariPraktek) ?></p>
                                <p><strong>Jam Praktek:</strong> <?= htmlspecialchars($jamPraktek) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Belum ada dokter terdaftar.</p>
        <?php endif; ?>
    </div>
</div>
<!-- Dokter Section End -->

<!-- Footer Start -->
<?php include 'footer.php'; ?>
<!-- Footer End -->

<!-- Copyright Start -->
<div class="container-fluid copyright bg-dark py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <span class="text-light"><i class="fas fa-copyright me-2"></i>FixPoint, All rights reserved.</span>
            </div>
            <div class="col-md-6 my-auto text-center text-md-end text-white"></div>
        </div>
    </div>
</div>
<!-- Copyright End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

<!-- JS Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<?php
// Sertakan koneksi
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>FixPoint - Kontak</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Icon & Bootstrap -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        body {
            padding-top: 140px;
        }

        .navbar-custom {
            background-color: #343a40;
        }

        .navbar-custom .navbar-brand {
            font-weight: 800;
            color: #fff;
            font-size: 28px;
        }

        .navbar-custom .navbar-brand:hover {
            color: #FFD700;
        }

        .navbar-custom .nav-link {
            color: #fff;
            font-weight: 600;
            margin: 0 10px;
            transition: 0.3s;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #FFD700;
        }

        .btn-apply {
            background: #FFD700;
            color: #000;
            font-weight: 600;
            border-radius: 50px;
            transition: 0.3s;
        }

        .btn-apply:hover {
            background: #FFC107;
            color: #000;
        }

        .navbar-toggler {
            border: none;
        }
        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }
    </style>
</head>

<body>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-xl navbar-custom fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">FixPoint</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fas fa-bars text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
           <ul class="navbar-nav mx-auto mb-2 mb-xl-0">
    <li class="nav-item">
        <a class="nav-link active" href="beranda.php">Beranda</a>
    </li>

    <!-- Tentang Dropdown -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="tentangDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tentang
        </a>
        <ul class="dropdown-menu" aria-labelledby="tentangDropdown">
            <li><a class="dropdown-item" href="sejarah.php">Sejarah</a></li>
            <li><a class="dropdown-item" href="visi_misi.php">Visi & Misi</a></li>
            <li><a class="dropdown-item" href="direksi.php">Direksi</a></li>
            <li><a class="dropdown-item" href="dokter.php">Dokter</a></li>
            <li><a class="dropdown-item" href="kerja_sama.php">Kerja Sama</a></li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="fasilitas.php">Fasilitas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="berita.php">Berita</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="kuisioner.php">Kuisioner</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="data_mutu.php">Data Mutu</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="dokumentasi.php">Dokumentasi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="loker.php">Loker</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="contact.php">Kontak</a>
    </li>
</ul>

            <div class="d-flex">
                <a href="apply.php" class="btn btn-apply">Lamar Sekarang</a>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- Kontak Start -->
<div class="container py-5">
    <h2 class="text-center mb-4">Hubungi Kami</h2>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-secondary">
                <div class="card-body">
                    <h5 class="card-title mb-3">Kirim Pesan</h5>
                    <form action="proses_kontak.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea name="pesan" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-apply">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-secondary">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Kontak</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i>1429 Netus Rd, NY 48247</p>
                    <p><i class="fas fa-envelope me-2"></i>example@gmail.com</p>
                    <p><i class="fas fa-phone me-2"></i>+0123 4567 8910</p>
                    <hr>
                    <h6>Ikuti Kami</h6>
                    <div class="d-flex mt-2">
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Kontak End -->

<!-- Footer Start -->
<div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
    <div class="container py-5">
        <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5);">
            <div class="row g-4">
                <div class="col-lg-3">
                    <a href="#">
                        <h1 class="text-primary mb-0">FIXPOINT</h1>
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative mx-auto">
                        <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                        <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex justify-content-end pt-3">
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Copyright Start -->
<div class="container-fluid copyright bg-dark py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
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

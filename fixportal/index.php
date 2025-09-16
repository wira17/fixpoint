<?php
// Sertakan koneksi (kalau diperlukan untuk data dinamis)
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>FixPoint - Beranda</title>
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

        .hero {
            background: url('img/hero-bg.jpg') center center / cover no-repeat;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }

        .hero h1 {
            font-size: 60px;
            font-weight: 800;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .section {
            padding: 80px 0;
        }
    </style>
</head>

<body>

<!-- Navbar Start -->
<?php include 'navbar.php'; ?>

<!-- Navbar End -->

<!-- Hero Section Start -->
<div class="hero">
    <div class="container">
        <h1>Selamat Datang di FixPoint</h1>
        <p>Temukan peluang karir terbaik dan informasi terkini seputar perusahaan kami.</p>
        <a href="loker.php" class="btn btn-apply btn-lg">Lihat Lowongan</a>
    </div>
</div>
<!-- Hero Section End -->

<!-- About Section Start -->
<div class="container section">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="img/about.jpg" class="img-fluid rounded shadow-sm" alt="Tentang FixPoint">
        </div>
        <div class="col-lg-6">
            <h2>Tentang FixPoint</h2>
            <p>FixPoint adalah perusahaan yang bergerak di bidang teknologi dan inovasi, menyediakan peluang kerja dan pengembangan karir yang berkualitas. Kami berkomitmen menciptakan lingkungan kerja yang inspiratif dan mendukung pertumbuhan profesional setiap individu.</p>
            <a href="contact.php" class="btn btn-apply mt-3">Hubungi Kami</a>
        </div>
    </div>
</div>
<!-- About Section End -->

<!-- Fitur / Layanan Start -->
<div class="container section text-center">
    <h2 class="mb-5">Layanan Kami</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-secondary p-4">
                <i class="fas fa-briefcase fa-3x mb-3 text-primary"></i>
                <h5 class="card-title">Lowongan Kerja</h5>
                <p class="card-text">Temukan lowongan pekerjaan terbaru sesuai bidang dan keahlian Anda.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-secondary p-4">
                <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                <h5 class="card-title">Konsultasi Karir</h5>
                <p class="card-text">Dapatkan saran dan bimbingan untuk mengembangkan karir Anda lebih baik.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-secondary p-4">
                <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                <h5 class="card-title">Pelatihan & Workshop</h5>
                <p class="card-text">Ikuti pelatihan dan workshop untuk meningkatkan skill dan kemampuan profesional Anda.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

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

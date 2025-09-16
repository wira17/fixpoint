<?php
// Sertakan koneksi
include $_SERVER['DOCUMENT_ROOT'].'/fixpoint/dist/koneksi.php';

// Ambil data fasilitas
$result = $conn->query("SELECT * FROM fasilitas ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FixPoint - Fasilitas</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        body { 
    padding-top: 140px; 
    background-color: #D0DECE; /* Warna baru */
}


        .navbar-custom { background-color: #343a40; }
        .navbar-custom .navbar-brand { font-weight: 800; color: #fff; font-size: 28px; }
        .navbar-custom .navbar-brand:hover { color: #FFD700; }
        .navbar-custom .nav-link { color: #fff; font-weight: 600; margin: 0 10px; transition: 0.3s; }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active { color: #FFD700; }

        .fasilitas-section { max-width: 1320px; }
        .fasilitas-card { border-radius: 10px; font-size: 13px; overflow: hidden; }
        .fasilitas-card .card-img-top {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        .fasilitas-card .card-body { padding: 12px; }
        .fasilitas-card .card-title {
            font-size: 14px; font-weight: 700; color: #343a40; margin-bottom: 6px;
        }
        .fasilitas-card .card-footer {
            font-size: 11px; background-color: #fff; border-top: 1px solid #dee2e6;
        }
        .modal-foto {
            width: 100%; height: 260px; object-fit: cover; border-radius: 8px;
        }
        .modal-body { font-size: 13px; }
        .modal-body h6 { font-size: 14px; }

        @media (min-width: 992px) {
           .fasilitas-section { 
    max-width: 1320px; 
    background-color: #f8f9fa; /* Warna baru */
    padding: 20px; /* Bisa tambahkan padding agar terlihat rapi */
    border-radius: 1px; /* Opsional */
}

        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5 fasilitas-section">
    <h2 class="text-center mb-4">Fasilitas</h2>
    <div class="row g-3">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                    $foto = isset($row['foto']) ? trim($row['foto']) : '';
                    if ($foto === '' ) {
                        $fotoSrc = 'img/default-fasilitas.jpg';
                    } elseif (preg_match('/^(https?:\/\/|\/)/', $foto)) {
                        $fotoSrc = $foto;
                    } else {
                        $fotoSrc = '../fixpoint/dist/uploads/fasilitas/' . $foto;
                    }
                ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card fasilitas-card h-100 border-secondary shadow-sm">
                        <img src="<?= htmlspecialchars($fotoSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_fasilitas']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($row['nama_fasilitas']) ?></h5>
                            <?php if (!empty($row['keterangan'])): ?>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($row['keterangan'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-center">
                            <?php if($foto): ?>
                                <button class="btn btn-sm btn-primary lihatFoto" data-foto="<?= $foto ?>"><i class="fas fa-image"></i> Lihat Foto</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Tidak ada fasilitas saat ini.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Modal Lihat Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Foto Fasilitas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body text-center"><img id="imgFoto" src="" style="max-width:100%;"></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script>
$(document).ready(function(){
    $('.lihatFoto').click(function(){
        var foto = $(this).data('foto');
        $('#imgFoto').attr('src', 'uploads/fasilitas/' + foto);
        $('#modalFoto').modal('show');
    });
});
</script>
</body>
</html>

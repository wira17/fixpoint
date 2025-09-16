<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// ========================
// Cek akses user
// ========================
$query  = "SELECT 1 FROM akses_menu 
           JOIN menu ON akses_menu.menu_id = menu.id 
           WHERE akses_menu.user_id = '$user_id' 
           AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>
            alert('Anda tidak memiliki akses ke halaman ini.');
            window.location.href='dashboard.php';
          </script>";
    exit;
}

$success = "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>f.i.x.p.o.i.n.t</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .form-inline label { width: 120px; }
        .list-group-item strong { min-width: 150px; display: inline-block; }
    </style>
</head>
<body>
<div id="app">
    <div class="main-wrapper main-wrapper-1">

        <!-- Navbar & Sidebar -->
        <?php include 'navbar.php'; ?>
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <section class="section">
                <div class="section-body">

                    <div class="card">
                   <div class="card-header d-flex justify-content-between align-items-center">
    <h4>Profil Saya</h4>
    <button class="btn btn-light btn-sm" onclick="printProfil()">
        <i class="fas fa-print"></i> Print
    </button>
</div>


                        <div class="card-body">

                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-akun" data-toggle="tab" href="#akun" role="tab">Informasi Akun</a>
                                </li>
                             
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-pendidikan" data-toggle="tab" href="#pendidikan" role="tab">Kualifikasi & Pendidikan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-riwayat_pekerjaan" data-toggle="tab" href="#riwayat" role="tab">Riwayat Pekerjaan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-kesehatan" data-toggle="tab" href="#kesehatan" role="tab">Kesehatan & Asuransi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-dokumen" data-toggle="tab" href="#dokumen" role="tab">Dokumen Pendukung</a>
                                </li>
                                   <li class="nav-item">
                                    <a class="nav-link" id="tab-pribadi" data-toggle="tab" href="#pribadi" role="tab">Informasi Pribadi</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3" id="profileTabsContent">
                                <div class="tab-pane fade show active" id="akun" role="tabpanel">
                                    <?php include 'informasi_akun.php'; ?>
                                </div>
                          
                                <div class="tab-pane fade" id="pekerjaan" role="tabpanel">
                                    <?php include 'pekerjaan.php'; ?>
                                </div>

                                <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                                    <?php include 'riwayat_pendidikan.php'; ?>
                                </div>

                                <div class="tab-pane fade" id="riwayat" role="tabpanel">
                                    <?php include 'riwayat_pekerjaan.php'; ?>
                                </div>

                                <div class="tab-pane fade" id="kesehatan" role="tabpanel">
                                    <?php include 'riwayat_kesehatan.php'; ?>
                                </div>

                                <div class="tab-pane fade" id="dokumen" role="tabpanel">
                                    <?php include 'dokumen.php'; ?>
                                </div>
                                    <div class="tab-pane fade" id="pribadi" role="tabpanel">
                                    <?php include 'informasi_pribadi.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>

    </div>
</div>

<!-- JS -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>

<script>
    // Toggle Form Edit Akun
    function toggleForm() {
        const form = document.getElementById('formEdit');
        const view = document.getElementById('dataView');
        const editBtn = document.getElementById('editButton');
        const isEditing = form.style.display === 'block';
        form.style.display = isEditing ? 'none' : 'block';
        view.style.display = isEditing ? 'block' : 'none';
        editBtn.style.display = isEditing ? 'block' : 'none';
    }

    $(document).ready(function() {
        $('select[name="jabatan"]').select2({ placeholder: "Pilih Jabatan" });
        $('select[name="unit_kerja"]').select2({ placeholder: "Pilih Unit Kerja" });
        $('select[name="atasan_id"]').select2({ placeholder: "Pilih Atasan" });

        <?php if (!empty($success)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $success ?>',
            showConfirmButton: false,
            timer: 2000,
            position: 'center'
        });
        <?php endif; ?>
    });
</script>

<script>
function printProfil() {
    var content = document.querySelector('.tab-content').innerHTML;

    var printWindow = window.open('', '', 'height=800,width=1000');
    printWindow.document.write('<html><head><title>Print Profil</title>');
    printWindow.document.write('<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">');
    printWindow.document.write('<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">');
    printWindow.document.write('<style>h4{margin-top:20px;} .list-group-item{border:none;padding:5px 0;} .section-title{font-weight:bold;margin-top:15px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="container">');
    printWindow.document.write(content);
    printWindow.document.write('</div></body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>

</body>
</html>

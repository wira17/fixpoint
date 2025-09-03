<?php
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<script>alert('Anda belum login.'); window.location.href='login.php';</script>";
    exit;
}

$current_file = basename(__FILE__);

// Cek akses menu
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = ? AND menu.file_menu = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $current_file);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// --- Proses simpan data karyawan ---
if(isset($_POST['simpan'])){
    // Ambil semua data form
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $nik = $_POST['nik'] ?? '';
    $ttl = $_POST['ttl'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $status_pernikahan = $_POST['status_pernikahan'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_ktp = $_POST['no_ktp'] ?? '';
    $no_npwp = $_POST['no_npwp'] ?? '';
    $foto_karyawan = uploadFile($_FILES['foto_karyawan'] ?? null, 'foto_');

    $jabatan = $_POST['jabatan'] ?? '';
    $unit_kerja = $_POST['unit_kerja'] ?? '';
    $tgl_mulai = $_POST['tgl_mulai'] ?? '';
    $status_kepegawaian = $_POST['status_kepegawaian'] ?? '';
    $no_str_sip = $_POST['no_str_sip'] ?? '';
    $shift = $_POST['shift'] ?? '';
    $atasan_langsung = $_POST['atasan_langsung'] ?? '';

    $pendidikan_terakhir = $_POST['pendidikan_terakhir'] ?? '';
    $institusi_pendidikan = $_POST['institusi_pendidikan'] ?? '';
    $sertifikasi = $_POST['sertifikasi'] ?? '';
    $pelatihan = $_POST['pelatihan'] ?? '';

    $pengalaman_kerja = $_POST['pengalaman_kerja'] ?? '';
    $nama_institusi = $_POST['nama_institusi'] ?? '';
    $durasi_kerja = $_POST['durasi_kerja'] ?? '';
    $posisi_dijabat = $_POST['posisi_dijabat'] ?? '';

    $gol_darah = $_POST['gol_darah'] ?? '';
    $riwayat_penyakit = $_POST['riwayat_penyakit'] ?? '';
    $status_vaksin = $_POST['status_vaksin'] ?? '';
    $no_bpjs_kesehatan = $_POST['no_bpjs_kesehatan'] ?? '';
    $no_bpjs_ketenagakerjaan = $_POST['no_bpjs_ketenagakerjaan'] ?? '';
    $asuransi_tambahan = $_POST['asuransi_tambahan'] ?? '';

    // Dokumen pendukung
    $dok_ktp = uploadFile($_FILES['dok_ktp'] ?? null, 'ktp_');
    $dok_ijazah = uploadFile($_FILES['dok_ijazah'] ?? null, 'ijazah_');
    $dok_str_sip = uploadFile($_FILES['dok_str_sip'] ?? null, 'strsip_');
    $dok_sertifikat = uploadFile($_FILES['dok_sertifikat'] ?? null, 'sertifikat_');
    $dok_pengalaman = uploadFile($_FILES['dok_pengalaman'] ?? null, 'pengalaman_');
    $dok_pas_foto = uploadFile($_FILES['dok_pas_foto'] ?? null, 'pasfoto_');

    // Insert ke database
    $insert = $conn->prepare("INSERT INTO data_karyawan 
    (nama_lengkap, nik, ttl, jenis_kelamin, status_pernikahan, alamat, no_hp, email, no_ktp, no_npwp, foto_karyawan, 
     jabatan, unit_kerja, tgl_mulai, status_kepegawaian, no_str_sip, shift, atasan_langsung,
     pendidikan_terakhir, institusi_pendidikan, sertifikasi, pelatihan,
     pengalaman_kerja, nama_institusi, durasi_kerja, posisi_dijabat,
     gol_darah, riwayat_penyakit, status_vaksin, no_bpjs_kesehatan, no_bpjs_ketenagakerjaan, asuransi_tambahan,
     dok_ktp, dok_ijazah, dok_str_sip, dok_sertifikat, dok_pengalaman, dok_pas_foto,
     created_at) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())");

    $insert->bind_param("ssssssssssssssssssssssssssssssssssss", 
        $nama_lengkap, $nik, $ttl, $jenis_kelamin, $status_pernikahan, $alamat, $no_hp, $email, $no_ktp, $no_npwp, $foto_karyawan,
        $jabatan, $unit_kerja, $tgl_mulai, $status_kepegawaian, $no_str_sip, $shift, $atasan_langsung,
        $pendidikan_terakhir, $institusi_pendidikan, $sertifikasi, $pelatihan,
        $pengalaman_kerja, $nama_institusi, $durasi_kerja, $posisi_dijabat,
        $gol_darah, $riwayat_penyakit, $status_vaksin, $no_bpjs_kesehatan, $no_bpjs_ketenagakerjaan, $asuransi_tambahan,
        $dok_ktp, $dok_ijazah, $dok_str_sip, $dok_sertifikat, $dok_pengalaman, $dok_pas_foto
    );

    $_SESSION['flash_message'] = $insert->execute() ? "✅ Data karyawan berhasil disimpan." : "❌ Gagal menyimpan data: ".$insert->error;
    header("Location: data_karyawan.php");
    exit;
}

// Fungsi upload file
function uploadFile($file, $prefix){
    if($file && $file['error']==0){
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $path = 'uploads/'.$prefix.time().'.'.$ext;
        move_uploaded_file($file['tmp_name'], $path);
        return $path;
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Karyawan</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<style>
.flash-center {position:fixed;top:20%;left:50%;transform:translate(-50%,-50%);z-index:1050;min-width:300px;max-width:90%;text-align:center;padding:15px;border-radius:8px;font-weight:500;box-shadow:0 5px 15px rgba(0,0,0,0.3);}
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
<?php if(isset($_SESSION['flash_message'])): ?>
<div class="alert alert-info flash-center" id="flashMsg">
<?= htmlspecialchars($_SESSION['flash_message']) ?>
</div>
<?php unset($_SESSION['flash_message']); endif; ?>

<div class="card">
<div class="card-header">
<h4>Form Data Karyawan</h4>
</div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<ul class="nav nav-tabs" id="karyawanTab" role="tablist">
<li class="nav-item"><a class="nav-link active" id="data-pribadi-tab" data-toggle="tab" href="#data-pribadi" role="tab">Data Pribadi</a></li>
<li class="nav-item"><a class="nav-link" id="info-pekerjaan-tab" data-toggle="tab" href="#info-pekerjaan" role="tab">Informasi Pekerjaan</a></li>
<li class="nav-item"><a class="nav-link" id="pendidikan-tab" data-toggle="tab" href="#pendidikan" role="tab">Kualifikasi & Pendidikan</a></li>
<li class="nav-item"><a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">Riwayat Pekerjaan</a></li>
<li class="nav-item"><a class="nav-link" id="kesehatan-tab" data-toggle="tab" href="#kesehatan" role="tab">Kesehatan & Asuransi</a></li>
<li class="nav-item"><a class="nav-link" id="dokumen-tab" data-toggle="tab" href="#dokumen" role="tab">Dokumen Pendukung</a></li>
</ul>

<div class="tab-content mt-3">
<!-- Data Pribadi -->
<div class="tab-pane fade show active" id="data-pribadi" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" required></div>
<div class="form-group"><label>NIK</label><input type="text" name="nik" class="form-control" required></div>
<div class="form-group"><label>Tempat & Tanggal Lahir</label><input type="text" name="ttl" class="form-control" required></div>
<div class="form-group"><label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-control" required>
<option value="">Pilih</option>
<option value="Laki-laki">Laki-laki</option>
<option value="Perempuan">Perempuan</option>
</select></div>
<div class="form-group"><label>Status Pernikahan</label><input type="text" name="status_pernikahan" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>Alamat Lengkap</label><textarea name="alamat" class="form-control"></textarea></div>
<div class="form-group"><label>No. HP</label><input type="text" name="no_hp" class="form-control"></div>
<div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
<div class="form-group"><label>No. KTP</label><input type="text" name="no_ktp" class="form-control"></div>
<div class="form-group"><label>No. NPWP</label><input type="text" name="no_npwp" class="form-control"></div>
<div class="form-group"><label>Foto Karyawan</label><input type="file" name="foto_karyawan" class="form-control"></div>
</div>
</div>
</div>

<!-- Informasi Pekerjaan -->
<div class="tab-pane fade" id="info-pekerjaan" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Jabatan/Posisi</label><input type="text" name="jabatan" class="form-control"></div>
<div class="form-group"><label>Departemen/Unit Kerja</label><input type="text" name="unit_kerja" class="form-control"></div>
<div class="form-group"><label>Tanggal Mulai Bekerja</label><input type="date" name="tgl_mulai" class="form-control"></div>
<div class="form-group"><label>Status Kepegawaian</label><input type="text" name="status_kepegawaian" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>No. STR/SIP</label><input type="text" name="no_str_sip" class="form-control"></div>
<div class="form-group"><label>Jam Kerja / Shift</label><input type="text" name="shift" class="form-control"></div>
<div class="form-group"><label>Atasan Langsung</label><input type="text" name="atasan_langsung" class="form-control"></div>
</div>
</div>
</div>

<!-- Kualifikasi & Pendidikan -->
<div class="tab-pane fade" id="pendidikan" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Pendidikan Terakhir</label><input type="text" name="pendidikan_terakhir" class="form-control"></div>
<div class="form-group"><label>Institusi Pendidikan</label><input type="text" name="institusi_pendidikan" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>Sertifikasi Profesi</label><input type="text" name="sertifikasi" class="form-control"></div>
<div class="form-group"><label>Pelatihan yang Pernah Diikuti</label><textarea name="pelatihan" class="form-control"></textarea></div>
</div>
</div>
</div>

<!-- Riwayat Pekerjaan -->
<div class="tab-pane fade" id="riwayat" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Pengalaman Kerja Sebelumnya</label><textarea name="pengalaman_kerja" class="form-control"></textarea></div>
<div class="form-group"><label>Nama Institusi</label><input type="text" name="nama_institusi" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>Durasi Kerja</label><input type="text" name="durasi_kerja" class="form-control"></div>
<div class="form-group"><label>Posisi yang Dijabat</label><input type="text" name="posisi_dijabat" class="form-control"></div>
</div>
</div>
</div>

<!-- Kesehatan & Asuransi -->
<div class="tab-pane fade" id="kesehatan" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Golongan Darah</label><input type="text" name="gol_darah" class="form-control"></div>
<div class="form-group"><label>Riwayat Penyakit Penting</label><textarea name="riwayat_penyakit" class="form-control"></textarea></div>
<div class="form-group"><label>Status Vaksinasi</label><input type="text" name="status_vaksin" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>No. BPJS Kesehatan</label><input type="text" name="no_bpjs_kesehatan" class="form-control"></div>
<div class="form-group"><label>No. BPJS Ketenagakerjaan</label><input type="text" name="no_bpjs_ketenagakerjaan" class="form-control"></div>
<div class="form-group"><label>Asuransi Tambahan</label><input type="text" name="asuransi_tambahan" class="form-control"></div>
</div>
</div>
</div>

<!-- Dokumen Pendukung -->
<div class="tab-pane fade" id="dokumen" role="tabpanel">
<div class="row">
<div class="col-md-6">
<div class="form-group"><label>Scan KTP</label><input type="file" name="dok_ktp" class="form-control"></div>
<div class="form-group"><label>Ijazah & Transkrip Nilai</label><input type="file" name="dok_ijazah" class="form-control"></div>
<div class="form-group"><label>STR/SIP</label><input type="file" name="dok_str_sip" class="form-control"></div>
</div>
<div class="col-md-6">
<div class="form-group"><label>Sertifikat Pelatihan</label><input type="file" name="dok_sertifikat" class="form-control"></div>
<div class="form-group"><label>Surat Pengalaman Kerja</label><input type="file" name="dok_pengalaman" class="form-control"></div>
<div class="form-group"><label>Pas Foto</label><input type="file" name="dok_pas_foto" class="form-control"></div>
</div>
</div>
</div>

<button type="submit" name="simpan" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Simpan Data Karyawan</button>
</form>
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
<script>
$(document).ready(function(){
    setTimeout(function(){$("#flashMsg").fadeOut("slow");},3000);
});
</script>
</body>
</html>

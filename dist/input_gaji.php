<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

$current_file = basename(__FILE__); // 

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

$notif = '';

// ...lanjutkan kode lainnya seperti proses simpan


// Ambil data karyawan
$karyawan_result = mysqli_query($conn, "SELECT id, nama FROM users ORDER BY nama ASC");
$tahun_sekarang = date('Y');
$tahun_opsi = range($tahun_sekarang, $tahun_sekarang - 5);

// Ambil nilai default dari master table
$getNominal = function($table) use ($conn) {
  $res = mysqli_query($conn, "SELECT nominal FROM $table LIMIT 1");
  $row = mysqli_fetch_assoc($res);
  return $row ? $row['nominal'] : 0;
};

$default_gaji_pokok  = $getNominal('gaji_pokok');
$default_struktural  = $getNominal('struktural');
$default_fungsional  = $getNominal('fungsional');
$default_masa_kerja  = $getNominal('masa_kerja');

$default_bpjs_kes    = $getNominal('potongan_bpjs_kes');
$default_bpjs_jht    = $getNominal('potongan_bpjs_jht');
$default_bpjs_jp     = $getNominal('potongan_bpjs_tk_jp');
$default_dana_sosial = $getNominal('potongan_dana_sosial');

function getDropdownOptions($table, $conn) {
  $data = [];
  $result = mysqli_query($conn, "SELECT id, nominal FROM $table ORDER BY nominal ASC");
  while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }
  return $data;
}

$gajiPokokOptions     = getDropdownOptions('gaji_pokok', $conn);
$strukturalOptions    = getDropdownOptions('struktural', $conn);
$fungsionalOptions    = getDropdownOptions('fungsional', $conn);
$masaKerjaOptions     = getDropdownOptions('masa_kerja', $conn);
$bpjsKesOptions       = getDropdownOptions('potongan_bpjs_kes', $conn);
$bpjsJhtOptions       = getDropdownOptions('potongan_bpjs_jht', $conn);
$bpjsJpOptions        = getDropdownOptions('potongan_bpjs_tk_jp', $conn);
$danaSosialOptions    = getDropdownOptions('potongan_dana_sosial', $conn);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  // Ambil data
  $karyawan_id = $_POST['karyawan_id'];
  $periode = $_POST['periode'];
  $tahun = $_POST['tahun'];
  $gaji_pokok = str_replace(['Rp', '.', ','], '', $_POST['gaji_pokok']);

  $struktural = str_replace(['Rp', '.', ','], '', $_POST['struktural']);
  $fungsional = str_replace(['Rp', '.', ','], '', $_POST['fungsional']);
  $fungsional2 = str_replace(['Rp', '.', ','], '', $_POST['fungsional2']);
  $kesehatan = str_replace(['Rp', '.', ','], '', $_POST['kesehatan']);
  $masa_kerja = str_replace(['Rp', '.', ','], '', $_POST['masa_kerja']);
  $lembur = str_replace(['Rp', '.', ','], '', $_POST['lembur']);
  $lainya = str_replace(['Rp', '.', ','], '', $_POST['lainya']);

  $bpjs_kes = str_replace(['Rp', '.', ','], '', $_POST['bpjs_kes']);
  $bpjs_jht = str_replace(['Rp', '.', ','], '', $_POST['bpjs_jht']);
  $bpjs_jp = str_replace(['Rp', '.', ','], '', $_POST['bpjs_jp']);
  $dana_sosial = str_replace(['Rp', '.', ','], '', $_POST['dana_sosial']);
  $absensi = str_replace(['Rp', '.', ','], '', $_POST['absensi']);
  $angsuran = str_replace(['Rp', '.', ','], '', $_POST['angsuran']);

  $bruto = $gaji_pokok + $struktural + $fungsional + $fungsional2 + $kesehatan + $masa_kerja + $lembur + $lainya;
  $potongan_total = $bpjs_kes + $bpjs_jht + $bpjs_jp + $dana_sosial + $absensi + $angsuran;

// Ambil pph21 berdasarkan bruto (diasumsikan persentase dalam desimal, misal 0.10)
$pph_result = mysqli_query($conn, "SELECT * FROM pph21 WHERE $bruto >= gaji_min ORDER BY gaji_min DESC LIMIT 1");


$pph_row = mysqli_fetch_assoc($pph_result);
$pph_persen = $pph_row ? floatval($pph_row['persentase']) : 0.0;
$pph21 = $bruto * $pph_persen / 100;




  $gaji_bersih = $bruto - $pph21 - $potongan_total;

  $query = mysqli_query($conn, "INSERT INTO input_gaji (karyawan_id, periode, tahun, gaji_pokok, struktural, fungsional, fungsional2, kesehatan, masa_kerja, lembur, lainya, bruto, pph21, potongan_total, gaji_bersih, bpjs_kes, bpjs_jht, bpjs_jp, dana_sosial, absensi, angsuran) VALUES
    ('$karyawan_id', '$periode', '$tahun', '$gaji_pokok', '$struktural', '$fungsional', '$fungsional2', '$kesehatan', '$masa_kerja', '$lembur', '$lainya', '$bruto', '$pph21', '$potongan_total', '$gaji_bersih', '$bpjs_kes', '$bpjs_jht', '$bpjs_jp', '$dana_sosial', '$absensi', '$angsuran')");

 
  // Insert data dan redirect jika sukses
  if ($query) {
    $_SESSION['flash_message'] = "Data gaji berhasil disimpan.";
    header("Location: input_gaji.php");
    exit;
  } else {
    $notif = "Gagal menyimpan data.";
  }
} else {
  $pph_persen_form = 0.0; // default saat load halaman pertama
}

$pph_rules = [];
$result_pph = mysqli_query($conn, "SELECT * FROM pph21 ORDER BY gaji_min ASC");
while ($row = mysqli_fetch_assoc($result_pph)) {
  $pph_rules[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>f.i.x.p.o.i.n.t</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
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
              <h4>Input Gaji Karyawan</h4>
            </div>
            <div class="card-body">

              <?php if ($notif): ?>
                <div class="alert alert-danger"> <?= $notif ?> </div>
              <?php endif; ?>

              <?php if (isset($_SESSION['flash_message'])): ?>
                <div id="notif-toast" class="alert alert-success text-center">
                  <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                </div>
              <?php endif; ?>


<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" href="input_gaji.php">Input Gaji</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="data_gaji.php">Data Gaji</a>
  </li>
</ul>
<br>


  <form action="simpan_gaji.php" method="POST">


    <div class="card">
      <div class="card-body">
        <div class="row">
          <!-- KIRI: Data Karyawan -->
          <div class="col-md-4">
            <div class="form-group">
              <label>Nama Karyawan</label>
              <select name="karyawan_id" class="form-control" required>
                <option value="">Pilih Karyawan</option>
                <?php while ($k = mysqli_fetch_assoc($karyawan_result)): ?>
                  <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Periode Bulan</label>
              <select name="periode" class="form-control" required>
                <option value="">Pilih Bulan</option>
                <?php
                  $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                            'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                  foreach ($bulan as $b): ?>
                    <option value="<?= $b ?>"><?= $b ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Tahun</label>
              <select name="tahun" class="form-control" required>
                <option value="">Pilih Tahun</option>
                <?php foreach ($tahun_opsi as $th): ?>
                  <option value="<?= $th ?>"><?= $th ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Gaji Pokok</label>
              <select name="gaji_pokok" class="form-control" required>
                <option value="">Pilih Gaji Pokok</option>
                <?php foreach ($gajiPokokOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- TENGAH: Penerimaan -->
          <div class="col-md-4">
            <div class="form-group">
              <label>Struktural</label>
              <select name="struktural" class="form-control" required>
                <option value="">Pilih Struktural</option>
                <?php foreach ($strukturalOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Fungsional</label>
              <select name="fungsional" class="form-control" required>
                <option value="">Pilih Fungsional</option>
                <?php foreach ($fungsionalOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Fungsional 2</label>
              <input type="number" name="fungsional2" class="form-control">
            </div>

            <div class="form-group">
              <label>Masa Kerja</label>
              <select name="masa_kerja" class="form-control" required>
                <option value="">Pilih Masa Kerja</option>
                <?php foreach ($masaKerjaOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Lainnya</label>
              <input type="number" name="lainya" class="form-control">
            </div>

            <div class="form-group">
              <label>Lembur</label>
              <input type="number" name="lembur" class="form-control">
            </div>
          </div>

          <!-- KANAN: Potongan -->
          <div class="col-md-4">
            <div class="form-group">
              <label>BPJS Kesehatan</label>
              <select name="bpjs_kes" class="form-control" required>
                <option value="">Pilih BPJS Kesehatan</option>
                <?php foreach ($bpjsKesOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>BPJS TK JHT</label>
              <select name="bpjs_jht" class="form-control" required>
                <option value="">Pilih BPJS TK JHT</option>
                <?php foreach ($bpjsJhtOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>BPJS TK JP</label>
              <select name="bpjs_jp" class="form-control" required>
                <option value="">Pilih BPJS TK JP</option>
                <?php foreach ($bpjsJpOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Dana Sosial</label>
              <select name="dana_sosial" class="form-control" required>
                <option value="">Pilih Dana Sosial</option>
                <?php foreach ($danaSosialOptions as $opt): ?>
                  <option value="<?= $opt['nominal'] ?>">Rp <?= number_format($opt['nominal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Potongan Absensi</label>
              <input type="number" name="absensi" class="form-control">
            </div>

            <div class="form-group">
              <label>Angsuran Pinjaman</label>
              <input type="number" name="angsuran" class="form-control">
            </div>
          </div>
        </div>
      </div>
    </div>


  <hr>
  <div class="row mt-3">
    <div class="col-md-4">
      <div class="form-group">
        <label>Total Penerimaan (Bruto)</label>
        <input type="text" id="bruto" class="form-control" readonly>
      </div>
    </div>
    <div class="col-md-4">
   <div class="form-group">
    <label>Estimasi PPh21</label>
    <input type="text" id="pph21" class="form-control" readonly>
  </div>
  </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>Total Potongan</label>
        <input type="text" id="potongan_total" class="form-control" readonly>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Gaji Bersih</label>
        <input type="text" id="gaji_bersih" class="form-control" readonly>
      </div>
    </div>

  </div>



    <!-- Tombol Simpan -->
    <div class="text-center mt-3">
      <button type="submit" name="simpan" class="btn btn-success px-4">
        <i class="fas fa-save mr-1"></i> Simpan
      </button>
    </div>
  </form>




          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<!-- Notifikasi Toast -->
<!-- Toast Notifikasi -->
<script>
  $(document).ready(function () {
    $('#notif-toast').fadeIn(300).delay(2000).fadeOut(500);
  });
</script>

<!-- Hitung Otomatis Gaji -->
<script>
  // Fungsi untuk membersihkan input dan mengubah ke angka
  function parseNumber(val) {
    if (typeof val === 'undefined' || val === null) return 0;
    return parseFloat(val.toString().replace(/[^\d]/g, '')) || 0;
  }

  // Fungsi untuk menentukan persentase PPh21 berdasarkan bruto (sama dengan backend)
function getPphPersen(bruto) {
  let persen = 0;
  for (const rule of pphRules) {
    const min = parseFloat(rule.gaji_min);
    if (bruto >= min) {
      persen = parseFloat(rule.persentase) / 100;
    }
  }
  return persen;
}



  // Fungsi untuk update ringkasan gaji
  function updateRingkasan() {
    const getVal = name => parseNumber($(`[name="${name}"]`).val());

    const bruto =
      getVal("gaji_pokok") +
      getVal("struktural") +
      getVal("fungsional") +
      getVal("fungsional2") +
      getVal("kesehatan") +
      getVal("masa_kerja") +
      getVal("lembur") +
      getVal("lainya");

    const potongan =
      getVal("bpjs_kes") +
      getVal("bpjs_jht") +
      getVal("bpjs_jp") +
      getVal("dana_sosial") +
      getVal("absensi") +
      getVal("angsuran");

    // Ambil persentase PPh21 dari fungsi sesuai bruto
    let pph_persen = getPphPersen(bruto);

    // Hitung PPh21
    let pph21 = bruto * pph_persen;

    const gajiBersih = bruto - potongan - pph21;

    // Jika ada hidden input #pph_persen, update nilainya juga
    if ($('#pph_persen').length) {
      $('#pph_persen').val(pph_persen);
    }

    // Tampilkan hasil ringkasan dengan format Rupiah
    $('#bruto').val("Rp " + bruto.toLocaleString('id-ID'));
    $('#potongan_total').val("Rp " + potongan.toLocaleString('id-ID'));
    $('#pph21').val("Rp " + pph21.toLocaleString('id-ID'));
    $('#gaji_bersih').val("Rp " + gajiBersih.toLocaleString('id-ID'));
  }

  // Trigger update otomatis saat input berubah
  $(document).ready(function () {
    const inputFields = [
      "gaji_pokok", "struktural", "fungsional", "fungsional2", "kesehatan", "masa_kerja", "lembur", "lainya",
      "bpjs_kes", "bpjs_jht", "bpjs_jp", "dana_sosial", "absensi", "angsuran"
    ];

    inputFields.forEach(field => {
      $(`[name="${field}"]`).on("input", updateRingkasan);
    });

    // Jalankan saat halaman pertama kali dimuat
    updateRingkasan();
  });


</script>
<script>
  const pphRules = <?= json_encode($pph_rules); ?>;
</script>


</body>
</html>
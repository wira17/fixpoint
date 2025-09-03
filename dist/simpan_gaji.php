<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  function bersihkan($val) {
    return intval(str_replace(['Rp', '.', ','], '', $val));
  }

  // Ambil data input
  $karyawan_id   = $_POST['karyawan_id'];
  $periode       = $_POST['periode'];
  $tahun         = $_POST['tahun'];

  // Penerimaan
  $gaji_pokok    = bersihkan($_POST['gaji_pokok']);
  $struktural    = bersihkan($_POST['struktural']);
  $fungsional    = bersihkan($_POST['fungsional']);
  $fungsional2   = bersihkan($_POST['fungsional2']);
  $kesehatan     = bersihkan($_POST['kesehatan']);
  $masa_kerja    = bersihkan($_POST['masa_kerja']);
  $lembur        = bersihkan($_POST['lembur']);
  $lainya        = bersihkan($_POST['lainya']);

  // Potongan
  $bpjs_kes      = bersihkan($_POST['bpjs_kes']);
  $bpjs_jht      = bersihkan($_POST['bpjs_jht']);
  $bpjs_jp       = bersihkan($_POST['bpjs_jp']);
  $dana_sosial   = bersihkan($_POST['dana_sosial']);
  $absensi       = bersihkan($_POST['absensi']);
  $angsuran      = bersihkan($_POST['angsuran']);

  // Hitung Bruto dan Potongan
  $bruto = $gaji_pokok + $struktural + $fungsional + $fungsional2 + $kesehatan + $masa_kerja + $lembur + $lainya;
  $potongan_total = $bpjs_kes + $bpjs_jht + $bpjs_jp + $dana_sosial + $absensi + $angsuran;

  // Hitung PPh21
  $pph_result = mysqli_query($conn, "SELECT * FROM pph21 WHERE $bruto >= gaji_min ORDER BY gaji_min DESC LIMIT 1");
  $pph_row = mysqli_fetch_assoc($pph_result);
  $pph_persen = $pph_row ? floatval($pph_row['persentase']) : 0.0;
  $pph21 = $bruto * $pph_persen / 100;

  // Gaji bersih
  $gaji_bersih = $bruto - $pph21 - $potongan_total;

  // Ambil ID user yang sedang login (petugas)
  $user_input = $_SESSION['user_id'];

  // Cek duplikat data gaji
  $cek = mysqli_query($conn, "SELECT * FROM input_gaji WHERE karyawan_id='$karyawan_id' AND periode='$periode' AND tahun='$tahun'");
  if (mysqli_num_rows($cek) > 0) {
    $_SESSION['flash_message'] = "⚠️ Gaji untuk karyawan dan periode ini sudah ada.";
    header("Location: input_gaji.php");
    exit;
  }

  // Simpan ke database
 $created_at = date('Y-m-d H:i:s'); // waktu sekarang

$query = mysqli_query($conn, "INSERT INTO input_gaji (
    karyawan_id, periode, tahun, 
    gaji_pokok, struktural, fungsional, fungsional2, kesehatan, masa_kerja, lembur, lainya, 
    bruto, pph21, potongan_total, gaji_bersih, 
    bpjs_kes, bpjs_jht, bpjs_jp, dana_sosial, absensi, angsuran,
    user_input, created_at
  ) VALUES (
    '$karyawan_id', '$periode', '$tahun',
    '$gaji_pokok', '$struktural', '$fungsional', '$fungsional2', '$kesehatan', '$masa_kerja', '$lembur', '$lainya',
    '$bruto', '$pph21', '$potongan_total', '$gaji_bersih',
    '$bpjs_kes', '$bpjs_jht', '$bpjs_jp', '$dana_sosial', '$absensi', '$angsuran',
    '$user_input', '$created_at'
  )");


  if ($query) {
    $_SESSION['flash_message'] = "✅ Data gaji berhasil disimpan.";
  } else {
    $_SESSION['flash_message'] = "❌ Gagal menyimpan data.";
  }

  header("Location: input_gaji.php");
  exit;
}
?>

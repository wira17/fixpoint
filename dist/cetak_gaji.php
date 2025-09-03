<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

$id_gaji = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data gaji & karyawan
$query = "
  SELECT g.*, u.nama AS nama_karyawan, u.nik, u.jabatan, u.unit_kerja,
         petugas.nama AS petugas_input
  FROM input_gaji g
  LEFT JOIN users u ON g.karyawan_id = u.id
  LEFT JOIN users petugas ON g.user_input = petugas.id
  WHERE g.id = $id_gaji
";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
  echo "Data gaji tidak ditemukan.";
  exit;
}
$data = mysqli_fetch_assoc($result);

// Ambil data perusahaan
$perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan ORDER BY id DESC LIMIT 1");
$instansi = mysqli_fetch_assoc($perusahaan);

// Hitung total penerimaan & potongan
$total_penerimaan = $data['gaji_pokok'] + $data['struktural'] + $data['fungsional'] +
                    $data['fungsional2'] + $data['kesehatan'] + $data['masa_kerja'] +
                    $data['lembur'] + $data['lainya'];

$total_potongan = $data['pph21'] + $data['bpjs_kes'] + $data['bpjs_jht'] +
                  $data['bpjs_jp'] + $data['dana_sosial'] + $data['absensi'] +
                  $data['angsuran'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Slip Gaji - <?= htmlspecialchars($data['nama_karyawan']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 10px; font-size: 12px; }
    .slip { max-width: 700px; margin: auto; border: 1px solid #000; padding: 15px 20px; }
    .kop-surat { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; position: relative; }
    .kop-surat img { height: 50px; position: absolute; top: 0; left: 0; }
    .kop-text h3 { margin: 0; font-size: 16px; font-weight: bold; }
    .kop-text p { margin: 0; font-size: 12px; }
    h2 { text-align: center; margin: 15px 0; font-size: 15px; text-decoration: underline; }
    .karyawan-info { margin-bottom: 15px; }
    .karyawan-info table { width: 100%; }
    .karyawan-info td { padding: 3px; }
    .row { display: flex; justify-content: space-between; gap: 20px; }
    .col { flex: 1; }
    .col table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .col th { background: #f0f0f0; text-align: left; padding: 5px; border-bottom: 1px solid #ccc; }
    .col td { padding: 4px 5px; }
    .text-right { text-align: right; }
    .total-row { display: flex; justify-content: space-between; margin-top: 10px; font-weight: bold; font-size: 13px; }
    .total-box { margin-top: 10px; border-top: 2px solid #000; padding-top: 10px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between; }
  </style>
</head>
<body onload="window.print()">
  <div class="slip">
    <div class="kop-surat">
      <?php if (!empty($instansi['logo'])): ?>
        <img src="dist/uploads/logo/<?= $instansi['logo'] ?>" alt="Logo">
      <?php endif; ?>
      <div class="kop-text">
        <h3><?= strtoupper($instansi['nama_perusahaan']) ?></h3>
        <p><?= $instansi['alamat'] ?>, <?= $instansi['kota'] ?>, <?= $instansi['provinsi'] ?></p>
        <p>Telp: <?= $instansi['kontak'] ?> | Email: <?= $instansi['email'] ?></p>
      </div>
    </div>

    <h2>SLIP GAJI KARYAWAN</h2>

    <div class="karyawan-info">
      <table>
        <tr>
          <td><strong>Nama</strong></td>
          <td>: <?= htmlspecialchars($data['nama_karyawan']) ?></td>
          <td><strong>Periode</strong></td>
          <td>: <?= $data['periode'] ?> <?= $data['tahun'] ?></td>
        </tr>
        <tr>
          <td><strong>NIK</strong></td>
          <td>: <?= $data['nik'] ?></td>
          <td><strong>Unit</strong></td>
          <td>: <?= $data['unit_kerja'] ?></td>
        </tr>
        <tr>
          <td><strong>Jabatan</strong></td>
          <td>: <?= $data['jabatan'] ?></td>
          <td><strong>Tanggal Input</strong></td>
          <td>: <?= date('d/m/Y H:i', strtotime($data['created_at'])) ?></td>
        </tr>
      </table>
    </div>

    <div class="row">
      <!-- Penerimaan -->
      <div class="col">
        <table>
          <thead><tr><th colspan="2">PENERIMAAN</th></tr></thead>
          <tbody>
            <tr><td>Gaji Pokok</td><td class="text-right">Rp <?= number_format($data['gaji_pokok'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Struktural</td><td class="text-right">Rp <?= number_format($data['struktural'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Fungsional</td><td class="text-right">Rp <?= number_format($data['fungsional'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Fungsional 2</td><td class="text-right">Rp <?= number_format($data['fungsional2'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Kesehatan</td><td class="text-right">Rp <?= number_format($data['kesehatan'], 0, ',', '.') ?></td></tr>
            <tr><td>Masa Kerja</td><td class="text-right">Rp <?= number_format($data['masa_kerja'], 0, ',', '.') ?></td></tr>
            <tr><td>Lembur</td><td class="text-right">Rp <?= number_format($data['lembur'], 0, ',', '.') ?></td></tr>
            <tr><td>Lainnya</td><td class="text-right">Rp <?= number_format($data['lainya'], 0, ',', '.') ?></td></tr>
            <tr><td><strong>Total</strong></td><td class="text-right"><strong>Rp <?= number_format($total_penerimaan, 0, ',', '.') ?></strong></td></tr>
          </tbody>
        </table>
      </div>

      <!-- Potongan -->
      <div class="col">
        <table>
          <thead><tr><th colspan="2">POTONGAN</th></tr></thead>
          <tbody>
            <tr><td>PPH 21</td><td class="text-right">Rp <?= number_format($data['pph21'], 0, ',', '.') ?></td></tr>
            <tr><td>BPJS Kesehatan</td><td class="text-right">Rp <?= number_format($data['bpjs_kes'], 0, ',', '.') ?></td></tr>
            <tr><td>BPJS JHT</td><td class="text-right">Rp <?= number_format($data['bpjs_jht'], 0, ',', '.') ?></td></tr>
            <tr><td>BPJS JP</td><td class="text-right">Rp <?= number_format($data['bpjs_jp'], 0, ',', '.') ?></td></tr>
            <tr><td>Dana Sosial</td><td class="text-right">Rp <?= number_format($data['dana_sosial'], 0, ',', '.') ?></td></tr>
            <tr><td>Absensi</td><td class="text-right">Rp <?= number_format($data['absensi'], 0, ',', '.') ?></td></tr>
            <tr><td>Angsuran</td><td class="text-right">Rp <?= number_format($data['angsuran'], 0, ',', '.') ?></td></tr>
            <tr><td><strong>Total</strong></td><td class="text-right"><strong>Rp <?= number_format($total_potongan, 0, ',', '.') ?></strong></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="total-box">
      <span>Total Diterima</span>
      <span>Rp <?= number_format($data['gaji_bersih'], 0, ',', '.') ?></span>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 50px;">
      <div style="text-align: center; width: 45%; min-height: 120px;">
        <p>Karyawan,</p>
        <div style="margin-top: 60px;"><strong>( <?= htmlspecialchars($data['nama_karyawan']) ?> )</strong></div>
      </div>
      <div style="text-align: center; width: 45%; min-height: 120px;">
        <p><?= htmlspecialchars($instansi['kota']) ?>, <?= date('d-m-Y') ?></p>
        <p><strong>Bendahara Gaji</strong></p>
        <div style="margin-top: 30px;"><strong>( <?= htmlspecialchars($data['petugas_input']) ?> )</strong></div>
      </div>
    </div>
  </div>
</body>
</html>

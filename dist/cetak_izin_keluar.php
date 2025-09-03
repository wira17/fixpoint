<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include 'koneksi.php';

// Require library QR Code
include '../phpqrcode/qrlib.php';


if (!isset($_GET['id'])) {
    die('ID izin keluar tidak ditemukan.');
}

$id = intval($_GET['id']);

// Query data izin keluar termasuk nik atasan dan sdm
$query = mysqli_query($conn, "
    SELECT ik.*, 
           u.nik, u.nama, u.jabatan, u.unit_kerja,
           atasan.nama AS nama_atasan, atasan.nik AS nik_atasan,
           sdm.nama AS nama_sdm, sdm.nik AS nik_sdm,
           ik.status_atasan
    FROM izin_keluar ik
    JOIN users u ON ik.user_id = u.id
    LEFT JOIN users atasan ON ik.acc_oleh_atasan = atasan.id
    LEFT JOIN users sdm ON ik.acc_oleh_sdm = sdm.id
    WHERE ik.id = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die('Data izin keluar tidak ditemukan.');
}

// Ambil data perusahaan
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Fungsi generate QR Code base64
function generateQrBase64($text) {
    ob_start();
    QRcode::png($text, null, QR_ECLEVEL_L, 3); // output PNG ke output buffer
    $imageString = ob_get_contents();
    ob_end_clean();
    return 'data:image/png;base64,' . base64_encode($imageString);
}

// Generate QR untuk atasan dan sdm (gunakan NIK atau nama sesuai kebutuhan)
$qr_atasan = !empty($data['nik_atasan']) ? generateQrBase64($data['nik_atasan']) : '';
$qr_sdm = !empty($data['nik_sdm']) ? generateQrBase64($data['nik_sdm']) : '';

// HTML dan CSS (saya sesuaikan bagian footer untuk menampilkan barcode)
$html = '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
  .ticket {
    border: 2px dashed #000;
    padding: 15px;
    width: 100%;
    box-sizing: border-box;
    position: relative;
  }
  .header {
    text-align: center;
    margin-bottom: 10px;
  }
  .header .nama-perusahaan {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
  }
  .header .alamat {
    font-size: 10px;
  }
  .title {
    text-align: center;
    font-size: 13px;
    font-weight: bold;
    margin: 10px 0;
    text-transform: uppercase;
  }
  .info {
    margin: 10px 0;
  }
  .info div {
    margin-bottom: 4px;
  }
  .label {
    display: inline-block;
    width: 120px;
    font-weight: bold;
  }
  .keterangan {
    margin-top: 10px;
    font-size: 10px;
  }

  /* Footer signature */
  .footer-table {
    width: 100%;
    margin-top: 18px;
    font-size: 10px;
    border-collapse: collapse;
  }
  .footer-table td {
    vertical-align: top;
    padding: 0 20px;
    width: 50%;
    text-align: center;
  }
  .footer-label {
    font-weight: bold;
    padding-bottom: 8px;
  }
  .footer-spacer {
    height: 40px;
  }
  .sig-line {
    display: block;
    border-bottom: 1px solid #000;
    padding: 4px 4px 2px 4px;
    min-width: 160px;
    margin: 0 auto;
  }
  .footer-name, .footer-nik {
    font-weight: bold;
    margin: 0;
  }
  .footer-nik {
    font-size: 9px;
    margin-top: 2px;
  }
  .barcode {
    margin-top: 4px;
    width: 80px;
    height: 80px;
  }
</style>

<div class="ticket">
  <div class="header">
    <div class="nama-perusahaan">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
    <div class="alamat">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
    Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
  </div>

  <div class="title">Formulir Izin Keluar</div>

<div class="info">
    <div><span class="label">Tanggal Keluar</span>: ' . date('d-m-Y', strtotime($data['tanggal'])) . '</div>
    <div><span class="label">Jam Keluar</span>: ' . htmlspecialchars($data['jam_keluar']) . '</div>
    <div><span class="label">Jam Kembali</span>: ' . htmlspecialchars($data['jam_kembali']) . '</div>
    <div><span class="label">Update Jam Kembali</span>: ' . htmlspecialchars($data['jam_kembali_real']) . '</div>
    <div><span class="label">NIK</span>: ' . htmlspecialchars($data['nik']) . '</div>
    <div><span class="label">Nama</span>: ' . htmlspecialchars($data['nama']) . '</div>
    <div><span class="label">Jabatan</span>: ' . htmlspecialchars($data['jabatan']) . '</div>
    <div><span class="label">Unit</span>: ' . htmlspecialchars($data['unit_kerja']) . '</div>
    <div><span class="label">Status Atasan</span>: ' . htmlspecialchars(ucfirst($data['status_atasan'])) . '</div>
</div>


  <div class="keterangan"><strong>Keperluan:</strong><br>' . nl2br(htmlspecialchars($data['keperluan'])) . '</div>

  <!-- Footer: tabel tanda tangan dengan barcode -->
  <table class="footer-table">
    <tr>
      <td>
        <div class="footer-label">Atasan Langsung</div>
      </td>
      <td>
        <div class="footer-label">Bagian SDM</div>
      </td>
    </tr>
    <tr>
      <td class="footer-spacer"></td>
      <td class="footer-spacer"></td>
    </tr>
    <tr>
      <td>
        <span class="sig-line">' . (!empty($data['nama_atasan']) ? htmlspecialchars($data['nama_atasan']) : '&nbsp;') . '</span>
        <p class="footer-nik">' . (!empty($data['nik_atasan']) ? 'NIK: ' . htmlspecialchars($data['nik_atasan']) : '') . '</p>
      </td>
      <td>
        <span class="sig-line">' . (!empty($data['nama_sdm']) ? htmlspecialchars($data['nama_sdm']) : '&nbsp;') . '</span>
        <p class="footer-nik">' . (!empty($data['nik_sdm']) ? 'NIK: ' . htmlspecialchars($data['nik_sdm']) : '') . '</p>
      </td>
    </tr>
  </table>
</div>
';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A5', 'portrait');
$dompdf->render();

// Optional watermark
$canvas = $dompdf->getCanvas();
if (method_exists($canvas, 'set_opacity')) {
    $canvas->set_opacity(0.07);
}
$imagePath = 'assets/watermark.jpg';
if (file_exists($imagePath)) {
    $width = 400;
    $height = 200;
    $x = ($canvas->get_width() - $width) / 2;
    $y = ($canvas->get_height() - $height) / 2;
    $canvas->image($imagePath, $x, $y, $width, $height);
}

$dompdf->stream('izin_keluar_' . $data['id'] . '.pdf', ['Attachment' => false]);

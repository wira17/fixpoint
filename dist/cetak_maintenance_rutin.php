<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include 'koneksi.php';

function tgl_indo($tanggal, $jam = false) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[(int)date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    $waktu = $jam ? ' ' . date('H:i', strtotime($tanggal)) : '';
    return "$tgl $bln $thn$waktu";
}

if (!isset($_GET['id'])) {
    die('ID tidak ditemukan.');
}

$id = intval($_GET['id']);

// Ambil data maintenance
$q = mysqli_query($conn, "SELECT m.*, u.nama AS teknisi_nama, b.nama_barang, b.kode_barang,
                                u.nik, u.jabatan, u.unit_kerja
                         FROM maintanance_rutin m
                         JOIN users u ON m.user_id = u.id
                         JOIN barang b ON m.barang_id = b.id
                         WHERE m.id = '$id'");

$data = mysqli_fetch_assoc($q);

if (!$data) {
    die('Data tidak ditemukan.');
}

// Ambil data perusahaan
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

$tgl_input = tgl_indo($data['waktu_input'], true);

$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .kop img { width: 80px; }
  .kop .nama-perusahaan { font-size: 16px; font-weight: bold; text-transform: uppercase; }
  .kop .alamat { font-size: 12px; }
  hr { border: 1px solid #000; margin: 10px 0; }
  .judul { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin-top: 20px; }
  .nomor { text-align: center; margin-bottom: 20px; font-size: 12px; }
  .isi { text-align: justify; margin-top: 20px; }
  .info-table td { vertical-align: top; padding: 3px; }
  .signature { margin-top: 50px; width: 100%; } 
</style>

<div class="kop" style="text-align: center;">
  <img src="../uploads/' . htmlspecialchars($perusahaan['logo']) . '" alt="Logo"><br>
  <div class="nama-perusahaan">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div class="alamat">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
  Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
</div>
<hr>

<div class="judul">LAPORAN MAINTENANCE RUTIN PERANGKAT</div>
<div class="nomor">
  ID Laporan: <strong>#' . htmlspecialchars($data['id']) . '</strong><br>
</div>

<div class="isi">
  Telah dilakukan pengecekan dan pemeliharaan rutin terhadap perangkat berikut:
</div>

<table class="info-table" style="margin-top: 15px;">
  <tr><td width="150">Tanggal Maintenance</td><td>: ' . $tgl_input . ' WIB</td></tr>
  <tr><td>Teknisi</td><td>: ' . htmlspecialchars($data['teknisi_nama']) . '</td></tr>
  <tr><td>Perangkat</td><td>: ' . htmlspecialchars($data['kode_barang']) . ' - ' . htmlspecialchars($data['nama_barang']) . '</td></tr>
  <tr><td>Kondisi Fisik</td><td>: ' . htmlspecialchars($data['kondisi_fisik']) . '</td></tr>
  <tr><td>Fungsi Perangkat</td><td>: ' . htmlspecialchars($data['fungsi_perangkat']) . '</td></tr>
</table>

<div class="isi" style="margin-top: 15px;">
  <strong>Catatan:</strong><br>' . nl2br(htmlspecialchars($data['catatan'])) . '
</div>

<div class="isi" style="margin-top: 20px;">
  Demikian laporan ini dibuat untuk dokumentasi dan tindak lanjut bila diperlukan.
</div>

<table class="signature" style="width: 100%; text-align: center; margin-top: 50px;">
  <tr>
    <td style="width: 33%">Teknisi</td>
    <td style="width: 33%">Mengetahui</td>
    <td style="width: 33%">Pihak Terkait</td>
  </tr>
  <tr><td style="height: 60px;"></td><td></td><td></td></tr>
  <tr>
    <td><strong><u>' . htmlspecialchars($data['teknisi_nama']) . '</u></strong><br>NIK: ' . htmlspecialchars($data['nik']) . '</td>
    <td><strong><u>__________________</u></strong></td>
    <td><strong><u>__________________</u></strong></td>
  </tr>
</table>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Watermark
$canvas = $dompdf->getCanvas();
$canvas->set_opacity(0.07);
$watermarkPath = 'assets/watermark.jpg';
if (file_exists($watermarkPath)) {
    $width = 500;
    $height = 300;
    $x = ($canvas->get_width() - $width) / 2;
    $y = ($canvas->get_height() - $height) / 4;
    $canvas->image($watermarkPath, $x, $y, $width, $height);
}

$dompdf->stream('maintenance_rutin_' . $data['id'] . '.pdf', ['Attachment' => false]);
?>
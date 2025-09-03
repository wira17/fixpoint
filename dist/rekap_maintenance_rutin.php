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

$dari = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';

if (!$dari || !$sampai) {
    die('Filter tanggal tidak lengkap.');
}

// Ambil data perusahaan
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);
$logoPath = realpath('dist/images/logo/' . $perusahaan['logo']);
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
}

// Ambil data maintenance
$query = mysqli_query($conn, "
    SELECT mr.*, db.nama_barang, db.lokasi
    FROM maintanance_rutin mr
    JOIN data_barang_it db ON mr.barang_id = db.id
    WHERE DATE(mr.waktu_input) BETWEEN '$dari' AND '$sampai'
    ORDER BY mr.waktu_input DESC
");

$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 10px; }
  table { border-collapse: collapse; width: 100%; margin-top: 10px; }
  th, td { border: 1px solid #000; padding: 4px; }
  th { background-color: #333; color: #fff; }
  .text-success { color: green; font-weight: bold; }
  .text-warning { color: orange; font-weight: bold; }
  .text-danger { color: red; font-weight: bold; }
</style>

<div style="text-align:center;">
  <img src="' . $logoBase64 . '" alt="Logo" style="width:60px;"><br>
  <div style="font-size:14px;font-weight:bold;">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div style="font-size:10px;">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
  Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
</div>

<hr>

<h3 style="text-align:center; margin:0;">Laporan Maintenance Rutin</h3>
<p style="text-align:center; margin:0;">Periode: ' . tgl_indo($dari) . ' - ' . tgl_indo($sampai) . '</p>

<table>
<thead>
<tr>
  <th>No</th>
  <th>Nama Barang</th>
  <th>Lokasi</th>
  <th>Kondisi Fisik</th>
  <th>Fungsi Perangkat</th>
  <th>Catatan</th>
  <th>Teknisi</th>
  <th>Waktu</th>
  <th>Status</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $waktu_input = strtotime($row['waktu_input']);
    $selisih_bulan = floor((time() - $waktu_input) / (30 * 24 * 60 * 60));

    if ($selisih_bulan < 1) {
        $status_text = 'Aman';
        $status_class = 'text-success';
    } elseif ($selisih_bulan < 2) {
        $status_text = 'Persiapkan Maintenance';
        $status_class = 'text-warning';
    } else {
        $status_text = 'Wajib Maintenance';
        $status_class = 'text-danger';
    }

    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($row['nama_barang']) . '</td>
        <td>' . htmlspecialchars($row['lokasi']) . '</td>
        <td>' . htmlspecialchars($row['kondisi_fisik']) . '</td>
        <td>' . htmlspecialchars($row['fungsi_perangkat']) . '</td>
        <td>' . htmlspecialchars($row['catatan']) . '</td>
        <td>' . htmlspecialchars($row['nama_teknisi']) . '</td>
        <td>' . tgl_indo($row['waktu_input'], true) . '</td>
        <td class="' . $status_class . '">' . $status_text . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Watermark opsional
$canvas = $dompdf->getCanvas();
$canvas->set_opacity(0.05);
$watermarkPath = 'assets/watermark.jpg';
if (file_exists($watermarkPath)) {
    $width = 500;
    $height = 300;
    $x = ($canvas->get_width() - $width) / 2;
    $y = ($canvas->get_height() - $height) / 2;
    $canvas->image($watermarkPath, $x, $y, $width, $height);
}

$dompdf->stream('rekap_maintenance_rutin.pdf', ['Attachment' => false]);

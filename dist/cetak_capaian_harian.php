<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include 'koneksi.php';

// Fungsi format tanggal
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

// Ambil parameter filter
$keyword     = $_GET['keyword'] ?? "";
$unit_filter = $_GET['unit_filter'] ?? "";
$tgl_awal    = $_GET['tgl_awal'] ?? "";
$tgl_akhir   = $_GET['tgl_akhir'] ?? "";

// Escape untuk keamanan SQL
$keyword_sql     = mysqli_real_escape_string($conn, $keyword);
$unit_filter_sql = mysqli_real_escape_string($conn, $unit_filter);
$tgl_awal_sql    = mysqli_real_escape_string($conn, $tgl_awal);
$tgl_akhir_sql   = mysqli_real_escape_string($conn, $tgl_akhir);

// Query data capaian harian dengan filter
$query = "SELECT ch.*, mi.nama_indikator, u.nama AS user_nama
          FROM capaian_harian ch
          JOIN master_indikator_mutu mi ON ch.indikator_id = mi.id
          JOIN users u ON ch.user_id = u.id
          WHERE mi.nama_indikator LIKE '%$keyword_sql%'";

if ($unit_filter_sql) {
    $query .= " AND ch.unit_name = '$unit_filter_sql'";
}
if ($tgl_awal_sql && $tgl_akhir_sql) {
    $query .= " AND ch.tanggal BETWEEN '$tgl_awal_sql' AND '$tgl_akhir_sql'";
}
$query .= " ORDER BY ch.tanggal ASC";

$result = mysqli_query($conn, $query);

// Data perusahaan (kop & logo)
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Path logo
$logoPath = realpath('dist/images/logo/' . $perusahaan['logo']);
$logoBase64 = '';
if ($logoPath && file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
}

// ================= HTML ===================
$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; }
  .kop { text-align: center; }
  .kop img { width: 70px; }
  .nama-perusahaan { font-size: 16px; font-weight: bold; text-transform: uppercase; }
  .alamat { font-size: 12px; }
  hr { border: 1px solid #000; margin: 10px 0; }
  .judul { text-align: center; font-size: 15px; font-weight: bold; margin-top: 15px; }
  table { border-collapse: collapse; width: 100%; margin-top: 20px; }
  table, th, td { border: 1px solid black; }
  th, td { padding: 5px; text-align: center; }
</style>

<div class="kop">
  <img src="' . ($logoBase64 ?: '') . '" alt="Logo"><br>
  <div class="nama-perusahaan">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div class="alamat">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
  Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
</div>

<hr>

<div class="judul">LAPORAN CAPAIAN HARIAN INDIKATOR MUTU</div>';

// Tampilkan periode jika ada filter tanggal
if ($tgl_awal && $tgl_akhir) {
    $html .= '<p style="text-align:center;">Periode: <strong>' . tgl_indo($tgl_awal) . '</strong> s/d <strong>' . tgl_indo($tgl_akhir) . '</strong></p>';
}

$html .= '
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Tanggal</th>
      <th>Unit</th>
      <th>Indikator</th>
      <th>Numerator</th>
      <th>Denominator</th>
      <th>Pencapaian (%)</th>
      <th>User</th>
    </tr>
  </thead>
  <tbody>';

$no = 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '
        <tr>
          <td>' . $no++ . '</td>
          <td>' . tgl_indo($row['tanggal']) . '</td>
          <td>' . htmlspecialchars($row['unit_name']) . '</td>
          <td>' . htmlspecialchars($row['nama_indikator']) . '</td>
          <td>' . htmlspecialchars($row['nilai_numerator']) . '</td>
          <td>' . htmlspecialchars($row['nilai_denominator']) . '</td>
          <td>' . number_format((float)$row['pencapaian'], 2) . '</td>
          <td>' . htmlspecialchars($row['user_nama']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="8">Tidak ada data</td></tr>';
}

$html .= '</tbody></table>';

// ================= DOMPDF ===================
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // landscape agar tabel lebar
$dompdf->render();

// Watermark (opsional)
$canvas = $dompdf->getCanvas();
$canvas->set_opacity(0.07);
$watermarkPath = 'assets/watermark.jpg';
if (file_exists($watermarkPath)) {
    $width = 500;
    $height = 300;
    $x = ($canvas->get_width() - $width) / 2;
    $y = ($canvas->get_height() - $height) / 3;
    $canvas->image($watermarkPath, $x, $y, $width, $height);
}

// Output PDF ke browser
$dompdf->stream('laporan_capaian_harian.pdf', ['Attachment' => false]);
?>

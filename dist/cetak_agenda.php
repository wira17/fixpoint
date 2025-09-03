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

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Ambil data agenda
$q = mysqli_query($conn, "SELECT a.*, u.nama AS user_nama 
    FROM agenda_direktur a 
    LEFT JOIN users u ON a.user_input = u.id 
    WHERE MONTH(a.tanggal) = '$bulan' AND YEAR(a.tanggal) = '$tahun' 
    ORDER BY a.tanggal ASC");

$data_agenda = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data_agenda[] = $row;
}

// Ambil data perusahaan
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Ambil logo dan encode base64
$logoPath = 'dist/assets/logo6.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $dataLogo = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataLogo);
}

$html = '<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .kop img { width: 80px; }
  .kop .nama-perusahaan { font-size: 16px; font-weight: bold; text-transform: uppercase; }
  .kop .alamat { font-size: 12px; }
  hr { border: 1px solid #000; margin: 10px 0; }
  .judul { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin-top: 20px; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  table th, table td { border: 1px solid #000; padding: 5px; text-align: left; }
</style>';

$html .= '<div class="kop" style="text-align: center;">
 <img src="' . $logoBase64 . '" alt="Logo"><br>
  <div class="nama-perusahaan">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div class="alamat">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
  Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
</div>
<hr>
<div class="judul">AGENDA DIREKTUR<br>Bulan ' . tgl_indo("$tahun-$bulan-01") . '</div>';

$html .= '<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Judul</th>
      <th>Tanggal</th>
      <th>Jam</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>';

$no = 1;
foreach ($data_agenda as $row) {
    $html .= '<tr>
      <td>' . $no++ . '</td>
      <td>' . htmlspecialchars($row['judul']) . '</td>
      <td>' . tgl_indo($row['tanggal']) . '</td>
      <td>' . htmlspecialchars($row['jam']) . '</td>
      <td>' . nl2br(htmlspecialchars($row['keterangan'])) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Tambahkan watermark jika file ada
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

$filename = 'agenda_direktur_' . $bulan . '_' . $tahun . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);

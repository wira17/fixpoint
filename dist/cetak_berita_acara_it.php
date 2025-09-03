<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

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

// Validasi parameter tanggal
if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
    die('Parameter tanggal belum lengkap.');
}

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

if (!validateDate($start_date) || !validateDate($end_date)) {
    die('Format tanggal tidak valid.');
}

$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

// Query data hardware dengan join tiket dan users
$sql_hardware = "SELECT ba.*, t.nomor_tiket, t.kendala, t.kategori, t.tanggal_input, 
                        u.nik, u.nama, u.jabatan, u.unit_kerja
                 FROM berita_acara ba
                 JOIN tiket_it_hardware t ON ba.tiket_id = t.id
                 JOIN users u ON t.user_id = u.id
                 WHERE ba.tanggal_ba BETWEEN '$start_datetime' AND '$end_datetime'
                 ORDER BY ba.tanggal_ba ASC";
$res_hardware = mysqli_query($conn, $sql_hardware);

// Query data software yang benar
$sql_software = "SELECT ba.*, t.nomor_tiket, t.kendala, t.kategori, t.tanggal_input, 
                        t.nik, t.nama, t.jabatan, t.unit_kerja, t.teknisi_nama, t.catatan_it
                 FROM berita_acara_software ba
                 JOIN tiket_it_software t ON ba.tiket_id = t.id
                 WHERE ba.tanggal_ba BETWEEN '$start_datetime' AND '$end_datetime'
                 ORDER BY ba.tanggal_ba ASC";
$res_software = mysqli_query($conn, $sql_software);

// Ambil data perusahaan (1 row)
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Siapkan base64 logo
$logoBase64 = '';
$logoPath = realpath('dist/images/logo/' . $perusahaan['logo']);
if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
}

$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .kop img { width: 80px; }
  .kop .nama-perusahaan { font-size: 16px; font-weight: bold; text-transform: uppercase; }
  .kop .alamat { font-size: 12px; }
  hr { border: 1px solid #000; margin: 10px 0; }
  h2 { text-align: center; margin-top: 40px; text-decoration: underline; }
  table { border-collapse: collapse; width: 100%; margin-top: 10px; }
  th, td { border: 1px solid #000; padding: 4px; vertical-align: top; }
  th { background-color: #ddd; }
  .page-break { page-break-after: always; }
</style>

<div class="kop" style="text-align: center;">
  <img src="' . $logoBase64 . '" alt="Logo"><br>
  <div class="nama-perusahaan">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div class="alamat">' . htmlspecialchars($perusahaan['alamat']) . ', ' . htmlspecialchars($perusahaan['kota']) . ', ' . htmlspecialchars($perusahaan['provinsi']) . '<br>
  Telp: ' . htmlspecialchars($perusahaan['kontak']) . ' | Email: ' . htmlspecialchars($perusahaan['email']) . '</div>
</div>
<hr>
<h1 style="text-align:center;">Laporan Berita Acara IT</h1>
<p style="text-align:center;">Periode: ' . tgl_indo($start_date) . ' s/d ' . tgl_indo($end_date) . '</p>
';

// TABEL HARDWARE
$html .= '<h2>Berita Acara Hardware</h2>';
$html .= '<table>';
$html .= '<thead>
    <tr>
      <th>#</th>
      <th>Nomor BA</th>
      <th>Nomor Tiket</th>
      <th>Tanggal BA</th>
      <th>NIK</th>
      <th>Nama Pelapor</th>
      <th>Jabatan</th>
      <th>Unit Kerja</th>
      <th>Kategori</th>
      <th>Kendala</th>
      <th>Catatan Teknisi</th>
    </tr>
  </thead><tbody>';

if(mysqli_num_rows($res_hardware) > 0){
    $no = 1;
    while($row = mysqli_fetch_assoc($res_hardware)){
        $html .= '<tr>';
        $html .= '<td>'. $no++ .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nomor_ba']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nomor_tiket']) .'</td>';
        $html .= '<td>'. tgl_indo($row['tanggal_ba'], true) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nik']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nama']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['jabatan']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['unit_kerja']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['kategori']) .'</td>';
        $html .= '<td>'. nl2br(htmlspecialchars($row['kendala'])) .'</td>';
        $html .= '<td>'. nl2br(htmlspecialchars($row['catatan_teknisi'])) .'</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="11" style="text-align:center;">Tidak ada data hardware pada periode ini.</td></tr>';
}
$html .= '</tbody></table>';

// Page break antar bagian
$html .= '<div class="page-break"></div>';

// TABEL SOFTWARE
$html .= '<h2>Berita Acara Software</h2>';
$html .= '<table>';
$html .= '<thead>
    <tr>
      <th>#</th>
      <th>Nomor BA</th>
      <th>Nomor Tiket</th>
      <th>Tanggal BA</th>
      <th>NIK</th>
      <th>Nama Pelapor</th>
      <th>Jabatan</th>
      <th>Unit Kerja</th>
      <th>Kategori</th>
      <th>Kendala</th>
      <th>Catatan Teknisi</th>
    </tr>
  </thead><tbody>';

if(mysqli_num_rows($res_software) > 0){
    $no = 1;
    while($row = mysqli_fetch_assoc($res_software)){
        $html .= '<tr>';
        $html .= '<td>'. $no++ .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nomor_ba']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nomor_tiket']) .'</td>';
        $html .= '<td>'. tgl_indo($row['tanggal_ba'], true) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nik']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['nama']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['jabatan']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['unit_kerja']) .'</td>';
        $html .= '<td>'. htmlspecialchars($row['kategori']) .'</td>';
        $html .= '<td>'. nl2br(htmlspecialchars($row['kendala'])) .'</td>';
        $html .= '<td>'. nl2br(htmlspecialchars($row['catatan_it'])) .'</td>'; // catatan_it bukan catatan_teknisi
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="11" style="text-align:center;">Tidak ada data software pada periode ini.</td></tr>';
}

$html .= '</tbody></table>';


// Render dan tampilkan PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Landscape biar kolom lebih luas
$dompdf->render();

$dompdf->stream('laporan_berita_acara_it_' . $start_date . '_sampai_' . $end_date . '.pdf', ['Attachment' => false]);
exit;
?>

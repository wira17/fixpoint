<?php
session_start();
include 'koneksi.php';
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

date_default_timezone_set('Asia/Jakarta');

// Ambil data perusahaan (untuk kop surat)
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Ambil filter
$tgl_dari   = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';
$search     = $_GET['search'] ?? '';

// Build query
$where  = "WHERE 1=1";
if (!empty($tgl_dari) && !empty($tgl_sampai)) {
    $where .= " AND DATE(c.tanggal) BETWEEN '".mysqli_real_escape_string($conn,$tgl_dari)."' AND '".mysqli_real_escape_string($conn,$tgl_sampai)."'";
} elseif (!empty($tgl_dari)) {
    $where .= " AND DATE(c.tanggal) >= '".mysqli_real_escape_string($conn,$tgl_dari)."'";
} elseif (!empty($tgl_sampai)) {
    $where .= " AND DATE(c.tanggal) <= '".mysqli_real_escape_string($conn,$tgl_sampai)."'";
}
if (!empty($search)) {
    $searchTerm = mysqli_real_escape_string($conn, $search);
    $where .= " AND (c.judul LIKE '%$searchTerm%' OR c.isi LIKE '%$searchTerm%' OR u.nama LIKE '%$searchTerm%')";
}

// Ambil data catatan kerja
$sql = "SELECT c.*, u.nama 
        FROM catatan_kerja c 
        JOIN users u ON c.user_id = u.id
        $where ORDER BY c.tanggal DESC";
$q = mysqli_query($conn, $sql);

// Konversi logo ke base64 agar pasti tampil di PDF
$logoBase64 = '';
if (!empty($perusahaan['logo'])) {
    $logoPath = realpath('uploads/' . $perusahaan['logo']);
    if ($logoPath && file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
    }
}

// HTML laporan
$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
  .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
  .kop img { float:left; max-height:70px; margin-right:10px; }
  .kop .nama { font-size: 16px; font-weight:bold; text-transform:uppercase; }
  .kop .alamat { font-size: 11px; margin-top:2px; }
  h3 { text-align:center; margin-bottom:5px; clear:both; }
  p { text-align:center; margin-top:0; font-size:11px; }
  table { border-collapse: collapse; width: 100%; margin-top:10px; }
  table, th, td { border: 1px solid #000; }
  th, td { padding: 5px; }
  th { background: #f2f2f2; text-align:center; }
</style>

<div class="kop">';

if (!empty($logoBase64)) {
    $html .= '<img src="'.$logoBase64.'" alt="Logo">';
}

$html .= '
  <div class="nama">'.htmlspecialchars($perusahaan['nama_perusahaan']).'</div>
  <div class="alamat">'
      .htmlspecialchars($perusahaan['alamat']).', '
      .htmlspecialchars($perusahaan['kota']).', '
      .htmlspecialchars($perusahaan['provinsi']).'<br>
      Telp: '.htmlspecialchars($perusahaan['kontak']).' | Email: '.htmlspecialchars($perusahaan['email']).'
  </div>
</div>

<h3>LAPORAN REKAP CATATAN KERJA</h3>
<p>Dicetak pada: '.date("d-m-Y H:i").'</p>

<table>
<thead>
<tr>
  <th>No</th>
  <th>Nama Pengguna</th>
  <th>Judul</th>
  <th>Catatan</th>
  <th>Tanggal</th>
</tr>
</thead>
<tbody>';

$no = 1;
if (mysqli_num_rows($q) > 0) {
    while($row = mysqli_fetch_assoc($q)) {
        $html .= "<tr>
          <td align='center'>".$no++."</td>
          <td>".htmlspecialchars($row['nama'])."</td>
          <td>".htmlspecialchars($row['judul'])."</td>
          <td>".nl2br(htmlspecialchars($row['isi']))."</td>
          <td>".date('d-m-Y H:i', strtotime($row['tanggal']))."</td>
        </tr>";
    }
} else {
    $html .= "<tr><td colspan='5' align='center'>Tidak ada data</td></tr>";
}
$html .= '</tbody></table>';

// Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output PDF (langsung tampil di browser)
$dompdf->stream("laporan_catatan_kerja.pdf", ["Attachment" => false]);
?>

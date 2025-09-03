<?php
session_start();

// Konfigurasi koneksi dan timezone
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Include Dompdf manual (jika tanpa Composer)
require_once 'dompdf/autoload.inc.php';

use Dompdf\src\Dompdf;
use Dompdf\src\Options;

// Ambil parameter tanggal
$tanggal_dari = $_GET['dari'] ?? '';
$tanggal_sampai = $_GET['sampai'] ?? '';

// Buat query filter tanggal
$whereFilter = "";
if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
  $whereFilter = "WHERE tgl_terima BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
}

// Query data surat masuk per bulan
$data_laporan = mysqli_query($conn, "
  SELECT DATE_FORMAT(tgl_terima, '%Y-%m') AS bulan, COUNT(*) AS jumlah
  FROM surat_masuk
  $whereFilter
  GROUP BY bulan
  ORDER BY bulan DESC
");

// Query data berdasarkan sifat
$data_sifat = mysqli_query($conn, "
  SELECT sifat_surat, COUNT(*) AS jumlah
  FROM surat_masuk
  $whereFilter
  GROUP BY sifat_surat
");

// Mulai tampung output HTML
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h3, h4 { margin-bottom: 5px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 15px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: left; }
    th { background-color: #f0f0f0; }
  </style>
</head>
<body>

<h3 style="text-align:center;">Laporan Surat Masuk</h3>
<p>Periode: <?= htmlspecialchars($tanggal_dari) ?> s/d <?= htmlspecialchars($tanggal_sampai) ?></p>

<h4>Jumlah Surat per Bulan</h4>
<table>
  <tr>
    <th>Bulan</th>
    <th>Jumlah</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($data_laporan)) : ?>
    <tr>
      <td><?= date('F Y', strtotime($row['bulan'] . '-01')) ?></td>
      <td><?= $row['jumlah'] ?></td>
    </tr>
  <?php endwhile ?>
</table>

<h4>Jumlah Surat Berdasarkan Sifat</h4>
<table>
  <tr>
    <th>Sifat Surat</th>
    <th>Jumlah</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($data_sifat)) : ?>
    <tr>
      <td><?= htmlspecialchars($row['sifat_surat']) ?></td>
      <td><?= $row['jumlah'] ?></td>
    </tr>
  <?php endwhile ?>
</table>

</body>
</html>

<?php
// Ambil isi output HTML
$html = ob_get_clean();

// Inisialisasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Atur ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Tampilkan PDF di browser
$dompdf->stream("laporan_surat_masuk.pdf", ["Attachment" => false]);
exit;
?>

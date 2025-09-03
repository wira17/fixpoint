<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include 'koneksi.php';
session_start();

// Fungsi ubah format tanggal ke Indonesia
function tgl_indo($tanggal) {
    if (!$tanggal || $tanggal == "0000-00-00") return "-";
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $split = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Ambil nama user login
$nama_user = $_SESSION['nama'] ?? 'Petugas';

// Ambil filter tanggal dari URL
$tgl_dari = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

$where = "WHERE 1=1";

if (!empty($tgl_dari) && !empty($tgl_sampai)) {
    $where .= " AND tanggal BETWEEN '$tgl_dari' AND '$tgl_sampai'";
} elseif (!empty($tgl_dari)) {
    $where .= " AND tanggal >= '$tgl_dari'";
} elseif (!empty($tgl_sampai)) {
    $where .= " AND tanggal <= '$tgl_sampai'";
}

// Ambil data izin keluar
$query = mysqli_query($conn, "
    SELECT * FROM izin_keluar
    $where
    ORDER BY tanggal DESC, created_at DESC
");

$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Format HTML resmi
$html = '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
  table { border-collapse: collapse; width: 100%; font-size: 10px; }
  th, td { border: 1px solid #000; padding: 4px; text-align: center; }
  th { background: #eee; }
  .header { text-align: center; margin-bottom: 15px; }
  .judul { font-size: 16px; font-weight: bold; text-transform: uppercase; }
  .subjudul { font-size: 12px; }
  .pembuka { text-align: justify; margin-bottom: 15px; }
  .penutup { text-align: justify; margin-top: 15px; }
  .ttd { width: 200px; text-align: center; float: right; margin-top: 40px; }
</style>

<div class="header">
  <div class="judul">LAPORAN IZIN KELUAR</div>
  <div class="subjudul">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</div>
  <div class="subjudul">Periode: ' . 
    (!empty($tgl_dari) ? tgl_indo($tgl_dari) : '-') . ' s/d ' . 
    (!empty($tgl_sampai) ? tgl_indo($tgl_sampai) : '-') . 
  '</div>
</div>

<div class="pembuka">
  Dengan hormat,<br>
  Berikut kami sampaikan rekapitulasi data izin keluar pegawai pada periode tersebut di atas:
</div>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>Bagian</th>
      <th>Tanggal</th>
      <th>Jam Keluar</th>
      <th>Jam Kembali</th>
      <th>Keperluan</th>
      <th>ACC Atasan</th>
      <th>ACC SDM</th>
    </tr>
  </thead>
  <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '
    <tr>
      <td>' . $no++ . '</td>
      <td>' . htmlspecialchars($row['nama']) . '</td>
      <td>' . htmlspecialchars($row['bagian']) . '</td>
      <td>' . tgl_indo($row['tanggal']) . '</td>
      <td>' . htmlspecialchars($row['jam_keluar']) . '</td>
      <td>' . ($row['jam_kembali'] ? htmlspecialchars($row['jam_kembali']) : '-') . '</td>
      <td>' . htmlspecialchars($row['keperluan']) . '</td>
      <td>' . ucfirst($row['status_atasan']) . '</td>
      <td>' . ucfirst($row['status_sdm']) . '</td>
    </tr>';
}

if ($no === 1) {
    $html .= '<tr><td colspan="9">Tidak ada data</td></tr>';
}

$html .= '
  </tbody>
</table>

<div class="penutup">
  Demikian laporan ini kami buat untuk digunakan sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
</div>

<div class="ttd">
  ' . $perusahaan['kota'] . ', ' . tgl_indo(date('Y-m-d')) . '<br>
  Hormat kami,<br><br><br><br>
  <strong>' . htmlspecialchars($nama_user) . '</strong>
</div>
';

// Buat PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Surat resmi biasanya portrait
$dompdf->render();
$dompdf->stream("laporan_izin_keluar.pdf", ["Attachment" => false]);

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

// Ambil filter
$filterNama   = $_GET['nama'] ?? '';
$filterNik    = $_GET['nik'] ?? '';
$filterDari   = $_GET['dari'] ?? date('Y-m-d');
$filterSampai = $_GET['sampai'] ?? date('Y-m-d');

$sql = "SELECT * FROM izin_keluar WHERE tanggal BETWEEN ? AND ?";
$params = [$filterDari, $filterSampai];
$types = "ss";

if(!empty($filterNama)) {
    $sql .= " AND nama LIKE ?";
    $params[] = "%$filterNama%";
    $types .= "s";
}
if(!empty($filterNik)) {
    $sql .= " AND nik LIKE ?";
    $params[] = "%$filterNik%";
    $types .= "s";
}

$sql .= " ORDER BY tanggal DESC, created_at DESC";
$q = $conn->prepare($sql);
$q->bind_param($types, ...$params);
$q->execute();
$result = $q->get_result();

// Ambil data perusahaan
$q_perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan LIMIT 1");
$perusahaan = mysqli_fetch_assoc($q_perusahaan);

// Path logo
$logoPath = realpath('dist/images/logo/' . $perusahaan['logo']);
$logoBase64 = '';
if(file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoBase64 = 'data:image/'.$logoType.';base64,'.base64_encode($logoData);
}

// HTML
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; }
.kop img { width: 80px; }
.kop .nama-perusahaan { font-size: 14px; font-weight: bold; text-transform: uppercase; }
.kop .alamat { font-size: 10px; }
hr { border: 1px solid #000; margin: 10px 0; }
.judul { text-align: center; font-size: 12px; font-weight: bold; text-decoration: underline; margin-top: 10px; }
.table-data { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
.table-data th, .table-data td { border: 1px solid #000; padding: 3px; }
</style>

<div class="kop" style="text-align: center;">
  <img src="'.$logoBase64.'" alt="Logo"><br>
  <div class="nama-perusahaan">'.htmlspecialchars($perusahaan['nama_perusahaan']).'</div>
  <div class="alamat">'.htmlspecialchars($perusahaan['alamat']).', '.htmlspecialchars($perusahaan['kota']).', '.htmlspecialchars($perusahaan['provinsi']).'</div>
</div>

<hr>

<div class="judul">LAPORAN IZIN KELUAR SDM LENGKAP</div>
<div style="text-align:center; margin-bottom:5px;">
Periode: <strong>'.tgl_indo($filterDari).' s/d '.tgl_indo($filterSampai).'</strong>
</div>

<table class="table-data">
<thead>
<tr style="text-align:center;">
<th>No</th><th>NIK</th><th>Nama</th><th>Jabatan</th><th>Bagian</th><th>Atasan Langsung</th>
<th>Tanggal</th><th>Jam Keluar</th><th>Jam Kembali</th><th>Jam Kembali Real</th><th>Keperluan</th>
<th>Created At</th><th>Status Atasan</th><th>Waktu ACC Atasan</th>
<th>Status SDM</th><th>Waktu ACC SDM</th><th>ACC Oleh SDM</th><th>ACC Oleh Atasan</th>
</tr>
</thead>
<tbody>
';

if($result->num_rows > 0) {
    $no = 1;
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
        <td style="text-align:center;">'.$no++.'</td>
        <td>'.htmlspecialchars($row['nik']).'</td>
        <td>'.htmlspecialchars($row['nama']).'</td>
        <td>'.htmlspecialchars($row['jabatan']).'</td>
        <td>'.htmlspecialchars($row['bagian']).'</td>
        <td>'.htmlspecialchars($row['atasan_langsung']).'</td>
        <td>'.tgl_indo($row['tanggal']).'</td>
        <td>'.htmlspecialchars($row['jam_keluar']).'</td>
        <td>'.htmlspecialchars($row['jam_kembali']).'</td>
        <td>'.($row['jam_kembali_real'] ? tgl_indo($row['jam_kembali_real'], true) : '-').'</td>
        <td>'.htmlspecialchars($row['keperluan']).'</td>
        <td>'.($row['created_at'] ? tgl_indo($row['created_at'], true) : '-').'</td>
        <td style="text-align:center;">'.ucfirst($row['status_atasan']).'</td>
        <td>'.($row['waktu_acc_atasan'] ? tgl_indo($row['waktu_acc_atasan'], true) : '-').'</td>
        <td style="text-align:center;">'.ucfirst($row['status_sdm']).'</td>
        <td>'.($row['waktu_acc_sdm'] ? tgl_indo($row['waktu_acc_sdm'], true) : '-').'</td>
        <td>'.($row['acc_oleh_sdm'] ?? '-').'</td>
        <td>'.($row['acc_oleh_atasan'] ?? '-').'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="18" style="text-align:center;">Tidak ada data.</td></tr>';
}

$html .= '</tbody></table>';

// Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('laporan_izin_sdm_lengkap.pdf', ['Attachment'=>false]);
?>

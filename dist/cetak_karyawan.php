<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include 'koneksi.php';

// Ambil ID user
if (!isset($_GET['id'])) die('ID user tidak ditemukan.');
$user_id = intval($_GET['id']);

// Query data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) die('User tidak ditemukan.');

// Ambil Informasi Pribadi
$stmt = $conn->prepare("SELECT * FROM informasi_pribadi WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$info_pribadi = $stmt->get_result()->fetch_assoc();

// Riwayat pekerjaan
$stmt = $conn->prepare("SELECT * FROM riwayat_pekerjaan WHERE user_id = ? ORDER BY tanggal_mulai DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pekerjaan_res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Riwayat pendidikan
$stmt = $conn->prepare("SELECT * FROM riwayat_pendidikan WHERE user_id = ? ORDER BY tgl_lulus DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pendidikan_res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Riwayat kesehatan
$stmt = $conn->prepare("SELECT * FROM riwayat_kesehatan WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$kesehatan = $stmt->get_result()->fetch_assoc();

// Dokumen pendukung
$stmt = $conn->prepare("SELECT * FROM dokumen_pendukung WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dokumen = $stmt->get_result()->fetch_assoc();

// Ambil data perusahaan untuk kop surat
$q_perusahaan = $conn->query("SELECT * FROM perusahaan LIMIT 1");
$perusahaan = $q_perusahaan->fetch_assoc();

// HTML + CSS modern untuk PDF
$html = '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
body { font-family: "Segoe UI", Tahoma, sans-serif; font-size: 11px; color: #333; }
.header { text-align: center; margin-bottom: 15px; }
.header .nama-perusahaan { font-size: 16px; font-weight: bold; text-transform: uppercase; }
.header .alamat { font-size: 10px; color: #555; }
.title { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0; text-transform: uppercase; color: #222; }
.table-container { margin-top: 10px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; font-size: 11px; }
th { background-color: #f2f2f2; text-align: left; }
tr:nth-child(even) { background-color: #fafafa; }
h3 { background-color: #007BFF; color: #fff; padding: 6px 10px; font-size: 12px; margin-bottom: 5px; border-radius: 4px; }
a { text-decoration: none; color: #007BFF; }
</style>

<div class="header">
  <div class="nama-perusahaan">'.htmlspecialchars($perusahaan['nama_perusahaan']).'</div>
  <div class="alamat">'.htmlspecialchars($perusahaan['alamat']).', '.htmlspecialchars($perusahaan['kota']).', '.htmlspecialchars($perusahaan['provinsi']).'<br>
  Telp: '.htmlspecialchars($perusahaan['kontak']).' | Email: '.htmlspecialchars($perusahaan['email']).'</div>
</div>

<div class="title">Data Karyawan</div>

<div class="table-container">
<table>
<tr>
<th>NIK</th><td>'.htmlspecialchars($user['nik']).'</td>
<th>Nama</th><td>'.htmlspecialchars($user['nama']).'</td>
</tr>
<tr>
<th>Jabatan</th><td>'.htmlspecialchars($user['jabatan'] ?? '-').'</td>
<th>Unit Kerja</th><td>'.htmlspecialchars($user['unit_kerja'] ?? '-').'</td>
</tr>
<tr>
<th>Email</th><td>'.htmlspecialchars($user['email']).'</td>
<th>No HP</th><td>'.htmlspecialchars($user['no_hp'] ?? '-').'</td>
</tr>
<tr>
<th>Status</th><td>'.htmlspecialchars(ucfirst($user['status']) ?? '-').'</td>
<th>Tanggal Daftar</th><td>'.htmlspecialchars($user['created_at']).'</td>
</tr>
</table>
</div>

<h3>Informasi Pribadi</h3>
<table>
<tr>
<th>Jenis Kelamin</th>
<th>Tempat Lahir</th>
<th>Tanggal Lahir</th>
<th>Alamat</th>
<th>Kota</th>
<th>No KTP</th>
<th>Hubungan Keluarga</th>
</tr>
<tr>
<td>'.htmlspecialchars($info_pribadi['jenis_kelamin'] ?? '-').'</td>
<td>'.htmlspecialchars($info_pribadi['tempat_lahir'] ?? '-').'</td>
<td>'.(!empty($info_pribadi['tanggal_lahir']) ? date('d-m-Y', strtotime($info_pribadi['tanggal_lahir'])) : '-').'</td>
<td>'.htmlspecialchars($info_pribadi['alamat'] ?? '-').'</td>
<td>'.htmlspecialchars($info_pribadi['kota'] ?? '-').'</td>
<td>'.htmlspecialchars($info_pribadi['no_ktp'] ?? '-').'</td>
<td>'.htmlspecialchars($info_pribadi['hubungan_keluarga'] ?? '-').'</td>
</tr>
</table>

<h3>Riwayat Pekerjaan</h3>
<table>
<tr><th>No</th><th>Nama Perusahaan & Posisi</th><th>Periode</th><th>Alasan Keluar</th></tr>';
$i=1;
foreach($pekerjaan_res as $p){
    $html .= '<tr>
        <td>'.$i.'</td>
        <td>'.htmlspecialchars($p['nama_perusahaan'].' ('.$p['posisi'].')').'</td>
        <td>'.htmlspecialchars($p['tanggal_mulai'].' s/d '.$p['tanggal_selesai']).'</td>
        <td>'.htmlspecialchars($p['alasan_keluar'] ?? '-').'</td>
    </tr>';
    $i++;
}
$html .= '</table>';

$html .= '<h3>Riwayat Pendidikan</h3>
<table>
<tr><th>No</th><th>Pendidikan & Jurusan</th><th>Kampus</th><th>Tanggal Lulus</th><th>No Ijazah</th></tr>';
$i=1;
foreach($pendidikan_res as $pd){
    $html .= '<tr>
        <td>'.$i.'</td>
        <td>'.htmlspecialchars($pd['pendidikan_terakhir'].' '.$pd['jurusan']).'</td>
        <td>'.htmlspecialchars($pd['kampus']).'</td>
        <td>'.htmlspecialchars($pd['tgl_lulus']).'</td>
        <td>'.htmlspecialchars($pd['no_ijazah'] ?? '-').'</td>
    </tr>';
    $i++;
}
$html .= '</table>';

$html .= '<h3>Riwayat Kesehatan</h3>
<table>
<tr>
<th>Golongan Darah</th>
<th>Riwayat Penyakit</th>
<th>Status Vaksinasi</th>
<th>BPJS Kesehatan</th>
<th>BPJS Kerja</th>
<th>Asuransi Tambahan</th>
</tr>
<tr>
<td>'.htmlspecialchars($kesehatan['gol_darah'] ?? '-').'</td>
<td>'.htmlspecialchars($kesehatan['riwayat_penyakit'] ?? '-').'</td>
<td>'.htmlspecialchars($kesehatan['status_vaksinasi'] ?? '-').'</td>
<td>'.htmlspecialchars($kesehatan['no_bpjs_kesehatan'] ?? '-').'</td>
<td>'.htmlspecialchars($kesehatan['no_bpjs_kerja'] ?? '-').'</td>
<td>'.htmlspecialchars($kesehatan['asuransi_tambahan'] ?? '-').'</td>
</tr>
</table>';

$html .= '<h3>Dokumen Pendukung</h3>
<table>
<tr>
<th>No</th>
<th>Jenis Dokumen</th>
<th>File</th>
</tr>';

$dok_fields = ['ktp'=>'KTP','ijazah'=>'Ijazah','str'=>'STR','sip'=>'SIP','vaksin'=>'Vaksin','pelatihan'=>'Pelatihan','surat_kerja'=>'Surat Kerja','pas_foto'=>'Pas Foto'];
$no=1;
foreach($dok_fields as $field=>$label){
    if(!empty($dokumen[$field])){
        $file_link = htmlspecialchars('uploads/'.$dokumen[$field]);
        $html .= '<tr>
            <td>'.$no.'</td>
            <td>'.$label.'</td>
            <td><a href="'.$file_link.'" target="_blank">Lihat File</a></td>
        </tr>';
        $no++;
    }
}
$html .= '</table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('karyawan_'.$user['id'].'.pdf', ['Attachment' => false]);
?>

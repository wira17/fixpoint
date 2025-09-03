<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  $tiket_id = $_POST['tiket_id'];
  $nomor_tiket = $_POST['nomor_tiket'];
  $catatan_teknisi = mysqli_real_escape_string($conn, $_POST['catatan_teknisi']);
  $tanggal_ba = date('Y-m-d H:i:s');
  $hari = date('d');
  $bulan = date('m');
  $tahun = date('Y');
  $teknisi = $_SESSION['nama'] ?? 'Teknisi Tidak Diketahui';

  // CEK APAKAH TIKET SUDAH ADA DI BERITA ACARA
  $cek_tiket = mysqli_query($conn, "SELECT id FROM berita_acara WHERE tiket_id = '$tiket_id'");
  if (mysqli_num_rows($cek_tiket) > 0) {
    echo "<script>alert('Gagal! Tiket ini sudah memiliki Berita Acara.'); window.location.href='berita_acara.php?tiket_id=$tiket_id';</script>";
    exit;
  }

  // HITUNG NOMOR URUT BERITA ACARA BULAN INI
  $cek_nomor = mysqli_query($conn, "SELECT COUNT(*) as total FROM berita_acara 
                                    WHERE MONTH(tanggal_ba) = '$bulan' AND YEAR(tanggal_ba) = '$tahun'");
  $data_nomor = mysqli_fetch_assoc($cek_nomor);
  $urutan = str_pad($data_nomor['total'] + 1, 4, '0', STR_PAD_LEFT);
  $nomor_ba = "$urutan/BA-ITHARD/RSPH/$hari/$bulan/$tahun";

  // AMBIL DATA TIKET DAN USER
  $query = mysqli_query($conn, "SELECT t.*, u.nik, u.nama, u.jabatan, u.unit_kerja 
                                FROM tiket_it_hardware t 
                                JOIN users u ON t.user_id = u.id 
                                WHERE t.id = '$tiket_id'");
  $data = mysqli_fetch_assoc($query);

  if ($data) {
    $tanggal = $data['tanggal_input'];
    $nik = $data['nik'];
    $nama_pelapor = $data['nama'];
    $jabatan = $data['jabatan'];
    $unit_kerja = $data['unit_kerja'];
    $kategori = $data['kategori'];
    $kendala = $data['kendala'];

    // SIMPAN KE TABEL BERITA ACARA
    $insert = mysqli_query($conn, "INSERT INTO berita_acara (
      tiket_id, nomor_ba, nomor_tiket, tanggal, nik, nama_pelapor, jabatan, unit_kerja,
      kategori, kendala, catatan_teknisi, tanggal_ba, teknisi
    ) VALUES (
      '$tiket_id', '$nomor_ba', '$nomor_tiket', '$tanggal', '$nik', '$nama_pelapor', '$jabatan',
      '$unit_kerja', '$kategori', '$kendala', '$catatan_teknisi', '$tanggal_ba', '$teknisi'
    )");

    if ($insert) {
      echo "<script>alert('Berita Acara berhasil disimpan.'); window.location.href='berita_acara.php?tiket_id=$tiket_id';</script>";
    } else {
      echo "Gagal menyimpan: " . mysqli_error($conn);
    }
  } else {
    echo "Data tiket tidak ditemukan.";
  }
} else {
  echo "Permintaan tidak valid.";
}
?>

<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  $tiket_id = $_POST['tiket_id'];
  $nomor_tiket = $_POST['nomor_tiket'];
  $catatan_teknisi = mysqli_real_escape_string($conn, $_POST['catatan_teknisi']);
  $tanggal_ba = date('Y-m-d H:i:s');
  $teknisi = $_SESSION['nama'] ?? 'Teknisi Tidak Diketahui';

  // Ambil data tiket & pelapor
  $query = mysqli_query($conn, "SELECT t.*, u.nik, u.nama AS nama_pelapor, u.jabatan, u.unit_kerja 
                                FROM tiket_it_software t 
                                JOIN users u ON t.user_id = u.id 
                                WHERE t.id = $tiket_id");

  if ($query && mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    $tanggal = $data['tanggal_input'];
    $nik = $data['nik'];
    $nama_pelapor = $data['nama_pelapor'];
    $jabatan = $data['jabatan'];
    $unit_kerja = $data['unit_kerja'];
    $kategori = $data['kategori'];
    $kendala = $data['kendala'];

    // Cek jika sudah ada berita acara
    $cek = mysqli_query($conn, "SELECT id FROM berita_acara_software WHERE tiket_id = '$tiket_id'");
    if (mysqli_num_rows($cek) > 0) {
      echo "<script>alert('Berita Acara untuk tiket ini sudah dibuat.'); window.location.href='berita_acara_software.php?tiket_id=$tiket_id';</script>";
      exit;
    }

    // === GENERATE NOMOR BA OTOMATIS ===
    $bulan = date('m');
    $tahun = date('Y');

    // Ambil jumlah yang sudah ada di bulan & tahun ini
    $queryCount = mysqli_query($conn, "SELECT COUNT(*) AS total FROM berita_acara_software 
                                       WHERE MONTH(tanggal_ba) = '$bulan' AND YEAR(tanggal_ba) = '$tahun'");
    $countData = mysqli_fetch_assoc($queryCount);
    $urutan = str_pad($countData['total'] + 1, 4, '0', STR_PAD_LEFT);

    $nomor_ba = "$urutan/BA-ITSOFT/RSPH/$bulan/" . date('m') . "/$tahun";

    // === SIMPAN DATA ===
    $insert = mysqli_query($conn, "INSERT INTO berita_acara_software (
      nomor_ba, tiket_id, nomor_tiket, tanggal, nik, nama_pelapor, jabatan, unit_kerja,
      kategori, kendala, catatan_teknisi, tanggal_ba, teknisi
    ) VALUES (
      '$nomor_ba', '$tiket_id', '$nomor_tiket', '$tanggal', '$nik', '$nama_pelapor', '$jabatan',
      '$unit_kerja', '$kategori', '$kendala', '$catatan_teknisi', '$tanggal_ba', '$teknisi'
    )");

    if ($insert) {
      echo "<script>alert('Berita Acara berhasil disimpan.'); window.location.href='berita_acara_software.php?tiket_id=$tiket_id';</script>";
    } else {
      echo "Gagal menyimpan: " . mysqli_error($conn);
    }
  } else {
    echo "Data tiket tidak ditemukan.";
  }
} else {
  echo "Permintaan tidak valid.";
}

<?php
include 'security.php'; // sudah handle session_start + cek login + timeout
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];


// Ambil data barang
$data_barang = mysqli_query($conn, "SELECT * FROM data_barang_it ORDER BY waktu_input DESC");

// Ambil data perusahaan untuk kop surat
$perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan ORDER BY id DESC LIMIT 1");
$instansi = mysqli_fetch_assoc($perusahaan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Data Barang IT</title>
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    @media print {
      .no-print {
        display: none;
      }
    }

    body {
      font-size: 13px;
      padding: 10px;
    }

    .kop-surat {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
      position: relative;
    }

    .kop-surat img {
      height: 60px;
      position: absolute;
      top: 0;
      left: 0;
    }

    .kop-text h3 {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
    }

    .kop-text p {
      margin: 0;
      font-size: 12px;
    }

    .table th, .table td {
      vertical-align: middle !important;
    }

    .footer {
      margin-top: 40px;
      font-size: 12px;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="container mt-4">

    <!-- KOP SURAT -->
    <div class="kop-surat">
      <?php if (!empty($instansi['logo'])): ?>
        <img src="dist/uploads/logo/<?= $instansi['logo'] ?>" alt="Logo">
      <?php endif; ?>
      <div class="kop-text">
        <h3><?= strtoupper($instansi['nama_perusahaan']) ?></h3>
        <p><?= $instansi['alamat'] ?>, <?= $instansi['kota'] ?>, <?= $instansi['provinsi'] ?></p>
        <p>Telp: <?= $instansi['kontak'] ?> | Email: <?= $instansi['email'] ?></p>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
      <h5>Data Barang IT - FIXPOINT</h5>
      <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Print</button>
    </div>

    <table class="table table-bordered table-sm">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>No. Barang</th>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Merk</th>
          <th>Spesifikasi</th>
          <th>IP Address</th>
          <th>Lokasi</th>
          <th>Kondisi</th>
          <th>Tanggal Input</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($barang = mysqli_fetch_assoc($data_barang)) : ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($barang['no_barang']) ?></td>
            <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
            <td><?= htmlspecialchars($barang['kategori']) ?></td>
            <td><?= htmlspecialchars($barang['merk']) ?></td>
            <td><?= htmlspecialchars($barang['spesifikasi']) ?></td>
            <td><?= htmlspecialchars($barang['ip_address']) ?></td>
            <td><?= htmlspecialchars($barang['lokasi']) ?></td>
            <td><?= htmlspecialchars($barang['kondisi']) ?></td>
            <td><?= date('d-m-Y H:i', strtotime($barang['waktu_input'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="footer">
      Dicetak pada: <?= date('d-m-Y H:i') ?>
    </div>
  </div>
</body>
</html>

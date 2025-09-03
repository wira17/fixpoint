<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

$current_file = basename(__FILE__);



// Ambil data surat masuk yang didisposisikan ke user login
$query = "SELECT * FROM surat_masuk 
          WHERE FIND_IN_SET('$user_id', disposisi_ke)
          ORDER BY tgl_terima DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Kotak Masuk</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/fontawesome.min.css">
</head>
<body>
  <div class="container mt-4">
    <h4 class="mb-4"><i class="fas fa-inbox"></i> Kotak Masuk Surat</h4>

    <table class="table table-bordered table-hover">
      <thead class="thead-light">
        <tr>
          <th>No</th>
          <th>No. Surat</th>
          <th>Tanggal Surat</th>
          <th>Pengirim</th>
          <th>Perihal</th>
          <th>Status Balasan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['no_surat']}</td>
                  <td>" . date('d/m/Y', strtotime($row['tgl_surat'])) . "</td>
                  <td>{$row['pengirim']}</td>
                  <td>{$row['perihal']}</td>
                  <td>";

          // Tampilkan status dengan label warna
          if ($row['status_balasan'] == 'Belum Dibalas') {
            echo "<span class='badge badge-danger'>Belum Dibalas</span>";
          } elseif ($row['status_balasan'] == 'Sudah Dibalas') {
            echo "<span class='badge badge-success'>Sudah Dibalas</span>";
          } else {
            echo "<span class='badge badge-secondary'>Tidak Perlu Dibalas</span>";
          }

          echo "</td>
                <td>
                  <a href='detail_surat.php?id={$row['id']}' class='btn btn-info btn-sm'>
                    <i class='fas fa-eye'></i> Lihat
                  </a>
                </td>
              </tr>";
          $no++;
        }

        if ($no == 1) {
          echo "<tr><td colspan='7' class='text-center'>Tidak ada surat untuk Anda.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

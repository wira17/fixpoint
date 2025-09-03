<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__); // 

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// Proses pencarian
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_perusahaan']);
  $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
  $kota = mysqli_real_escape_string($conn, $_POST['kota']);
  $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
  $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  $logo = ""; // Default jika tidak upload
  $logo_dir = __DIR__ . '/images/logo/';

 if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $logo_name = basename($_FILES['logo']['name']);
    $file_type = strtolower(pathinfo($logo_name, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($file_type, $allowed_types)) {
        $unique_name = uniqid('logo_', true) . '.' . $file_type;
        $final_path = $logo_dir . $unique_name;

        // Tambahkan debug sementara
        if (!is_dir($logo_dir)) {
            echo "Folder tidak ditemukan: $logo_dir";
            exit;
        }

        if (!is_writable($logo_dir)) {
            echo "Folder tidak bisa ditulisi: $logo_dir";
            exit;
        }

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $final_path)) {
            $logo = $unique_name;
        } else {
            echo "Gagal memindahkan file ke: $final_path";
            print_r($_FILES['logo']);
            exit;
        }
    } else {
        echo "Tipe file tidak didukung: $file_type";
        exit;
    }
}


  // Simpan ke database
  $insert = mysqli_query($conn, "INSERT INTO perusahaan (nama_perusahaan, alamat, kota, provinsi, kontak, email, logo) 
                                 VALUES ('$nama', '$alamat', '$kota', '$provinsi', '$kontak', '$email', '$logo')");

  if ($insert) {
    $_SESSION['flash_message'] = "Data perusahaan berhasil disimpan.";
    header("Location: perusahaan.php");
    exit;
  } else {
    $_SESSION['flash_message'] = "Gagal menyimpan data ke database.";
    header("Location: perusahaan.php");
    exit;
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>f.i.x.p.o.i.n.t</title>

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />

  <style>
    #notif-toast {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
      display: none;
      min-width: 300px;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php include 'navbar.php'; ?>
      <?php include 'sidebar.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-body">

            <div class="card">
              <div class="card-header">
                <h4>Input Data Perusahaan</h4>
              </div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Nama Perusahaan</label>
                      <input type="text" name="nama_perusahaan" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                      <label>Kontak</label>
                      <input type="text" name="kontak" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"></textarea>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>Kota</label>
                      <input type="text" name="kota" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                      <label>Provinsi</label>
                      <input type="text" name="provinsi" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                      <label>Email</label>
                      <input type="email" name="email" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Upload Logo (opsional)</label>
                    <input type="file" name="logo" class="form-control">
                  </div>

                  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
              </div>
            </div>

            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Perusahaan</h4>
                <form method="GET" class="form-inline">
                  <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Nama/Kota/Provinsi" />
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
                </form>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                      <tr class="text-center">
                        <th>No</th>
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>Kontak</th>
                        <th>Email</th>
                        <th>Logo</th>
                        <th>Tanggal Buat</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>

                  <tbody>
  <?php
  if (isset($_SESSION['flash_message'])) {
    echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
    unset($_SESSION['flash_message']);
  }

  $no = 1;
  $query = "SELECT * FROM perusahaan";
  if (!empty($keyword)) {
    $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
    $query .= " WHERE nama_perusahaan LIKE '%$keywordEscaped%' OR kota LIKE '%$keywordEscaped%' OR provinsi LIKE '%$keywordEscaped%'";
  }
  $query .= " ORDER BY created_at DESC";

  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td class='text-center'>{$no}</td>";
      echo "<td>" . htmlspecialchars($row['nama_perusahaan']) . "</td>";
      echo "<td>" . nl2br(htmlspecialchars($row['alamat'])) . "</td>";
      echo "<td>{$row['kota']}</td>";
      echo "<td>{$row['provinsi']}</td>";
      echo "<td>{$row['kontak']}</td>";
      echo "<td>{$row['email']}</td>";
      echo "<td class='text-center'>";
      if (!empty($row['logo'])) {
       echo "<img src='images/logo/{$row['logo']}' alt='Logo' width='50'>
";

      } else {
        echo "-";
      }
      echo "</td>";
      echo "<td class='text-center'>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";

      // Tombol Aksi
      echo "<td class='text-center'>";
      echo "<button 
              class='btn btn-sm btn-warning' 
              onclick='editPerusahaan(" . json_encode($row) . ")' 
              data-toggle='modal' data-target='#modalEdit'>
              <i class='fas fa-edit'></i> Edit
            </button>";
      echo "</td>";

      echo "</tr>";
      $no++;
    }
  } else {
    echo "<tr><td colspan='10' class='text-center'>Tidak ada data ditemukan.</td></tr>";
  }
  ?>
</tbody>

                  </table>


                </div>
              </div>
            </div>

          </div>
        </section>
      </div>
    </div>
  </div>



                  <!-- Modal Edit Perusahaan -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" enctype="multipart/form-data" action="update_perusahaan.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Perusahaan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Nama Perusahaan</label>
              <input type="text" name="nama_perusahaan" id="edit-nama" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Kontak</label>
              <input type="text" name="kontak" id="edit-kontak" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" id="edit-alamat" class="form-control" rows="2"></textarea>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label>Kota</label>
              <input type="text" name="kota" id="edit-kota" class="form-control">
            </div>
            <div class="form-group col-md-4">
              <label>Provinsi</label>
              <input type="text" name="provinsi" id="edit-provinsi" class="form-control">
            </div>
            <div class="form-group col-md-4">
              <label>Email</label>
              <input type="email" name="email" id="edit-email" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label>Upload Logo (kosongkan jika tidak diganti)</label>
            <input type="file" name="logo" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <script>
    $(document).ready(function () {
      var toast = $('#notif-toast');
      if (toast.length) {
        toast.fadeIn(300).delay(2000).fadeOut(500);
      }
    });
  </script>

  <script>
  function editPerusahaan(data) {
    $('#edit-id').val(data.id);
    $('#edit-nama').val(data.nama_perusahaan);
    $('#edit-kontak').val(data.kontak);
    $('#edit-alamat').val(data.alamat);
    $('#edit-kota').val(data.kota);
    $('#edit-provinsi').val(data.provinsi);
    $('#edit-email').val(data.email);
  }
</script>

</body>
</html>

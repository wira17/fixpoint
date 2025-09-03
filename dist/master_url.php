<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek akses
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
  echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
  exit;
}

// Pencarian
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Proses tambah data
if (isset($_POST['simpan'])) {
  $nama_koneksi = mysqli_real_escape_string($conn, trim($_POST['nama_koneksi']));
  $base_url = mysqli_real_escape_string($conn, trim($_POST['base_url']));
  $insert = mysqli_query($conn, "INSERT INTO master_url (nama_koneksi, base_url) VALUES ('$nama_koneksi', '$base_url')");
  if ($insert) {
    $_SESSION['flash_message'] = "Koneksi berhasil ditambahkan.";
    echo "<script>location.href='master_url.php';</script>";
    exit;
  } else {
    echo "<div class='alert alert-danger'>Gagal menambahkan koneksi.</div>";
  }
}

// Proses hapus
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  mysqli_query($conn, "DELETE FROM master_url WHERE id = $id");
  $_SESSION['flash_message'] = "Koneksi berhasil dihapus.";
  echo "<script>location.href='master_url.php';</script>";
  exit;
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
    .help-icon {
      color: red;
      font-size: 18px;
      cursor: pointer;
      margin-left: 8px;
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
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4>
                  Master URL / IP
                  <i class="fas fa-question-circle help-icon" data-toggle="modal" data-target="#helpModal"></i>
                </h4>
                <form method="GET" class="form-inline">
                  <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari koneksi" />
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
                </form>
              </div>
              <div class="card-body">

                <!-- Form tambah koneksi -->
                <form method="POST" class="form-inline mb-3">
                  <div class="form-group mr-2" style="flex: 1;">
                    <input type="text" name="nama_koneksi" class="form-control w-100" placeholder="Nama koneksi" required />
                  </div>
                  <div class="form-group mr-2" style="flex: 1;">
                    <input type="text" name="base_url" class="form-control w-100" placeholder="Base URL / IP" required />
                  </div>
                  <button type="submit" name="simpan" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah
                  </button>
                </form>

                <?php
                if (isset($_SESSION['flash_message'])) {
                  echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
                  unset($_SESSION['flash_message']);
                }

                // Pagination
                $limit = 6;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $limit;

                $totalQuery = "SELECT COUNT(*) as total FROM master_url";
                if (!empty($keyword)) {
                  $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
                  $totalQuery .= " WHERE nama_koneksi LIKE '%$keywordEscaped%' OR base_url LIKE '%$keywordEscaped%'";
                }
                $totalResult = mysqli_query($conn, $totalQuery);
                $totalRows = mysqli_fetch_assoc($totalResult)['total'];
                $totalPages = ceil($totalRows / $limit);

                $query = "SELECT * FROM master_url";
                if (!empty($keyword)) {
                  $query .= " WHERE nama_koneksi LIKE '%$keywordEscaped%' OR base_url LIKE '%$keywordEscaped%'";
                }
                $query .= " ORDER BY nama_koneksi ASC LIMIT $offset, $limit";
                $result = mysqli_query($conn, $query);
                $no = $offset + 1;
                ?>

                <div class="table-responsive">
                  <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                      <tr class="text-center">
                        <th>No</th>
                        <th>Nama Koneksi</th>
                        <th>Base URL / IP</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          echo "<td class='text-center'>{$no}</td>";
                          echo "<td>{$row['nama_koneksi']}</td>";
                          echo "<td>{$row['base_url']}</td>";
                          echo "<td class='text-center'>
                                  <a href='master_url.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus koneksi ini?\")'>
                                    <i class='fas fa-trash'></i> Hapus
                                  </a>
                                </td>";
                          echo "</tr>";
                          $no++;
                        }
                      } else {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>

                <?php if ($totalPages > 1): ?>
                  <nav>
                    <ul class="pagination justify-content-center mt-3">
                      <!-- Tombol First -->
                      <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=1&keyword=<?= urlencode($keyword) ?>">First</a>
                      </li>
                      <!-- Tombol Prev -->
                      <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page-1) ?>&keyword=<?= urlencode($keyword) ?>">Prev</a>
                      </li>
                      <!-- Nomor Halaman -->
                      <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): ?>
                          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                          </li>
                      <?php endfor; ?>
                      <!-- Tombol Next -->
                      <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page+1) ?>&keyword=<?= urlencode($keyword) ?>">Next</a>
                      </li>
                      <!-- Tombol Last -->
                      <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $totalPages ?>&keyword=<?= urlencode($keyword) ?>">Last</a>
                      </li>
                    </ul>
                  </nav>
                <?php endif; ?>

              </div>
            </div>
          </div>

        </section>
      </div>
    </div>
  </div>

  <!-- Modal Help -->
  <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-info-circle"></i> Panduan Setting Master URL</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><b>Nama Koneksi</b><br>
          Isi dengan nama bebas yang anda inginkan, misalnya: <code>BPJS</code> atau <code>API Server</code>.</p>
          <p><b>Base URL</b><br>
          Isi dengan alamat URL atau IP tujuan.<br>
          Contoh: <code>https://new-apijkn.bpjs-kesehatan.go.id</code></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
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
</body>
</html>

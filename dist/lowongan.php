<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek akses menu
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Proses tambah / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posisi = trim($_POST['posisi']);
    $tanggal_akhir = trim($_POST['tanggal_akhir']);
    $deskripsi = trim($_POST['deskripsi']);
    $id = $_POST['id'] ?? '';

    // Handle upload foto
    $foto = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/lowongan/' . $foto);
    }

    if ($id) {
        // Update
        if($foto){
            $stmt = $conn->prepare("UPDATE lowongan SET posisi=?, tanggal_akhir=?, deskripsi=?, foto=? WHERE id=?");
            $stmt->bind_param("ssssi", $posisi, $tanggal_akhir, $deskripsi, $foto, $id);
        } else {
            $stmt = $conn->prepare("UPDATE lowongan SET posisi=?, tanggal_akhir=?, deskripsi=? WHERE id=?");
            $stmt->bind_param("sssi", $posisi, $tanggal_akhir, $deskripsi, $id);
        }
        $stmt->execute();
        $stmt->close();
        $flash_message = "Lowongan berhasil diperbarui.";
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO lowongan (posisi, tanggal_akhir, deskripsi, foto) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $posisi, $tanggal_akhir, $deskripsi, $foto);
        $stmt->execute();
        $stmt->close();
        $flash_message = "Lowongan berhasil ditambahkan.";
    }

    $_SESSION['flash_message'] = $flash_message;
    header("Location: lowongan.php");
    exit;
}

// Pencarian
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
<title>f.i.x.p.o.i.n.t - Lowongan Kerja</title>
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
.table-responsive { overflow-x: auto; }
.table td, .table th { white-space: nowrap; }
.modal-lg-custom { max-width: 80% !important; }
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
              <h4>Lowongan Kerja</h4>
              <form method="GET" class="form-inline">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Posisi" />
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
              </form>
            </div>
            <div class="card-body">
              <?php
              if (isset($_SESSION['flash_message'])) {
                  echo "<div id='notif-toast' class='alert alert-info text-center'>{$_SESSION['flash_message']}</div>";
                  unset($_SESSION['flash_message']);
              }
              ?>
              <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalLowongan">Tambah Lowongan</button>
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark">
                    <tr class="text-center">
                      <th>No</th>
                      <th>Posisi</th>
                      <th>Tanggal Akhir</th>
                      <th>Deskripsi</th>
                      <th>Brosur</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
<?php
// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Query
$where = "";
if (!empty($keyword)) {
    $keywordEscaped = mysqli_real_escape_string($conn, $keyword);
    $where = "WHERE posisi LIKE '%$keywordEscaped%'";
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM lowongan $where";
$countResult = mysqli_query($conn, $countQuery);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalData / $limit);

// Fetch data
$query = "SELECT * FROM lowongan $where ORDER BY tanggal_post DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$no = $offset + 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td class='text-center'>{$no}</td>";
        echo "<td>{$row['posisi']}</td>";
        echo "<td class='text-center'>{$row['tanggal_akhir']}</td>";
        echo "<td class='text-center'>";
        echo "<button class='btn btn-info btn-sm lihatDeskripsi' data-posisi='".htmlspecialchars($row['posisi'],ENT_QUOTES)."' data-deskripsi='".htmlspecialchars($row['deskripsi'],ENT_QUOTES)."'><i class='fas fa-eye'></i></button>";
        echo "</td>";
        echo "<td class='text-center'>";
        if($row['foto']){
            echo "<button class='btn btn-warning btn-sm lihatFoto' data-foto='{$row['foto']}'><i class='fas fa-image'></i></button>";
        } else {
            echo "-";
        }
        echo "</td>";
        echo "<td class='text-center'>";
        echo "<a href='#' class='btn btn-primary btn-sm editLowongan' data-id='{$row['id']}' data-posisi='".htmlspecialchars($row['posisi'],ENT_QUOTES)."' data-tanggal='{$row['tanggal_akhir']}' data-deskripsi='".htmlspecialchars($row['deskripsi'],ENT_QUOTES)."'>Edit</a> ";
        echo "<a href='hapus_lowongan.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin hapus?\")'>Hapus</a>";
        echo "</td>";
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>Tidak ada data ditemukan.</td></tr>";
}
?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <nav>
                <ul class="pagination justify-content-center mt-3">
                  <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
                  <?php endif; ?>
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a></li>
                  <?php endfor; ?>
                  <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalLowongan" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg-custom" role="document">
    <form method="post" id="formLowongan" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLowonganLabel">Tambah Lowongan</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="lowongan_id">
          <div class="form-group"><label>Posisi</label><input type="text" name="posisi" id="posisi" class="form-control" required></div>
          <div class="form-group"><label>Tanggal Terakhir Melamar</label><input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required></div>
          <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi" class="form-control" rows="5"></textarea></div>
          <div class="form-group"><label>Brosur Lowongan (Foto)</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Lihat Deskripsi -->
<div class="modal fade" id="modalDeskripsi" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg-custom" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Deskripsi Lowongan</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body"><h5 id="lihatPosisi"></h5><p id="lihatDeskripsi"></p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>

<!-- Modal Lihat Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg-custom" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Brosur Lowongan</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body text-center"><img id="imgBrosur" src="" style="max-width:100%;"></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
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
$(document).ready(function(){
    // Toast
    var toast = $('#notif-toast');
    if (toast.length) { toast.fadeIn(300).delay(2000).fadeOut(500); }

    // Edit Lowongan
    $('.editLowongan').click(function(){
        var id = $(this).data('id');
        var posisi = $(this).data('posisi');
        var tanggal = $(this).data('tanggal');
        var deskripsi = $(this).data('deskripsi');
        $('#modalLowonganLabel').text('Edit Lowongan');
        $('#lowongan_id').val(id);
        $('#posisi').val(posisi);
        $('#tanggal_akhir').val(tanggal);
        $('#deskripsi').val(deskripsi);
        $('#modalLowongan').modal('show');
    });

    // Reset modal
    $('#modalLowongan').on('hidden.bs.modal', function () {
        $('#modalLowonganLabel').text('Tambah Lowongan');
        $('#lowongan_id').val('');
        $('#formLowongan')[0].reset();
    });

    // Lihat Deskripsi
    $('.lihatDeskripsi').click(function(){
        var posisi = $(this).data('posisi');
        var deskripsi = $(this).data('deskripsi');
        $('#lihatPosisi').text(posisi);
        $('#lihatDeskripsi').text(deskripsi);
        $('#modalDeskripsi').modal('show');
    });

    // Lihat Foto
    $('.lihatFoto').click(function(){
        var foto = $(this).data('foto');
        $('#imgBrosur').attr('src', 'uploads/lowongan/' + foto);
        $('#modalFoto').modal('show');
    });
});
</script>
</body>
</html>

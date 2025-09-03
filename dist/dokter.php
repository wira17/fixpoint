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
    $nama_dokter = trim($_POST['nama_dokter']);
    $poliklinik = trim($_POST['poliklinik']);
    $hari_praktek = isset($_POST['hari_praktek']) ? implode(',', $_POST['hari_praktek']) : '';
    $jam_praktek = trim($_POST['jam_praktek']);
    $id = $_POST['id'] ?? '';

    // Handle upload foto
    $foto = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/dokter/' . $foto);
    }

    if ($id) {
        // Update
        if($foto){
            $stmt = $conn->prepare("UPDATE dokter SET nama_dokter=?, poliklinik=?, hari_praktek=?, jam_praktek=?, foto=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama_dokter, $poliklinik, $hari_praktek, $jam_praktek, $foto, $id);
        } else {
            $stmt = $conn->prepare("UPDATE dokter SET nama_dokter=?, poliklinik=?, hari_praktek=?, jam_praktek=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama_dokter, $poliklinik, $hari_praktek, $jam_praktek, $id);
        }
        $stmt->execute();
        $stmt->close();
        $flash_message = "Data dokter berhasil diperbarui.";
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO dokter (nama_dokter, poliklinik, hari_praktek, jam_praktek, foto) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $nama_dokter, $poliklinik, $hari_praktek, $jam_praktek, $foto);
        $stmt->execute();
        $stmt->close();
        $flash_message = "Data dokter berhasil ditambahkan.";
    }

    $_SESSION['flash_message'] = $flash_message;
    header("Location: dokter.php");
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
<title>f.i.x.p.o.i.n.t - Dokter</title>
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
              <h4>Data Dokter</h4>
              <form method="GET" class="form-inline">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control mr-2" placeholder="Cari Nama Dokter / Poliklinik" />
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
              <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalDokter">Tambah Dokter</button>
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark">
                    <tr class="text-center">
                      <th>No</th>
                      <th>Nama Dokter</th>
                      <th>Poliklinik</th>
                      <th>Hari Praktek</th>
                      <th>Jam Praktek</th>
                      <th>Foto</th>
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
    $where = "WHERE nama_dokter LIKE '%$keywordEscaped%' OR poliklinik LIKE '%$keywordEscaped%'";
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM dokter $where";
$countResult = mysqli_query($conn, $countQuery);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalData / $limit);

// Fetch data
$query = "SELECT * FROM dokter $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$no = $offset + 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td class='text-center'>{$no}</td>";
        echo "<td>{$row['nama_dokter']}</td>";
        echo "<td>{$row['poliklinik']}</td>";
        echo "<td class='text-center'>{$row['hari_praktek']}</td>";
        echo "<td class='text-center'>{$row['jam_praktek']}</td>";
        echo "<td class='text-center'>";
        if($row['foto']){
            echo "<button class='btn btn-warning btn-sm lihatFoto' data-foto='{$row['foto']}'><i class='fas fa-image'></i></button>";
        } else {
            echo "-";
        }
        echo "</td>";
        echo "<td class='text-center'>";
        echo "<a href='#' class='btn btn-primary btn-sm editDokter' data-id='{$row['id']}' data-nama='{$row['nama_dokter']}' data-poliklinik='{$row['poliklinik']}' data-hari='{$row['hari_praktek']}' data-jam='{$row['jam_praktek']}'>Edit</a> ";
        echo "<a href='hapus_dokter.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin hapus?\")'>Hapus</a>";
        echo "</td>";
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>Tidak ada data ditemukan.</td></tr>";
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
<div class="modal fade" id="modalDokter" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg-custom" role="document">
    <form method="post" id="formDokter" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDokterLabel">Tambah Dokter</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="dokter_id">
          <div class="form-group"><label>Nama Dokter</label><input type="text" name="nama_dokter" id="nama_dokter" class="form-control" required></div>
          <div class="form-group"><label>Poliklinik</label><input type="text" name="poliklinik" id="poliklinik" class="form-control" required></div>
          <div class="form-group">
            <label>Hari Praktek</label>
            <select name="hari_praktek[]" id="hari_praktek" class="form-control" multiple required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
              <option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="form-group"><label>Jam Praktek</label><input type="text" name="jam_praktek" id="jam_praktek" class="form-control" placeholder="08:00 - 16:00" required></div>
          <div class="form-group"><label>Foto</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Lihat Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg-custom" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Foto Dokter</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body text-center"><img id="imgFoto" src="" style="max-width:100%;"></div>
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

    // Edit Dokter
    $('.editDokter').click(function(){
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var poliklinik = $(this).data('poliklinik');
        var hari = $(this).data('hari').split(',');
        var jam = $(this).data('jam');
        $('#modalDokterLabel').text('Edit Dokter');
        $('#dokter_id').val(id);
        $('#nama_dokter').val(nama);
        $('#poliklinik').val(poliklinik);
        $('#jam_praktek').val(jam);
        $('#hari_praktek').val(hari);
        $('#modalDokter').modal('show');
    });

    // Reset modal
    $('#modalDokter').on('hidden.bs.modal', function () {
        $('#modalDokterLabel').text('Tambah Dokter');
        $('#dokter_id').val('');
        $('#formDokter')[0].reset();
        $('#hari_praktek').val([]);
    });

    // Lihat Foto
    $('.lihatFoto').click(function(){
        var foto = $(this).data('foto');
        $('#imgFoto').attr('src', 'uploads/dokter/' + foto);
        $('#modalFoto').modal('show');
    });
});
</script>
</body>
</html>

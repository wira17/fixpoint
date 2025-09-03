<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Ambil nama user
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['nama'] ?? $_SESSION['username'] ?? '';
if ($nama_user === '' && $user_id > 0) {
    $qUser = mysqli_query($conn, "SELECT nama FROM users WHERE id = $user_id LIMIT 1");
    if ($qUser && mysqli_num_rows($qUser) === 1) $nama_user = mysqli_fetch_assoc($qUser)['nama'];
}
if ($nama_user === '') $nama_user = 'User ID #' . $user_id;

// Cek akses user
$current_file = basename(__FILE__);
$rAkses = mysqli_query($conn, "SELECT 1 FROM akses_menu 
           JOIN menu ON akses_menu.menu_id = menu.id 
           WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'");
if (!$rAkses || mysqli_num_rows($rAkses) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// ==== HANDLE FORM SIMPAN ====
if(isset($_POST['simpan'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pokja_id = intval($_POST['pokja_id']);
    $elemen_penilaian = mysqli_real_escape_string($conn, $_POST['elemen_penilaian']);
    $petugas = $nama_user;
    $waktu_input = date('Y-m-d H:i:s');

    $file_path = NULL;
    $file_name_original = NULL;

    if(isset($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] == 0){
        $uploadDir = 'uploads/dokumen/';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $file_name_original = $_FILES['file_dokumen']['name'];
        $ext = pathinfo($file_name_original, PATHINFO_EXTENSION);
        $newName = uniqid('dokumen_').'.'.$ext;
        $file_path = $uploadDir.$newName;
        if(!move_uploaded_file($_FILES['file_dokumen']['tmp_name'], $file_path)){
            $_SESSION['flash_message'] = 'Upload file gagal!';
            header("Location: input_dokumen.php?tab=input");
            exit;
        }
    }

    $sqlInsert = "INSERT INTO dokumen 
        (judul, pokja_id, elemen_penilaian, file_path, file_name_original, petugas, waktu_input) 
        VALUES 
        ('$judul', $pokja_id, '$elemen_penilaian', ".($file_path ? "'$file_path'" : "NULL").", ".($file_name_original ? "'$file_name_original'" : "NULL").", '$petugas', '$waktu_input')";

    if(mysqli_query($conn, $sqlInsert)){
        $_SESSION['flash_message'] = 'Data berhasil disimpan.';
    } else {
        $_SESSION['flash_message'] = 'Error: '.mysqli_error($conn);
    }
    header("Location: input_dokumen.php?tab=input");
    exit;
}

// Tentukan tab aktif
$activeTab = $_GET['tab'] ?? 'input';

// Ambil list pokja untuk filter
$list_pokja = mysqli_query($conn, "SELECT id, nama_pokja FROM master_pokja ORDER BY nama_pokja ASC");

// Ambil filter
$filter_judul = $_GET['judul'] ?? '';
$filter_pokja = $_GET['pokja_id'] ?? '';

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$sqlCount = "SELECT COUNT(*) AS total FROM dokumen d 
             JOIN master_pokja mp ON d.pokja_id = mp.id WHERE 1=1 ";
if($filter_judul != '') $sqlCount .= " AND d.judul LIKE '%".mysqli_real_escape_string($conn, $filter_judul)."%' ";
if($filter_pokja != '') $sqlCount .= " AND d.pokja_id = ".intval($filter_pokja)." ";
$totalData = mysqli_fetch_assoc(mysqli_query($conn, $sqlCount))['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data dokumen
$sqlData = "SELECT d.*, mp.nama_pokja FROM dokumen d 
            JOIN master_pokja mp ON d.pokja_id = mp.id WHERE 1=1 ";
if($filter_judul != '') $sqlData .= " AND d.judul LIKE '%".mysqli_real_escape_string($conn, $filter_judul)."%' ";
if($filter_pokja != '') $sqlData .= " AND d.pokja_id = ".intval($filter_pokja)." ";
$sqlData .= " ORDER BY d.waktu_input DESC LIMIT $limit OFFSET $offset";
$data_dokumen = mysqli_query($conn, $sqlData);

// Array untuk modal edit
$modals = [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Dokumen</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<style>
  .dokumen-table { font-size: 13px; white-space: nowrap; }
  .dokumen-table th, .dokumen-table td { padding: 6px 10px; vertical-align: middle; }
  .flash-center {
    position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%);
    z-index: 1050; min-width: 300px; max-width: 90%; text-align: center;
    padding: 15px; border-radius: 8px; font-weight: 500;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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

        <?php if(isset($_SESSION['flash_message'])): ?>
          <div class="alert alert-info flash-center" id="flashMsg">
            <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
          </div>
        <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Input Dokumen Akreditasi</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" id="dokumenTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='input')?'active':'' ?>" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Data</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= ($activeTab=='data')?'active':'' ?>" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Dokumen</a>
                </li>
              </ul>

              <div class="tab-content mt-3">
                <!-- Form Input -->
                <div class="tab-pane fade <?= ($activeTab=='input')?'show active':'' ?>" id="input" role="tabpanel">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Judul File/Dokumen</label>
                          <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>Pokja</label>
                          <select name="pokja_id" class="form-control" required>
                            <option value="">-- Pilih Pokja --</option>
                            <?php 
                            mysqli_data_seek($list_pokja, 0);
                            while($p=mysqli_fetch_assoc($list_pokja)): ?>
                              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_pokja']) ?></option>
                            <?php endwhile; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Elemen Penilaian</label>
                          <input type="text" name="elemen_penilaian" class="form-control">
                        </div>
                        <div class="form-group">
                          <label>File (Upload)</label>
                          <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                          <small class="form-text text-muted">Maks 20MB.</small>
                        </div>
                        <div class="form-group">
                          <label>Petugas</label>
                          <input type="text" class="form-control" value="<?= htmlspecialchars($nama_user) ?>" readonly>
                        </div>
                      </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  </form>
                </div>

                <!-- Data Dokumen -->
                <div class="tab-pane fade <?= ($activeTab=='data')?'show active':'' ?>" id="data" role="tabpanel">
                  
                  <!-- Filter Pencarian -->
                  <form method="GET" class="form-inline mb-2">
                    <input type="hidden" name="tab" value="data">
                    <div class="form-group mr-2">
                      <input type="text" name="judul" class="form-control" placeholder="Cari Judul" value="<?= htmlspecialchars($filter_judul) ?>">
                    </div>
                    <div class="form-group mr-2">
                      <select name="pokja_id" class="form-control">
                        <option value="">-- Semua Pokja --</option>
                        <?php 
                        mysqli_data_seek($list_pokja, 0);
                        while($p=mysqli_fetch_assoc($list_pokja)):
                        ?>
                          <option value="<?= $p['id'] ?>" <?= ($filter_pokja==$p['id'])?'selected':'' ?>><?= htmlspecialchars($p['nama_pokja']) ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
                    <a href="data_dokumen.php?tab=data" class="btn btn-secondary btn-sm ml-2"><i class="fas fa-sync"></i> Reset</a>
                  </form>

                  <div class="table-responsive">
                    <table class="table table-bordered dokumen-table">
                      <thead class="thead-dark">
                        <tr>
                          <th>No</th>
                          <th>Judul</th>
                          <th>Pokja</th>
                          <th>Elemen</th>
                          <th>File</th>
                          <th>Petugas</th>
                          <th>Tanggal Input</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        if(mysqli_num_rows($data_dokumen)==0):
                        ?>
                          <tr><td colspan="8" class="text-center">Data tidak ditemukan.</td></tr>
                        <?php 
                        else:
                          $no = $offset + 1;
                          while($dok=mysqli_fetch_assoc($data_dokumen)):
                        ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($dok['judul']) ?></td>
                          <td><?= htmlspecialchars($dok['nama_pokja']) ?></td>
                          <td><?= htmlspecialchars($dok['elemen_penilaian']??'') ?></td>
                          <td>
                            <?php if(!empty($dok['file_path'])): ?>
                              <a href="<?= htmlspecialchars($dok['file_path']) ?>" target="_blank"><i class="fas fa-eye"></i></a>
                            <?php else: ?>-
                            <?php endif; ?>
                          </td>
                          <td><?= htmlspecialchars($dok['petugas']) ?></td>
                          <td><?= date('d-m-Y H:i', strtotime($dok['waktu_input'])) ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $dok['id'] ?>"><i class="fas fa-edit"></i></button>
                            <a href="hapus_dokumen.php?id=<?= $dok['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')"><i class="fas fa-trash"></i></a>
                          </td>
                        </tr>

                        <?php
                        // Modal Edit
                        $modals[] = '
                        <div class="modal fade" id="editModal'.$dok['id'].'" tabindex="-1" role="dialog">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <form method="POST" action="update_dokumen.php?tab=data" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="'.$dok['id'].'">
                                <div class="modal-header">
                                  <h5 class="modal-title">Edit Dokumen</h5>
                                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label>Judul</label>
                                    <input type="text" name="judul" class="form-control" value="'.htmlspecialchars($dok['judul']).'" required>
                                  </div>
                                  <div class="form-group">
                                    <label>Pokja</label>
                                    <select name="pokja_id" class="form-control" required>';
                                    $list_pokja2 = mysqli_query($conn, "SELECT id,nama_pokja FROM master_pokja ORDER BY nama_pokja ASC");
                                    while($p2=mysqli_fetch_assoc($list_pokja2)){
                                        $sel = ($dok['pokja_id']==$p2['id'])?'selected':'';
                                        $modals[count($modals)-1] .= '<option value="'.$p2['id'].'" '.$sel.'>'.htmlspecialchars($p2['nama_pokja']).'</option>';
                                    }
                        $modals[count($modals)-1] .= '</select></div>
                                  <div class="form-group">
                                    <label>Elemen Penilaian</label>
                                    <input type="text" name="elemen_penilaian" class="form-control" value="'.htmlspecialchars($dok['elemen_penilaian']).'">
                                  </div>
                                  <div class="form-group">
                                    <label>Ganti File (Opsional)</label>
                                    <input type="file" name="file_dokumen" class="form-control">
                                    '.(!empty($dok['file_path'])?'<small class="form-text text-muted">File saat ini: '.htmlspecialchars($dok['file_name_original']).'</small>':'').'
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                  <button type="submit" name="update" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>';
                          endwhile; 
                        endif;
                        ?>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <?php if($totalPages>1): ?>
                  <nav aria-label="Page navigation">
                    <ul class="pagination">
                      <?php for($i=1;$i<=$totalPages;$i++): ?>
                        <li class="page-item <?= ($i==$page)?'active':'' ?>">
                          <a class="page-link" href="?tab=data&page=<?= $i ?>&judul=<?= urlencode($filter_judul) ?>&pokja_id=<?= urlencode($filter_pokja) ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>
                    </ul>
                  </nav>
                  <?php endif; ?>

                </div> <!-- End Tab Data -->
              </div> <!-- End Tab Content -->
            </div>
          </div>

        </div>
      </section>
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

<?php foreach($modals as $modal) echo $modal; ?>

<script>
$(document).ready(function() {
  setTimeout(function(){ $("#flashMsg").fadeOut("slow"); }, 2500);
});
</script>
</body>
</html>

<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$current_file = basename(__FILE__);

// Cek apakah user boleh mengakses halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Ambil data jika sedang edit
$edit_setting = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $get = mysqli_query($conn, "SELECT * FROM wa_setting WHERE id = $edit_id");
    if ($get && mysqli_num_rows($get) > 0) {
        $edit_setting = mysqli_fetch_assoc($get);
    }
}

// Hapus setting
if (isset($_GET['hapus_id'])) {
    $hapus_id = intval($_GET['hapus_id']);
    mysqli_query($conn, "DELETE FROM wa_setting WHERE id = $hapus_id");
    header("Location: wa_setting.php");
    exit;
}

// Simpan atau update
if (isset($_POST['simpan'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nilai = mysqli_real_escape_string($conn, $_POST['nilai']);

    if ($id > 0) {
        mysqli_query($conn, "UPDATE wa_setting SET nama = '$nama', nilai = '$nilai', updated_at = NOW() WHERE id = $id");
    } else {
        mysqli_query($conn, "INSERT INTO wa_setting (nama, nilai) VALUES ('$nama', '$nilai')");
    }

    header("Location: wa_setting.php");
    exit;
}

// Ambil semua setting WhatsApp
$all_settings = mysqli_query($conn, "SELECT * FROM wa_setting ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>f.i.x.p.o.i.n.t - WhatsApp Gateway</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="stylesheet" href="assets/css/components.css" />
</head>
<body>
<div id="app">
  <div class="main-wrapper main-wrapper-1">
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
      <section class="section">

        <div class="section-body">
          <!-- FORM TAMBAH / EDIT -->
          <div class="card">
            <div class="card-header">
              <h4>
                <?= $edit_setting ? 'Edit Setting' : 'Tambah Setting' ?>
                <!-- Ikon tanda tanya merah -->
                <a href="javascript:void(0);" data-toggle="modal" data-target="#helpModal" 
                   class="text-danger ml-2" title="Panduan Setting WhatsApp Gateway">
                  <i class="fas fa-question-circle"></i>
                </a>
              </h4>
              <div class="card-header-action">
                <a href="javascript:void(0);" class="btn btn-icon btn-info" data-collapse="#formWA">
                  <i class="fas fa-chevron-down"></i>
                </a>
              </div>
            </div>

            <div class="collapse show" id="formWA">
              <div class="card-body">
                <form method="POST">
                  <input type="hidden" name="id" value="<?= $edit_setting['id'] ?? '' ?>">
                  <div class="form-group">
                    <label>Nama Setting</label>
                    <input type="text" name="nama" class="form-control" required 
                           value="<?= htmlspecialchars($edit_setting['nama'] ?? '') ?>" 
                           placeholder="Contoh: wa_number atau wa_gateway_url">
                  </div>
                  <div class="form-group">
                    <label>Nilai</label>
                    <textarea name="nilai" class="form-control" rows="3" required 
                              placeholder="Masukkan nomor WA atau URL gateway"><?= htmlspecialchars($edit_setting['nilai'] ?? '') ?></textarea>
                  </div>
                  <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                  </button>
                  <?php if ($edit_setting): ?>
                    <a href="wa_setting.php" class="btn btn-secondary ml-2">Batal</a>
                  <?php endif; ?>
                </form>
              </div>
            </div>
          </div>

          <!-- TABEL DATA -->
          <div class="card mt-4">
            <div class="card-header">
              <h4>Data WhatsApp Setting</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Nilai</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $no = 1; if ($all_settings && mysqli_num_rows($all_settings) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($all_settings)) : ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['nilai']) ?></td>
                        <td>
                          <a href="wa_setting.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                          </a>
                          <a href="wa_setting.php?hapus_id=<?= $row['id'] ?>" 
                             onclick="return confirm('Yakin ingin menghapus?')" 
                             class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                          </a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center text-danger">Data WhatsApp Setting masih kosong.</td>
                    </tr>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- END TABEL -->

        </div>
      </section>
    </div>
  </div>
</div>

<!-- Modal Panduan Setting -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-info-circle"></i> Panduan Setting WhatsApp Gateway</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <ol>
          <li><b>wa_number</b> → Masukkan nomor WhatsApp yang sudah terhubung dengan gateway.  
              Contoh: <code>6281234567890</code> (gunakan format internasional tanpa tanda +).</li>
          <li><b>wa_gateway_url</b> → Masukkan URL endpoint gateway WA Anda.  
              Contoh: <code>https://domain-gateway.com/send-message</code></li>
          <li><b>api_key</b> (jika diperlukan) → Masukkan API Key yang diberikan oleh penyedia gateway.</li>
          <li>Pastikan server Anda dapat mengakses URL gateway tanpa terblokir firewall.</li>
          <li>Gunakan tombol <b>Simpan</b> untuk menyimpan konfigurasi.</li>
        </ol>
        <p class="text-danger"><b>Catatan:</b> Hubungi penyedia layanan WhatsApp Gateway Anda jika ada parameter tambahan yang harus ditambahkan.</p>
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
    $("[data-collapse]").on("click", function () {
      const icon = $(this).find("i");
      $($(this).data("collapse")).collapse("toggle");
      icon.toggleClass("fa-chevron-down fa-chevron-up");
    });
  });
</script>

</body>
</html>

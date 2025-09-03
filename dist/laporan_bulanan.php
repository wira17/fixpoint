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


$user_id = $_SESSION['user_id'];
$queryUser = mysqli_query($conn, "SELECT nik, nama, jabatan, unit_kerja FROM users WHERE id = '$user_id'");
$userData = mysqli_fetch_assoc($queryUser);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Bulanan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <style>
 
  .modal-backdrop { z-index: 1040 !important; }
  .modal { z-index: 1050 !important; }
  .table-nowrap td, .table-nowrap th {
    white-space: nowrap;
    vertical-align: middle;
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
              <h4><i class="fas fa-calendar-alt text-primary mr-2"></i> Laporan Kerja Bulanan</h4>
            </div>

            <div class="card-body">
              <ul class="nav nav-tabs" id="laporanTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab">Input Laporan</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">Riwayat Laporan</a>
                </li>
              </ul>

              <div class="tab-content mt-4" id="laporanTabContent">
                <!-- Form Input -->
                <div class="tab-pane fade show active" id="input" role="tabpanel">
                  <form method="POST" action="simpan_laporan_bulanan.php" enctype="multipart/form-data">

                    <div class="row">
                      <div class="form-group col-md-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?= $userData['nik']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $userData['nama']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?= $userData['jabatan']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Unit Kerja</label>
                        <input type="text" name="unit_kerja" class="form-control" value="<?= $userData['unit_kerja']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control" required>
                          <option value="">Pilih Bulan</option>
                          <?php
                          $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                                    '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                          foreach ($bulan as $key => $val) {
                            echo "<option value='$key'>$val</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y'); ?>" required>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Judul Laporan</label>
                        <input type="text" name="judul" class="form-control" required>
                      </div>
                      <div class="form-group col-md-12">
                        <label>Uraian Kegiatan / Capaian</label>
                        <textarea name="keterangan" class="form-control" rows="4" required></textarea>
                      </div>
                      <div class="form-group col-md-12">
                        <label>Unggah Dokumen (Opsional, PDF/Word/Excel)</label>
                        <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                      </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Laporan</button>
                  </form>
                </div>

                <!-- Riwayat -->
                <div class="tab-pane fade" id="riwayat" role="tabpanel">
                  <div class="table-responsive-custom mt-3">
                    <table class="table table-bordered table-striped">
                   <thead>
  <tr>
    <th style="width: 40px;">No</th>
    <th style="width: 80px;">Bulan</th>
    <th style="width: 60px;">Tahun</th>
    <th style="width: 180px;">Judul</th>
    <th>Keterangan</th>
    <th style="width: 80px;">Dokumen</th>
    <th style="width: 70px;">Aksi</th>
  </tr>
</thead>

                      <tbody>
                        <?php
                        $no = 1;
                        $q = mysqli_query($conn, "SELECT * FROM laporan_bulanan WHERE user_id = '$user_id' ORDER BY tahun DESC, bulan DESC");
                        if (mysqli_num_rows($q) > 0) {
                          while ($d = mysqli_fetch_assoc($q)) {
                            echo "<tr>
                              <td>{$no}</td>
                              <td>" . $bulan[$d['bulan']] . "</td>
                              <td>{$d['tahun']}</td>
                              <td>" . htmlspecialchars($d['judul']) . "</td>
                              <td>" . nl2br(htmlspecialchars($d['keterangan'])) . "</td>
                              <td>";
                              if (!empty($d['file_laporan'])) {
                                echo "<a href='uploads/laporan_bulanan/{$d['file_laporan']}' target='_blank' class='btn btn-sm btn-secondary'><i class='fas fa-file-download'></i></a>";
                              } else {
                                echo "<span class='text-muted'>-</span>";
                              }
                              echo "</td>
                          <td>
<button type='button' class='btn btn-sm btn-warning editBtn' 
  data-id='{$d['id']}' 
  data-bulan='{$d['bulan']}' 
  data-tahun='{$d['tahun']}'
  data-judul=\"" . htmlspecialchars($d['judul'], ENT_QUOTES) . "\"
  data-keterangan=\"" . htmlspecialchars($d['keterangan'], ENT_QUOTES) . "\"
  data-toggle='modal' data-target='#editModal'>
  <i class='fas fa-edit'></i>
</button>

</td>


                            </tr>";
                            $no++;
                          }
                        } else {
                          echo "<tr><td colspan='7' class='text-center'>Belum ada laporan.</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>





                  </div>
                </div> <!-- End Riwayat -->
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>
  </div>
</div>


<!-- Modal Edit -->
<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document"> <!-- Tambah modal-dialog-scrollable -->
    <form action="update_laporan_bulanan.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Laporan Bulanan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <!-- Tambah max-height dan overflow di sini -->
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <div class="form-row">
            <input type="hidden" name="id" id="edit-id">

            <div class="form-group col-md-4">
              <label>Bulan</label>
              <select name="bulan" id="edit-bulan" class="form-control" required>
                <?php foreach ($bulan as $key => $val) {
                  echo "<option value='$key'>$val</option>";
                } ?>
              </select>
            </div>

            <div class="form-group col-md-4">
              <label>Tahun</label>
              <input type="number" name="tahun" id="edit-tahun" class="form-control" required>
            </div>

            <div class="form-group col-md-12">
              <label>Judul</label>
              <input type="text" name="judul" id="edit-judul" class="form-control" required>
            </div>

            <div class="form-group col-md-12">
              <label>Uraian</label>
              <textarea name="keterangan" id="edit-keterangan" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group col-md-12">
              <label>Ganti Dokumen (Opsional)</label>
              <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>



<!-- SCRIPTS -->
<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/modules/moment.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>
<script>
$(document).ready(function(){
  $('.editBtn').on('click', function(){
    var id = $(this).data('id');
    var bulan = $(this).data('bulan');
    var tahun = $(this).data('tahun');
    var judul = $(this).data('judul');
    var keterangan = $(this).data('keterangan');

    $('#edit-id').val(id);
    $('#edit-bulan').val(bulan);
    $('#edit-tahun').val(tahun);
    $('#edit-judul').val(judul);
    $('#edit-keterangan').val(keterangan);

    // Hapus modal backdrop sebelumnya jika ada
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');

    // Tampilkan modal
    $('#editModal').modal({
      backdrop: 'static',
      keyboard: false
    });
  });
});
</script>


</body>
</html>

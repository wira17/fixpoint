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
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
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
    .table-responsive-custom {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive-custom table {
      width: 100%;
      min-width: 1200px;
      white-space: nowrap;
    }

    .d-flex.gap-1 > form {
      margin-right: 5px;
    }
    .table thead th {
      background-color: #000 !important;
      color: #fff !important;
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
                <i class="fas fa-user-clock text-danger mr-2"></i>
                Laporan Off-Duty (diluar jam kerja)
                <i class="fas fa-question-circle help-icon" data-toggle="modal" data-target="#helpModal"></i>
              </h4>
            </div>

            <div class="card-body">
              <ul class="nav nav-tabs" id="tabMenu" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab">Form Laporan</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="laporan-tab" data-toggle="tab" href="#laporan" role="tab">Laporan Saya</a>
                </li>
              </ul>

              <div class="tab-content mt-3">
                <!-- Tab Form -->
                <div class="tab-pane fade show active" id="form" role="tabpanel">
                  <form method="POST" action="simpan_off_duty.php" id="formOffDuty">
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?= $userData['nik']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $userData['nama']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?= $userData['jabatan']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Unit Kerja</label>
                        <input type="text" name="unit_kerja" class="form-control" value="<?= $userData['unit_kerja']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label>Kategori</label>
                        <select class="form-control" name="kategori" id="kategori" required>
                          <option value="">-- Pilih Kategori --</option>
                          <optgroup label="Hardware">
                            <?php
                            $hardware = mysqli_query($conn, "SELECT nama_kategori FROM kategori_hardware");
                            while ($h = mysqli_fetch_assoc($hardware)) {
                              echo "<option value='hardware:{$h['nama_kategori']}'>{$h['nama_kategori']}</option>";
                            }
                            ?>
                          </optgroup>
                          <optgroup label="Software">
                            <?php
                            $software = mysqli_query($conn, "SELECT nama_kategori FROM kategori_software");
                            while ($s = mysqli_fetch_assoc($software)) {
                              echo "<option value='software:{$s['nama_kategori']}'>{$s['nama_kategori']}</option>";
                            }
                            ?>
                          </optgroup>
                        </select>
                      </div>

                      <div class="form-group col-md-4" id="petugas-container" style="display: none;">
                        <label>Petugas IT</label>
                        <select name="petugas" class="form-control" id="petugas">
                          <option value="">-- Pilih Petugas --</option>
                        </select>
                      </div>

                      <div class="form-group col-md-12">
                        <label>Keterangan / Kendala</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                      </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-danger">Kirim Laporan Off-Duty</button>
                  </form>
                </div>

                <!-- Tab Laporan -->
                <div class="tab-pane fade" id="laporan" role="tabpanel">
                  <div class="table-responsive-custom">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>No Tiket</th>
                          <th>Tanggal</th>
                          <th>Kategori</th>
                          <th>Petugas IT</th>
                          <th>Keterangan</th>
                          <th>Status</th>
                          <th>Catatan IT</th>
                          <th>Tanggal Validasi</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $laporan = mysqli_query($conn, "SELECT * FROM laporan_off_duty WHERE user_id = '$user_id' ORDER BY tanggal DESC");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($laporan)) {
                          $validator = '-';
                          if (!empty($row['validator_id'])) {
                            $qv = mysqli_query($conn, "SELECT nama FROM users WHERE id = '{$row['validator_id']}'");
                            $dv = mysqli_fetch_assoc($qv);
                            $validator = $dv['nama'] ?? 'Tidak Diketahui';
                          }

                          echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['no_tiket']}</td>
                            <td>" . date('d-m-Y H:i', strtotime($row['tanggal'])) . "</td>
                            <td>{$row['kategori']}</td>
                            <td>{$row['petugas']}</td>
                            <td>{$row['keterangan']}</td>
                            <td>" . renderValidasiBadge($row['status_validasi']) . "</td>
                            <td>" . (!empty($row['catatan_it']) ? htmlspecialchars($row['catatan_it']) : '-') . "</td>
                            <td>" . (!empty($row['tanggal_validasi']) ? date('d-m-Y H:i', strtotime($row['tanggal_validasi'])) : '-') . "</td>
                            <td>
                              <a href='cetak_off_duty.php?id={$row['id']}' target='_blank' title='Cetak Tiket'>
                                <i class='fa fa-print text-primary'></i>
                              </a>
                            </td>
                          </tr>";
                          $no++;
                        }

                        function renderValidasiBadge($status) {
                          switch (strtolower($status)) {
                            case 'menunggu':
                              return "<span class='badge badge-warning'>Menunggu</span>";
                            case 'diproses':
                              return "<span class='badge badge-info'>Diproses</span>";
                            case 'selesai':
                              return "<span class='badge badge-success'>Selesai</span>";
                            case 'tidak bisa diperbaiki':
                              return "<span class='badge badge-dark'>Tidak Bisa Diperbaiki</span>";
                            case 'ditolak':
                              return "<span class='badge badge-danger'>Ditolak</span>";
                            default:
                              return "<span class='badge badge-secondary'>-</span>";
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div> <!-- End Tab Laporan -->
              </div>
            </div> <!-- End Card Body -->

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
        <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-info-circle"></i> Pengertian Off-Duty</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <!-- Gambar ilustrasi kecil -->
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
             alt="Ilustrasi Off-Duty" 
             style="width:80px; height:80px; margin-bottom:15px;">

        <p><b>Off-Duty</b> adalah laporan gangguan atau kendala yang terjadi <u>di luar jam kerja resmi</u> 
        yang membutuhkan penanganan segera oleh tim IT.</p>
        <p>Contohnya: gangguan akses sistem, error aplikasi, atau masalah jaringan yang muncul 
        setelah jam operasional normal.</p>
        <p>Form ini digunakan agar kendala dapat dicatat, ditindaklanjuti, dan dipantau 
        penyelesaiannya meskipun terjadi di luar jam kerja.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<script src="assets/modules/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/modules/popper.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="assets/js/stisla.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="assets/js/custom.js"></script>

<script>
  $('#kategori').on('change', function () {
    const val = $(this).val();
    const tipe = val.split(':')[0];

    if (tipe === 'hardware' || tipe === 'software') {
      $('#petugas-container').show();
      $('#petugas').html('<option>Memuat...</option>');

      $.getJSON('get_petugas.php?tipe=' + tipe, function (data) {
        let options = '<option value="">-- Pilih Petugas --</option>';
        data.forEach(function (item) {
          options += `<option value="${item.value}">${item.label}</option>`;
        });
        $('#petugas').html(options);
      });

    } else {
      $('#petugas-container').hide();
      $('#petugas').val('');
    }
  });
</script>
</body>
</html>

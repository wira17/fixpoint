<?php
include 'security.php'; 
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

// Fungsi format tanggal Indonesia
function formatTanggalIndo($tanggal) {
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1] - 1] . ' ' . $pecah[0];
}

// Filter bulan & tahun
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Ambil nomor WA direktur dari users
$wa_user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_hp FROM users WHERE jabatan='Direktur' LIMIT 1"));
$wa_number = $wa_user_row['no_hp'] ?? '';

// Ambil URL gateway WA dari tabel wa_setting
$wa_gateway_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM wa_setting WHERE nama='wa_gateway_url' LIMIT 1"));
$wa_gateway = $wa_gateway_row['nilai'] ?? '';

$notif = '';

// Proses simpan agenda
if (isset($_POST['simpan'])) {
    $judul      = mysqli_real_escape_string($conn, $_POST['judul']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tanggal    = $_POST['tanggal'];
    $jam        = $_POST['jam'];

    $file_pendukung = '';
    if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['file_pendukung']['name'], PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $file_pendukung = 'agenda_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['file_pendukung']['tmp_name'], 'uploads/' . $file_pendukung);
        }
    }

    // Insert ke database
    $insert = mysqli_query($conn, "INSERT INTO agenda_direktur (
        judul, keterangan, tanggal, jam, file_pendukung, tgl_input, user_input
    ) VALUES (
        '$judul', '$keterangan', '$tanggal', '$jam', '$file_pendukung', NOW(), '$user_id'
    )");

    if ($insert) {
        // Kirim WA otomatis
        $wa_sent = false;

        if ($wa_number && $wa_gateway) {
            $wa_text = "📝 *AGENDA BARU DIREKTUR*\n";
            $wa_text .= "Judul: $judul\n";
            $wa_text .= "Tanggal: " . formatTanggalIndo($tanggal) . "\n";
            $wa_text .= "Jam: $jam\n";
            $wa_text .= "Keterangan: $keterangan";

            if ($file_pendukung) {
                $wa_text .= "\nFile: http://" . $_SERVER['HTTP_HOST'] . "/uploads/" . $file_pendukung;
            }

            $wa_data = http_build_query([
                'number' => $wa_number,
                'text'   => $wa_text
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $wa_gateway);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $wa_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($response !== false) {
                $wa_sent = true;
            } else {
                $wa_sent = false;
                error_log("WA gagal: $curl_error");
            }
        }

        $notif .= $wa_sent
            ? "Berhasil menyimpan agenda dan terkirim ke WhatsApp!"
            : "Berhasil menyimpan agenda, namun gagal mengirim WhatsApp!";
    } else {
        $notif = "Gagal menyimpan agenda!";
    }
}

// Hitung total & ambil data agenda
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM agenda_direktur WHERE MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun'");
$total_result = mysqli_fetch_assoc($total_query);
$total_data = $total_result['total'];
$total_pages = ceil($total_data / $limit);

$data_agenda = mysqli_query($conn, "SELECT a.*, u.nama AS user_nama 
    FROM agenda_direktur a 
    LEFT JOIN users u ON a.user_input = u.id 
    WHERE MONTH(a.tanggal)='$filter_bulan' AND YEAR(a.tanggal)='$filter_tahun'
    ORDER BY a.tanggal DESC
    LIMIT $limit OFFSET $offset");

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Agenda Direktur</title>
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.table-responsive-custom { width: 100%; overflow-x: auto; }
.table-agenda { white-space: nowrap; min-width: 1000px; }
.table-agenda tr[style] { font-weight: bold; color: #155724; }
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
                        <div class="card-header"><h4>Agenda Direktur</h4></div>
                        <div class="card-body">

                            <ul class="nav nav-tabs" id="agendaTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?= (isset($_GET['bulan']) ? '' : 'active') ?>" id="form-tab" data-toggle="tab" href="#form" role="tab">Input Agenda</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (isset($_GET['bulan']) ? 'active' : '') ?>" id="data-tab" data-toggle="tab" href="#data" role="tab">Data Agenda</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="agendaTabContent">
                                <div class="tab-pane fade <?= (isset($_GET['bulan']) ? '' : 'show active') ?>" id="form" role="tabpanel">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group"><label>Judul Agenda</label><input name="judul" class="form-control" required></div>
                                                <div class="form-group"><label>Tanggal Agenda</label><input type="date" name="tanggal" class="form-control" required></div>
                                                <div class="form-group"><label>Jam Agenda</label><input type="time" name="jam" class="form-control" required></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3" required></textarea></div>
                                                <div class="form-group"><label>File Pendukung (PDF)</label><input type="file" name="file_pendukung" accept=".pdf" class="form-control"></div>
                                            </div>
                                        </div>
                                        <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                    </form>
                                </div>

                                <div class="tab-pane fade <?= (isset($_GET['bulan']) ? 'show active' : '') ?>" id="data" role="tabpanel">
                                    <div class="table-responsive-custom">
                                        <form method="GET" class="form-inline mb-3">
                                            <label class="mr-2">Filter Bulan:</label>
                                            <select name="bulan" class="form-control mr-2">
                                                <?php foreach ($bulanIndo as $num => $nama) {
                                                    $selected = ($filter_bulan == $num) ? 'selected' : '';
                                                    echo "<option value='$num' $selected>$nama</option>";
                                                } ?>
                                            </select>
                                            <select name="tahun" class="form-control mr-2">
                                                <?php
                                                $tahun_sekarang = date('Y');
                                                for ($t = $tahun_sekarang - 2; $t <= $tahun_sekarang + 2; $t++) {
                                                    $selected = ($filter_tahun == $t) ? 'selected' : '';
                                                    echo "<option value='$t' $selected>$t</option>";
                                                }
                                                ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Tampilkan</button>
                                            <a href="cetak_agenda.php?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>" target="_blank" class="btn btn-success"><i class="fas fa-print"></i> Cetak</a>
                                        </form>

                                        <table class="table table-bordered table-striped table-agenda">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Judul</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>Keterangan</th>
                                                    <th>File</th>
                                                    <th>WA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                $today = date('Y-m-d');
                                                while ($row = mysqli_fetch_assoc($data_agenda)) :
                                                    $highlight = ($row['tanggal'] == $today) ? 'style="background-color: #d4edda;"' : '';
                                                ?>
                                                <tr <?= $highlight ?>>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                                    <td><?= formatTanggalIndo($row['tanggal']) ?></td>
                                                    <td><?= htmlspecialchars(substr($row['jam'],0,5)) ?></td>
                                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                                    <td>
                                                        <?php if ($row['file_pendukung']) : ?>
                                                            <a href="uploads/<?= $row['file_pendukung'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-file-pdf"></i> Lihat
                                                            </a>
                                                        <?php else : ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="kirim_wa.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-success" title="Kirim WA">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>

                                        <nav>
                                            <ul class="pagination justify-content-center">
                                                <?php for ($i=1; $i<=$total_pages; $i++): ?>
                                                    <li class="page-item <?= ($page==$i)?'active':'' ?>">
                                                        <a class="page-link" href="?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>&page=<?= $i ?>#data"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </nav>

                                    </div>
                                </div> <!-- End tab data -->
                            </div> <!-- End tab content -->

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
<script>
$(document).ready(function(){
    var hash = window.location.hash;
    if(hash){ $('.nav-tabs a[href="'+hash+'"]').tab('show'); }

    // SweetAlert notifikasi
    <?php if($notif): ?>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            html: '<?= $notif ?>',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true
        });
    <?php endif; ?>
});
</script>
</body>
</html>

<?php
include 'security.php';
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$mode = $_GET['mode'] ?? ''; // 'target' atau kosong
$imut_id = isset($_GET['imut_id']) ? intval($_GET['imut_id']) : 0;

if ($imut_id <= 0) {
    if ($mode === 'target') {
        echo json_encode(['status' => 'error', 'message' => 'Indikator tidak valid']);
    } else {
        echo "<div class='alert alert-danger'>Indikator tidak valid.</div>";
    }
    exit;
}

// MODE 1: Ambil target indikator (JSON)
if ($mode === 'target') {
    $q = mysqli_query($conn, "SELECT target FROM master_imut_rs WHERE id = $imut_id");
    if ($row = mysqli_fetch_assoc($q)) {
        echo json_encode(['status' => 'ok', 'target' => $row['target']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
    exit;
}

// MODE 2: Ambil tabel capaian (HTML)

// Array nama bulan bahasa Indonesia
$nama_bulan_indo = [
  'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
  'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
  'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];

// Query data capaian dengan join unit_kerja
$sql = "SELECT c.id, c.tanggal, c.nilai, u.nama_unit 
        FROM capaian_imut_rs c 
        JOIN unit_kerja u ON c.unit_id = u.id
        WHERE c.imut_id = $imut_id 
        ORDER BY c.tanggal DESC, u.nama_unit ASC";

$res = mysqli_query($conn, $sql);
?>
<table class="table table-bordered table-sm table-hover">
    <thead class="thead-light">
        <tr>
            <th>No</th>
            <th>Unit RS</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th class="text-right">Capaian (%)</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($res) > 0): 
            $no = 1;
            while($row = mysqli_fetch_assoc($res)):
                $bulan_en = date('F', strtotime($row['tanggal']));
                $tahun = date('Y', strtotime($row['tanggal']));
                $nama_bulan = $nama_bulan_indo[$bulan_en] ?? $bulan_en;
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_unit']); ?></td>
            <td><?= $nama_bulan; ?></td>
            <td><?= $tahun; ?></td>
            <td class="text-right"><?= number_format($row['nilai'], 2); ?></td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus" title="Hapus data capaian" data-id="<?= $row['id']; ?>">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6" class="text-center">Belum ada data capaian.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
$(function(){
    $('.btn-hapus').click(function(){
        if (!confirm('Yakin ingin menghapus data capaian ini?')) return;
        var id = $(this).data('id');
        $.post('hapus_capaian_imut_rs.php', {id: id}, function(res){
            if(res.status === 'ok'){
                // Reload tabel capaian
                $.get('get_capaian_imut_rs.php', {imut_id: <?= $imut_id ?>}, function(html){
                    $('#tabelCapaian').html(html);
                });
            } else {
                alert(res.message || 'Gagal menghapus data');
            }
        }, 'json');
    });
});
</script>

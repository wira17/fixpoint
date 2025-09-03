<?php
include 'security.php';
include 'koneksi.php';

$indikator_id = intval($_GET['id'] ?? 0);

// Urutkan dari bulan/tahun terlama ke terbaru
$sql = "SELECT * FROM capaian_imut 
        WHERE indikator_id = $indikator_id 
        ORDER BY tahun ASC, bulan ASC";
$q = mysqli_query($conn, $sql);

$bulanArr = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];

if (mysqli_num_rows($q) > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-sm table-striped table-hover">';
    echo '<thead class="thead-dark">
            <tr class="text-center">
              <th>No</th>
              <th>Bulan</th>
              <th>Tahun</th>
              <th>Target (%)</th>
              <th>Numerator</th>
              <th>Denominator</th>
              <th>Capaian (%)</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>';
    $no = 1;
    while ($r = mysqli_fetch_assoc($q)) {
        $target = number_format($r['target'], 2);
        $numerator = number_format($r['numerator'], 0);
        $denominator = number_format($r['denominator'], 0);
        $capaian = number_format($r['capaian'], 2);

        if ($r['capaian'] >= $r['target']) {
            $badge = 'badge-success';
        } elseif ($r['capaian'] >= ($r['target'] * 0.8)) {
            $badge = 'badge-warning';
        } else {
            $badge = 'badge-danger';
        }

        echo '<tr>
                <td class="text-center">'.$no++.'</td>
                <td>'.$bulanArr[$r['bulan']].'</td>
                <td class="text-center">'.$r['tahun'].'</td>
                <td class="text-right">'.$target.'</td>
                <td class="text-right">'.$numerator.'</td>
                <td class="text-right">'.$denominator.'</td>
                <td class="text-center"><span class="badge '.$badge.'">'.$capaian.'</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" title="Edit" onclick="openEditModal('.$r['id'].')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Hapus" onclick="hapusData('.$r['id'].')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
} else {
    echo '<div class="alert alert-info text-center">Belum ada data capaian.</div>';
}
?>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-sm" style="max-width:400px;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title">Edit Capaian</h6>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-2" id="editFormContent">
        <div class="text-center text-muted">Memuat...</div>
      </div>
    </div>
  </div>
</div>

<script>
function openEditModal(id) {
    $('#editModal').modal('show');
    $('#editFormContent').html('<div class="text-center text-muted">Memuat...</div>');
    $.get('form_edit_capaian.php', { id: id }, function(html) {
        $('#editFormContent').html(html);
    });
}

function hapusData(id) {
    if (confirm('Yakin ingin menghapus data ini?')) {
        $.post('hapus_capaian.php', { id: id }, function(res) {
            if (res.status === 'ok') {
                alert('Data berhasil dihapus.');
                location.reload();
            } else {
                alert(res.message || 'Gagal menghapus data.');
            }
        }, 'json');
    }
}
</script>

<?php
include 'security.php';
include 'koneksi.php';

if (!isset($_GET['imp_id'])) {
    echo 'ID IMP tidak ditemukan.';
    exit;
}

$imp_id = intval($_GET['imp_id']);

// Query data capaian
$query = mysqli_query($conn, "SELECT * FROM capaian_imp WHERE imp_id = $imp_id ORDER BY tahun DESC, bulan DESC");

if (mysqli_num_rows($query) > 0) {
    echo '<table class="table table-sm table-bordered">';
    echo '<thead class="thead-light">';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Bulan</th>';
    echo '<th>Tahun</th>';
    echo '<th>Target (%)</th>';
    echo '<th>Numerator</th>';
    echo '<th>Denominator</th>';
    echo '<th>Capaian (%)</th>';
    echo '<th>Aksi</th>';
    echo '</tr>';
    echo '</thead><tbody>';

    $bulan_nama = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];

    $no = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        $bulan = $bulan_nama[$row['bulan']] ?? $row['bulan'];
        echo '<tr>';
        echo '<td>'.$no++.'</td>';
        echo '<td>'.$bulan.'</td>';
        echo '<td>'.$row['tahun'].'</td>';
        echo '<td>'.$row['target'].'</td>';
        echo '<td>'.$row['numerator'].'</td>';
        echo '<td>'.$row['denominator'].'</td>';
        echo '<td>'.$row['capaian'].'</td>';
        echo '<td>
                <button class="btn btn-danger btn-sm btn-hapus" data-id="'.$row['id'].'">
                  <i class="fas fa-trash"></i> Hapus
                </button>
              </td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<div class="text-center text-muted">Belum ada data capaian.</div>';
}
?>

<script>
$(document).ready(function(){
  // Handle hapus capaian
  $('.btn-hapus').click(function(){
    if (!confirm('Yakin hapus data capaian ini?')) return;

    var id = $(this).data('id');
    $.post('hapus_capaian_imp.php', {id: id}, function(res){
      if(res.status == 'ok'){
        alert('Data capaian berhasil dihapus');
        // Refresh tabel
        $('.btn-capaian[data-id="<?= $imp_id ?>"]').click();
      } else {
        alert(res.message || 'Gagal menghapus data');
      }
    }, 'json');
  });
});
</script>

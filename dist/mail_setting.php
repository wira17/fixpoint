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

// Ambil data setting pertama (jika ada)
$mail_setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mail_settings LIMIT 1"));

// Proses simpan / update
if (isset($_POST['simpan'])) {
  $id               = intval($_POST['id'] ?? 0);
  $mail_host        = mysqli_real_escape_string($conn, $_POST['mail_host']);
  $mail_port        = intval($_POST['mail_port']);
  $mail_username    = mysqli_real_escape_string($conn, $_POST['mail_username']);
  $mail_password    = mysqli_real_escape_string($conn, $_POST['mail_password']);
  $mail_from_email  = mysqli_real_escape_string($conn, $_POST['mail_from_email']);
  $mail_from_name   = mysqli_real_escape_string($conn, $_POST['mail_from_name']);
  $base_url         = mysqli_real_escape_string($conn, $_POST['base_url']);

  if ($id > 0) {
    mysqli_query($conn, "UPDATE mail_settings SET 
      mail_host='$mail_host',
      mail_port=$mail_port,
      mail_username='$mail_username',
      mail_password='$mail_password',
      mail_from_email='$mail_from_email',
      mail_from_name='$mail_from_name',
      base_url='$base_url'
      WHERE id=$id");
  } else {
    mysqli_query($conn, "INSERT INTO mail_settings (
      mail_host, mail_port, mail_username, mail_password,
      mail_from_email, mail_from_name, base_url
    ) VALUES (
      '$mail_host', $mail_port, '$mail_username', '$mail_password',
      '$mail_from_email', '$mail_from_name', '$base_url'
    )");
  }
  header("Location: mail_setting.php");
  exit;
}

// Ambil semua data mail_settings
$all_settings = mysqli_query($conn, "SELECT * FROM mail_settings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>f.i.x.p.o.i.n.t</title>
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
            <div class="card">
              <div class="card-header">
                <h4>Form Mail Setting</h4>
                <div class="card-header-action">
                  <a href="javascript:void(0);" class="btn btn-icon btn-info" data-collapse="#formMail">
                    <i class="fas fa-chevron-down"></i>
                  </a>
                </div>
              </div>

              <div class="collapse show" id="formMail">
                <div class="card-body">
                  <form method="POST">
                    <input type="hidden" name="id" value="<?= $mail_setting['id'] ?? '' ?>">
                    <div class="form-group">
                      <label>Mail Host</label>
                      <input type="text" name="mail_host" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_host'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Mail Port</label>
                      <input type="number" name="mail_port" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_port'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Mail Username</label>
                      <input type="text" name="mail_username" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_username'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Mail Password</label>
                      <input type="password" name="mail_password" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_password'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>From Email</label>
                      <input type="email" name="mail_from_email" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_from_email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>From Name</label>
                      <input type="text" name="mail_from_name" class="form-control" value="<?= htmlspecialchars($mail_setting['mail_from_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Base URL</label>
                      <input type="text" name="base_url" class="form-control" value="<?= htmlspecialchars($mail_setting['base_url'] ?? '') ?>" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">
                      <i class="fas fa-save"></i> Simpan
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- TABEL DATA -->
            <div class="card mt-4">
              <div class="card-header">
                <h4>Data Mail Settings</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Host</th>
                        <th>Port</th>
                        <th>Username</th>
                        <th>From Email</th>
                        <th>From Name</th>
                        <th>Base URL</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      while ($row = mysqli_fetch_assoc($all_settings)) :
                      ?>
                       <tr>
  <td><?= $no++ ?></td>
  <td><?= htmlspecialchars($row['mail_host']) ?></td>
  <td><?= htmlspecialchars($row['mail_port']) ?></td>
  <td><?= htmlspecialchars($row['mail_username']) ?></td>
  <td><?= htmlspecialchars($row['mail_from_email']) ?></td>
  <td><?= htmlspecialchars($row['mail_from_name']) ?></td>
  <td><?= htmlspecialchars($row['base_url']) ?></td>
  <td>
    <button 
      class="btn btn-sm btn-warning btn-edit"
      data-id="<?= $row['id'] ?>"
      data-host="<?= htmlspecialchars($row['mail_host']) ?>"
      data-port="<?= htmlspecialchars($row['mail_port']) ?>"
      data-username="<?= htmlspecialchars($row['mail_username']) ?>"
      data-password="<?= htmlspecialchars($row['mail_password']) ?>"
      data-from_email="<?= htmlspecialchars($row['mail_from_email']) ?>"
      data-from_name="<?= htmlspecialchars($row['mail_from_name']) ?>"
      data-base_url="<?= htmlspecialchars($row['base_url']) ?>"
    >
      <i class="fas fa-edit"></i> Edit
    </button>
  </td>
</tr>

                      <?php endwhile; ?>
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

  <!-- JS Scripts -->
   <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <!-- Script toggle collapse icon -->
  <script>
    $(document).ready(function () {
      $("[data-collapse]").each(function () {
        var $this = $(this),
            target = $this.data("collapse");

        $this.on("click", function () {
          $(target).collapse("toggle");

          var icon = $(this).find("i");
          if (icon.hasClass("fa-chevron-down")) {
            icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
          } else {
            icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
          }
        });
      });
    });
  </script>

  <script>
  $(document).ready(function () {
    // Collapse icon toggle
    $("[data-collapse]").each(function () {
      var $this = $(this),
          target = $this.data("collapse");

      $this.on("click", function () {
        $(target).collapse("toggle");
        var icon = $(this).find("i");
        icon.toggleClass("fa-chevron-down fa-chevron-up");
      });
    });

    // Isi form saat tombol edit diklik
    $('.btn-edit').on('click', function () {
      $('#formMail').collapse('show');

      $('input[name="id"]').val($(this).data('id'));
      $('input[name="mail_host"]').val($(this).data('host'));
      $('input[name="mail_port"]').val($(this).data('port'));
      $('input[name="mail_username"]').val($(this).data('username'));
      $('input[name="mail_password"]').val($(this).data('password'));
      $('input[name="mail_from_email"]').val($(this).data('from_email'));
      $('input[name="mail_from_name"]').val($(this).data('from_name'));
      $('input[name="base_url"]').val($(this).data('base_url'));

      // Scroll ke form
      $('html, body').animate({
        scrollTop: $("#formMail").offset().top - 100
      }, 600);
    });
  });
</script>

</body>
</html>

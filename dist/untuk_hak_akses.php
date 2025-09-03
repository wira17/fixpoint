// ✅ Ambil ID user dan nama file saat ini
$user_id = $_SESSION['user_id'];
$current_file = basename($_SERVER['PHP_SELF']);

// ✅ Cek hak akses pengguna untuk halaman ini
$query = "SELECT 1 FROM akses_menu 
          JOIN menu ON akses_menu.menu_id = menu.id 
          WHERE akses_menu.user_id = '$user_id' AND menu.file_menu = '$current_file'";

$cek = mysqli_query($conn, $query);

// ✅ Jika tidak punya akses, tampilkan notifikasi profesional dan redirect
if (!$cek || mysqli_num_rows($cek) === 0) {
  echo '
  <html>
  <head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      body {
        font-family: "Poppins", sans-serif;
        background: linear-gradient(to right, #ffecd2, #fcb69f);
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }
    </style>
  </head>
  <body>
    <script>
      Swal.fire({
        icon: "error",
        title: "Akses Ditolak!",
        html: "<b>Oops...</b> Anda tidak memiliki izin untuk membuka halaman ini.",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "<i class=\'fa fa-home\'></i> Kembali ke FixPoint",
        background: "#fff url(\'https://www.transparenttextures.com/patterns/paper-fibers.png\')",
        customClass: {
          popup: "animated fadeInDown"
        }
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = "dashboard.php";
        }
      });
    </script>
  </body>
  </html>
  ';
  exit;
}
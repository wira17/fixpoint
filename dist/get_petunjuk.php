<?php
include 'koneksi.php';

$html = '';

// Kategori Software
$software = mysqli_query($conn, "SELECT nama_kategori FROM kategori_software");
$html .= "<h6 class='text-primary font-weight-bold'>Kategori Software</h6><ul>";
while ($row = mysqli_fetch_assoc($software)) {
  $html .= "<li>" . htmlspecialchars($row['nama_kategori']) . "</li>";
}
$html .= "</ul>";

// Kategori Hardware
$hardware = mysqli_query($conn, "SELECT nama_kategori FROM kategori_hardware");
$html .= "<h6 class='text-success font-weight-bold mt-3'>Kategori Hardware</h6><ul>";
while ($row = mysqli_fetch_assoc($hardware)) {
  $html .= "<li>" . htmlspecialchars($row['nama_kategori']) . "</li>";
}
$html .= "</ul>";

echo $html;

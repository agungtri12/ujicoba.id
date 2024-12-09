<?php
// Konfigurasi koneksi ke database
$host = 'localhost'; // Nama host, biasanya localhost
$user = 'root';      // Username database
$pass = '';          // Password database, kosong jika default
$dbname = 'user_management'; // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

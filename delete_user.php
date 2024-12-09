<?php
session_start();
require 'db.php'; // Menghubungkan ke database

// Pastikan hanya admin yang dapat mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Periksa apakah ID pengguna telah diterima melalui URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitasi input untuk mencegah SQL Injection

    // Query untuk menghapus pengguna berdasarkan ID
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Pengguna berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal menghapus pengguna.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan pada server.";
    }
} else {
    $_SESSION['message'] = "ID pengguna tidak ditemukan.";
}

// Redirect kembali ke halaman daftar pengguna
header("Location: manage_users.php");
exit;

$conn->close();
?>

<?php
session_start();
session_destroy(); // Menghapus data sesi
header("Location: login.php"); // Mengalihkan ke halaman login
exit;
?>

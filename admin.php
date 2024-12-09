<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Mengambil email admin dari sesi
$email = $_SESSION['email'];

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nama lengkap admin berdasarkan email
$stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();

// Menutup koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navbar */
        nav {
            background-color: #333;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 15px;
            align-items: center;
        }

        nav .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
        }

        nav ul li a:hover {
            background-color: #4CAF50;
        }

        /* Main Content */
        .container {
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
            text-align: center;
        }

        .container h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .container p {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .btn:hover {
            background-color: #45a049;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                flex-direction: column;
                margin-top: 10px;
            }

            nav ul li {
                margin-left: 0;
                margin-bottom: 10px;
            }

            .container {
                padding: 20px;
                max-width: 95%;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">Admin Panel</div>
    <ul>
        <li><a href="admin.php">Beranda</a></li>
        <li><a href="manage_users.php">Manajemen Pengguna</a></li>
        <li><a href="add_points.php">Tambah Point</a></li>
        <li><a href="add_products.php">Tambah Barang Tukar</a></li>
        <li><a href="upload_article.php">Upload Artikel</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- Main Content -->
<div class="container">
    <h2>Selamat datang, <?php echo htmlspecialchars($full_name); ?>!</h2>
    <p>Anda telah berhasil login sebagai admin. Di sini Anda dapat mengelola konten dan pengaturan.</p>
    <a href="manage_users.php" class="btn">Manajemen Pengguna</a><br>
    <a href="upload_article.php" class="btn">Upload Artikel</a><br>
    <a href="logout.php" class="btn">Logout</a>
</div>

</body>
</html>
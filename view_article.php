<?php
session_start();

// Cek apakah user sudah login dan memiliki role 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ./login.php");
    exit;
}

// Pastikan session variables 'username' dan 'profile_image' ada
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'default-profile.png'; // Ganti dengan path gambar default

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID artikel dari URL
$article_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk mengambil detail artikel berdasarkan ID
$query = "SELECT article_name, synopsis, full_content, image FROM articles WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $article_id); // Bind parameter ID
$stmt->execute();
$result = $stmt->get_result();

// Jika artikel ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $article_name = $row['article_name'];
    $synopsis = $row['synopsis'];
    $content = $row['full_content'];
    $image = $row['image'];
} else {
    echo "<p>Artikel tidak ditemukan.</p>";
    exit;
}

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Comic Sans MS', 'Arial', sans-serif;
            background-color: #fdfdfd;
            color: #444;
        }
        /* Navbar Styling */
        nav {
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            align-items: center;
            border-bottom: 3px solid #388E3C;
        }
        nav .logo {
            font-size: 1.8em;
            font-weight: bold;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1em;
            padding: 8px 12px;
            border-radius: 20px;
            transition: background-color 0.2s ease;
        }
        nav ul li a:hover {
            background-color: #66BB6A;
        }
        nav ul li a.active {
            background-color: #388E3C;
            font-weight: bold;
        }
        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        .username {
            font-size: 1.1em;
        }

        /* Main Content Styling */
        .article-container {
            width: 80%;
            margin: 40px auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .article-container img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .article-container h1 {
            font-size: 2em;
            color: #4CAF50;
        }
        .article-container p {
            font-size: 1.1em;
            line-height: 1.6;
        }
        .back-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 150px;
            margin: 20px auto;
            display: block;
            font-weight: bold;
        }
        .back-btn:hover {
            background-color: #66BB6A;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">Artikel</div>
    <ul>
        <li><a href="beranda.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'beranda.php' ? 'active' : ''; ?>">Beranda</a></li>
        <li><a href="artikel.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'active' : ''; ?>">Artikel</a></li>
        <li><a href="./settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Pengaturan</a></li>
    </ul>
    <div class="profile">
        <img src="uploads/<?php echo $profile_image; ?>" alt="Profile" class="profile-img">
        <span class="username"><?php echo $username; ?></span>
    </div>
</nav>

<!-- Article Content -->
<div class="article-container">
    <img src="<?php echo $image; ?>" alt="<?php echo $article_name; ?>">
    <h1><?php echo $article_name; ?></h1>
    <p><?php echo nl2br($content); ?></p>
    <a href="artikel.php" class="back-btn">Kembali ke Artikel</a>
</div>

</body>
</html>

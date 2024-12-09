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

// Ambil full_name dari tabel user berdasarkan username
$query = "SELECT full_name FROM users WHERE email = ?";
$stmt = $conn->prepare($query);  // Prepare the query
$stmt->bind_param("s", $_SESSION['email']); // Bind the email parameter (assuming email is stored in session)
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result

if ($result->num_rows > 0) {
    // Ambil data full_name
    $row = $result->fetch_assoc();
    $username = $row['full_name']; // Ganti dengan full_name dari database
} else {
    $username = 'Guest'; // Jika tidak ditemukan, set 'Guest'
}

// Cek apakah ada parameter pencarian
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Ambil artikel dari database
if ($search_query !== '') {
    $query = "SELECT id, article_name, synopsis, image FROM articles WHERE article_name LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT id, article_name, synopsis, image FROM articles ORDER BY created_at DESC";
    $result = $conn->query($query);
}

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel</title>
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

        /* Search Section */
        .search-container {
            margin: 20px;
            text-align: center;
        }
        .search-container form {
            display: inline-block;
            width: 100%;
            max-width: 400px;
        }
        .search-container input[type="text"] {
            width: 80%;
            padding: 10px;
            border: 2px solid #4CAF50;
            border-radius: 20px 0 0 20px;
            outline: none;
        }
        .search-container button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #66BB6A;
        }

        /* Main Content Styling */
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .article-card {
            background-color: #fff;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .article-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .article-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .article-card h3 {
            font-size: 1.3em;
            margin: 15px;
            color: #4CAF50;
        }
        .article-card p {
            font-size: 0.95em;
            color: #555;
            margin: 0 15px 15px;
        }
        .article-card a {
            display: block;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 0 0 10px 10px;
            font-weight: bold;
        }
        .article-card a:hover {
            background-color: #66BB6A;
        }

        /* Footer Styling */
        footer {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 0.9em;
        }
        footer a {
            color: #FFEE58;
            text-decoration: none;
            font-weight: bold;
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

<!-- Search Section -->
<div class="search-container">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Cari artikel..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Cari</button>
    </form>
</div>

<!-- Main Content -->
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $article_id = $row['id'];
            $article_name = $row['article_name'];
            $synopsis = $row['synopsis'];
            $image = $row['image'];
            echo "
                <div class='article-card'>
                    <img src='$image' alt='$article_name'>
                    <h3>$article_name</h3>
                    <p>" . substr($synopsis, 0, 100) . "...</p>
                    <a href='view_article.php?id=$article_id'>Baca Selengkapnya</a>
                </div>
            ";
        }
    } else {
        echo "<p>Tidak ada artikel yang ditemukan.</p>";
    }
    ?>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Artikel. Semua hak dilindungi.</p>
    <p><a href="#">Kebijakan Privasi</a> | <a href="#">Kontak</a></p>
</footer>

</body>
</html>

<?php
session_start();

// Cek apakah user sudah login dan memiliki role 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ./login.php");
    exit;
}

// Pastikan session variables 'username' dan 'profile_image' ada
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'default-profile.png';
 // Ganti dengan path gambar default

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil full_name dari tabel user berdasarkan username
$query = "SELECT u.full_name, COALESCE(SUM(up.points), 0) AS total_points 
          FROM users u 
          LEFT JOIN user_points up ON u.id = up.user_id 
          WHERE u.email = ? 
          GROUP BY u.id";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['email']); // Assuming email is stored in session
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['full_name'];  // Full name from the users table
    $total_points = $row['total_points'];  // Total points from the user_points table
} else {
    $username = 'Guest';
    $total_points = 0; // Default value if no points are found
}


// Ambil artikel terbaru
$query = "SELECT id, article_name, synopsis, image FROM articles ORDER BY created_at DESC LIMIT 6";
$result = $conn->query($query);

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset and global styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #f7f3e9;
            font-family: "Poppins", sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #5a5a5a;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar .nav-link {
            color: white;
            padding: 15px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: #d4a373;
        }

        .profile {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #d4a373;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .username {
            font-weight: bold;
            color: #d4a373;
        }

        .logout-btn {
            margin-top: auto;
            padding: 12px;
            background-color: #d4a373;
            color: white;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #b58456;
        }

        /* Content styling */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .banner {
            background-image: url('./6.jpg');
            background-size: cover;
            background-position: center;
            padding: 50px 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .banner-content {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .btn-banner {
            display: inline-block;
            padding: 12px 20px;
            background-color: #FF7043;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-banner:hover {
            background-color: #FF5722;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .article-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .article-card:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .article-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .article-card h3 {
            margin: 15px;
            font-size: 1.4em;
            color: #FF7043;
        }

        .article-card p {
            margin: 0 15px 15px;
            color: #555;
        }

        .btn {
            display: block;
            padding: 10px;
            margin: 10px auto;
            background-color: #FF7043;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #FF5722;
        }
        #chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 2000;
    pointer-events: auto; /* Menjamin chatbot dapat diklik */
}

        footer {
            text-align: center;
            padding: 15px;
            background-color: #FF8A65;
            color: white;
            margin-top: auto;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .toggle-sidebar {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1100;
                background-color: #5a5a5a;
                color: white;
                padding: 10px;
                border: none;
                cursor: pointer;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-sidebar" aria-label="Toggle Sidebar">☰</button>
    <div class="sidebar">
    <div class="profile">
            <img src="uploads/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="profile-img">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
        </div>
        <a href="bernda.php" class="nav-link">Beranda</a>
        <a href="./artikel.php" class="nav-link">Articles</a>
        <a href="./user_points.php" class="nav-link">Point Saya</a>
        <a href="./produk.php" class="nav-link">Tukar Point</a>
        <a href="settings.php" class="nav-link">Settings</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    </div>
    <div class="content">
        <div class="banner">
            <div class="banner-content">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
                <p>Your total points: <?php echo htmlspecialchars($total_points); ?></p>
                <a href="profile.php" class="btn-banner">Go to Profile</a>
            </div>
        </div>
        <!-- Tambahkan Elemen Chatbot di Akhir Body -->
        <div id="chatbot-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 2000;">
        <button id="chatbot-toggle" style="background-color: #FF7043; color: white; border: none; padding: 10px 15px; border-radius: 30px; cursor: pointer;">Chat with AI</button>
        <div id="chatbot-frame" style="display: none; width: 350px; height: 500px; background: white; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <iframe src="./chatbot.html" width="100%" height="100%" style="border: none;"></iframe>
        </div>
</div>
        <h2>Recent Articles</h2>
        <div class="container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="article-card">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Article Image">
                    <h3><?php echo htmlspecialchars($row['article_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['synopsis']); ?></p>
                    <a href="article.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn">Read More</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Your Website | <a href="#">Contact Us</a></p>
    </footer>
    <script>
    // Script untuk mengelola chatbot toggle
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotFrame = document.getElementById('chatbot-frame');

    chatbotToggle.addEventListener('click', () => {
        if (chatbotFrame.style.display === 'none') {
            chatbotFrame.style.display = 'block';
            chatbotToggle.textContent = 'Close Chat';
        } else {
            chatbotFrame.style.display = 'none';
            chatbotToggle.textContent = 'Chat with AI';
        }
    });

    // Memindahkan event listener toggle sidebar ke luar event listener chatbot
    document.querySelector('.toggle-sidebar').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
    });
</script>

</body>
</html>

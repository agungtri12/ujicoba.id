<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$user_id = $_SESSION['user_id'];

// Ambil data poin yang digabungkan berdasarkan tanggal dan nama barang
$stmt = $conn->prepare("
    SELECT 
        date_added, 
        GROUP_CONCAT(description SEPARATOR ', ') AS items, 
        SUM(points) AS total_points, 
        SUM(weight) AS total_weight 
    FROM user_points 
    WHERE user_id = ? 
    GROUP BY date_added 
    ORDER BY date_added DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poin Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CDN -->
    <style>
        body {
            background-color: #f7f3e9;
            font-family: "Georgia", serif;
            display: flex;
            flex-direction: column;
        }

        /* Sidebar Styling */
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
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: white;
            font-weight: bold;
            padding: 15px;
            display: block;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link:hover, .sidebar .active {
            background-color: #d4a373;
            color: white;
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
            border: 2px solid #d4a373;
            object-fit: cover;
            margin-right: 10px;
        }

        .username {
            font-weight: bold;
            color: #d4a373;
        }

        /* Content Styling */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                width: 100%;
                height: auto;
                top: 0;
                left: -100%;
                transition: all 0.3s ease-in-out;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block;
                background-color: #5a5a5a;
                color: white;
                padding: 10px 15px;
                border: none;
                font-size: 18px;
                cursor: pointer;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1100;
            }
        }

        /* Styling for the logout button */
        .logout-btn {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            padding: 12px;
            background-color: #d4a373;
            color: white;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .logout-btn i {
            margin-right: 8px;
        }

        .logout-btn:hover {
            background-color: #b58456;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<button class="toggle-sidebar d-lg-none">☰</button>
<div class="sidebar">
    <div class="profile">
        <img src="uploads/<?php echo $profile_image ?? 'default.png'; ?>" alt="Profile" class="profile-img">
        <div>
            <span class="username"><?php echo $username ?? 'Guest'; ?></span>
            <p class="mb-0">Points: <?php echo $total_points ?? 0; ?></p>
        </div>
    </div>
    <nav>
        <a href="beranda.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'beranda.php' ? 'active' : ''; ?>">Beranda</a>
        <a href="./artikel.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_article.php' ? 'active' : ''; ?>">Artikel</a>
        <a href="user_points.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_points.php' ? 'active' : ''; ?>">Poin Saya</a>
        <a href="./settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Pengaturan</a>
    </nav>
    
    <!-- Logout Button -->
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<!-- Content -->
<div class="content">
    <div class="container">
        <div class="card">
            <div class="card-header text-center bg-primary text-white">
                <h4>Poin Saya</h4>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Total Poin</th>
                                <th>Total Berat (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                                    <td><?php echo htmlspecialchars($row['items']); ?></td>
                                    <td><?php echo htmlspecialchars($row['total_points']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['total_weight'], 2)); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center">Belum ada poin yang ditambahkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Sidebar
    document.querySelector('.toggle-sidebar').addEventListener('click', function () {
        document.querySelector('.sidebar').classList.toggle('active');
    });
</script>
</body>
</html>

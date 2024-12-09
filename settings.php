<?php
session_start();

// Cek apakah user sudah login dan memiliki role 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil email dari session
$email = $_SESSION['email'];

// Ambil data user dari database
$query = "SELECT id, full_name, profile_image FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Menetapkan variabel $username
$username = $user['full_name']; // Menetapkan nama lengkap sebagai username

$error_message = ''; // Untuk menyimpan pesan error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Proses untuk mengganti nama
    if (!empty($full_name)) {
        $update_query = "UPDATE users SET full_name = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $full_name, $email);
        $update_stmt->execute();
    }
    
    // Validasi password
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $error_message = 'Password baru dan konfirmasi password tidak sama.';
    } else {
        // Proses untuk mengganti password
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $hashed_password, $email);
            $update_stmt->execute();
        }
    }
    
    // Proses untuk mengganti gambar profil
    if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        
        // Memeriksa apakah file yang diunggah adalah gambar
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_file_type, $allowed_types)) {
            // Pindahkan file ke folder tujuan
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);

            // Update gambar profil di database
            $update_query = "UPDATE users SET profile_image = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $target_file, $email);
            $update_stmt->execute();
        } else {
            $error_message = 'Hanya gambar dengan ekstensi JPG, JPEG, PNG, atau GIF yang diperbolehkan.';
        }
    }

    // Refresh halaman setelah pembaruan
    if (empty($error_message)) {
        header("Location: settings.php");
        exit;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* Navbar Styling */
        nav {
            background: #ffffff;
            color: #333;
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            align-items: center;
            border-radius: 15px;
            width: 100%;
            max-width: 1200px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        nav .logo {
            font-size: 1.8em;
            font-weight: bold;
            color: #2575fc;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        nav ul li a {
            color: #333;
            text-decoration: none;
            font-size: 1em;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background: #2575fc;
            color: #fff;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #2575fc;
        }

        .username {
            font-size: 1em;
            font-weight: 500;
        }

        /* Main Content */
        .container {
            max-width: 800px;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: start;
        }

        .container h1 {
            font-size: 2em;
            color: #2575fc;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2575fc;
            box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
            outline: none;
        }

        .form-group button {
            background: #2575fc;
            color: #fff;
            padding: 12px 25px;
            border-radius: 50px;
            border: none;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
            align-self: flex-start;
        }

        .form-group button:hover {
            background: #1a60c9;
        }

        .profile-img-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #2575fc;
            margin-bottom: 20px;
            display: block;
        }

        /* Error Styling */
        .error-message {
            color: #e74c3c;
            font-size: 1em;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">Artikel</div>
    <ul>
        <li><a href="beranda.php">Beranda</a></li>
        <li><a href="./settings.php"><i class="fas fa-cogs"></i> Pengaturan</a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
    <div class="profile">
        <img src="uploads/<?php echo $user['profile_image']; ?>" alt="Profile" class="profile-img">
        <span class="username"><?php echo $username; ?></span>
    </div>
</nav>

<div class="container">
    <h1><i class="fas fa-user-cog"></i> Pengaturan Pengguna</h1>

    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="user_id">ID Pengguna:</label>
            <input type="text" id="user_id" name="user_id" value="<?php echo $user['id']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="full_name">Nama Lengkap:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="new_password">Password Baru:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru">
        </div>

        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password">
        </div>

        <div class="form-group">
            <label for="profile_image">Ganti Gambar Profil:</label>
            <input type="file" id="profile_image" name="profile_image">
        </div>

        <div class="form-group">
            <button type="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>


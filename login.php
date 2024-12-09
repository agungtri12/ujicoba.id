<?php
require 'db.php'; // Menghubungkan ke database
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Membuat koneksi ke database
    $conn = new mysqli('localhost', 'root', '', 'user_management');

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil id, role, dan password yang ter-hash berdasarkan email
    $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $role, $hashed_password);
    $stmt->fetch();

    // Verifikasi password dengan password_verify()
    if ($id && password_verify($password, $hashed_password)) {
        // Jika login berhasil, simpan informasi pengguna dalam sesi
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email; // Menyimpan email untuk digunakan di halaman lain

        // Redirect berdasarkan role (admin atau user)
        if ($role == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: beranda.php");
        }
        exit; // Menghentikan eksekusi lebih lanjut
    } else {
        // Jika email atau password salah
        $error = "Email atau password salah!";
    }

    // Menutup koneksi
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .input-container {
            position: relative;
            margin: 10px 0;
        }

        input {
            width: 100%;
            padding: 15px 35px;
            border: 2px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }

        input:focus {
            border-color: #3498db;
            transform: translateY(-3px);
        }

        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #3498db;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            h2 {
                font-size: 20px;
            }

            input {
                font-size: 14px;
            }

            button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Login</h2>

    <!-- Menampilkan pesan error jika login gagal -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-container">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required><br>
        </div>
        <div class="input-container">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
        </div>
        <div class="input-container">
            <label for="show-password" style="color: #333;">
                <input type="checkbox" id="show-password">
            </label>
        </div>
        <button type="submit">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="links">
        <p><a href="register.php">Belum memiliki akun?</a></p>
        <p><a href="forgot_password.php">Lupa kata sandi?</a></p>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('show-password').addEventListener('change', function() {
        var passwordField = document.getElementById('password');
        if (this.checked) {
            passwordField.type = 'text'; // Show password
        } else {
            passwordField.type = 'password'; // Hide password
        }
    });
</script>

</body>
</html>

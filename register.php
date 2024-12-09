<?php
require 'db.php'; // Connecting to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form input data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password and confirm password
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists in the database
        $conn = new mysqli('localhost', 'root', '', 'user_management');
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists
            $error = "Email sudah terdaftar!";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssss", $full_name, $email, $phone_number, $hashed_password);

            if ($stmt->execute()) {
                // Registration successful
                header("Location: login.php"); // Redirect to login page
                exit;
            } else {
                // Error in inserting data
                $error = "Terjadi kesalahan, coba lagi!";
            }
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="path/to/your-icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
        }

        .container:hover {
            transform: scale(1.05);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .container form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #888;
            font-size: 18px;
        }

        .input-container input {
            padding: 12px 20px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
            width: 85%;
        }

        .input-container input:focus {
            border-color: #FF6F61;
            box-shadow: 0 0 5px rgba(255, 111, 97, 0.7);
        }

        button {
            padding: 12px;
            background: #FF6F61;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: #D19C97;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        p {
            font-size: 14px;
            text-align: center;
            color: #666;
        }

        p a {
            color: #FF6F61;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                max-width: 90%;
            }

            .container h2 {
                font-size: 24px;
            }

            button {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Register</h2>

        <!-- Display error message if registration fails -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" placeholder="Nama Lengkap" required>
            </div>
            <div class="input-container">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-container">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone_number" placeholder="Nomor Telepon" required>
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>

<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses formulir
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $points = $_POST['points'];
    $image = $_FILES['image'];

    // Validasi input
    if (!empty($name) && !empty($points) && !empty($image['name'])) {
        // Upload gambar
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Periksa tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                // Simpan ke database
                $stmt = $conn->prepare("INSERT INTO products (name, description, image, points) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $name, $description, $target_file, $points);
                $stmt->execute();
                $stmt->close();

                $message = "Produk berhasil ditambahkan!";
            } else {
                $message = "Gagal mengunggah gambar.";
            }
        } else {
            $message = "Format gambar tidak didukung. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Ambil semua produk
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #333;
            color: white;
        }

        table img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Produk</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nama Produk" required>
            <textarea name="description" placeholder="Deskripsi Produk"></textarea>
            <input type="file" name="image" accept="image/*" required>
            <input type="number" name="points" placeholder="Jumlah Poin" required>
            <button type="submit">Tambah Produk</button>
        </form>

        <h2>Daftar Produk</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Gambar</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td><img src='{$row['image']}' alt='Gambar Produk'></td>
                            <td>{$row['points']}</td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5'>Belum ada produk.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

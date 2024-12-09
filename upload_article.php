<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $article_name = $_POST['article_name'];
    $synopsis = $_POST['synopsis'];
    $full_content = $_POST['full_content'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];

    // Cek apakah file yang diupload adalah gambar
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $image_path = $_SERVER['DOCUMENT_ROOT'] . '/il/uploads/' . time() . '.' . $image_ext;

if (in_array($image_ext, $allowed_extensions) && $image_error === 0) {
    if ($image_size < 5000000) { // Maksimal 5MB
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Koneksi ke database
            $conn = new mysqli('localhost', 'root', '', 'user_management');
            if ($conn->connect_error) {
                die("Koneksi gagal: " . $conn->connect_error);
            }

            // Insert data artikel ke database
            $stmt = $conn->prepare("INSERT INTO articles (article_name, synopsis, full_content, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $article_name, $synopsis, $full_content, $image_path);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            echo "<script>alert('Artikel berhasil diupload!');</script>";
        } else {
            echo "<script>alert('Gagal memindahkan file gambar!');</script>";
        }
    } else {
        echo "<script>alert('Ukuran gambar terlalu besar! Maksimal 5MB.');</script>";
    }
} else {
    echo "<script>alert('Format gambar tidak valid.');</script>";
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Artikel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manajemen Pengguna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_points.php">Tambah Point</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="upload_article.php">Upload Artikel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Upload Artikel Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="article_name" class="form-label">Nama Artikel</label>
                    <input type="text" class="form-control" id="article_name" name="article_name" placeholder="Masukkan nama artikel" required>
                </div>
                <div class="mb-3">
                    <label for="synopsis" class="form-label">Sinopsis</label>
                    <textarea class="form-control" id="synopsis" name="synopsis" rows="3" placeholder="Masukkan sinopsis artikel" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="full_content" class="form-label">Isi Artikel</label>
                    <textarea class="form-control" id="full_content" name="full_content" rows="6" placeholder="Masukkan isi artikel" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Gambar Artikel</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Upload Artikel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

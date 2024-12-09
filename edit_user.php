<?php
session_start();
require 'db.php'; // Menghubungkan ke database

// Pastikan hanya admin yang dapat mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Mengambil ID pengguna yang akan diedit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Query untuk mendapatkan data pengguna berdasarkan ID
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Pastikan data pengguna ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Pengguna tidak ditemukan!";
        exit;
    }
} else {
    echo "ID pengguna tidak ditemukan!";
    exit;
}

// Proses jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    // Jika password diubah, maka enkripsi password baru
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $email, $phone_number, $password, $user_id);
    } else {
        // Jika password kosong, hanya update data lainnya
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $email, $phone_number, $user_id);
    }

    if ($stmt->execute()) {
        // Setelah berhasil diubah, kita set flag untuk menampilkan modal
        $success = true;
    } else {
        $error = true;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Edit Pengguna</h1>
    <form action="" method="POST">
        <div class="form-group">
            <label for="full_name">Nama Lengkap</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= $user['full_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Nomor Telepon</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= $user['phone_number']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
    <a href="manage_users.php" class="btn btn-secondary mt-3">Kembali ke Daftar Pengguna</a>
</div>

<?php if (isset($success) && $success): ?>
    <!-- Modal Keberhasilan -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Sukses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Akun berhasil diperbarui!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a href=manage_users.php" class="btn btn-primary">Kembali ke Daftar Pengguna</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');  // Menampilkan modal
        });
    </script>
<?php elseif (isset($error) && $error): ?>
    <!-- Modal Kegagalan -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Kesalahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Terjadi kesalahan saat memperbarui akun. Coba lagi nanti.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');  // Menampilkan modal error
        });
    </script>
<?php endif; ?>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Gunakan jQuery versi penuh -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

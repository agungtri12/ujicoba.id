<?php
// Password yang ingin digunakan untuk akun admin
$password = 'adminpassword123';

// Meng-hash password menggunakan algoritma BCRYPT
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Menyiapkan email dan data lainnya untuk akun admin
$email = 'admin2@example.com'; // Gunakan email yang tidak ada dalam database
$full_name = 'Admin';
$phone_number = '081234567890';
$role = 'admin';

// Membuat koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Memeriksa apakah email sudah ada dalam database
$stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Jika email sudah ada, tampilkan pesan kesalahan
    echo "Email sudah terdaftar! Gunakan email lain.";
} else {
    // Jika email belum ada, masukkan data admin ke dalam database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $phone_number, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Akun admin berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan akun admin: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>

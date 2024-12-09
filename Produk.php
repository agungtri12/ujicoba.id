<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan session variables 'username' dan 'profile_image' ada
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
$profile_image = isset($_SESSION['profile_image']) ? htmlspecialchars($_SESSION['profile_image']) : 'default-profile.png'; // Ganti dengan path gambar default

// Ambil full_name dan total points dari tabel user_points
$query = "SELECT u.full_name, COALESCE(SUM(up.points), 0) AS total_points 
          FROM users u 
          LEFT JOIN user_points up ON u.id = up.user_id 
          WHERE u.email = ? 
          GROUP BY u.id";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['email']); // Mengambil email dari session
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['full_name'];  // Nama lengkap dari tabel users
    $total_points = $row['total_points'];  // Total poin dari tabel user_points
} else {
    $username = 'Guest';
    $total_points = 0; // Nilai default jika tidak ada poin
}

$user_id = $_SESSION['user_id'];

// Proses penukaran produk
if (isset($_POST['redeem_product_id'])) {
    $product_id = $_POST['redeem_product_id'];
    $stmt = $conn->prepare("SELECT points FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        $product_points = $product['points'];

        if ($total_points >= $product_points) {
            // Cek jika poin cukup, lanjutkan ke form pengisian data
            $new_points = $total_points - $product_points;

            // Menambahkan transaksi penukaran poin di tabel user_points
            $negative_points = -$product_points; // Mengurangi poin
            $stmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $negative_points); // Menggunakan variabel yang dihitung sebelumnya
            $stmt->execute();

            // Menyimpan data produk yang sudah ditukarkan (opsional)
            $stmt = $conn->prepare("INSERT INTO redeemed_products (user_id, product_id, date_redeemed) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();

            // Redirect ke form pengisian data
            echo "<script>
                    alert('Poin cukup! Silakan isi form untuk melanjutkan.');
                    window.location = 'form_redeem.php'; // Ganti dengan halaman form yang sesuai
                  </script>";
        } else {
            // Jika poin tidak cukup
            echo "<script>alert('Poin Anda tidak cukup untuk menukarkan produk ini.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Tersedia</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 2em;
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 15px;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 0;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .product-card h3 {
            font-size: 1.3em;
            color: #333;
            margin: 10px 0;
            font-weight: bold;
        }

        .product-card p {
            font-size: 1em;
            color: #555;
            margin-bottom: 15px;
        }

        .product-card .points {
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }

        .product-card button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .product-card button:hover {
            background-color: #45a049;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .product-card {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Produk Tersedia</h2>
        <div class="user-info">
            <img src="images/<?php echo $profile_image; ?>" alt="Profile Image">
            <div>
                <p><strong>Welcome, <?php echo $username; ?>!</strong></p>
                <p><strong>Points: </strong><?php echo $total_points ?? 0; ?></p>
            </div>
        </div>

        <div class="products">
            <?php
            // Fetch products from the database
            $product_result = $conn->query("SELECT * FROM products");
            if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
                    echo "
                    <div class='product-card'>
                        <img src='{$product['image']}' alt='Gambar Produk'>
                        <h3>{$product['name']}</h3>
                        <p>{$product['description']}</p>
                        <div class='points'>Poin: {$product['points']}</div>
                        <form method='POST'>
                            <input type='hidden' name='redeem_product_id' value='{$product['id']}'>
                            <button type='submit'>Redeem</button>
                        </form>
                    </div>
                    ";
                }
            } else {
                echo "<p>Tidak ada produk yang tersedia saat ini.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>

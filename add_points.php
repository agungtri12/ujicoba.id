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

$items_list = [
    'Botol Plastik' => ['points' => 3, 'weight' => 0.02383],
    'Kardus' => ['points' => 5, 'weight' => 0.5],
    'Ember' => ['points' => 10, 'weight' => 2],
    'Kertas' => ['points' => 2, 'weight' => 0.1],
    'Kaleng' => ['points' => 7, 'weight' => 0.15]
];


// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $items = $_POST['items']; // Array barang
    $total_points = 0;
    $total_weight = 0;
    $current_date = date('Y-m-d'); // Ambil tanggal, bulan, dan tahun saat ini
 
    // Hitung total poin dan berat
    foreach ($items as $item) {
        $quantity = $item['quantity'];
        $item_name = $item['description'];
        $points_per_item = $items_list[$item_name]['points'];  // Ambil poin berdasarkan barang
        $weight_per_item = $items_list[$item_name]['weight'];  // Ambil berat berdasarkan barang
        $points = $quantity * $points_per_item;
        $weight = $quantity * $weight_per_item;

        $total_points += $points;
        $total_weight += $weight;

        // Tambahkan poin ke database
        $stmt = $conn->prepare("INSERT INTO user_points (user_id, points, description, weight, date_added) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $points, $item_name, $weight, $current_date);
        $stmt->execute();
    }

    $message = "Total $total_points poin dan $total_weight kg berhasil ditambahkan pada $current_date!";
}

// Ambil daftar pengguna dengan role 'user'
$result = $conn->query("SELECT id, id FROM users WHERE role = 'user'");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Poin Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Arial', sans-serif;
        }
        nav {
            background: linear-gradient(45deg, #4CAF50, #2f9e44);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        nav .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-left: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
            transition: background 0.3s ease;
        }
        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .card-header {
            background: linear-gradient(45deg, #4CAF50, #2f9e44);
            color: white;
            font-weight: bold;
            text-align: center;
            border-radius: 15px 15px 0 0;
            padding: 15px;
        }
        .btn-custom {
            background: linear-gradient(45deg, #4CAF50, #2f9e44);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #45a049, #267f37);
            transform: scale(1.05);
        }
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
        }
        .form-select:focus, .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-success" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="admin.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark active" href="manage_users.php">Manajemen Pengguna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="add_points.php">Tambah Poin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="upload_article.php">Upload Artikel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<!-- Container -->
<div class="container mt-5">
    <div class="card fade-in">
        <div class="card-header">Tambah Poin Pengguna</div>
        <div class="card-body">
            <?php if (isset($message)): ?>
                <div class="alert alert-success text-center" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Pilih Pengguna:</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['id']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="items-container">
                    <div class="mb-3 p-3 border rounded">
                        <label for="items[0][description]" class="form-label">Pilih Barang:</label>
                        <select name="items[0][description]" class="form-select" required onchange="updatePoints(0)">
                            <?php foreach ($items_list as $item => $info): ?>
                                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="items[0][quantity]" class="form-label">Jumlah Barang:</label>
                        <input type="number" name="items[0][quantity]" min="1" class="form-control" required placeholder="Jumlah" onchange="updatePoints(0)">

                        <label for="items[0][points]" class="form-label">Poin per Barang:</label>
                        <input type="text" name="items[0][points]" class="form-control" readonly>

                        <label for="items[0][weight]" class="form-label">Berat Barang (kg):</label>
                        <input type="text" name="items[0][weight]" class="form-control" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-custom" onclick="addItemGroup()">Tambah Barang</button>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-custom">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIndex = 1;
    const itemsList = <?php echo json_encode($items_list); ?>;

    function addItemGroup() {
        const container = document.getElementById('items-container');
        const newItem = `
            <div class="mb-3 p-3 border rounded">
                <label for="items[${itemIndex}][description]" class="form-label">Pilih Barang:</label>
                <select name="items[${itemIndex}][description]" class="form-select" required onchange="updatePoints(${itemIndex})">
                    ${Object.keys(itemsList).map(item => `<option value="${item}">${item}</option>`).join('')}
                </select>

                <label for="items[${itemIndex}][quantity]" class="form-label">Jumlah Barang:</label>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" class="form-control" required placeholder="Jumlah" onchange="updatePoints(${itemIndex})">

                <label for="items[${itemIndex}][points]" class="form-label">Poin per Barang:</label>
                <input type="text" name="items[${itemIndex}][points]" class="form-control" readonly>

                <label for="items[${itemIndex}][weight]" class="form-label">Berat Barang (kg):</label>
                <input type="text" name="items[${itemIndex}][weight]" class="form-control" readonly>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newItem);
        itemIndex++;
    }

    function updatePoints(index) {
        const descriptionElement = document.querySelector(`[name="items[${index}][description]"]`);
        const quantityElement = document.querySelector(`[name="items[${index}][quantity]"]`);
        const pointsElement = document.querySelector(`[name="items[${index}][points]"]`);
        const weightElement = document.querySelector(`[name="items[${index}][weight]"]`);

        const selectedItem = descriptionElement.value;
        const quantity = parseInt(quantityElement.value) || 0;

        if (itemsList[selectedItem]) {
            pointsElement.value = itemsList[selectedItem].points * quantity;
            weightElement.value = itemsList[selectedItem].weight * quantity;
        } else {
            pointsElement.value = '';
            weightElement.value = '';
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


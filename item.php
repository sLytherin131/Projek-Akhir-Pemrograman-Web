<?php
// Pastikan user sudah login sebelum mengakses halaman ini
session_start(); // Sesuaikan ini dengan cara Anda mengatur session

// Contoh cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Ganti ini dengan halaman login Anda
    exit();
}

// Kode PHP untuk mengambil dan menampilkan data item dari database
$conn = new mysqli("localhost", "username", "password", "nama_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM item";
$result = $conn->query($sql);

$itemList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $itemList[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Item</title>
    <!-- Tambahkan CSS Anda di sini -->
    <style>
        /* Contoh CSS untuk styling */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .add-link {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Master Item</h1>
        
        <!-- Tabel untuk menampilkan data item -->
        <table>
            <thead>
                <tr>
                    <th>Kode Item</th>
                    <th>Nama</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Isi tabel dari data yang diambil dari database -->
                <?php foreach ($itemList as $item) : ?>
                    <tr>
                        <td><?php echo $item['kodeitem']; ?></td>
                        <td><?php echo $item['nama']; ?></td>
                        <td><?php echo $item['hargabeli']; ?></td>
                        <td><?php echo $item['hargajual']; ?></td>
                        <td><?php echo $item['stok']; ?></td>
                        <td><?php echo $item['satuan']; ?></td>
                        <td>
                            <a href="edit_item.php?id=<?php echo $item['kodeitem']; ?>">Edit</a> |
                            <a href="hapus_item.php?id=<?php echo $item['kodeitem']; ?>">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tautan tambah item -->
        <a href="tambah_item.php" class="add-link">Tambah Item</a>
    </div>
</body>
</html>

<?php
// Pastikan user sudah login sebelum mengakses halaman ini
session_start(); // Sesuaikan ini dengan cara Anda mengatur session

// Contoh cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Ganti ini dengan halaman login Anda
    exit();
}

// Proses form jika ada data yang disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kode untuk menyimpan item ke database
    $nama = $_POST["nama"];
    $hargabeli = $_POST["hargabeli"];
    $hargajual = $_POST["hargajual"];
    $stok = $_POST["stok"];
    $satuan = $_POST["satuan"];

    $conn = new mysqli("localhost", "username", "password", "nama_database");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO item (nama, hargabeli, hargajual, stok, satuan) VALUES ('$nama', '$hargabeli', '$hargajual', '$stok', '$satuan')";

    if ($conn->query($sql) === TRUE) {
        echo "Data item berhasil ditambahkan";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Item</title>
    <!-- Tambahkan CSS Anda di sini -->
    <style>
        /* Contoh CSS untuk styling */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: inline-block;
            width: 120px;
        }
        .form-group input {
            width: calc(100% - 120px);
            padding: 5px;
            font-size: 16px;
        }
        .form-group button {
            margin-top: 10px;
            padding: 8px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Item</h1>
        
        <!-- Form untuk menambahkan item -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="hargabeli">Harga Beli:</label>
                <input type="number" id="hargabeli" name="hargabeli" required>
            </div>
            <div class="form-group">
                <label for="hargajual">Harga Jual:</label>
                <input type="number" id="hargajual" name="hargajual" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required>
            </div>
            <div class="form-group">
                <label for="satuan">Satuan:</label>
                <input type="text" id="satuan" name="satuan" required>
            </div>
            <div class="form-group">
                <button type="submit">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>

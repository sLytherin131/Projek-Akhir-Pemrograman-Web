<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Menentukan nama file untuk menyimpan nama admin
$filename = 'admin_name.txt';

// Membaca nama admin dari file jika ada, atau menggunakan nama default
$adminName = file_exists($filename) ? file_get_contents($filename) : 'Isaac Yerima Nugroho';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. MobilID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ptmobil {
            text-align: center;
        }
        .sidebar {
            width: 190px;
            background-color: #333;
            color: white;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .dropdown-btn {
            padding: 15px;
            border: none;
            background: none;
            color: white;
            cursor: pointer;
            outline: none;
            width: 100%;
            display: flex;
        }
        .dropdown-btn i {
            margin-left: 10px;
        }
        .dropdown-container {
            display: none;
            background-color: #414141;
            width: 100%;
        }
        .main {
            flex: 1;
            padding: 20px;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .container h2 {
            text-align: center;
        }
        .container h3 {
            text-align: center;
            color: #555;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdown = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdown.length; i++) {
                dropdown[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    var icon = this.querySelector("i");
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                        icon.classList.remove("fa-caret-up");
                        icon.classList.add("fa-caret-down");
                    } else {
                        dropdownContent.style.display = "block";
                        icon.classList.remove("fa-caret-down");
                        icon.classList.add("fa-caret-up");
                    }
                });
            }
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2 class="ptmobil">PT. MobilID</h2>
        <a href="dashboard.php">Home</a>
        <button class="dropdown-btn">Master
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="master_item.php">Item</a>
            <a href="master_pelanggan.php">Pelanggan</a>
            <a href="master_pemasok.php">Pemasok</a>
            <a href="master_rekening.php">Rekening</a>
        </div>
        <button class="dropdown-btn">Transaksi
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="daftar_pembelian.php">Transaksi Pembelian</a>
            <a href="daftar_penjualan.php">Transaksi Penjualan</a>
        </div>
        <a href="setting.php">Setting</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <div class="container">
            <h2>Welcome to PT. MobilID Dashboard</h2>
            <h3>Admin: <?php echo htmlspecialchars($adminName); ?></h3>
            <p>Manage your business data efficiently.</p>
        </div>
    </div>
</body>
</html>

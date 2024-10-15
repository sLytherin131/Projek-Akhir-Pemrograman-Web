<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pt.mobil_id";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $search_sql = " WHERE dj.nojual LIKE '%$search%' OR dj.qty LIKE '%$search%'";
} else {
    $search_sql = '';
}

// Query untuk mengambil data dari tabel hjual dan djual
$sql = "SELECT dj.nojual, dj.nogenerate, dj.qty, dj.HargaJual, hj.keterangan
        FROM djual dj
        JOIN hjual hj ON dj.nojual = hj.nojual
        " . $search_sql;

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi Penjualan - PT. MobilID</title>
    <style>
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-container {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #FC6E51;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #FFCE54;
        }
        .search-container {
            margin-bottom: 10px;
        }
        .search-container input[type="text"] {
            width: 200px;
            margin-right: 10px;
        }
        .btn1 {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #FC6E51;
            color: white;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .btn1:hover {
            background-color: #FFCE54;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #0077B6;
            color: white;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #00B4D8;
        }
        .btnhapus {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            background-color: #ff0000;
            color: white;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btnhapus:hover {
            background-color: #ff0000;
        }
        .btng {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #45a049;
            color: white;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btng:hover {
            background-color: #58BB43;
        }

        /* CSS untuk cetak ke PDF */
        @media print {
            body {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="container">
            <h2>Daftar Transaksi Penjualan</h2>
            <div class="search-container">
                <form action="daftar_penjualan.php" method="get">
                    <input type="text" name="search" placeholder="Search by No Jual or QTY" value="<?php echo $search; ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <a href="add_penjualan.php" class="btn1">Tambah Penjualan</a>
            <table>
                <tr>
                    <th>No Jual</th>
                    <th>No Generate</th>
                    <th>QTY</th>
                    <th>Harga Jual</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["nojual"] . "</td>";
                        echo "<td>" . $row["nogenerate"] . "</td>";
                        echo "<td>" . $row["qty"] . "</td>";
                        echo "<td>" . $row["HargaJual"] . "</td>";
                        echo "<td>" . $row["keterangan"] . "</td>";
                        echo "<td><a href='hapus_transaksi_pen.php?id=" . $row["nojual"] . "' class='btnhapus'>Hapus</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data.</td></tr>";
                }

                $conn->close();
                ?>
            </table>
            <br>
            <a href="#" onclick="window.print();" class="btng">Generate PDF</a>
            <a href="dashboard.php" class="btn">Back to Home</a>
        </div>
    </div>
</body>
</html>

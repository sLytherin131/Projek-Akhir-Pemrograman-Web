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
    $search_sql = " WHERE db.nogenerate LIKE '%$search%' OR db.KodeItem LIKE '%$search%'";
} else {
    $search_sql = '';
}

// Query untuk mengambil data dari tabel hbeli dan dbeli
$sql = "SELECT db.nogenerate, db.nobeli, db.KodeItem, db.qty, db.HargaBeli, i.Stok, hb.keterangan
        FROM dbeli db
        JOIN hbeli hb ON db.nogenerate = hb.nobeli
        JOIN item i ON db.KodeItem = i.KodeItem" . $search_sql;

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi - PT. MobilID</title>
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
            background-color: #45a049;
            color: white;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btn1:hover {
            background-color: #58BB43;
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
        .btn2 {
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
        .btn2:hover {
            background-color: #FFCE54;
        }
        .btn-pemasok {
            background-color: #45a049;
        }
        .btn-pemasok:hover {
            background-color: #58BB43;
        }
        .btn-rekening {
            background-color: #0077B6;
        }
        .btn-rekening:hover {
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
            <h2>Daftar Transaksi Pembelian</h2>
            <div class="search-container">
                <form action="daftar_pembelian.php" method="get">
                    <input type="text" name="search" placeholder="Search by No Generate or Kode Item" value="<?php echo $search; ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <a href="add_pembelian.php" class="btn2">Tambah Pembelian</a>
            <a href="master_pemasok.php" class="btn2 btn-pemasok" style="margin-left: 10px;">Tambah Pemasok</a>
            <a href="master_rekening.php" class="btn2 btn-rekening" style="margin-left: 10px;">Tambah Rekening</a>
            <table>
                <tr>
                    <th>No Generate</th>
                    <th>No Beli</th>
                    <th>Kode Item</th>
                    <th>QTY</th>
                    <th>Harga Beli</th>
                    <th>Stok</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["nogenerate"] . "</td>";
                        echo "<td>" . $row["nobeli"] . "</td>";
                        echo "<td>" . $row["KodeItem"] . "</td>";
                        echo "<td>" . $row["qty"] . "</td>";
                        echo "<td>" . $row["HargaBeli"] . "</td>";
                        echo "<td>" . $row["Stok"] . "</td>";
                        echo "<td>" . $row["keterangan"] . "</td>";
                        echo "<td><a href='hapus_transaksi_pem.php?id=" . $row["nogenerate"] . "' class='btnhapus'>Hapus</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
                }

                $conn->close();
                ?>
            </table>
            <br>
            <a href="#" onclick="window.print();" class="btn1">Generate PDF</a>
            <a href="dashboard.php" class="btn">Back to Home</a>
        </div>
    </div>
</body>
</html>

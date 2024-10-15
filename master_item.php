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
    $search_sql = " WHERE Nama LIKE '%$search%' OR KodeItem LIKE '%$search%'";
} else {
    $search_sql = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kodeitem = $_POST["KodeItem"];
    $nama = $_POST["Nama"];
    $hargabeli = $_POST["HargaBeli"];
    $hargajual = $_POST["HargaJual"];
    $stok = $_POST["Stok"];
    $satuan = $_POST["Satuan"];

    $sql = "INSERT INTO item (KodeItem, Nama, HargaBeli, HargaJual, Stok, Satuan) VALUES ('$kodeitem', '$nama', '$hargabeli', '$hargajual', '$stok', '$satuan')";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_item.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch data from database
$sql = "SELECT * FROM item" . $search_sql;
$result = $conn->query($sql);

// Generate sequential Kode Item
function generateKodeItem($conn) {
    $sql = "SELECT KodeItem FROM item ORDER BY KodeItem DESC LIMIT 1";
    $result = $conn->query($sql);
    $lastKode = '00000'; // Default value if no records found

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastKode = $row['KodeItem']; // Get the last KodeItem
    }

    $newKode = str_pad((int)$lastKode + 1, 5, '0', STR_PAD_LEFT); // Increment and pad with leading zeros
    return $newKode;
}

$new_kode_item = generateKodeItem($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Master - PT. MobilID</title>
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
        .btn1:hover {
            background-color: #58BB43;
        }
        .btn:hover {
            background-color: #00B4D8;
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="container">
            <h2>Item Master</h2>
            <div class="search-container">
                <form action="master_item.php" method="get">
                    <input type="text" name="search" placeholder="Search by Name or Code" value="<?php echo $search; ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="form-container">
                <form action="master_item.php" method="post">
                    <input type="text" name="KodeItem" value="<?php echo $new_kode_item; ?>" readonly>
                    <input type="text" name="Nama" placeholder="Nama Item" required>
                    <input type="number" name="HargaBeli" placeholder="Harga Beli" required>
                    <input type="number" name="HargaJual" placeholder="Harga Jual" required>
                    <input type="number" name="Stok" placeholder="Stok" required>
                    <input type="text" name="Satuan" placeholder="Satuan" required>
                    <input type="submit" value="Tambah Item">
                </form>
            </div>
            <table>
                <tr>
                    <th>Kode Item</th>
                    <th>Nama</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['KodeItem']}</td>
                                <td>{$row['Nama']}</td>
                                <td>{$row['HargaBeli']}</td>
                                <td>{$row['HargaJual']}</td>
                                <td>{$row['Stok']}</td>
                                <td>{$row['Satuan']}</td>
                                <td>
                                    <a href='edit_item.php?id={$row['KodeItem']}'>Edit</a> |
                                    <a href='delete_item.php?id={$row['KodeItem']}'>Hapus</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No items found</td></tr>";
                }
                ?>
            </table>
            <br>
            <a href="generate_pdf.php?table=item" target="_blank" class="btn1">Generate PDF</a>
            <a href="dashboard.php" class="btn">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>

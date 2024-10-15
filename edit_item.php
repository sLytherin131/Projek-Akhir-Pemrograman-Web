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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $KodeItem = $_POST["KodeItem"];
    $Nama = $_POST["Nama"];
    $HargaBeli = $_POST["HargaBeli"];
    $HargaJual = $_POST["HargaJual"];
    $Stok = $_POST["Stok"];
    $Satuan = $_POST["Satuan"];

    $sql = "UPDATE item SET Nama='$Nama', HargaBeli='$HargaBeli', HargaJual='$HargaJual', Stok='$Stok', Satuan='$Satuan' WHERE KodeItem='$KodeItem'";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_item.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$KodeItem = $_GET['id'];
$result = $conn->query("SELECT * FROM item WHERE KodeItem='$KodeItem'");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - PT. MobilID</title>
    <style>
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 50%;
            margin: 50px auto;
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
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
        }
        .back {
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
        .back:hover {
            background-color: #00B4D8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Item</h2>
        <form action="edit_item.php" method="post">
            <input type="hidden" name="KodeItem" value="<?php echo $row['KodeItem']; ?>">
            <input type="text" name="Nama" value="<?php echo $row['Nama']; ?>" placeholder="Nama" required>
            <input type="number" name="HargaBeli" value="<?php echo $row['HargaBeli']; ?>" placeholder="Harga Beli" required>
            <input type="number" name="HargaJual" value="<?php echo $row['HargaJual']; ?>" placeholder="Harga Jual" required>
            <input type="number" name="Stok" value="<?php echo $row['Stok']; ?>" placeholder="Stok" required>
            <input type="text" name="Satuan" value="<?php echo $row['Satuan']; ?>" placeholder="Satuan" required>
            <input type="submit" value="Update Item">
            <a href="master_item.php" class="back">Back</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

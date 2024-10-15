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
    $koderekening = $_POST["koderekening"];
    $nama = $_POST["namarekening"];
    $saldo = $_POST["saldo"];

    $sql = "UPDATE rekening SET namarekening='$nama', saldo='$saldo' WHERE koderekening='$koderekening'";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_rekening.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$koderekening = $_GET['id'];
$result = $conn->query("SELECT * FROM rekening WHERE koderekening='$koderekening'");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rekening - PT. MobilID</title>
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
        <h2>Edit Rekening</h2>
        <form action="edit_rekening.php" method="post">
            <input type="hidden" name="koderekening" value="<?php echo $row['koderekening']; ?>">
            <input type="text" name="namarekening" value="<?php echo $row['namarekening']; ?>" placeholder="Nama Rekening" required>
            <input type="number" name="saldo" value="<?php echo $row['saldo']; ?>" placeholder="Saldo" required>
            <input type="submit" value="Update Rekening">
            <a href="master_rekening.php" class="back">Back</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

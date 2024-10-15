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
    $kodepemasok = $_POST["kodepemasuk"];
    $nama = $_POST["namapemasok"];
    $alamat = $_POST["alamat"];
    $kota = $_POST["kota"];
    $telepon = $_POST["telepon"];
    $email = $_POST["email"];

    $sql = "UPDATE pemasok SET namapemasok='$nama', alamat='$alamat', kota='$kota', telepon='$telepon', email='$email' WHERE kodepemasuk='$kodepemasok'";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_pemasok.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$kodepemasok = $_GET['id'];
$result = $conn->query("SELECT * FROM pemasok WHERE kodepemasuk='$kodepemasok'");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemasok - PT. MobilID</title>
    <style>
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 50%;
            margin: 50px auto;
        }
        input[type="text"], input[type="number"], input[type="email"] {
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
        <h2>Edit Pemasok</h2>
        <form action="edit_pemasok.php" method="post">
            <input type="hidden" name="kodepemasuk" value="<?php echo $row['kodepemasuk']; ?>">
            <input type="text" name="namapemasok" placeholder="Nama Pemasok" value="<?php echo $row['namapemasok']; ?>" required>
            <input type="text" name="alamat" placeholder="Alamat" value="<?php echo $row['alamat']; ?>" required>
            <input type="text" name="kota" placeholder="Kota" value="<?php echo $row['kota']; ?>" required>
            <input type="text" name="telepon" placeholder="Telepon" value="<?php echo $row['telepon']; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $row['email']; ?>" required>
            <input type="submit" value="Update Pemasok">
            <a href="master_pemasok.php" class="back">Back</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

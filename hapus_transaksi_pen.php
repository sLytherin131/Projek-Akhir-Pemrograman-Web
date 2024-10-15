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

$nogenerate = $_GET['id'];

// Delete from dbeli
$sql_dbeli = "DELETE FROM djual WHERE nogenerate='$nogenerate'";
if ($conn->query($sql_dbeli) === TRUE) {
    // Extract nobeli from nogenerate
    $nokode = ltrim($nogenerate, '0'); // remove leading zeros
    $kode_hjual = str_pad($nokode, 5, '0', STR_PAD_LEFT); // pad to 5 digits with leading zeros

    // Delete from hjual
    $sql_hjual = "DELETE FROM hjual WHERE nojual='$kode_hjual'";
    if ($conn->query($sql_hjual) === TRUE) {
        header("Location: daftar_penjualan.php");
        exit();
    } else {
        echo "Error deleting from hjual: " . $conn->error;
    }
} else {
    echo "Error deleting from djual: " . $conn->error;
}

$conn->close();
?>

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
$sql_dbeli = "DELETE FROM dbeli WHERE nogenerate='$nogenerate'";
if ($conn->query($sql_dbeli) === TRUE) {
    // Extract nobeli from nogenerate
    $nokode = ltrim($nogenerate, '0'); // remove leading zeros
    $kode_hbeli = str_pad($nokode, 5, '0', STR_PAD_LEFT); // pad to 5 digits with leading zeros

    // Delete from hbeli
    $sql_hbeli = "DELETE FROM hbeli WHERE nobeli='$kode_hbeli'";
    if ($conn->query($sql_hbeli) === TRUE) {
        header("Location: daftar_pembelian.php");
        exit();
    } else {
        echo "Error deleting from hbeli: " . $conn->error;
    }
} else {
    echo "Error deleting from dbeli: " . $conn->error;
}

$conn->close();
?>

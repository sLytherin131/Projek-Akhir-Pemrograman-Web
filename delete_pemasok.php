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

$kodepemasok = $_GET['id'];

$sql = "DELETE FROM pemasok WHERE kodepemasuk='$kodepemasok'";

if ($conn->query($sql) === TRUE) {
    header("Location: master_pemasok.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

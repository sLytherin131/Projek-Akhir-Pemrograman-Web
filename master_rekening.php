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
    $search_sql = " WHERE namarekening LIKE '%$search%' OR koderekening LIKE '%$search%'";
} else {
    $search_sql = '';
}

$koderekening = ''; // Initialize $koderekening variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["namarekening"];
    $saldo = $_POST["saldo"];

    // Generate new kode rekening
    function generateKodeRekening($conn) {
        $sql = "SELECT koderekening FROM rekening ORDER BY koderekening DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastKode = $row['koderekening'];
            $newKode = sprintf('%05d', intval($lastKode) + 1); // Format to 5 digits with leading zeros
        } else {
            $newKode = '00001'; // Default starting kode rekening
        }
        return $newKode;
    }

    $koderekening = generateKodeRekening($conn);

    // Insert into database
    $sql = "INSERT INTO rekening (koderekening, namarekening, saldo) VALUES ('$koderekening', '$nama', '$saldo')";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_rekening.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM rekening" . $search_sql;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening Master - PT. MobilID</title>
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
            <h2>Rekening Master</h2>
            <div class="search-container">
                <form action="master_rekening.php" method="get">
                    <input type="text" name="search" placeholder="Search by Name or Code" value="<?php echo $search; ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="form-container">
                <form action="master_rekening.php" method="post">
                    <input type="text" name="namarekening" placeholder="Nama Rekening" required>
                    <input type="number" name="saldo" placeholder="Saldo" required>
                    <input type="submit" value="Tambah Rekening">
                </form>
            </div>
            <table>
                <tr>
                    <th>Kode Rekening</th>
                    <th>Nama</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['koderekening']}</td>
                                <td>{$row['namarekening']}</td>
                                <td>{$row['saldo']}</td>
                                <td>
                                    <a href='edit_rekening.php?id={$row['koderekening']}'>Edit</a> |
                                    <a href='delete_rekening.php?id={$row['koderekening']}'>Hapus</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No accounts found</td></tr>";
                }
                ?>
            </table>
            <br>
            <a href="generate_pdf.php?table=rekening" target="_blank" class="btn1">Generate PDF</a>
            <a href="dashboard.php" class="btn">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>

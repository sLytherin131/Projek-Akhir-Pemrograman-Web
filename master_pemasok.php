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
    $search_sql = " WHERE namapemasok LIKE '%$search%' OR kodepemasuk LIKE '%$search%'";
} else {
    $search_sql = '';
}

// Function to generate next supplier code
function generateNextSupplierCode($conn) {
    $sql = "SELECT MAX(kodepemasuk) AS max_code FROM pemasok";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['max_code']) {
        $next_code = intval($row['max_code']) + 1;
    } else {
        $next_code = 1;
    }
    return str_pad($next_code, 5, '0', STR_PAD_LEFT); // Pad with zeros to make it five digits
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kodepemasok = generateNextSupplierCode($conn);
    $nama = $_POST["namapemasok"];
    $alamat = $_POST["alamat"];
    $kota = $_POST["kota"];
    $telepon = $_POST["telepon"];
    $email = $_POST["email"];

    $sql = "INSERT INTO pemasok (kodepemasuk, namapemasok, alamat, kota, telepon, email) 
            VALUES ('$kodepemasok', '$nama', '$alamat', '$kota', '$telepon', '$email')";

    if ($conn->query($sql) === TRUE) {
        header("Location: master_pemasok.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch data from database
$sql = "SELECT * FROM pemasok" . $search_sql;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemasok Master - PT. MobilID</title>
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
            <h2>Pemasok Master</h2>
            <div class="search-container">
                <form action="master_pemasok.php" method="get">
                    <input type="text" name="search" placeholder="Search by Name or Code" value="<?php echo $search; ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="form-container">
                <form action="master_pemasok.php" method="post">
                    <input type="text" name="namapemasok" placeholder="Nama Pemasok" required>
                    <input type="text" name="alamat" placeholder="Alamat" required>
                    <input type="text" name="kota" placeholder="Kota" required>
                    <input type="text" name="telepon" placeholder="Telepon" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="submit" value="Tambah Pemasok">
                </form>
            </div>
            <table>
                <tr>
                    <th>Kode Pemasok</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['kodepemasuk']}</td>
                                <td>{$row['namapemasok']}</td>
                                <td>{$row['alamat']}</td>
                                <td>{$row['kota']}</td>
                                <td>{$row['telepon']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <a href='edit_pemasok.php?id={$row['kodepemasuk']}'>Edit</a> |
                                    <a href='delete_pemasok.php?id={$row['kodepemasuk']}'>Hapus</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No suppliers found</td></tr>";
                }
                ?>
            </table>
            <br>
            <a href="generate_pdf.php?table=pemasok" target="_blank" class="btn1">Generate PDF</a>
            <a href="dashboard.php" class="btn">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>

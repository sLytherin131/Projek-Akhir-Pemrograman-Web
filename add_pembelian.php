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

function generateNoGenerate($conn) {
    $sql = "SELECT MAX(nogenerate) AS max_nogenerate FROM dbeli";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $lastNogenerate = $row['max_nogenerate'];

    if ($lastNogenerate === null) {
        $newNogenerate = '00001';
    } else {
        $newNogenerate = sprintf('%05d', intval($lastNogenerate) + 1);
    }

    return $newNogenerate;
}

// Function to update item stock
function updateItemStock($conn, $kodeitem, $qty) {
    $updateStokSql = "UPDATE item SET Stok = Stok + $qty WHERE KodeItem = '$kodeitem'";
    $conn->query($updateStokSql);
}

// Function to deduct balance from selected bank account
function deductBalance($conn, $koderekening, $total) {
    $updateBalanceSql = "UPDATE rekening SET Saldo = Saldo - $total WHERE koderekening = '$koderekening'";
    $conn->query($updateBalanceSql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nogenerate = generateNoGenerate($conn);
    $tanggal = $_POST["tanggal"];
    $koderekening = $_POST["koderekening"];
    $kodepemasok = $_POST["kodepemasok"];
    $kodeitem = $_POST["kodeitem"];
    $qty = $_POST["qty"];
    $hargabeli = $_POST["hargabeli"];
    $keterangan = $_POST["keterangan"];
    $total = $qty * $hargabeli;

    // Insert into hbeli table
    $sql1 = "INSERT INTO hbeli (nobeli, noref, tanggal, kodepemasuk, total, keterangan) 
             VALUES ('$nogenerate', '', '$tanggal', '$kodepemasok', '$total', '$keterangan')";

    // Insert into dbeli table
    $sql2 = "INSERT INTO dbeli (nogenerate, nobeli, KodeItem, qty, HargaBeli, Stok) 
             VALUES ('$nogenerate', '$nogenerate', '$kodeitem', '$qty', '$hargabeli', '$qty')";

    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
        // Update item stock
        updateItemStock($conn, $kodeitem, $qty);

        // Deduct balance from selected bank account
        deductBalance($conn, $koderekening, $total);

        header("Location: daftar_pembelian.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$rekening_result = $conn->query("SELECT koderekening, namarekening FROM rekening");
$pemasok_result = $conn->query("SELECT kodepemasuk, namapemasok FROM pemasok");
$item_result = $conn->query("SELECT KodeItem, Nama, HargaBeli FROM item");
$items = $item_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembelian - PT. MobilID</title>
    <style>
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        input[type="text"], input[type="number"], input[type="date"], select, textarea {
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
    <script>
        function updateItemDetails() {
            const items = <?php echo json_encode($items); ?>;
            const selectedItem = document.getElementById('namaItem').value;
            const item = items.find(item => item.KodeItem === selectedItem);

            if (item) {
                document.getElementById('kodeitem').value = item.KodeItem;
                document.getElementById('namabarang').value = item.Nama;
                document.getElementById('hargabeli').value = item.HargaBeli;
            } else {
                document.getElementById('kodeitem').value = '';
                document.getElementById('namabarang').value = '';
                document.getElementById('hargabeli').value = '';
            }

            updateTotal();
        }

        function updateTotal() {
            const qty = document.getElementById('qty').value;
            const hargaBeli = document.getElementById('hargabeli').value;
            document.getElementById('total').value = qty * hargaBeli;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Tambah Pembelian</h2>
        <form action="add_pembelian.php" method="post">
            <label for="nogenerate">No Generate</label>
            <input type="text" id="nogenerate" name="nogenerate" value="<?php echo generateNoGenerate($conn); ?>" readonly>

            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <label for="koderekening">Kode Rekening</label>
            <select id="koderekening" name="koderekening" required>
                <?php while ($row = $rekening_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['koderekening']; ?>"><?php echo $row['namarekening']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="kodepemasok">Nama Pemasok</label>
            <select id="kodepemasok" name="kodepemasok" required>
                <?php $pemasok_result->data_seek(0); ?>
                <?php while ($row = $pemasok_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['kodepemasuk']; ?>"><?php echo $row['namapemasok']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="namaItem">Nama Item</label>
            <select id="namaItem" name="kodeitem" onchange="updateItemDetails()" required>
                <option value="">Pilih Item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['KodeItem']; ?>"><?php echo $item['Nama']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="kodeitem">Kode Item</label>
            <input type="text" id="kodeitem" name="kodeitem" readonly>

            <label for="namabarang">Nama Barang</label>
            <input type="text" id="namabarang" name="namabarang" readonly>

            <label for="hargabeli">Harga Beli</label>
            <input type="number" id="hargabeli" name="hargabeli" readonly>

            <label for="qty">QTY</label>
            <input type="number" id="qty" name="qty" oninput="updateTotal()" required>

            <label for="total">Total</label>
            <input type="number" id="total" name="total" readonly>

            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="3" required></textarea>

            <input type="submit" value="Proses">
            <a href="daftar_pembelian.php" class="back">Back</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

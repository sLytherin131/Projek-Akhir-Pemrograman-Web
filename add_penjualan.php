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
    $sql = "SELECT MAX(nojual) AS max_nojual FROM hjual";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $lastNojual = $row['max_nojual'];

    if ($lastNojual === null) {
        $newNojual = '00001';
    } else {
        $newNojual = sprintf('%05d', intval($lastNojual) + 1);
    }

    return $newNojual;
}

// Function to update item stock
function updateItemStock($conn, $kodeitem, $qty) {
    $updateStokSql = "UPDATE item SET Stok = Stok - $qty WHERE KodeItem = '$kodeitem'";
    $conn->query($updateStokSql);
}

// Function to update rekening saldo
function updateRekeningSaldo($conn, $koderekening, $total) {
    $updateSaldoSql = "UPDATE rekening SET saldo = saldo + $total WHERE koderekening = '$koderekening'";
    $conn->query($updateSaldoSql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nojual = generateNoGenerate($conn);
    $tanggal = $_POST["tanggal"];
    $kodepelanggan = $_POST["kodepelanggan"];
    $kodeitem = $_POST["kodeitem"];
    $qty = $_POST["qty"];
    $hargajual = $_POST["hargajual"];
    $total = $qty * $hargajual;
    $keterangan = $_POST["keterangan"];
    $koderekening = $_POST["koderekening"];

    // Insert into hjual table
    $sql1 = "INSERT INTO hjual (nojual, tanggal, KodePelanggan, total, keterangan) 
             VALUES ('$nojual', '$tanggal', '$kodepelanggan', '$total', '$keterangan')";

    // Insert into djual table
    $sql2 = "INSERT INTO djual (nojual, nogenerate, KodeItem, qty, HargaJual) 
             VALUES ('$nojual', '$nojual', '$kodeitem', '$qty', '$hargajual')";

    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
        // Update item stock
        updateItemStock($conn, $kodeitem, $qty);
        
        // Update rekening saldo
        updateRekeningSaldo($conn, $koderekening, $total);

        header("Location: daftar_penjualan.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$pelanggan_result = $conn->query("SELECT KodePelanggan, namapelanggan FROM pelanggan");
$item_result = $conn->query("SELECT KodeItem, Nama, HargaJual FROM item");
$items = $item_result->fetch_all(MYSQLI_ASSOC);
$rekening_result = $conn->query("SELECT koderekening, namarekening FROM rekening");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penjualan - PT. MobilID</title>
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
                document.getElementById('hargajual').value = item.HargaJual;
            } else {
                document.getElementById('kodeitem').value = '';
                document.getElementById('namabarang').value = '';
                document.getElementById('hargajual').value = '';
            }

            updateTotal();
        }

        function updateTotal() {
            const qty = document.getElementById('qty').value;
            const hargaJual = document.getElementById('hargajual').value;
            document.getElementById('total').value = qty * hargaJual;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Tambah Penjualan</h2>
        <form action="add_penjualan.php" method="post">
            <label for="nojual">No Jual</label>
            <input type="text" id="nojual" name="nojual" value="<?php echo generateNoGenerate($conn); ?>" readonly>

            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" required>

            <label for="kodepelanggan">Nama Pelanggan</label>
            <select id="kodepelanggan" name="kodepelanggan" required>
                <?php while ($row = $pelanggan_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['KodePelanggan']; ?>"><?php echo $row['namapelanggan']; ?></option>
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

            <label for="hargajual">Harga Jual</label>
            <input type="number" id="hargajual" name="hargajual" readonly>

            <label for="qty">QTY</label>
            <input type="number" id="qty" name="qty" oninput="updateTotal()" required>

            <label for="total">Total</label>
            <input type="number" id="total" name="total" readonly>

            <label for="koderekening">Rekening</label>
            <select id="koderekening" name="koderekening" required>
                <?php while ($row = $rekening_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['koderekening']; ?>"><?php echo $row['namarekening']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="3" required></textarea>

            <input type="submit" value="Proses">
            <a href="daftar_penjualan.php" class="back">Back</a>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>
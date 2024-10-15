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

$table = $_GET['table'];

// Define table columns
$table_columns = [
    'item' => ['KodeItem', 'Nama', 'HargaBeli', 'HargaJual', 'Stok', 'Satuan'],
    'pelanggan' => ['KodePelanggan', 'namapelanggan', 'alamat', 'kota', 'telepon', 'email'],
    'pemasok' => ['kodepemasuk', 'namapemasok', 'alamat', 'kota', 'telepon', 'email'],
    'rekening' => ['koderekening', 'namarekening', 'saldo']
];

if (!array_key_exists($table, $table_columns)) {
    die("Invalid table name.");
}

// Function to generate PDF using PDFShift API
function generatePDF($html) {
    $api_key = 'sk_351578bf83f4607fb610d1ffa86c4d5a5deb6e5a'; // Replace with your PDFShift API key

    $params = array(
        'source' => $html,
        'landscape' => false,
        'use_print' => false
    );

    $curl = curl_init('https://api.pdfshift.io/v3/convert/pdf');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode('api:' . $api_key)
    ));

    $response = curl_exec($curl);
    if ($response === false) {
        $error_message = curl_error($curl);
        curl_close($curl);
        throw new Exception("cURL error: $error_message");
    }

    // Get the HTTP status code
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Close cURL resource
    curl_close($curl);

    // Check if the request was successful
    if ($http_status !== 200) {
        throw new Exception("Request failed with HTTP status code: $http_status");
    }

    return $response;
}

// Fetch data from database
$sql = "SELECT " . implode(', ', $table_columns[$table]) . " FROM $table";
$result = $conn->query($sql);
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Generate HTML table
$html = '<html><body><table border="1">';
$html .= '<tr><th>' . implode('</th><th>', $table_columns[$table]) . '</th></tr>';
foreach ($data as $row) {
    $html .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
}
$html .= '</table></body></html>';

try {
    // Generate PDF using PDFShift API
    $pdf_content = generatePDF($html);

    // Output the PDF directly to the browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="generated_pdf.pdf"');
    echo $pdf_content;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

$conn->close();
?>

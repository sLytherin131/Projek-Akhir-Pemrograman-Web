<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$filename = 'admin_name.txt';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newAdminName = $_POST['admin_name'];
    file_put_contents($filename, $newAdminName);
    header("Location: dashboard.php");
    exit();
}

$adminName = file_exists($filename) ? file_get_contents($filename) : 'Default Admin Name';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting - PT. MobilID</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .container h2 {
            text-align: center;
        }
        .container form {
            display: flex;
            flex-direction: column;
        }
        .container label {
            margin: 10px 0 5px;
        }
        .container input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .container input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #333;
            color: white;
            cursor: pointer;
        }
        .container input[type="submit"]:hover {
            background-color: #575757;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Setting</h2>
        <form method="post" action="setting.php">
            <label for="admin_name">Admin Name:</label>
            <input type="text" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($adminName); ?>" required>
            <input type="submit" value="Save">
        </form>
    </div>
</body>
</html>

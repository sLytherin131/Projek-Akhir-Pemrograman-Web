<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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
        .form-container {
            text-align: center;
            margin-right: 55px; /* Adjust margin-left as needed */
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the form horizontally */
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            margin-bottom: 10px;
            color: #000000;
            margin-left: 60px;
        }
        p {
            margin-bottom: 20px;
            color: #000000;
            margin-left: 60px;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
            color: #333333;
            text-align: left; /* Align labels to the left */
            width: 100%; /* Ensure labels take full width */
        }
        .input-container {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 24px); /* Adjust width to leave space for toggle password icon */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc;
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
        .error-message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
            text-align: center;
        }
        .input-icon {
            margin-right: 10px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <?php
    session_start();
    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        if (empty($username) || empty($password) || $username !== "admin" || $password !== "admin") {
            $error_message = "Username or password is incorrect. Please try again.";
        } else {
            $_SESSION['username'] = $username; // Save username to session
            header("Location: dashboard.php");
            exit();
        }
    }
    ?>
    <div class="form-container">
        <h1>LOGIN</h1>
        <p>PT. MobilID</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-container">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="input-container">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
                <i class="fa fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <input type="submit" value="Submit">
            <?php
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
        </form>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            var passwordField = document.getElementById('password');
            var type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>

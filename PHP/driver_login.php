<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'login';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'signup') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dob = $_POST['dob'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($phone) || empty($dob) || empty($password) || empty($_FILES['aadhaar']) || empty($_FILES['license'])) {
            $message = "Error: All fields are required!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // File Upload Handling
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $aadhaarPath = $uploadDir . basename($_FILES['aadhaar']['name']);
            $licensePath = $uploadDir . basename($_FILES['license']['name']);

            if (move_uploaded_file($_FILES['aadhaar']['tmp_name'], $aadhaarPath) && move_uploaded_file($_FILES['license']['tmp_name'], $licensePath)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO drivers (name, phone, dob, password, aadhaar, license) VALUES (?, ?, ?, ?, ?, ?)");
                    $success = $stmt->execute([$name, $phone, $dob, $passwordHash, $aadhaarPath, $licensePath]);

                    if ($success) {
                        $message = "Signup Successful!";
                    } else {
                        $message = "Signup Failed!";
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = "Phone number already registered!";
                    } else {
                        $message = "Database Error: " . $e->getMessage();
                    }
                }
            } else {
                $message = "File upload failed!";
            }
        }
    }

    if ($action === 'login') {
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($phone) || empty($password)) {
            $message = "Error: Phone number and password required!";
        } else {
            $stmt = $conn->prepare("SELECT * FROM drivers WHERE phone = ?");
            $stmt->execute([$phone]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($driver && password_verify($password, $driver['password'])) {
                $_SESSION['driver_id'] = $driver['id'];
                header("Location: dashboard.php"); // Redirect after successful login
                exit();
            } else {
                $message = "Invalid Credentials!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Driver Login/Signup</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f3f4f6; font-family: Arial, sans-serif; }
        .container { background: white; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 10px; width: 100%; max-width: 400px; padding: 20px; text-align: center; }
        h2 { color: #374151; margin-bottom: 15px; }
        label { display: block; text-align: left; font-weight: bold; color: #4b5563; margin-top: 10px; }
        input { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 5px; margin-top: 5px; }
        button { width: 100%; background-color: #06b6d4; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 15px; }
        button:hover { background-color: #0891b2; }
        .toggle-button { background: none; color: #06b6d4; text-decoration: underline; cursor: pointer; border: none; font-weight: bold; margin-top: 10px; }
        .error { color: red; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="formTitle">Hospital Driver Login</h2>

        <?php if (!empty($message)) : ?>
            <p class="text-center text-red-600"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="driver_login.php" method="POST" enctype="multipart/form-data">
            <div id="loginFields">
                <label>Phone Number (+91):</label>
                <input type="tel" name="phone" required>

                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div id="signupFields" class="hidden">
                <label>Full Name:</label>
                <input type="text" name="name">

                <label>Date of Birth:</label>
                <input type="date" name="dob">

                <label>Password:</label>
                <input type="password" name="password">

                <label>Upload Aadhaar (PNG):</label>
                <input type="file" name="aadhaar" accept="image/png">

                <label>Upload License (PNG):</label>
                <input type="file" name="license" accept="image/png">
            </div>

            <input type="hidden" id="action" name="action" value="login">
            <button type="submit" id="submitButton">Login</button>
        </form>

        <p class="toggle-button" onclick="toggleForm()">New? Signup here</p>
    </div>

    <script>
        function toggleForm() {
            document.getElementById("signupFields").classList.toggle("hidden");
            document.getElementById("loginFields").classList.toggle("hidden");
            document.getElementById("action").value = document.getElementById("signupFields").classList.contains("hidden") ? "login" : "signup";
            document.getElementById("submitButton").innerText = document.getElementById("action").value === "login" ? "Login" : "Signup";
        }
    </script>
</body>
</html>

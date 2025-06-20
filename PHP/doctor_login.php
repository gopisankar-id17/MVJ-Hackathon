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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $specialization = trim($_POST['specialization'] ?? '');
        $degree = trim($_POST['degree'] ?? '');

        if (empty($name) || empty($email) || empty($password)) {
            $message = "Error: All fields are required!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imagePath = $uploadDir . basename($_FILES['image']['name']);
            $licensePath = $uploadDir . basename($_FILES['license']['name']);

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || $_FILES['license']['error'] !== UPLOAD_ERR_OK) {
                $message = "File upload failed!";
            } else {
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                move_uploaded_file($_FILES['license']['tmp_name'], $licensePath);

                try {
                    $stmt = $conn->prepare("INSERT INTO doctors (name, email, password, specialization, degree, image, license) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $success = $stmt->execute([$name, $email, $passwordHash, $specialization, $degree, $imagePath, $licensePath]);

                    if ($success) {
                        $message = "Signup Successful!";
                    } else {
                        $message = "Signup Failed!";
                    }
                } catch (PDOException $e) {
                    $message = "Database Error: " . $e->getMessage();
                }
            }
        }
    }

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $message = "Error: Email and password required!";
        } else {
            $stmt = $conn->prepare("SELECT * FROM doctors WHERE email = ?");
            $stmt->execute([$email]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($doctor && password_verify($password, $doctor['password'])) {
                $_SESSION['doctor_id'] = $doctor['id'];
                header("Location: dashboard.php");
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
    <title>Doctor Login/Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">

    <div class="flex bg-white bg-opacity-90 rounded-lg shadow-lg overflow-hidden w-full max-w-5xl p-10">
        <div class="w-1/2 bg-gradient-to-r from-blue-500 to-green-500 p-10 text-white hidden md:flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-3">Expert advice from top doctors</h2>
            <p class="text-sm mb-6">✔ Available 24/7 on any device</p>
            <p class="text-sm mb-6">✔ Private questions answered within 24 hrs</p>
        </div>

        <div class="w-full md:w-1/2 p-8">
            <h2 id="formTitle" class="text-2xl font-semibold text-gray-700 text-center mb-6">Doctor Login</h2>

            <?php if (!empty($message)) : ?>
                <p class="text-center text-red-600 mb-3"><?php echo $message; ?></p>
            <?php endif; ?>

            <form id="doctorForm" action="doctor_login.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div id="loginFields">
                    <label class="block text-gray-600 font-semibold">Email:</label>
                    <input type="email" name="email" id="email" required class="w-full p-3 border rounded">
                    
                    <label class="block text-gray-600 font-semibold">Password:</label>
                    <input type="password" name="password" id="password" required class="w-full p-3 border rounded">
                </div>

                <div id="signupFields" class="hidden space-y-4">
                    <label class="block text-gray-600 font-semibold">Name:</label>
                    <input type="text" name="name" id="name" class="w-full p-3 border rounded">

                    <label class="block text-gray-600 font-semibold">Specialization:</label>
                    <select name="specialization" id="specialization" class="w-full p-3 border rounded">
                        <option value="General" selected>General</option>
                        <option value="Cardiologist">Cardiologist</option>
                        <option value="Dermatologist">Dermatologist</option>
                        <option value="Neurologist">Neurologist</option>
                        <option value="Pediatrician">Pediatrician</option>
                        <option value="Psychiatrist">Psychiatrist</option>
                        <option value="Surgeon">Surgeon</option>
                    </select>

                    <label class="block text-gray-600 font-semibold">Degree:</label>
                    <input type="text" name="degree" id="degree" class="w-full p-3 border rounded">

                    <label class="block text-gray-600 font-semibold">Upload Profile Image:</label>
                    <input type="file" name="image" id="image" required class="w-full p-3 border rounded">

                    <label class="block text-gray-600 font-semibold">Upload Medical License:</label>
                    <input type="file" name="license" id="license" required class="w-full p-3 border rounded">
                </div>

                <input type="hidden" id="action" name="action" value="login">
                <button type="submit" id="submitButton" class="w-full bg-blue-600 text-white p-3 rounded text-lg font-bold">Login</button>
            </form>

            <button class="mt-4 text-blue-600 underline w-full text-center font-semibold" onclick="toggleForm()">New? Signup here</button>
        </div>
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

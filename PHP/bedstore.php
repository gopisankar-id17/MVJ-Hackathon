<?php
$conn = new mysqli("localhost", "root", "", "hospital");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bed_type = $_POST['bed_type'];
    $total_beds = $_POST['total_beds'];
    $available_beds = $_POST['available_beds'];

    $stmt = $conn->prepare("INSERT INTO beds (bed_type, total_beds, available_beds) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $bed_type, $total_beds, $available_beds);

    if ($stmt->execute()) {
        $message = "<p class='success'>✅ Bed availability added successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Error adding bed availability.</p>";
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Bed Availability</title>
    <style>
        /* Styles for the form and layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header {
            background-color: #54d5fd;
            color: white;
            padding: 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
        }

        .form-container {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-btn {
            background-color: #54d5fd;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .submit-btn:hover {
            background-color:#2396b9;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
        </header>

        <div class="form-container">
            <h2>Add Bed Availability</h2>
            <?php if (isset($message)) echo $message; ?>
            <form method="POST">
                <label for="bed_type">Bed Type</label>
                <select name="bed_type" required>
                    <option value="ICU">ICU</option>
                    <option value="General">General</option>
                    <option value="Emergency">Emergency</option>
                    <!-- You can add more bed types here -->
                </select>

                <label for="total_beds">Total Beds</label>
                <input type="number" name="total_beds" min="1" required>

                <label for="available_beds">Available Beds</label>
                <input type="number" name="available_beds" min="0" required>

                <button type="submit" class="submit-btn">Add Bed Availability</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>

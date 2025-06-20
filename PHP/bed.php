<?php
$conn = new mysqli("localhost", "root", "", "hospital");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $available_beds = $_POST['available_beds'];

    $stmt = $conn->prepare("UPDATE beds SET available_beds = ? WHERE id = ?");
    $stmt->bind_param("ii", $available_beds, $id);
    if ($stmt->execute()) {
        $message = "<p class='success'>✅ Bed availability added successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Error adding bed availability.</p>";
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM beds");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bed Availability</title>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and basic layout */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            padding: 0;
            margin: 0;
        }

        /* Dashboard container */
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .dashboard-header {
            background-color: #54d5fd;
            color: white;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
        }

        /* Bed availability table */
        .bed-availability {
            padding: 20px;
        }

        .availability-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .availability-table th, .availability-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .availability-table th {
            background-color: #54d5fd;
            color: white;
        }

        .availability-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Update button */
        .update-btn {
            background-color: #54d5fd;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .update-btn:hover {
            background-color: #2396b9;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Footer */
        .dashboard-footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .submit-btn {
            background-color: #54d5fd;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        /* Additional CSS for a cleaner look */
        .dashboard-container {
            font-size: 16px;
        }

        .modal-content input {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .modal-content label {
            font-weight: bold;
        }

        .availability-table td button {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Bed Availability Dashboard</h1>
        </header>

        <section class="bed-availability">
            <h2>Bed Availability Overview</h2>
            <?php if (isset($message)) echo $message; ?>
            <table class="availability-table">
                <thead>
                    <tr>
                        <th>Bed Type</th>
                        <th>Total Beds</th>
                        <th>Available Beds</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['bed_type']; ?></td>
                        <td><?php echo $row['total_beds']; ?></td>
                        <td><?php echo $row['available_beds']; ?></td>
                        <td>
                            <button class="update-btn" onclick="document.getElementById('update-<?php echo $row['id']; ?>').style.display='block'">Update</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Update Bed Modal -->
        <?php
        $result->data_seek(0); // Reset result pointer
        while ($row = $result->fetch_assoc()):
        ?>
        <div id="update-<?php echo $row['id']; ?>" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="document.getElementById('update-<?php echo $row['id']; ?>').style.display='none'">&times;</span>
                <h3>Update Available Beds for <?php echo $row['bed_type']; ?></h3>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <label for="available_beds">Available Beds:</label>
                    <input type="number" name="available_beds" value="<?php echo $row['available_beds']; ?>" required>
                    <button type="submit" class="submit-btn">Update</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>

        <footer class="dashboard-footer">
            <p>&copy; Ambulance AI</p>
        </footer>
    </div>

    <script>
        // Close modal if clicked outside
        window.onclick = function(event) {
            if (event.target.className == "modal") {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>

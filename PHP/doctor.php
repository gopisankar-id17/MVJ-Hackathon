<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Store message (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userid']) && isset($_POST['message'])) {
    $message = $_POST['message'];
    $userid = $_POST['userid'];

    // Insert message into database
    $sql = "INSERT INTO doctor (userid, message) VALUES ('$userid', '$message')";
    $conn->query($sql);
}

// Fetch messages
$messages = [];
$sql = "SELECT * FROM doctor ORDER BY timestamp ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'userid' => $row['userid'],
            'message' => $row['message'],
            'timestamp' => $row['timestamp']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Chat</title>
    <link rel="stylesheet" href="../CSS/chat.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #f3f3f3;
        }

        .chat-container {
            display: flex;
            height: 90vh;
            margin: 20px;
        }

        /* Sidebar */
        .chat-sidebar {
            width: 250px;
            background: #024aff;
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .chat-sidebar ul {
            list-style: none;
        }

        .chat-user {
            padding: 10px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.2);
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            transition: 0.3s;
        }

        .chat-user:hover,
        .chat-user.active {
            background: white;
            color: #024aff;
        }

        /* Chat Box */
        .chat-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 10px;
            margin-left: 20px;
            overflow: hidden;
        }

        .chat-window {
            display: none;
            flex-direction: column;
            height: 100%;
        }

        .chat-window.active {
            display: flex;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 70%;
            padding: 10px 15px;
            margin: 8px 10px;
            border-radius: 20px;
            position: relative;
            font-size: 14px;
            word-wrap: break-word;
        }

        /* Doctor Messages - Align Right */
        .doctor {
            align-self: flex-end;
            background: #024aff;
            color: white;
        }

        /* User Messages - Align Left */
        .user {
            align-self: flex-start;
            background: #ddd;
            color: black;
        }

        /* File preview */
        .message img,
        .message video,
        .message audio {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 5px;
        }

        /* Input Area */
        .chat-input {
            display: flex;
            padding: 10px;
            background: white;
            align-items: center;
        }

        input[type="file"] {
            display: none;
        }

        .label-file {
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            color: white;
            margin-right: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border-radius: 25px;
            outline: none;
            border: none;
            background: #f1f1f1;
        }

        button {
            padding: 10px;
            background: #024aff;
            color: white;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <h2>Chat Users</h2>
            <ul>
                <li class="chat-user active" data-chat="doctor">Patient 1</li>
            </ul>
        </div>

        <!-- Chat Box -->
        <div class="chat-box">
            <div id="doctor-chat" class="chat-window active">
                <h3>Chat with Patient 1</h3>
                <div class="chat-messages" id="doctor-messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?= $message['userid'] == 'doctor' ? 'doctor' : 'user'; ?>">
                            <span class="message-text"><?= htmlspecialchars($message['message']) ?></span>
                            <span class="message-time"><?= date('H:i', strtotime($message['timestamp'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Chat Input -->
                <div class="chat-input">
                    <label for="doctor-file" class="label-file">📎</label>
                    <input type="file" id="doctor-file" accept="image/*, video/*, audio/*">
                    <input type="text" id="doctor-message" placeholder="Type a message...">
                    <button onclick="sendMessage('doctor')">➤</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sendMessage(userType) {
            const messageInput = document.getElementById(`${userType}-message`);
            const message = messageInput.value.trim();

            if (message) {
                const formData = new FormData();
                formData.append('userid', userType);
                formData.append('message', message);

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    messageInput.value = '';
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function fetchMessages() {
            setTimeout(fetchMessages, 2000);
        }

        fetchMessages();
    </script>
</body>
</html>

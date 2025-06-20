<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambulance AI</title>
    <link rel="stylesheet" href="../CSS/landing.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <script>
        function sendEmail() {
            fetch('/send_email')
                .then(response => response.json())
                .then(data => alert(data.message || data.error))
                .catch(error => alert("Error sending email!"));
        }
    </script>
</head>

<body>
    <p class="head">Ambulance AI</p>
    <input type="checkbox" name="" id="sidebar" value="">
    <div class="toggle">
        <p><i class='bx bx-menu-alt-right bx-lg' id="right"></i></p>
        <p><i class='bx bx-menu-alt-left bx-lg' id="left"></i></p>
    </div>

    <div class="nav">
        <div class="nav-logo">
            <a href="./landing.php" class="logo">AI Ambulance</a>
        </div>
        <div class="nav-middle">
            <a href="./about.html">About</a>
        </div>
        <div class="nav-middle">
            <a href="#contact">Contact</a>
        </div>
        <div class="nav-right">
            <a href="./doctor_login.php" class="login ">Doctor Login</a>
        </div>
        <div class="nav-right">
            <a href="./user_login.php" class="login ">User Login</a>
        </div>
        <div class="nav-right">
            <a href="./driver_login.php" class="signup">Driver Login</a>
        </div>
    </div>

    <div class="content">
        <div class="map">
            <p>🚑 SOS Ambulance Services 🚑</p>
            <ul>
                <li>📌 One-Tap Ambulance Request</li>
                <li>📍 Real-Time GPS Tracking</li>
                <li>🏥 Hospital Bed Availability</li>
                <li>🚗 AI-Optimized Routes</li>
                <li>💬 Chat with Medical Teams (Text, Images, Videos, Audio)</li>
            </ul>
            <p>Your safety, our priority! 🚨</p>
        </div>
        <a href="http://127.0.0.1:5000/send_email_and_sms" style="font-size: 20px; padding: 10px; background-color: #54d5fd; color: black; border: none; cursor: pointer;height: 20%;width: 10%;    border-radius: 50%;">SOS</a>
        <div class="chat">
            <div class="chat-item">
            <p>Chat :</p>
            <ul>
                <li><a href="./user.php">User Chat</a></li>
                <li><a href="./doctor.php">Doctor Chat</a></li>
            </ul>
            </div>
            <div class="chat-item">
            <p>Hospital :</p>
            <ul>
                <li><a href="./map.html">Check Hospital</a></li>
            </ul>
            </div>
            <div class="chat-item">
            <p>Bed Available :</p>
            <ul>
                <li><a href="./bedstore.php">Manage</a></li>
                <li><a href="./bed.php">Available</a></li>
            </ul>
            </div>
        </div>
    </div>
</body>

</html>

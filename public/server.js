require('dotenv').config(); // Ensure environment variables are loaded from .env
const express = require("express");
const cors = require("cors");
const bodyParser = require("body-parser");
const path = require("path");

const { Groq } = require("groq-sdk");
const twilio = require("twilio");  // Import Twilio SDK

const app = express();

// Middleware setup
app.use(cors());
app.use(bodyParser.json());

// Serve static files (like HTML, CSS, JS)
app.use(express.static(path.join(__dirname, "public")));

// Check if the GROQ_API_KEY is being loaded correctly
console.log("GROQ_API_KEY:", process.env.GROQ_API_KEY);

// ✅ Initialize Groq API with API key from environment variable
const groq = new Groq({ apiKey: process.env.GROQ_API_KEY });

// ✅ Initialize Twilio with SID and Auth Token from environment variables
const twilioClient = new twilio(process.env.TWILIO_SID, process.env.TWILIO_AUTH_TOKEN);

// Home Route: Serve the main page
app.get("/", (req, res) => {
    res.sendFile(path.join(__dirname, "public", "askquestions.html"));
});

// ✅ SOS Route: Endpoint to send SOS SMS
app.post("/send-sos", async (req, res) => {
    const { message, phoneNumber } = req.body;

    // Ensure the message and phoneNumber are provided
    if (!message || !phoneNumber) {
        return res.status(400).json({ error: "Message and phone number are required." });
    }

    try {
        // Send SMS via Twilio
        const twilioResponse = await twilioClient.messages.create({
            body: message,
            from: process.env.TWILIO_PHONE_NUMBER, // Twilio phone number from env
            to: phoneNumber, // Recipient phone number from request
        });

        console.log("✅ Message Sent:", twilioResponse.sid);
        res.json({ success: "SOS message sent successfully!" });
    } catch (error) {
        console.error("❌ Error sending SMS:", error);
        res.status(500).json({ error: "Failed to send SOS message." });
    }
});

// Start the server
const PORT = process.env.PORT || 5000;  // Use the port from environment variables or fallback to 5000
app.listen(PORT, () => {
    console.log(`✅ Server running on http://localhost:${PORT}`);
});

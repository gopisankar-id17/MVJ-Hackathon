const chatBox = document.getElementById("chat-box");
const userInput = document.getElementById("user-input");
const sendBtn = document.getElementById("send-btn");
const voiceBtn = document.getElementById("voice-btn");

// 🎤 Speech Recognition (Voice Input)
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
const recognition = new SpeechRecognition();
recognition.lang = "en-US";
recognition.interimResults = false;

let recognitionActive = false;

voiceBtn.addEventListener("click", () => {
    if (recognitionActive) return; // Prevent starting recognition while it's already active

    recognitionActive = true;
    voiceBtn.disabled = true; // Disable the voice button while recognition is active
    appendMessage("bot", "🎤 Listening...");

    try {
        recognition.start();
    } catch (error) {
        console.error("Speech Recognition Error:", error);
        appendMessage("bot", "⚠️ Voice recognition is not available.");
    }
});

recognition.onresult = (event) => {
    const voiceText = event.results[0][0].transcript;
    userInput.value = voiceText;
    recognitionActive = false;
    voiceBtn.disabled = false; // Re-enable button after recognition is done
    appendMessage("user", voiceText); // Display recognized voice message
};

recognition.onerror = (event) => {
    console.error("Speech Recognition Error:", event.error);
    appendMessage("bot", "⚠️ Voice recognition failed. Try again.");
    recognitionActive = false;
    voiceBtn.disabled = false; // Re-enable button after error
};

// 🎙️ Text-to-Speech (Voice Output)
let speaking = false;

function speak(text) {
    if (speaking) {
        speechSynthesis.cancel(); // Stop previous speech if a new response comes in
    }

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = "en-US";
    utterance.rate = 1;

    speechSynthesis.speak(utterance);

    speaking = true;
    utterance.onend = () => {
        speaking = false;
    };
}

// ✅ Function to send message to backend
async function sendMessage() {
    const message = userInput.value.trim();
    if (!message) return; // Prevent sending empty messages

    // Display user message
    appendMessage("user", message);
    userInput.value = "";
    sendBtn.disabled = true; // Disable button to prevent multiple clicks

    // Show typing indicator while waiting
    const typingIndicator = appendMessage("bot", "Typing...");

    try {
        const response = await fetch("http://localhost:5000/generate-completion", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ content: message }),
        });

        if (!response.ok) {
            throw new Error(`Server error: ${response.status}`);
        }

        const data1 = await response.json();

        // Remove typing indicator safely (only if present)
        if (typingIndicator && typingIndicator.parentNode) {
            chatBox.removeChild(typingIndicator);
        }

        const data = data1.reply || "I couldn't understand that.";
        appendMessage("bot", data);
        speak(data); // Speak the response
    } catch (error) {
        console.error("Error:", error);

        if (typingIndicator && typingIndicator.parentNode) {
            chatBox.removeChild(typingIndicator);
        }

        appendMessage("bot", "⚠️ Unable to reach AI. Please check your connection.");
    } finally {
        sendBtn.disabled = false; // Re-enable button
    }
}

// ✅ Append messages to chat box
function appendMessage(sender, text) {
    const msgDiv = document.createElement("div");
    msgDiv.classList.add(sender === "user" ? "user-message" : "bot-message");
    msgDiv.textContent = text;
    chatBox.appendChild(msgDiv);

    // Auto-scroll to latest message
    setTimeout(() => {
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 100);

    return msgDiv; // Return element to remove later (for typing indicator)
}

// ✅ Send message on button click or Enter key
sendBtn.addEventListener("click", sendMessage);
userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
});

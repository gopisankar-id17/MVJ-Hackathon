function sendMessage(userType) {
    const messageInput = document.getElementById(`${userType}-message`);
    const message = messageInput.value.trim();

    if (message) {
        const formData = new FormData();
        formData.append('userid', userType);
        formData.append('message', message);

        fetch('store_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            messageInput.value = '';
            fetchMessages();
        })
        .catch(error => console.error('Error:', error));
    }
}
function fetchMessages() {
    fetch('fetch_messages.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('doctor-messages').innerHTML = '';
            document.getElementById('patient-messages').innerHTML = '';
            document.getElementById('driver-messages').innerHTML = '';

            data.forEach(message => {
                const chatWindow = document.getElementById(`${message.userid}-messages`);
                if (chatWindow) {
                    const messageElement = document.createElement('div');
                    messageElement.className = 'message';
                    messageElement.textContent = message.message;
                    chatWindow.appendChild(messageElement);
                }
            });
        })
        .catch(error => console.error('Error:', error));
}
setInterval(fetchMessages, 5000);
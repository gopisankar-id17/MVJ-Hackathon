from flask import Flask, jsonify
import smtplib
import requests
from email.message import EmailMessage

app = Flask(__name__)

EMAIL_USER = "gowthamcdstudies@gmail.com"
EMAIL_PASSWORD = "uygz uplr iwsv whdm"

def get_location():
    try:
        response = requests.get("https://ipinfo.io/json")
        data = response.json()
        location = data.get("loc", "Unknown Location")
        city = data.get("city", "Unknown City")
        region = data.get("region", "Unknown Region")
        country = data.get("country", "Unknown Country")

        google_maps_link = f"https://www.google.com/maps?q={location}"
        return f"Location: {city}, {region}, {country}\nGoogle Maps: {google_maps_link}"
    except Exception as e:
        return f"Could not retrieve location. Error: {e}"

def email_alert(subject, body, to):
    location_info = get_location()
    full_body = f"{body}\n\n{location_info}"

    msg = EmailMessage()
    msg.set_content(full_body)
    msg['subject'] = subject
    msg['to'] = to
    msg['from'] = EMAIL_USER  

    server = smtplib.SMTP("smtp.gmail.com", 587)
    server.starttls()
    server.login(EMAIL_USER, EMAIL_PASSWORD)
    server.send_message(msg)
    server.quit()

@app.route('/send_email')
def send_email():
    try:
        email_alert("SOS Emergency", "I'm in critical condition! Please help!", "gowthamcdkec@gmail.com")
        return jsonify({"message": "🚑 SOS email sent successfully!"})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)

import openai

# Initialize OpenAI client with API key
client = openai.OpenAI(api_key="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")  # Replace with your actual API key

def chat_with_gpt(prompt):
    response = client.chat.completions.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": prompt}]
    )
    return response.choices[0].message.content.strip()

if __name__ == "__main__":
    while True:
        user_input = input("U: ")
        if user_input.lower() in ["quit", "exit", "bye"]:
            break
        else:
            response = chat_with_gpt(user_input)
            print("Chatbot:", response)

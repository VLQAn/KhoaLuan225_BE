#!/usr/bin/env python3
import requests
import json

# Test the chatbot API
url = "http://127.0.0.1:8000/api/chatbot/ask"

# First, let's try to login to get a valid token
login_url = "http://127.0.0.1:8000/api/auth/login"
login_data = {
    "email": "user@example.com",
    "password": "password"
}

try:
    # Try login first
    print("Attempting login...")
    login_response = requests.post(login_url, json=login_data)
    print(f"Login status: {login_response.status_code}")
    print(f"Login response: {login_response.text[:200]}")
    
    if login_response.status_code == 200:
        token = login_response.json().get('token')
        print(f"Got token: {token}")
        
        # Now test the chatbot with the token
        headers = {
            "Authorization": f"Bearer {token}",
            "Content-Type": "application/json"
        }
        
        test_message = "Đặt 2 vé star wars"
        data = {"message": test_message}
        
        print(f"\nTesting chatbot with message: '{test_message}'")
        response = requests.post(url, json=data, headers=headers)
        print(f"Response status: {response.status_code}")
        print(f"Response: {json.dumps(response.json(), indent=2, ensure_ascii=False)}")
    
except Exception as e:
    print(f"Error: {e}")

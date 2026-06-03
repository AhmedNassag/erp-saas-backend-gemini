import requests

CLICKUP_BASE_URL = "https://api.clickup.com/api/v2/"
CLICKUP_HEADERS = {
    "Authorization": "pk_87724266_OMZQR40QGNZ6Z5OBH3KRBSIHCIFPMT4T",
}

def get_task(task_id):
    url = f"{CLICKUP_BASE_URL}task/{task_id}"
    response = requests.get(url, headers=CLICKUP_HEADERS)
    response.raise_for_status()
    data = response.json()
    return f"Title: {data['name']}\nDescription: {data['description']}"
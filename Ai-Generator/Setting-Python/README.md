# Generator-Setting-Python

This is a Python port of the Generator-Setting fullstack generator.

## Setup

1. Install Python 3.x
2. Run the setup script to create a virtual environment and install dependencies:
   ```powershell
   # Create virtual environment
   python -m venv venv
   
   # Activate virtual environment (Windows)
   .\venv\Scripts\activate
   
   # Install dependencies
   pip install -r requirements.txt
   ```
   
   *Note: I have already performed these steps for you in this directory.*

## Usage

1. Activate the virtual environment (if not already active):
   ```powershell
   .\venv\Scripts\activate
   ```
2. Run the generator:
   ```powershell
   python fullstack_generator.py
   ```
3. Run the gui:
   ```powershell
   python main.py
   ```

## Features

- **ClickUp Integration**: Fetches task details.
- **AI Integration**: Generates Schema from task description using OpenAI.
- **Code Generation**: Creates Backend (Laravel/MongoDB) and Frontend (Vue/other) files based on templates.

## Structure

- `fullstack_generator.py`: Main entry point.
- `generator_core.py`: Core logic for file generation.
- `generator_utils.py`: Utility functions.
- `generator_constants.py`: Configuration for fields and relationships.
- `ClickUpIntegration.py`: ClickUp API client.
- `LLMIntegaration.py`: OpenAI API client.
- `templates/`: (Copied from original project)





# 1 # python -m venv venv
# 2 # .\venv\Scripts\pip install -r requirements.txt
# 3 # .\venv\Scripts\activate
# 4 # python fullstack_generator.py
# 5 # .\venv\Scripts\pip install python-dotenv
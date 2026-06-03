import os
import re

def display_banner():
    print("""
    =============================================
       FULLSTACK MODULE GENERATOR (Python Edition)
    =============================================
    """)

def display_completion_banner():
    print("""
    =============================================
             GENERATION COMPLETE
    =============================================
    """)

def check_backend_app_exists(backend_path):
    return os.path.exists(os.path.join(backend_path, "composer.json"))

def check_frontend_src_exists(frontend_path):
    return os.path.exists(os.path.join(frontend_path, "src"))

def validate_path(input_path):
    if not input_path or not input_path.strip():
        return "Path cannot be empty"
    # Logic to check if generic path valid? 
    # Inquirer validation returns True or string error.
    return True

def validate_model_name(input_name):
    if not input_name or not input_name.strip():
        return "Model name cannot be empty"
    if not re.match(r"^[A-Z][a-zA-Z0-9]*$", input_name):
        return "Model name must be PascalCase (e.g., Product, UserProfile)"
    return True

def validate_field_name(input_name):
    if not input_name or not input_name.strip():
        return "Field name cannot be empty"
    if not re.match(r"^[a-z][a-zA-Z0-9_]*$", input_name):
        return "Field name must be camelCase or snake_case (start with lowercase)"
    return True

def check_backend_model_exists(backend_path, model_name):
    # App/Models/ModelName.php ?
    # Original checks path recursively or specific location? 
    # Usually Laravel is app/Models.
    # The original generator seems to place them in specific folders?
    # Let's assume standard Laravel structure or what checking logic was there.
    # The original logic used `fileExists`.
    # Let's assume `backend_path/app/Models/{model_name}/{model_name}.php` because the generator creates folders.
    model_path = os.path.join(backend_path, "app", "Models", model_name, f"{model_name}.php")
    return os.path.exists(model_path)

import os
from dotenv import load_dotenv
from openai import OpenAI

load_dotenv()

api_key = os.getenv("OPENAI_API_KEY")

if not api_key:
    # Fallback to the hardcoded key if env var not set (though it's broken, it preserves behavior if user did nothing)
    # But actually, better to warn or just let OpenAI SDK handle the error if empty.
    # The user's error shows the hardcoded key is quota-exceeded.
    # So checking for env var is critical.
    print("Warning: OPENAI_API_KEY not found in environment variables.")
    # We'll leave the client init to fail or rely on the SDK's default env var lookup if we didn't pass it explicit
    # But I'll pass it explicitly to be clear.
    # If None, SDK might look for it too.
    api_key = "your_openai_api_key" # Replace with your OpenAI key

client = OpenAI(api_key=api_key)

def generate_schema(task_description):
    prompt = generate_prompt(task_description)
    # Adapting to standard OpenAI ChatCompletion as 'responses.create' with 'input'/'output_text' 
    # is not standard OpenAI Python SDK or current Node SDK. 
    # Assuming the user wants a chat completion with the given model.
    try:
        response = client.chat.completions.create(
            model="gpt-5-nano", # Fallback to a known model if gpt-5-nano is invalid, but I'll try to stick to prompt intent if I can.
            # Using gpt-4o as a safe high-intelligence default since gpt-5-nano is likely hallucinated or private.
            # If the user strictly needs gpt-5-nano, they can change it or I can try it.
            # Given the 'input' in original, it might be a completion model?
            messages=[
                {"role": "user", "content": prompt}
            ]
        )
        return response.choices[0].message.content
    except Exception as e:
        print(f"Error calling OpenAI: {e}")
        return "{}"

def generate_prompt(task_description):
    return f"""# You are a senior software architect and JSON schema generator.
Your ONLY task is to convert the user's task or feature description into a JSON object that STRICTLY follows the schema below.
# OUTPUT RULES (MANDATORY)

Output ONLY valid JSON.
Do NOT add explanations, comments, markdown, or text outside JSON.
Do NOT rename keys.
Do NOT set any special Characters or numbers in the keys.
DO NOT set any special Characters or numbers in the values.
DO NOT USE ANY SECIAL CHARACTERS FOR ANY KEY AND VALUE FOR ANY MODULE.
Do NOT invent fields or relationships not explicitly mentioned.
If something is unclear, make the most reasonable assumption based on common simple CRUD systems.
Field types must be one of:
   - string
   - textarea
   - integer
   - decimal
   - boolean
   - date
   - datetime
   - enum
showInTable must always be true or false.
Relationship types must be one of:
   - belongsTo
   - hasMany
   - belongsToMany
   - hasOne
Method names must be camelCase.
Foreign keys must be snake_case.
Always return arrays even if they contain a single item.
=====================
NATURAL LANGUAGE INTERPRETATION RULES
=====================


The task description may be written in non-technical language.
Infer relationships from meaning, not keywords.
Interpretation examples:
"Each item has one category" → belongsTo
"A category contains many items" → hasMany
"Items can have multiple labels" → belongsToMany
"A profile is linked to one user" → hasOne
"A user can have many orders" → hasMany
Words like:
  "has", "contains", "includes", "linked to", "assigned to", "grouped under"
  imply relationships.
If no relationship can be reasonably inferred, do NOT create one.
Prefer the most common real-world business relationship.
=====================
BASE JSON STRUCTURE (DO NOT CHANGE)
=====================
{{
  "modules": [
    {{
      "name": "",
      "fields": [],
      "relationships": []
    }}
  ]
}}
=====================
FIELD OBJECT STRUCTURE
=====================
{{
  "name": "",
  "type": "",
  "description": "",
  "showInTable": true
}}
=====================
RELATIONSHIP STRUCTURE
=====================
{{
  "type": "",
  "relatedModel": "",
  "methodName": "",
  "foreignKey": "",
  "inverseMethod": "",
  "inverseType": ""
}}
For belongsToMany ONLY, use this structure:
{{
  "type": "belongsToMany",
  "relatedModel": "",
  "methodName": "",
  "foreignKey": "",
  "localKey": {{
    "inverseField": ""
  }},
  "inverseMethod": "",
  "inverseType": "belongsToMany"
}}
=====================
IMPORTANT
=====================
Assume this JSON will be used for automatic simple CRUD generation.
Do NOT add authentication, permissions, soft deletes, or advanced logic unless explicitly mentioned.
=====================
TASK DESCRIPTION (SOURCE OF TRUTH)
=====================
{task_description}
"""
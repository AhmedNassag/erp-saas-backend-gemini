import os
import json
import re
import sys
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple, Union
import inquirer
import shutil
# Import custom modules for ClickUp and LLM integration
try:
    from ClickUpIntegration import get_task
    from LLMIntegration import generate_schema
except ImportError:
    # Create dummy functions if modules don't exist
    def get_task(task_id):
        raise ImportError("ClickUpIntegration module not found")
    
    def generate_schema(task_data):
        raise ImportError("LLMIntegration module not found")

# ============================================================================
# FIELD TYPES - Combined with comprehensive validation
# ============================================================================
FIELD_TYPES = {
    "string": {
        "formType": "text",
        "validation": "required|string|max:255",
        "updateValidation": "required|string|max:255",
        "tableDisplay": "text",
        "testValue": '"Sample text"',
        "updateValue": '"Updated sample text"',
        "col": 6,
    },
    "email": {
        "formType": "text",
        "validation": "required|email|unique:@@table@@,email",
        "updateValidation": "required|email|unique:@@table@@,email,{$id}",
        "tableDisplay": "text",
        "testValue": '"test@example.com"',
        "updateValue": '"updated@example.com"',
        "col": 6,
    },
    "password": {
        "formType": "password",
        "validation": "required|min:8",
        "updateValidation": "nullable|min:8",
        "tableDisplay": "hidden",
        "testValue": '"password123"',
        "updateValue": '"newpassword123"',
        "col": 6,
    },
    "phone": {
        "formType": "text",
        "validation": "required|regex:/^([0-9\\s\\-\\+\\(\\)]*)$/|min:10|max:20",
        "updateValidation": "required|regex:/^([0-9\\s\\-\\+\\(\\)]*)$/|min:10|max:20",
        "tableDisplay": "text",
        "testValue": '"+1234567890"',
        "updateValue": '"+9876543210"',
        "col": 6,
    },
    "url": {
        "formType": "text",
        "validation": "required|url|max:500",
        "updateValidation": "required|url|max:500",
        "tableDisplay": "text",
        "testValue": '"https://example.com"',
        "updateValue": '"https://updated.example.com"',
        "col": 6,
    },
    "number": {
        "formType": "number",
        "validation": "required|integer|min:0",
        "updateValidation": "required|integer|min:0",
        "tableDisplay": "number",
        "testValue": "42",
        "updateValue": "52",
        "col": 6,
    },
    "integer": {
        "formType": "number",
        "validation": "required|integer|min:0",
        "updateValidation": "required|integer|min:0",
        "tableDisplay": "number",
        "testValue": "42",
        "updateValue": "52",
        "col": 6,
    },
    "decimal": {
        "formType": "number",
        "validation": "required|numeric|min:0",
        "updateValidation": "required|numeric|min:0",
        "tableDisplay": "number",
        "testValue": "99.99",
        "updateValue": "109.99",
        "col": 6,
    },
    "date": {
        "formType": "date",
        "validation": "required|date",
        "updateValidation": "required|date",
        "tableDisplay": "date",
        "testValue": '"2024-01-01"',
        "updateValue": '"2024-12-31"',
        "col": 6,
    },
    "datetime": {
        "formType": "datetime",
        "validation": "required|date",
        "updateValidation": "required|date",
        "tableDisplay": "date",
        "testValue": '"2024-01-01 10:30:00"',
        "updateValue": '"2024-12-31 15:45:00"',
        "col": 6,
    },
    "boolean": {
        "formType": "checkbox",
        "validation": "required|boolean",
        "updateValidation": "required|boolean",
        "tableDisplay": "boolean",
        "testValue": "true",
        "updateValue": "false",
        "col": 6,
    },
    "checkbox": {
        "formType": "checkbox",
        "validation": "required",
        "updateValidation": "required",
        "tableDisplay": "boolean",
        "testValue": "true",
        "updateValue": "false",
        "col": 6,
    },
    "image": {
        "formType": "file",
        "validation": "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        "updateValidation": "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        "tableDisplay": "image",
        "testValue": "null",
        "updateValue": "null",
        "col": 6,
    },
    "file": {
        "formType": "file",
        "validation": "nullable|file|mimes:pdf,doc,docx|max:5120",
        "updateValidation": "nullable|file|mimes:pdf,doc,docx|max:5120",
        "tableDisplay": "file",
        "testValue": "null",
        "updateValue": "null",
        "col": 6,
    },
    "array": {
        "formType": "options",
        "validation": "required|array",
        "updateValidation": "required|array",
        "tableDisplay": "text",
        "testValue": "['item1', 'item2']",
        "updateValue": "['updated1', 'updated2', 'updated3']",
        "col": 6,
        "hasOptions": True,
    },
    "lazy Select": {
        "formType": "options",
        "validation": "required",
        "updateValidation": "required",
        "tableDisplay": "text",
        "testValue": '"option1"',
        "updateValue": '"option2"',
        "col": 6,
        "hasOptions": True,
    },
    "user Select": {
        "formType": "user-select",
        "validation": "required",
        "updateValidation": "required",
        "tableDisplay": "text",
        "testValue": '"option1"',
        "updateValue": '"option2"',
        "col": 6,
        "hasOptions": True,
    },
    "team Select": {
        "formType": "team-select",
        "validation": "required",
        "updateValidation": "required",
        "tableDisplay": "text",
        "testValue": '"option1"',
        "updateValue": '"option2"',
        "col": 6,
        "hasOptions": True,
    },
    "textarea": {
        "formType": "textarea",
        "validation": "required",
        "updateValidation": "required",
        "tableDisplay": "text",
        "testValue": '"Long text content"',
        "updateValue": '"Updated long text content"',
        "col": 12,
    },
    "json": {
        "formType": "textarea",
        "validation": "required|json",
        "updateValidation": "required|json",
        "tableDisplay": "text",
        "testValue": '\'{"key": "value"}\'',
        "updateValue": '\'{"key": "updated_value", "new_key": "new_value"}\'',
        "col": 12,
    },
    "text": {
        "formType": "textarea",
        "validation": "required|string",
        "updateValidation": "required|string",
        "tableDisplay": "text",
        "testValue": '"Long text content"',
        "updateValue": '"Updated long text content"',
        "col": 12,
    },
}

# ============================================================================
# RELATIONSHIP TYPES
# ============================================================================
RELATIONSHIP_TYPES = {
    "belongsTo": {
        "description": "Many-to-One (stores foreign key as string)",
        "method": "belongsTo",
        "inverse": "hasMany",
        "supported": True,
    },
    "hasOne": {
        "description": "One-to-One (referenced or embedded)",
        "method": "hasOne",
        "inverse": "belongsTo",
        "supported": True,
    },
    "hasMany": {
        "description": "One-to-Many (array of references)",
        "method": "hasMany",
        "inverse": "belongsTo",
        "supported": True,
    },
    "belongsToMany": {
        "description": "Many-to-Many (array of IDs, no pivot table)",
        "method": "belongsToMany",
        "inverse": "belongsToMany",
        "supported": True,
    },
}

# ============================================================================
# UTILITY FUNCTIONS
# ============================================================================

def display_banner():
    print("\n╔══════════════════════════════════════════════════════════════════╗")
    print("║  🚀 Full Stack Module Generator (Complete Edition)              ║")
    print("║     Backend: MongoDB + Jenssegers | Frontend: Vue 3             ║")
    print("║     With Relationships, Auto-Models, and Update Support         ║")
    print("╚══════════════════════════════════════════════════════════════════╝\n")

def display_completion_banner():
    print("\n╔══════════════════════════════════════════════════════════════════╗")
    print("║   ✅ Full Stack Module Generation Complete!                     ║")
    print("╚══════════════════════════════════════════════════════════════════╝\n")

def validate_path(input_str: str) -> Union[bool, str]:
    """Validate path input."""
    trimmed = input_str.strip()
    if not trimmed:
        return "Path cannot be empty."
    return True

def validate_model_name(input_str: str) -> Union[bool, str]:
    """Validate model name."""
    trimmed = input_str.strip()
    if not trimmed:
        return "Model name cannot be empty."
    if trimmed.isdigit():
        return "Model name cannot be a number."
    if trimmed[0].isdigit():
        return "Model name cannot start with a number."
    if not re.match(r'^[a-zA-Z][a-zA-Z0-9]*$', trimmed):
        return "Model name must contain only letters and numbers."
    return True

def validate_field_name(input_str: str) -> Union[bool, str]:
    """Validate field name."""
    trimmed = input_str.strip()
    if not trimmed:
        return "Field name cannot be empty."
    if trimmed.isdigit():
        return "Field name cannot be a number."
    if trimmed[0].isdigit():
        return "Field name cannot start with a number."
    if not re.match(r'^[a-zA-Z][a-zA-Z0-9_]*$', trimmed):
        return "Field name must contain only letters, numbers, and underscores."
    return True

async def check_backend_app_exists(backend_path: str) -> bool:
    """Check if backend app directory exists."""
    try:
        return (Path(backend_path) / "app").is_dir()
    except:
        return False

async def check_frontend_src_exists(frontend_path: str) -> bool:
    """Check if frontend src directory exists."""
    try:
        return (Path(frontend_path) / "src").is_dir()
    except:
        return False

async def check_backend_model_exists(backend_path: str, model_name: str) -> bool:
    """Check if backend model exists."""
    try:
        model_path = Path(backend_path) / "app" / "Models" / model_name / f"{model_name}.php"
        return model_path.exists()
    except:
        return False

async def file_exists(file_path: str) -> bool:
    """Check if file exists."""
    return Path(file_path).exists()

# ============================================================================
# MODEL MANIPULATION
# ============================================================================

async def get_existing_fields(backend_path: str, model_name: str) -> List[str]:
    """Get existing fields from model."""
    try:
        model_path = Path(backend_path) / "app" / "Models" / model_name / f"{model_name}.php"
        content = model_path.read_text(encoding="utf-8")
        
        # Find protected $fillable array
        pattern = r'protected\s+\$fillable\s*=\s*\[([\s\S]*?)\];'
        match = re.search(pattern, content)
        
        if match:
            fillable_content = match.group(1)
            fields = []
            for field in fillable_content.split(','):
                field_clean = field.strip().strip('\'"')
                if field_clean:
                    fields.append(field_clean)
            return fields
        return []
    except:
        return []

async def add_field_to_model(backend_path: str, model_name: str, field_name: str) -> bool:
    """Add field to existing model."""
    try:
        model_path = Path(backend_path) / "app" / "Models" / model_name / f"{model_name}.php"
        content = model_path.read_text(encoding="utf-8")
        
        pattern = r'protected\s+\$fillable\s*=\s*\[([\s\S]*?)\];'
        match = re.search(pattern, content)
        
        if not match:
            return False
        
        fillable_content = match.group(1)
        if f"'{field_name}'" in fillable_content or f'"{field_name}"' in fillable_content:
            return False
        
        trimmed_content = fillable_content.strip()
        new_field = f"'{field_name}'"
        
        if not trimmed_content:
            new_fillable_content = new_field
        else:
            new_fillable_content = trimmed_content + f",\n        {new_field}"
        
        updated_content = re.sub(
            pattern,
            f'protected $fillable = [\n        {new_fillable_content}\n    ];',
            content
        )
        
        model_path.write_text(updated_content, encoding="utf-8")
        return True
    except:
        return False

async def register_backend_route(backend_path: str, model_name: str) -> bool:
    """Register backend route."""
    try:
        indexPath = Path(backend_path) / "routes" / "API" / "index.php"
        if not await file_exists(str(indexPath)):
            return False
        
        content = indexPath.read_text(encoding="utf-8")
        route_include = f"@include('Modules/{model_name}/{model_name}Routes.php');"
        
        if route_include in content:
            return False
        
        php_tag_index = content.find("<?php")
        if php_tag_index == -1:
            return False
        
        insert_position = php_tag_index + 5
        new_content = content[:insert_position] + "\n" + route_include + content[insert_position:]
        
        indexPath.write_text(new_content, encoding="utf-8")
        return True
    except:
        return False

async def update_frontend_router_index(frontend_path: str, name: str) -> bool:
    """Update frontend router index."""
    try:
        indexPath = Path(frontend_path) / "src" / "router" / "index.ts"
        if not await file_exists(str(indexPath)):
            return False
        
        content = indexPath.read_text(encoding="utf-8")
        import_statement = f'import {name} from "@/router/modules/{name}/{name}";'
        
        if import_statement not in content:
            import_regex = r'import\s+\w+\s+from\s+["\']@\/router\/modules\/[^"\']+["\'];'
            imports = re.findall(import_regex, content)
            
            if imports:
                last_import = imports[-1]
                last_import_index = content.rfind(last_import)
                insert_position = last_import_index + len(last_import)
                content = content[:insert_position] + "\n" + import_statement + content[insert_position:]
        
        route_statement = f"      ...{name},"
        if f"...{name}" not in content:
            children_match = re.search(r'children:\s*\[', content)
            if children_match:
                children_index = children_match.end()
                content = content[:children_index] + "\n" + route_statement + content[children_index:]
        
        indexPath.write_text(content, encoding="utf-8")
        return True
    except:
        return False

# ============================================================================
# FIELD GENERATION
# ============================================================================

def generate_table_columns(fields: List[Dict], name_lower: str) -> str:
    """Generate table columns for frontend."""
    columns = []
    for field in fields:
        if field.get("showInTable", True):
            if field.get("tableDisplay") == "boolean":
                columns.append(f'      {{ id: "{field["name"]}", title: this.$t("{name_lower}.{field["name"]}"), data: "{field["name"]}", defaultContent: "N/A", render: (data) => data ? "✓" : "✗" }},')
            elif field.get("tableDisplay") == "date":
                columns.append(f'      {{ id: "{field["name"]}", title: this.$t("{name_lower}.{field["name"]}"), data: "{field["name"]}", defaultContent: "N/A", render: (data) => new Date(data).toLocaleDateString() }},')
            else:
                columns.append(f'      {{ id: "{field["name"]}", title: this.$t("{name_lower}.{field["name"]}"), data: "{field["name"]}", defaultContent: "N/A" }},')
    return "\n".join(columns)

def format_options_array(options: List[Dict]) -> str:
    """Format options array for form fields."""
    options_str = ",\n".join([
        f'''          {{
            id: "{opt["id"]}",
            name: "{opt["name"]}"
          }}''' for opt in options
    ])
    return f"[\n{options_str}\n        ]"

def generate_form_fields(fields: List[Dict], name_lower: str) -> str:
    """Generate form fields for frontend."""
    form_fields = []
    
    print(f"\n🔍🔍🔍 [DEBUG] generate_form_fields START")
    print(f"🔍 Total fields: {len(fields)}")
    print(f"🔍 name_lower: '{name_lower}'")
    
    for i, field in enumerate(fields):
        print(f"\n🔍🔍🔍 [DEBUG] Processing field {i+1}")
        print(f"🔍 Field data: {json.dumps(field, indent=2)}")
        
        field_type = field.get("type", "")
        print(f"🔍 field_type: '{field_type}'")
        
        field_info = FIELD_TYPES.get(field_type, {})
        print(f"🔍 field_info: {field_info}")
        
        has_options = field_info.get("hasOptions", False)
        print(f"🔍 has_options: {has_options}")
        
        field_obj = f'''      {{
        name: "{field["name"]}",
        label: this.$t("{name_lower}.{field["name"]}"),
        type: "{field["formType"]}",
        col: {field.get("col", 6)}'''
        
        print(f"🔍 Initial field_obj created")
        print(f"🔍 field_obj so far:\n{field_obj}")
        
        # Process rules
        rules = field.get("rules", "")
        print(f"🔍 rules from field: '{rules}'")
        if rules:
            print(f"🔍 Rules exists, processing...")
            rules_array = rules.split('|')
            print(f"🔍 rules_array: {rules_array}")
            if 'required' in rules_array:
                field_obj += ',\n        rules: "required"'
                print(f"🔍 Added 'required' rule")
            elif 'nullable' in rules_array:
                field_obj += ',\n        rules: "nullable"'
                print(f"🔍 Added 'nullable' rule")
            elif rules.strip():
                field_obj += ',\n        rules: ""'
                print(f"🔍 Added empty rules")
        
        # Handle dynamic options
        is_dynamic = field.get("isDynamic", False)
        print(f"🔍 is_dynamic from field: {is_dynamic} (type: {type(is_dynamic)})")

        module_name = ""
        if(is_dynamic):
            module_name = field.get("options", '')
            
        # module_name = field.get("moduleName", "")
        print(f"🔍 module_name from field: '{module_name}' (type: {type(module_name)})")
        print(f"🔍 module_name == 'None': {module_name == 'None'}")
        print(f"🔍 module_name == None: {module_name is None}")
        
        # ✅ FIXED: التحقق بشكل صحيح من module_name
        # يجب أن نتحقق من أن module_name ليس None ولا سلسلة نصية فارغة ولا سلسلة "None"
        module_name_valid = (
            module_name is not None and 
            module_name != "" and 
            module_name != "None" and 
            module_name != "null"
        )
        
        print(f"🔍 module_name_valid: {module_name_valid}")
        print(f"🔍 Breakdown of module_name_valid:")
        print(f"  - module_name is not None: {module_name is not None}")
        print(f"  - module_name != '': {module_name != ''}")
        print(f"  - module_name != 'None': {module_name != 'None'}")
        print(f"  - module_name != 'null': {module_name != 'null'}")
        
        if is_dynamic and module_name_valid:
            print(f"🔍✅ ENTERING DYNAMIC OPTIONS BLOCK!")
            # ✅ FIXED: استخدام module_name مباشرة (ليست سلسلة "None")
            field_obj += f',\n        options: "{module_name}"'
            print(f"🔍 Added options: '{module_name}'")
            
            option_label = field.get("optionLabel", "name")
            print(f"🔍 option_label: '{option_label}'")
            field_obj += f',\n        optionLabel: "{option_label}"'
            
            option_value = field.get("optionValue", "id")
            print(f"🔍 option_value: '{option_value}'")
            if option_value:
                field_obj += f',\n        optionValue: "{option_value}"'
            else:
                field_obj += f',\n        optionValue: "id"'
        else:
            print(f"🔍❌ NOT entering dynamic options block. Reasons:")
            print(f"  - is_dynamic: {is_dynamic}")
            print(f"  - module_name_valid: {module_name_valid}")
            print(f"  - BOTH must be True, but is_dynamic AND module_name_valid = {is_dynamic and module_name_valid}")
        
        # Handle static options
        print(f"\n🔍 Checking static options...")
        print(f"  - has_options: {has_options}")
        print(f"  - not is_dynamic: {not is_dynamic}")
        print(f"  - has_options AND not is_dynamic: {has_options and not is_dynamic}")
        
        if has_options and not is_dynamic:
            print(f"🔍✅ ENTERING STATIC OPTIONS BLOCK!")
            options = field.get("options")
            print(f"🔍 options from field: {options} (type: {type(options)})")
            print(f"🔍 options is None: {options is None}")
            print(f"🔍 options == 'None': {options == 'None'}")
            print(f"🔍 options == 'null': {options == 'null'}")
            
            # التحقق من صحة الـ options
            if options is not None and options != "None" and options != "null":
                print(f"🔍 Options are valid (not None/null)")
                
                if isinstance(options, str) and options.strip():
                    print(f"🔍 Options is string: '{options}'")
                    field_obj += f',\n        options: "{options}"'
                    field_obj += ',\n        optionLabel: "name"'
                    print(f"🔍 Added string options")
                elif isinstance(options, list) and len(options) > 0:
                    print(f"🔍 Options is list with {len(options)} items")
                    options_str = format_options_array(options)
                    print(f"🔍 Formatted options string (first 200 chars): {options_str[:200]}...")
                    field_obj += f',\n        options: {options_str}'
                    field_obj += ',\n        optionLabel: "name"'
                    print(f"🔍 Added list options")
                else:
                    print(f"🔍 Options type not handled: {type(options)}")
                    print(f"🔍 Options value: {options}")
            else:
                print(f"🔍❌ Options are invalid or empty")
                print(f"  - is None: {options is None}")
                print(f"  - is 'None': {options == 'None'}")
                print(f"  - is 'null': {options == 'null'}")
        else:
            print(f"🔍❌ NOT entering static options block")
        
        # Handle multiple select
        multiple = field.get("multiple", False)
        print(f"🔍 multiple: {multiple}")
        if multiple:
            field_obj += ',\n        multiple: true'
            print(f"🔍 Added multiple: true")
        
        # Add description
        description = field.get("description", "")
        print(f"🔍 description: '{description}'")
        if description:
            field_obj += f',\n        description: "{description}"'
            print(f"🔍 Added description")
        
        field_obj += "\n      }"
        form_fields.append(field_obj)
        
        print(f"\n🔍🔍🔍 [DEBUG] Field {i+1} COMPLETE")
        print(f"🔍 Final field object:\n{field_obj}")
    
    result = ",\n".join(form_fields)
    print(f"\n🔍🔍🔍 [DEBUG] generate_form_fields END")
    print(f"🔍 Final result length: {len(result)} characters")
    print(f"🔍 First 1000 chars of result:\n{result[:1000]}")
    
    return result
def generate_translations(name_lower: str, fields: List[Dict]) -> Dict:
    """Generate translations for frontend."""
    translations = {
        name_lower: {
            name_lower: name_lower.capitalize()
        }
    }
    
    for field in fields:
        # Convert field name to readable label
        label = field["name"]
        label = re.sub(r'_', ' ', label)
        label = re.sub(r'([A-Z])', r' \1', label)
        label = ' '.join(word.capitalize() for word in label.split())
        
        translations[name_lower][field["name"]] = label
    
    return translations

# ============================================================================
# RELATIONSHIP GENERATION
# ============================================================================

def generate_belongs_to_many_repository_methods(relationships: List[Dict], model_name: str) -> str:
    """Generate create/update methods for belongsToMany relationships."""
    belongs_to_many_rels = [
        rel for rel in relationships 
        if rel.get("type") == "belongsToMany" and rel.get("localKey", {}).get("addSyncMethods")
    ]
    
    if not belongs_to_many_rels:
        return ""
    
    model_var = model_name.lower()
    methods = "\n"
    
    methods += f'''    /**
     * Create a new {model_name} with belongsToMany relationships
     * 
     * @param array|null $data
     * @return {model_name}
     */
    public function create($data = null)
    {{
        ${model_var} = parent::create($data);
'''
    
    for rel in belongs_to_many_rels:
        methods += f'''        if (isset($data['{rel["foreignKey"]}'])) {{
            ${model_var}->{rel["methodName"]}()->attach($data['{rel["foreignKey"]}'] ?? []);
        }}
'''
    
    methods += f'''        return ${model_var};
    }}
'''
    
    methods += f'''    /**
     * Update {model_name} with belongsToMany relationships
     * 
     * @param mixed $id
     * @param array|null $data
     * @return {model_name}
     */
    public function update($id, $data = null)
    {{
        ${model_var} = parent::update($id, $data);
'''
    
    for rel in belongs_to_many_rels:
        methods += f'''        if (isset($data['{rel["foreignKey"]}'])) {{
            ${model_var}->{rel["methodName"]}()->detach();
            ${model_var}->{rel["methodName"]}()->attach($data['{rel["foreignKey"]}']);
        }}
'''
    
    methods += f'''        return ${model_var};
    }}'''
    
    return methods

def generate_single_relationship(rel: Dict) -> str:
    """Generate single relationship method code."""
    rel_info = RELATIONSHIP_TYPES.get(rel["type"], {})
    
    code = f'''
    /**
     * {rel["type"]} relationship with {rel["relatedModel"]}'''
    
    if rel["type"] == "belongsToMany":
        code += f'''
     * MongoDB: '{rel["foreignKey"]}' in THIS model, '{rel.get("localKey", {}).get("inverseField", rel["relatedModel"].lower() + "_ids")}' in {rel["relatedModel"]}'''
    
    code += f'''
     */
    public function {rel["methodName"]}()
    {{'''
    
    if rel["type"] in ["hasOne", "hasMany"]:
        code += f'''
        return $this->{rel_info.get("method", rel["type"])}({rel["relatedModel"]}::class);
    }}'''
    elif rel["type"] == "belongsTo":
        code += f'''
        return $this->{rel_info.get("method", "belongsTo")}({rel["relatedModel"]}::class, '{rel["foreignKey"]}');
    }}'''
    elif rel["type"] == "belongsToMany":
        related_model_field = rel.get("localKey", {}).get("inverseField") or f'{rel["relatedModel"].lower()}_ids'
        code += f'''
        // MongoDB: '{rel["foreignKey"]}' in THIS model, '{related_model_field}' in {rel["relatedModel"]}
        return $this->{rel_info.get("method", "belongsToMany")}({rel["relatedModel"]}::class, null, '{related_model_field}', '{rel["foreignKey"]}');
    }}'''
    else:
        code += f'''
        return $this->{rel_info.get("method", rel["type"])}({rel["relatedModel"]}::class, '{rel["foreignKey"]}');
    }}'''
    
    code += "\n"
    return code

def generate_use_statements(relationships: List[Dict]) -> str:
    """Generate use statements for PHP model."""
    if not relationships:
        return ""
    
    related_models = sorted(set(rel["relatedModel"] for rel in relationships))
    use_statements = "\n".join(f"use App\\Models\\{model}\\{model};" for model in related_models)
    
    return "\n" + use_statements if use_statements else ""

def generate_relationship_methods(relationships: List[Dict]) -> str:
    """Generate all relationship methods for PHP model."""
    if not relationships:
        return ""
    
    methods = "\n    /**\n     * RELATIONSHIPS\n     */\n"
    
    for rel in relationships:
        methods += generate_single_relationship(rel)
    
    return methods

# ============================================================================
# FILE CREATION
# ============================================================================

async def create_backend_file(
    file_type: str,
    backend_path: str,
    template_file: str,
    name: str,
    name_kebab: str,
    name_fillable: List[str],
    validations: List[Dict] = None,
    relationships: List[Dict] = None
) -> None:
    """Create backend file from template."""
    try:
        backend_path_obj = Path(backend_path)
        
        # Determine directory based on file type
        if file_type == "Model":
            new_path = backend_path_obj / "app" / "Models" / name
        elif file_type == "Repository":
            new_path = backend_path_obj / "app" / "Repositories" / "Eloquent" / name
        elif file_type == "Service":
            new_path = backend_path_obj / "app" / "Services" / "Eloquent" / name
        elif file_type == "ControllerTest":
            new_path = backend_path_obj / "tests" / "Unit" / name
        elif file_type == "Routes":
            new_path = backend_path_obj / "routes" / "API" / "Modules" / name
        else:
            new_path = backend_path_obj / "app" / "Http" / "Controllers" / name
        
        new_path.mkdir(parents=True, exist_ok=True)
        
        # Determine file name
        if file_type == "Model":
            file_name = f"{name}.php"
        elif file_type == "ControllerTest":
            file_name = f"{name}ControllerTest.php"
        elif file_type == "Routes":
            file_name = f"{name}Routes.php"
        else:
            file_name = f"{name}{file_type}.php"
        
        file_path = new_path / file_name
        
        # Read template
        template_path = backend_path_obj / "generator-setting-backend" / template_file
        if not template_path.exists():
            raise FileNotFoundError(f"Template not found: {template_path}")
        
        data = template_path.read_text(encoding="utf-8")
        
        # Format fillable array
        fillable_formatted = ", ".join(f"'{field}'" for field in name_fillable) if name_fillable else ""
        
        # Replace placeholders
        text = data.replace("@@Name@@", name) \
                   .replace("@@Namekebab@@", name_kebab) \
                   .replace("@@NameFillable@@", fillable_formatted)
        
        # Model-specific replacements
        if file_type == "Model":
            use_statements = generate_use_statements(relationships or [])
            relationship_methods = generate_relationship_methods(relationships or [])
            text = text.replace("@@UseStatements@@", use_statements) \
                       .replace("@@Relationships@@", relationship_methods)
        
        # Repository-specific replacements
        if file_type == "Repository" and relationships:
            belongs_to_many_methods = generate_belongs_to_many_repository_methods(relationships, name)
            if belongs_to_many_methods:
                last_brace_index = text.rfind("}")
                if last_brace_index != -1:
                    text = text[:last_brace_index] + belongs_to_many_methods + "\n" + text[last_brace_index:]
        
        # Service-specific replacements
        if file_type == "Service" and validations:
            store_rules = "\n".join([
                f"                '{v['name']}' => '{v.get('rule', '').replace('@@table@@', name_kebab + 's')}',"
                for v in validations
            ])
            
            update_rules = "\n".join([
                f"                '{v['name']}' => '{v.get('updateRule', '').replace('@@table@@', name_kebab + 's')}',"
                for v in validations
            ])
            
            text = text.replace("@@StoreValidation@@", store_rules) \
                       .replace("@@UpdateValidation@@", update_rules)
        
        # ControllerTest-specific replacements
        if file_type == "ControllerTest" and validations:
            test_data_str = "\n".join([f"            '{v['name']}' => {v.get('testValue', 'null')}," for v in validations])
            update_data_str = "\n".join([f"            '{v['name']}' => {v.get('updateValue', 'null')}," for v in validations])
            fields_list = ", ".join([f"'{v['name']}'" for v in validations])
            
            text = text.replace("@@TestData@@", test_data_str) \
                       .replace("@@UpdateTestData@@", update_data_str) \
                       .replace("@@FieldsList@@", fields_list)
        
        # Write file
        file_path.write_text(text, encoding="utf-8")
        
    except Exception as err:
        print(f"❌ Error creating {file_type}: {err}")
        raise

async def create_frontend_file(
    file_type: str,
    frontend_path: str,
    template_file: str,
    name: str,
    name_kebab: str,
    name_lower: str,
    fields: List[Dict] = None,
    form_type: str = "page"
) -> None:
    """Create frontend file from template."""
    try:
        frontend_path_obj = Path(frontend_path)
        
        # Determine base path
        if file_type == "API":
            base_path = frontend_path_obj / "src" / "API"
        elif file_type == "Router":
            base_path = frontend_path_obj / "src" / "router" / "modules"
        else:  # Page
            base_path = frontend_path_obj / "src" / "views" / "Page"
        
        new_path = base_path / name
        new_path.mkdir(parents=True, exist_ok=True)
        
        # Determine file extension
        ext = ".vue" if file_type == "Page" else ".ts"
        file_path = new_path / f"{name}{ext}"
        
        # Choose the correct template file for Page type
        if file_type == "Page":
            if form_type == "page-form-dialog":
                template_file = "page-dialog.txt"
            else:
                template_file = "page.txt"
        
        print(f"\n🔍 [DEBUG] Template Details:")
        print(f"   File Type: {file_type}")
        print(f"   Template File: {template_file}")
        print(f"   Form Type: {form_type}")
        
        # Read template
        template_path = frontend_path_obj / "generator-setting-frontend" / template_file
        
        if not template_path.exists():
            print(f"❌ ERROR: Template not found at {template_path}")
            raise FileNotFoundError(f"Template not found: {template_path}")
        
        template = template_path.read_text(encoding='utf-8')
        
        # Replace placeholders
        template = template.replace("@@Name@@", name) \
                           .replace("@@Namekebab@@", name_kebab) \
                           .replace("@@name@@", name_lower)
        
        # Page-specific replacements
        if file_type == "Page" and fields:
            table_columns = generate_table_columns(fields, name_lower)
            form_fields = generate_form_fields(fields, name_lower)
            
            template = template.replace("@@TableColumns@@", table_columns) \
                               .replace("@@FormFields@@", form_fields)
        
        # Write file
        file_path.write_text(template, encoding="utf-8")
        print(f"✅ File written to: {file_path}")
        
        form_type_display = "Modal Dialog" if form_type == "page-form-dialog" else "Regular Form"
        print(f"   ✓ {file_type} created ({form_type_display})")
        
    except Exception as err:
        print(f"❌ Error in create_frontend_file: {err}")
        raise
# ============================================================================
# HELPER FUNCTIONS - RELATIONSHIP MANAGEMENT
# ============================================================================

async def add_use_statement_to_model(backend_path: str, model_name: str, related_model_name: str) -> bool:
    """Add use statement to model namespace."""
    model_path = Path(backend_path) / "app" / "Models" / model_name / f"{model_name}.php"
    
    try:
        content = model_path.read_text(encoding="utf-8")
        use_statement = f"use App\\Models\\{related_model_name}\\{related_model_name};"
        
        if use_statement in content:
            return True
        
        # Find namespace
        namespace_match = re.search(r'namespace\s+([^;]+);', content)
        if not namespace_match:
            print(f"   ⚠️  Could not find namespace in {model_name}")
            return False
        
        namespace_end_index = content.find(";", namespace_match.start()) + 1
        class_match = re.search(r'class\s+\w+', content)
        if not class_match:
            print(f"   ⚠️  Could not find class in {model_name}")
            return False
        
        class_start_index = class_match.start()
        between_content = content[namespace_end_index:class_start_index]
        import_use_statements = re.findall(r'use\s+[^;]*\\[^;]+;', between_content)
        
        if import_use_statements:
            last_use = import_use_statements[-1]
            last_use_index = content.rfind(last_use)
            insert_position = content.find(";", last_use_index) + 1
        else:
            insert_position = namespace_end_index
        
        new_content = content[:insert_position] + "\n" + use_statement + content[insert_position:]
        model_path.write_text(new_content, encoding="utf-8")
        
        print(f"   ✅ Added namespace: use App\\Models\\{related_model_name}\\{related_model_name}")
        return True
        
    except Exception as error:
        print(f"   ❌ Error adding use statement: {error}")
        return False

async def add_relationship_to_model(
    backend_path: str,
    model_name: str,
    relationship_method: Dict,
    related_model_name: str = None
) -> bool:
    """Add relationship to existing model."""
    model_path = Path(backend_path) / "app" / "Models" / model_name / f"{model_name}.php"
    
    try:
        content = model_path.read_text(encoding="utf-8")
        
        # Add use statement if needed
        if related_model_name:
            await add_use_statement_to_model(backend_path, model_name, related_model_name)
            content = model_path.read_text(encoding="utf-8")
        
        # Check if relationship already exists
        if f"function {relationship_method['name']}()" in content:
            print(f"   ⚠️  Relationship {relationship_method['name']}() already exists in {model_name}")
            return False
        
        # Find last closing brace
        last_brace_index = content.rfind("}")
        if last_brace_index == -1:
            print(f"   ❌ Could not find closing brace in {model_name}")
            return False
        
        # Check if there's already a relationships section
        has_relationships_section = "RELATIONSHIPS" in content
        
        relationship_code = ""
        if not has_relationships_section:
            relationship_code = "\n    /**\n     * ======================\n     * RELATIONSHIPS\n     * ======================\n     */\n"
        
        relationship_code += relationship_method["code"]
        
        # Insert relationship code before last closing brace
        new_content = content[:last_brace_index] + relationship_code + "\n" + content[last_brace_index:]
        model_path.write_text(new_content, encoding="utf-8")
        
        print(f"   ✅ Added relationship {relationship_method['name']}() to {model_name}")
        return True
        
    except Exception as error:
        print(f"   ❌ ERROR in add_relationship_to_model: {error}")
        raise

# ============================================================================
# DELETE MODULE FUNCTIONALITY
# ============================================================================

async def remove_line_from_file(file_path: str, search_pattern: str) -> bool:
    """Remove line or block from file."""
    try:
        content = Path(file_path).read_text(encoding="utf-8")
        lines = content.split("\n")
        filtered = [line for line in lines if search_pattern not in line]
        
        if len(filtered) < len(lines):
            Path(file_path).write_text("\n".join(filtered), encoding="utf-8")
            return True
        return False
    except:
        return False

async def remove_import_statement(file_path: str, module_name: str) -> bool:
    """Remove import/use statement from file."""
    try:
        content = Path(file_path).read_text(encoding="utf-8")
        
        # Remove Vue/JS imports
        content = re.sub(r'import\s+.*?from\s+[\'"]/.*/' + re.escape(module_name.lower()) + r'/.*?[\'"]\n?', '', content)
        content = re.sub(r'import\s+.*?from\s+[\'"]\./.*' + re.escape(module_name) + r'.*?[\'"]\n?', '', content)
        
        # Remove PHP use statements
        content = re.sub(r'use\s+App\\\w+\\' + re.escape(module_name) + r'\\' + re.escape(module_name) + r'.*?;\n?', '', content)
        
        Path(file_path).write_text(content, encoding="utf-8")
        return True
    except:
        return False



async def remove_from_router_index(frontend_path: str, module_name: str) -> bool:
    """Remove router entry for module - Case-insensitive and kebab-case aware."""
    router_index_path = Path(frontend_path) / "src" / "router" / "index.ts"

    print(f"   ℹ️  Removing '{module_name}' from router index...")

    if not router_index_path.exists():
        print(f"   ❌ File not found")
        return False

    try:
        content = router_index_path.read_text(encoding="utf-8")
        lines = content.splitlines()
        new_lines = []
        removed = False

        # helper kebab-case
        name_kebab = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', module_name).lower()

        # patterns (case-insensitive)
        import_path_re = re.compile(
            rf'["\'][^"\']*(?:/router/modules/)?(?:{re.escape(module_name)}|{re.escape(name_kebab)})[^"\']*["\']',
            re.IGNORECASE
        )
        identifier_re = re.compile(rf'\b{re.escape(module_name)}\b', re.IGNORECASE)
        spread_re = re.compile(rf'\.\.\.{re.escape(module_name)}(,|$)', re.IGNORECASE)

        for line in lines:
            line_lower = line.lower()

            # remove import lines that reference the module (path or identifier)
            if 'import' in line_lower and 'from' in line_lower:
                if import_path_re.search(line) or identifier_re.search(line.split('from')[0]):
                    print(f"   ✂️  Removing import: {line.strip()}")
                    removed = True
                    continue

            # remove spread entries (...ModuleName,)
            if spread_re.search(line):
                # entire-line spread
                if re.match(rf'^\s*\.\.\.{re.escape(module_name)},?\s*$', line, re.IGNORECASE):
                    print(f"   ✂️  Removing spread: {line.strip()}")
                    removed = True
                    continue

                # inline spread — remove token and fix commas
                new_line = re.sub(rf'\s*\.\.\.{re.escape(module_name)}\s*,?\s*', '', line, flags=re.IGNORECASE)
                new_line = re.sub(r'^\s*,\s*', '', new_line)
                new_line = re.sub(r',\s*$', '', new_line)
                if new_line != line:
                    print(f"   ✂️  Modified: {line.strip()} → {new_line.strip() if new_line.strip() else '(empty)'}")
                    removed = True
                    line = new_line

            # keep non-empty lines
            if line.strip():
                new_lines.append(line)

        if not removed:
            print(f"   ℹ️  Module '{module_name}' not found")
            return True

        new_content = '\n'.join(new_lines)
        new_content = re.sub(r'\n\n\n+', '\n\n', new_content)
        new_content = re.sub(r'\n\s*\n\s*\n+', '\n\n', new_content)
        new_content = '\n'.join([ln.rstrip() for ln in new_content.splitlines()])
        if new_content and not new_content.endswith('\n'):
            new_content += '\n'

        router_index_path.write_text(new_content, encoding="utf-8")
        print(f"   ✅ Successfully removed '{module_name}'")

        final_content = router_index_path.read_text(encoding="utf-8")
        if re.search(rf'\b{re.escape(module_name)}\b', final_content, re.IGNORECASE):
            for line in final_content.splitlines():
                if re.search(rf'\b{re.escape(module_name)}\b', line, re.IGNORECASE):
                    print(f"   ⚠️  Found potential remaining: {line.strip()}")
            return False

        print(f"   ✓ Clean removal confirmed")
        return True

    except Exception as error:
        print(f"   ❌ Error: {error}")
        return False
async def delete_directory(dir_path: str) -> bool:
    """Delete directory recursively."""
    try:
        dir_path_obj = Path(dir_path)
        if not dir_path_obj.exists():
            return True
        
        # Remove files
        for item in dir_path_obj.iterdir():
            if item.is_file():
                item.unlink()
            elif item.is_dir():
                await delete_directory(str(item))
        
        # Remove directory
        dir_path_obj.rmdir()
        return True
    except Exception as error:
        print(f"   ⚠️  Could not delete directory: {error}")
        return False

async def delete_module(
    backend_path: str,
    frontend_path: str,
    module_name: str,
    delete_backend: bool = True,
    delete_frontend: bool = True
) -> Dict:
    """Delete entire module."""
    results = {
        "deletedBackend": [],
        "deletedFrontend": [],
        "errors": []
    }
    
    try:
        print(f"\n🗑️  Deleting module: {module_name}")
        print("─────────────────────────────────────────────────────────")
        
        if delete_backend:
            # Delete backend directories
            backend_dirs = [
                ("app/Models", "backend model"),
                ("app/Repositories/Eloquent", "backend repository"),
                ("app/Services/Eloquent", "backend service"),
                ("app/Http/Controllers", "backend controller"),
                ("routes/API/Modules", "backend routes"),
                ("tests/Unit", "backend test")
            ]
            
            for dir_suffix, description in backend_dirs:
                dir_path = Path(backend_path) / dir_suffix / module_name
                if await delete_directory(str(dir_path)):
                    print(f"   ✓ Deleted {description}: {dir_suffix}/{module_name}")
                    results["deletedBackend"].append(f"{dir_suffix}/{module_name}")
            
            # Remove from routes file
            api_routes_paths = [
                Path(backend_path) / "routes" / "API" / "index.php",
                Path(backend_path) / "routes" / "api.php"
            ]
            
            for api_routes_path in api_routes_paths:
                if await file_exists(str(api_routes_path)):
                    try:
                        route_content = api_routes_path.read_text(encoding="utf-8")
                        name_kebab = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', module_name).lower()
                        
                        # Remove @include line
                        lines = route_content.split("\n")
                        filtered_lines = [
                            line for line in lines 
                            if not re.search(
                                r'@include\s*\(\s*[\'"]Modules/' + re.escape(module_name) + r'/' + re.escape(module_name) + r'Routes\.php[\'"]\s*\)',
                                line,
                                re.IGNORECASE
                            )
                        ]
                        route_content = "\n".join(filtered_lines)
                        
                        # Remove Route::apiResource registration
                        route_content = re.sub(
                            r'Route::apiResource\\([\'"]' + re.escape(name_kebab) + r'[\'"],\\s*' + re.escape(module_name) + r'Controller::class\\);?\\n?',
                            '',
                            route_content,
                            flags=re.IGNORECASE
                        )
                        
                        # Remove use statements
                        route_content = re.sub(
                            r'use\\s+App\\\\Http\\\\Controllers\\\\' + re.escape(module_name) + r'\\\\' + re.escape(module_name) + r'Controller;\\n?',
                            '',
                            route_content,
                            flags=re.IGNORECASE
                        )
                        
                        # Clean up extra blank lines
                        route_content = re.sub(r'\n\n\n+', '\n\n', route_content)
                        
                        api_routes_path.write_text(route_content, encoding="utf-8")
                        print("   ✓ Removed @include from routes file")
                        break
                    except Exception as err:
                        print(f"   ⚠️  Could not remove from routes: {err}")
        
        if delete_frontend:
            frontend_folders = [
                (f"src/API/{module_name}", "frontend API folder"),
                (f"src/views/Page/{module_name}", "frontend page folder"),
                (f"src/router/modules/{module_name}", "frontend router module folder")
            ]
            
            results = {
                "deletedFrontend": [],
                "errors": []
            }
            
            for folder_path_str, description in frontend_folders:
                folder_path = Path(frontend_path) / folder_path_str
                try:
                    if folder_path.exists() and folder_path.is_dir():
                        # حذف المجلد وكل محتوياته
                        shutil.rmtree(folder_path)
                        print(f"   ✓ Deleted {description}: {folder_path_str}")
                        results["deletedFrontend"].append(folder_path_str)
                    else:
                        print(f"   ℹ️  {description} folder not found: {folder_path_str}")
                except Exception as e:
                    error_msg = f"Failed to delete {description} folder: {str(e)}"
                    print(f"   ❌ {error_msg}")
                    results["errors"].append(error_msg)
            
            # Remove from router index
            await remove_from_router_index(frontend_path, module_name)
            print("   ✓ Removed from router/index.ts")
        
        print("\n✅ Module deletion complete!\n")
        return results
        
    except Exception as error:
        print(f"\n❌ Error deleting module: {error}")
        raise

# ============================================================================
# BATCH MODE - JSON FILE PROCESSING
# ============================================================================

def validate_batch_config(config: Dict) -> Dict:
    """Validate JSON batch configuration."""
    errors = []
    
    # Validate each module
    if "modules" in config and isinstance(config["modules"], list):
        for i, module in enumerate(config["modules"]):
            module_prefix = f"Module[{i}] ({module.get('name', 'unnamed')})"
            
            if "name" not in module:
                errors.append(f"{module_prefix}: Missing required field: name")
            else:
                name_validation = validate_model_name(module["name"])
                if name_validation != True:
                    errors.append(f"{module_prefix}: {name_validation}")
            
            # Validate fields
            if "fields" not in module or not isinstance(module["fields"], list):
                errors.append(f"{module_prefix}: fields must be an array")
            else:
                for j, field in enumerate(module["fields"]):
                    field_prefix = f"{module_prefix} Field[{j}]"
                    
                    if "name" not in field:
                        errors.append(f"{field_prefix}: Missing required field: name")
                    else:
                        field_name_validation = validate_field_name(field["name"])
                        if field_name_validation != True:
                            errors.append(f"{field_prefix}: {field_name_validation}")
                    
                    if "type" not in field:
                        errors.append(f"{field_prefix}: Missing required field: type")
                    elif field["type"] not in FIELD_TYPES:
                        errors.append(
                            f"{field_prefix}: Invalid type '{field['type']}'. Allowed: {', '.join(FIELD_TYPES.keys())}"
                        )
                    
                    if "showInTable" in field and not isinstance(field["showInTable"], bool):
                        errors.append(f"{field_prefix}: showInTable must be boolean")
            
            # Validate relationships
            if "relationships" in module and not isinstance(module["relationships"], list):
                errors.append(f"{module_prefix}: relationships must be an array")
            elif "relationships" in module:
                for j, rel in enumerate(module["relationships"]):
                    rel_prefix = f"{module_prefix} Relationship[{j}]"
                    
                    if "type" not in rel:
                        errors.append(f"{rel_prefix}: Missing required field: type")
                    elif rel["type"] not in RELATIONSHIP_TYPES:
                        errors.append(
                            f"{rel_prefix}: Invalid type '{rel['type']}'. Allowed: {', '.join(RELATIONSHIP_TYPES.keys())}"
                        )
                    
                    if "relatedModel" not in rel:
                        errors.append(f"{rel_prefix}: Missing required field: relatedModel")
                    else:
                        model_validation = validate_model_name(rel["relatedModel"])
                        if model_validation != True:
                            errors.append(f"{rel_prefix}: relatedModel: {model_validation}")
                    
                    if "methodName" not in rel:
                        errors.append(f"{rel_prefix}: Missing required field: methodName")
                    else:
                        method_validation = validate_field_name(rel["methodName"])
                        if method_validation != True:
                            errors.append(f"{rel_prefix}: methodName: {method_validation}")
                    
                    if "foreignKey" in rel and not isinstance(rel["foreignKey"], str):
                        errors.append(f"{rel_prefix}: foreignKey must be string")
    
    return {"isValid": len(errors) == 0, "errors": errors}

def convert_batch_fields(fields: List[Dict], name: str) -> Dict:
    """Convert field configurations from batch format."""
    backend_fillable = []
    backend_validations = []
    frontend_fields = []
    
    for field in fields:
        field_info = FIELD_TYPES[field["type"]]
        
        backend_fillable.append(field["name"])
        backend_validations.append({
            "name": field["name"],
            "type": field["type"],
            "rule": field_info["validation"],
            "updateRule": field_info["updateValidation"],
            "testValue": field_info["testValue"],
            "updateValue": field_info["updateValue"]
        })
        
        frontend_field = {
            "name": field["name"],
            "type": field["type"],
            "formType": field_info["formType"],
            "rules": field_info["validation"],
            "description": field.get("description", ""),
            "showInTable": field.get("showInTable", True),
            "col": field_info["col"],
        }
        
        if "options" in field:
            frontend_field["options"] = field["options"]
        
        if field.get("isDynamic", False):
            frontend_field.update({
                "isDynamic": True,
                "moduleName": field.get("module"),
                "optionLabel": field.get("optionLabel", "name"),
                "optionValue": field.get("optionValue")
            })
        
        if field.get("multiple", False):
            frontend_field["multiple"] = True
        
        frontend_fields.append(frontend_field)
    
    return {
        "backendFillable": backend_fillable,
        "backendValidations": backend_validations,
        "frontendFields": frontend_fields
    }

def get_default_inverse_method(rel_type: str, model_name: str) -> str:
    """Get default inverse method name based on relationship type."""
    lower_name = model_name.lower()
    if rel_type in ["hasMany", "belongsToMany"]:
        return lower_name + "s"
    return lower_name

def get_form_type_choice() -> str:
    """Get form type choice from user."""
    questions = [
        inquirer.List(
            "form_type",
            message="Frontend Form Type:",
            choices=[
                ("Regular Form (uses isFlipped)", "page"),
                ("Modal Dialog (v-dialog)", "page-form-dialog")
            ],
            default="page"
        )
    ]
    
    answers = inquirer.prompt(questions)
    return answers["form_type"]
async def process_batch_module(
    module_config: Dict,
    backend_path: str,
    frontend_path: str,
    gen_backend: bool,
    gen_frontend: bool,
    form_type: str = "page"
) -> Dict:
    """Process a single module from batch config."""
    result = {
        "name": module_config["name"],
        "success": False,
        "errors": [],
        "warnings": []
    }
    
    try:
        name = module_config["name"]
        name_kebab = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', name).lower()
        name_lower = name.lower()
        
        print(f"\n🔍 [DEBUG] Processing Module: {name}")
        print(f"   Form Type from config: {module_config.get('formType', 'Not specified')}")
        print(f"   Form Type passed: {form_type}")
        print(f"   Generate Frontend: {gen_frontend}")
        
        # Override with module-specific form type if provided
        module_form_type = module_config.get("formType", form_type)
        print(f"   Using Form Type: {module_form_type}")
        
        # Convert fields
        field_conversion = convert_batch_fields(module_config.get("fields", []), name)
        backend_fillable = field_conversion["backendFillable"]
        backend_validations = field_conversion["backendValidations"]
        frontend_fields = field_conversion["frontendFields"]
        
        # Process relationships
        relationships = []
        module_relationships = module_config.get("relationships", [])
        
        for rel in module_relationships:
            relationship = {
                "methodName": rel["methodName"],
                "type": rel["type"],
                "relatedModel": rel["relatedModel"],
                "foreignKey": rel.get("foreignKey", f"{rel['relatedModel'].lower()}_id"),
                "localKey": rel.get("localKey"),
                "inverse": True,
                "inverseMethod": rel.get("inverseMethod", get_default_inverse_method(rel["type"], name)),
                "inverseType": rel.get("inverseType", RELATIONSHIP_TYPES[rel["type"]]["inverse"])
            }
            
            # Add foreign key fields for belongsTo and belongsToMany
            if rel["type"] == "belongsTo":
                if relationship["foreignKey"] not in backend_fillable:
                    backend_fillable.append(relationship["foreignKey"])
                    backend_validations.append({
                        "name": relationship["foreignKey"],
                        "type": "string",
                        "rule": "required|string",
                        "updateRule": "required|string",
                        "testValue": '"507f1f77bcf86cd799439011"',
                        "updateValue": '"507f1f77bcf86cd799439012"'
                    })
            elif rel["type"] == "belongsToMany":
                array_field_name = relationship["foreignKey"]
                if array_field_name not in backend_fillable:
                    backend_fillable.append(array_field_name)
                    backend_validations.append({
                        "name": array_field_name,
                        "type": "array",
                        "rule": "nullable|array",
                        "updateRule": "nullable|array",
                        "testValue": "['507f1f77bcf86cd799439011', '507f1f77bcf86cd799439012']",
                        "updateValue": "['507f1f77bcf86cd799439013', '507f1f77bcf86cd799439014']"
                    })
                
                inverse_field_name = relationship.get("localKey", {}).get("inverseField") or f"{name.lower()}_ids"
                relationship["foreignKey"] = array_field_name
                relationship["localKey"] = {
                    "inverseField": inverse_field_name,
                    "addSyncMethods": True
                }
            
            relationships.append(relationship)
        
        # Generate backend
        if gen_backend:
            try:
                model_exists = await check_backend_model_exists(backend_path, name)
                
                if model_exists:
                    print(f"   ℹ️  Model {name} already exists, updating...")
                    
                    # Add new fields
                    existing_fields = await get_existing_fields(backend_path, name)
                    for field in backend_fillable:
                        if field not in existing_fields:
                            await add_field_to_model(backend_path, name, field)
                            print(f"   ✅ Added field '{field}' to {name}")
                    
                    # Add relationships
                    for rel in relationships:
                        try:
                            await add_relationship_to_model(
                                backend_path,
                                name,
                                {
                                    "name": rel["methodName"],
                                    "code": generate_single_relationship(rel)
                                },
                                rel["relatedModel"]
                            )
                        except Exception as err:
                            print(f"   ⚠️  {err}")
                else:
                    # Create new backend files
                    backend_files = [
                        ("Model", "model.txt"),
                        ("Repository", "repositories.txt"),
                        ("Service", "services.txt"),
                        ("Controller", "controller.txt"),
                        ("Routes", "route.txt"),
                        ("ControllerTest", "test.txt")
                    ]
                    
                    for file_type, template in backend_files:
                        await create_backend_file(
                            file_type,
                            backend_path,
                            template,
                            name,
                            name_kebab,
                            backend_fillable,
                            backend_validations if file_type in ["Service", "ControllerTest"] else [],
                            relationships if file_type in ["Model", "Repository"] else []
                        )
                        print(f"   ✓ {file_type} created")
                    
                    await register_backend_route(backend_path, name)
                    print("   ✓ Route registered")
                
                result["backend"] = {"model": "✓"}
                
                # Handle inverse relationships
                for rel in relationships:
                    if rel.get("inverse") and rel.get("inverseMethod"):
                        try:
                            related_model_exists = await check_backend_model_exists(
                                backend_path,
                                rel["relatedModel"]
                            )
                            
                            if not related_model_exists:
                                print(f"   ⚠️  Related model {rel['relatedModel']} not found, skipping inverse relationship")
                                continue
                            
                            # Determine inverse relationship
                            inverse_type = rel.get("inverseType", RELATIONSHIP_TYPES[rel["type"]]["inverse"])
                            
                            inverse_rel = {
                                "methodName": rel["inverseMethod"],
                                "type": inverse_type,
                                "relatedModel": name,
                                "foreignKey": rel["localKey"]["inverseField"] if rel["type"] == "belongsToMany" else rel["foreignKey"],
                                "localKey": {"inverseField": rel["foreignKey"]} if rel["type"] == "belongsToMany" else None
                            }
                            
                            inverse_code = generate_single_relationship(inverse_rel)
                            
                            # Add inverse relationship
                            await add_relationship_to_model(
                                backend_path,
                                rel["relatedModel"],
                                {
                                    "name": rel["inverseMethod"],
                                    "code": inverse_code
                                },
                                name
                            )
                            
                            # Add field for belongsToMany
                            if rel["type"] == "belongsToMany" and rel.get("localKey", {}).get("inverseField"):
                                inverse_field_name = rel["localKey"]["inverseField"]
                                related_fields = await get_existing_fields(backend_path, rel["relatedModel"])
                                
                                if inverse_field_name not in related_fields:
                                    await add_field_to_model(backend_path, rel["relatedModel"], inverse_field_name)
                                    print(f"   ✅ Added field '{inverse_field_name}' to {rel['relatedModel']}")
                            
                        except Exception as err:
                            result["warnings"].append(f"Inverse relationship {rel.get('inverseMethod')}: {err}")
                            print(f"   ⚠️  {err}")
                
            except Exception as err:
                result["errors"].append(f"Backend generation: {err}")
        
        # Generate frontend
        if gen_frontend:
            try:
                frontend_files = [
                    ("API", "api.txt"),
                    ("Page", "page.txt"),
                    ("Router", "router.txt")
                ]
                
                for file_type, template in frontend_files:
                    await create_frontend_file(
                        file_type,
                        frontend_path,
                        template,
                        name,
                        name_kebab,
                        name_lower,
                        frontend_fields if file_type == "Page" else None,
                        module_form_type  
                    )
                    print(f"   ✓ {file_type} created")
                
                await update_frontend_router_index(frontend_path, name)
                print("   ✓ Router index updated")
                
                result["frontend"] = {"api": "✓", "page": "✓", "router": "✓", "routerIndex": "✓"}
                
            except Exception as err:
                result["errors"].append(f"Frontend generation: {err}")
        
        result["success"] = len(result["errors"]) == 0
        return result
        
    except Exception as error:
        result["errors"].append(str(error))
        return result

# ============================================================================
# AI & CLICKUP MODE
# ============================================================================

async def ai_clickup_mode_wizard():
    """AI & ClickUp Mode Wizard."""
    print("\n🤖 AI & ClickUp Mode")
    print("─────────────────────────────────────────────────────────")
    print("This mode fetches task details from ClickUp and uses AI")
    print("to generate module configuration automatically.")
    print("─────────────────────────────────────────────────────────")
    
    try:
        # Step 1: Get paths
        print("\n📝 Step 1: Path Configuration")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "backend_path",
                message="Backend path (e.g., ../Backend):",
                default="../Backend",
                validate=lambda _, x: validate_path(x)
            ),
            inquirer.Text(
                "frontend_path",
                message="Frontend path (e.g., ../Frontend):",
                default="../Frontend",
                validate=lambda _, x: validate_path(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        backend_path = answers["backend_path"]
        frontend_path = answers["frontend_path"]
        
        # Step 2: Get ClickUp Task ID
        print("\n📝 Step 2: ClickUp Task ID")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "task_id",
                message="ClickUp Task ID:",
                validate=lambda _, x: bool(x.strip()) or "Task ID cannot be empty"
            )
        ]
        
        answers = inquirer.prompt(questions)
        task_id = answers["task_id"]
        
        print("\n⏳ Fetching task from ClickUp...")
        
        # Fetch task from ClickUp
        try:
            task_data = await get_task(task_id)
            print(f"✅ Task fetched: {task_data}")
        except Exception as error:
            print(f"\n❌ {error}")
            questions = [
                inquirer.Confirm(
                    "retry",
                    message="Would you like to try again?",
                    default=True
                )
            ]
            
            answers = inquirer.prompt(questions)
            if answers["retry"]:
                return await ai_clickup_mode_wizard()
            else:
                await main_menu()
                return
        
        # Display task info
        print("\n📋 Task Information:")
        print(f"   Name: {task_data}")
        print(f"   Description: {task_data[:100] if task_data else 'N/A'}{'...' if task_data and len(task_data) > 100 else ''}")
        
        questions = [
            inquirer.Confirm(
                "confirm_generate",
                message="Generate module configuration using AI?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if not answers["confirm_generate"]:
            print("\n❌ Operation cancelled\n")
            await main_menu()
            return
        
        print("\n🤖 Generating configuration with OpenAI...")
        
        # Generate config using AI
        try:
            generated_config = await generate_schema(task_data)
            print("✅ Configuration generated successfully!")
        except Exception as error:
            print(f"\n❌ {error}")
            questions = [
                inquirer.Confirm(
                    "manual_mode",
                    message="Would you like to switch to manual mode instead?",
                    default=True
                )
            ]
            
            answers = inquirer.prompt(questions)
            if answers["manual_mode"]:
                return await wizard()
            else:
                await main_menu()
                return
        
        # Parse the generated config
        try:
            parsed_config = json.loads(generated_config)
        except json.JSONDecodeError as error:
            print(f"\n❌ Failed to parse generated configuration: {error}")
            print(f"Raw config: {generated_config}")
            await main_menu()
            return
        
        # Ensure correct structure
        if "modules" not in parsed_config or not isinstance(parsed_config["modules"], list):
            print("\n❌ Generated configuration is missing 'modules' array")
            print(f"Received structure: {parsed_config}")
            await main_menu()
            return
        
        # Fix module names and ensure required arrays
        for module in parsed_config["modules"]:
            # Fix module names with spaces
            if "name" in module and " " in module["name"]:
                old_name = module["name"]
                module["name"] = module["name"].replace(" ", "")
                print(f'⚠️  Fixed module name: "{old_name}" → "{module["name"]}"')
            
            # Ensure required arrays exist
            if "fields" not in module:
                print(f'⚠️  Module "{module.get("name", "unknown")}" missing fields array, adding empty array')
                module["fields"] = []
            
            if "relationships" not in module:
                module["relationships"] = []
        
        # Display generated config
        print("\n📄 Generated Configuration:")
        print("─────────────────────────────────────────────────────────")
        print(json.dumps(parsed_config, indent=2))
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Confirm(
                "save_config",
                message="Save this configuration to a JSON file?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["save_config"]:
            questions = [
                inquirer.Text(
                    "config_file_name",
                    message="Config file name:",
                    default=f"clickup-{task_id}-config.json",
                    validate=lambda _, x: bool(x.strip()) and x.endswith(".json") or "File must end with .json"
                )
            ]
            
            answers = inquirer.prompt(questions)
            config_file_name = answers["config_file_name"]
            
            try:
                with open(config_file_name, "w", encoding="utf-8") as f:
                    json.dump(parsed_config, f, indent=2)
                print(f"\n✅ Configuration saved to: {config_file_name}")
            except Exception as error:
                print(f"\n⚠️  Could not save file: {error}")
        
        questions = [
            inquirer.Confirm(
                "proceed_generation",
                message="Proceed with module generation?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if not answers["proceed_generation"]:
            print("\n❌ Generation cancelled\n")
            await main_menu()
            return
        
        # Validate configuration
        validation = validate_batch_config(parsed_config)
        if not validation["isValid"]:
            print("\n❌ Generated configuration has errors:")
            for err in validation["errors"]:
                print(f"   • {err}")
            
            questions = [
                inquirer.Confirm(
                    "fix_manually",
                    message="Would you like to fix the configuration manually and continue?",
                    default=False
                )
            ]
            
            answers = inquirer.prompt(questions)
            if not answers["fix_manually"]:
                await main_menu()
                return
            
            print("\nPlease edit the saved JSON file and use Batch Mode to continue.")
            await main_menu()
            return
        
        # Check paths
        backend_valid = await check_backend_app_exists(backend_path)
        frontend_valid = await check_frontend_src_exists(frontend_path)
        
        if not backend_valid and not frontend_valid:
            print("\n❌ Neither backend nor frontend paths are valid!")
            await main_menu()
            return
        
        gen_backend = backend_valid
        gen_frontend = frontend_valid
        
        if not backend_valid:
            print(f"⚠️  Backend path not found: {backend_path}")
        if not frontend_valid:
            print(f"⚠️  Frontend path not found: {frontend_path}")
        
        print("\n🚀 Starting module generation...\n")
        
        results = []
        for i, module in enumerate(parsed_config["modules"]):
            print(f"\n📝 [{i+1}/{len(parsed_config['modules'])}] Processing: {module['name']}")
            print("─────────────────────────────────────────────────────────")
            
            result = await process_batch_module(
                module,
                backend_path,
                frontend_path,
                gen_backend,
                gen_frontend
            )
            results.append(result)
            
            if result["success"]:
                print(f"✅ {module['name']} generated successfully")
                if "backend" in result:
                    print(f"   Backend: {', '.join(result['backend'].values())}")
                if "frontend" in result:
                    print(f"   Frontend: {', '.join(result['frontend'].values())}")
            else:
                print(f"❌ {module['name']} generation failed:")
                for err in result["errors"]:
                    print(f"   • {err}")
            
            if result["warnings"]:
                print("   ⚠️  Warnings:")
                for warn in result["warnings"]:
                    print(f"   • {warn}")
        
        # Display summary
        display_completion_banner()
        print("📊 AI Generation Summary:")
        print("─────────────────────────────────────────────────────────")
        
        success_count = sum(1 for r in results if r["success"])
        failure_count = len(results) - success_count
        
        print(f"ClickUp Task: {task_data} ({task_id})")
        print(f"Total modules: {len(parsed_config['modules'])}")
        print(f"✅ Successful: {success_count}")
        if failure_count > 0:
            print(f"❌ Failed: {failure_count}")
        print("\nGenerated modules:")
        
        for result in results:
            status = "✓" if result["success"] else "✗"
            print(f"   [{status}] {result['name']}")
            for err in result["errors"]:
                print(f"       └─ {err}")
        
        print("\n")
        
        questions = [
            inquirer.Confirm(
                "continue_loop",
                message="Return to main menu?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["continue_loop"]:
            await main_menu()
        else:
            print("\nGoodbye! 👋\n")
            sys.exit(0)
        
    except Exception as error:
        print(f"\n❌ Error: {error}")
        import traceback
        traceback.print_exc()
        
        questions = [
            inquirer.Confirm(
                "back_to_menu",
                message="Return to main menu?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["back_to_menu"]:
            await main_menu()
        else:
            sys.exit(1)

# ============================================================================
# BATCH MODE WIZARD
# ============================================================================

async def batch_mode_wizard():
    """Batch Mode Wizard."""
    print("\n📦 Batch Mode Configuration")
    print("─────────────────────────────────────────────────────────")
    
    try:
        # Step 1: Get paths
        print("\n📝 Step 1: Path Configuration")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "backend_path",
                message="Backend path (e.g., ../Backend):",
                default="../Backend",
                validate=lambda _, x: validate_path(x)
            ),
            inquirer.Text(
                "frontend_path",
                message="Frontend path (e.g., ../Frontend):",
                default="../Frontend",
                validate=lambda _, x: validate_path(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        backend_path = answers["backend_path"]
        frontend_path = answers["frontend_path"]
        
        # Step 2: Get JSON config file
        print("\n📝 Step 2: JSON Configuration File")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "config_path",
                message="Path to JSON configuration file:",
                validate=lambda _, x: bool(x.strip()) and x.endswith(".json") or "File must be .json"
            )
        ]
        
        answers = inquirer.prompt(questions)
        config_path = answers["config_path"]
        
        # Read and parse JSON file
        try:
            with open(config_path, "r", encoding="utf-8") as f:
                config_content = f.read()
                config = json.loads(config_content)
        except FileNotFoundError:
            print(f"\n❌ Error reading file: File not found")
            sys.exit(1)
        except json.JSONDecodeError as error:
            print(f"\n❌ Invalid JSON format: {error}")
            sys.exit(1)
        
        # Validate configuration
        validation = validate_batch_config(config)
        if not validation["isValid"]:
            print("\n❌ Configuration validation errors:")
            for err in validation["errors"]:
                print(f"   • {err}")
            sys.exit(1)
        
        print(f"\n✓ Configuration validated successfully")
        print(f"📦 Found {len(config.get('modules', []))} module(s) to generate\n")
        
        # Check paths
        backend_valid = await check_backend_app_exists(backend_path)
        frontend_valid = await check_frontend_src_exists(frontend_path)
        
        if not backend_valid and not frontend_valid:
            print("\n❌ Neither backend nor frontend paths are valid!")
            sys.exit(1)
        
        gen_backend = backend_valid
        gen_frontend = frontend_valid
        
        if not backend_valid:
            print(f"⚠️  Backend path not found: {backend_path}")
        if not frontend_valid:
            print(f"⚠️  Frontend path not found: {frontend_path}")
        
        # Confirm processing
        questions = [
            inquirer.Confirm(
                "confirm",
                message=f"\nGenerate {len(config.get('modules', []))} module(s)?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if not answers["confirm"]:
            print("\n❌ Operation cancelled\n")
            await main_menu()
            return
        
        print("\n🚀 Starting batch generation...\n")
        
        results = []
        for i, module in enumerate(config.get("modules", [])):
            print(f"\n📝 [{i+1}/{len(config['modules'])}] Processing: {module['name']}")
            print("─────────────────────────────────────────────────────────")
            
            result = await process_batch_module(
                module,
                backend_path,
                frontend_path,
                gen_backend,
                gen_frontend
            )
            results.append(result)
            
            if result["success"]:
                print(f"✅ {module['name']} generated successfully")
                if "backend" in result:
                    print(f"   Backend: {', '.join(result['backend'].values())}")
                if "frontend" in result:
                    print(f"   Frontend: {', '.join(result['frontend'].values())}")
            else:
                print(f"❌ {module['name']} generation failed:")
                for err in result["errors"]:
                    print(f"   • {err}")
            
            if result["warnings"]:
                print("   ⚠️  Warnings:")
                for warn in result["warnings"]:
                    print(f"   • {warn}")
        
        # Display summary
        display_completion_banner()
        print("📊 Batch Generation Summary:")
        print("─────────────────────────────────────────────────────────")
        
        success_count = sum(1 for r in results if r["success"])
        failure_count = len(results) - success_count
        
        print(f"Total modules: {len(config['modules'])}")
        print(f"✅ Successful: {success_count}")
        if failure_count > 0:
            print(f"❌ Failed: {failure_count}")
        print("\nGenerated modules:")
        
        for result in results:
            status = "✓" if result["success"] else "✗"
            print(f"   [{status}] {result['name']}")
            for err in result["errors"]:
                print(f"       └─ {err}")
        
        print("\n")
        
        questions = [
            inquirer.Confirm(
                "continue_loop",
                message="Return to main menu?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["continue_loop"]:
            await main_menu()
        else:
            print("\nGoodbye! 👋\n")
            sys.exit(0)
        
    except Exception as error:
        print(f"\n❌ Error: {error}")
        sys.exit(1)

# ============================================================================
# EXAMPLE CONFIG GENERATION
# ============================================================================

async def generate_example_config():
    """Generate example JSON configuration."""
    example_config = {
        "backendPath": "../Backend",
        "frontendPath": "../Frontend",
        "modules": [
            {
                "name": "Product",
                "fields": [
                    {
                        "name": "title",
                        "type": "string",
                        "description": "Product title",
                        "showInTable": True
                    },
                    {
                        "name": "description",
                        "type": "textarea",
                        "description": "Product description",
                        "showInTable": False
                    },
                    {
                        "name": "price",
                        "type": "decimal",
                        "description": "Product price",
                        "showInTable": True
                    },
                    {
                        "name": "stock",
                        "type": "integer",
                        "description": "Stock quantity",
                        "showInTable": True
                    },
                    {
                        "name": "isActive",
                        "type": "boolean",
                        "description": "Is product active",
                        "showInTable": True
                    }
                ],
                "relationships": [
                    {
                        "type": "belongsTo",
                        "relatedModel": "Category",
                        "methodName": "category",
                        "foreignKey": "category_id",
                        "inverseMethod": "products",
                        "inverseType": "hasMany"
                    }
                ]
            },
            {
                "name": "Category",
                "fields": [
                    {
                        "name": "name",
                        "type": "string",
                        "description": "Category name",
                        "showInTable": True
                    },
                    {
                        "name": "slug",
                        "type": "string",
                        "description": "URL slug",
                        "showInTable": True
                    }
                ]
            }
        ]
    }
    
    example_path = Path.cwd() / "batch-config-example.json"
    try:
        with open(example_path, "w", encoding="utf-8") as f:
            json.dump(example_config, f, indent=2)
        print(f"\n✅ Example configuration created: {example_path}\n")
    except Exception as error:
        print(f"\n❌ Error creating example: {error}\n")

# ============================================================================
# DELETE MODULE WIZARD
# ============================================================================

async def delete_module_wizard():
    """Wizard for deleting a module."""
    print("\n📝 Module Deletion Wizard")
    print("─────────────────────────────────────────────────────────")
    
    try:
        # Get paths
        questions = [
            inquirer.Text(
                "backend_path",
                message="Backend path (e.g., ../Backend):",
                default="../Backend",
                validate=lambda _, x: validate_path(x)
            ),
            inquirer.Text(
                "frontend_path",
                message="Frontend path (e.g., ../Frontend):",
                default="../Frontend",
                validate=lambda _, x: validate_path(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        backend_path = answers["backend_path"]
        frontend_path = answers["frontend_path"]
        
        # Check if paths are valid
        backend_valid = await check_backend_app_exists(backend_path)
        frontend_valid = await check_frontend_src_exists(frontend_path)
        
        # Get module name
        questions = [
            inquirer.Text(
                "module_name",
                message="Module name to delete (e.g., Product):",
                validate=lambda _, x: validate_model_name(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        module_name = answers["module_name"]
        
        # Determine what to delete
        delete_options = []
        if backend_valid:
            delete_options.append("Backend only")
        if frontend_valid:
            delete_options.append("Frontend only")
        if backend_valid and frontend_valid:
            delete_options.append("Both (Backend + Frontend)")
        
        questions = [
            inquirer.List(
                "delete_what",
                message="What would you like to delete?",
                choices=delete_options,
                default=len(delete_options) - 1
            )
        ]
        
        answers = inquirer.prompt(questions)
        delete_what = answers["delete_what"]
        
        # Confirm deletion
        questions = [
            inquirer.Confirm(
                "confirm",
                message=f'⚠️  Are you sure you want to delete "{module_name}"? This cannot be undone!',
                default=False
            )
        ]
        
        answers = inquirer.prompt(questions)
        if not answers["confirm"]:
            print("\n❌ Deletion cancelled.\n")
            return
        
        delete_backend = "Backend" in delete_what
        delete_frontend = "Frontend" in delete_what
        
        await delete_module(
            backend_path,
            frontend_path,
            module_name,
            delete_backend,
            delete_frontend
        )
        
        questions = [
            inquirer.Confirm(
                "again",
                message="Would you like to perform another action?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["again"]:
            await main_menu()
        else:
            print("\nGoodbye! 👋\n")
            sys.exit(0)
        
    except Exception as error:
        print(f"\n❌ Error: {error}")
        sys.exit(1)

# ============================================================================
# MAIN WIZARD
# ============================================================================

async def wizard():
    """Main wizard for manual module creation."""
    try:
        # Step 1: Get paths
        print("\n📝 Step 1: Path Configuration")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "backend_path",
                message="Backend path (e.g., ../Backend):",
                default="../Backend",
                validate=lambda _, x: validate_path(x)
            ),
            inquirer.Text(
                "frontend_path",
                message="Frontend path (e.g., ../Frontend):",
                default="../Frontend",
                validate=lambda _, x: validate_path(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        backend_path = answers["backend_path"]
        frontend_path = answers["frontend_path"]
        
        # Check path validity
        backend_valid = await check_backend_app_exists(backend_path)
        frontend_valid = await check_frontend_src_exists(frontend_path)
        
        if not backend_valid:
            print(f"   ⚠️  Backend app directory not found at {backend_path}")
        if not frontend_valid:
            print(f"   ⚠️  Frontend src directory not found at {frontend_path}")
        
        if not backend_valid and not frontend_valid:
            print("   ❌ Neither backend nor frontend paths are valid!")
            sys.exit(1)
        
        # Step 2: Generation options
        print("\n📝 Step 2: Generation Options")
        print("─────────────────────────────────────────────────────────")
        
        generation_choices = []
        if backend_valid:
            generation_choices.append("Backend Only")
        if frontend_valid:
            generation_choices.append("Frontend Only")
        if backend_valid and frontend_valid:
            generation_choices.append("Both (Backend + Frontend)")
        
        questions = [
            inquirer.List(
                "generate_what",
                message="What would you like to generate?",
                choices=generation_choices,
                default=0
            )
        ]
        
        answers = inquirer.prompt(questions)
        generate_what = answers["generate_what"]
        
        gen_backend = "Backend" in generate_what
        gen_frontend = "Frontend" in generate_what
        
        # Step 3: Module name
        print("\n📝 Step 3: Module Name")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "name",
                message="Module name (e.g., Product, User):",
                validate=lambda _, x: validate_model_name(x)
            )
        ]
        
        answers = inquirer.prompt(questions)
        name = answers["name"]
        name_kebab = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', name).lower()
        name_lower = name.lower()
        
        # Step 4: Frontend Form Type (NEW - Moved up!)
        print("\n📝 Step 4: Frontend Form Type")
        print("─────────────────────────────────────────────────────────")
        print("   📋 Regular Form - Uses isFlipped to show/hide form")
        print("   🪟 Modal Dialog - Opens form in a modal dialog using v-dialog")
        print("─────────────────────────────────────────────────────────")
        
        # Only ask if frontend is being generated
        if gen_frontend:
            questions = [
                inquirer.List(
                    "form_type",
                    message="Choose form type:",
                    choices=[
                        ("Regular Form (isFlipped)", "page"),
                        ("Modal Dialog (v-dialog)", "page-form-dialog")
                    ],
                    default="page"
                )
            ]
            
            answers = inquirer.prompt(questions)
            form_type = answers["form_type"]
        else:
            form_type = "page"  # Default
        
        print(f"   ✓ Selected: {'Modal Dialog' if form_type == 'page-form-dialog' else 'Regular Form'}")
        
        # Step 5: Fields
        step_num = 5
        print(f"\n📝 Step {step_num}: Fields")
        print("─────────────────────────────────────────────────────────")
        
        questions = [
            inquirer.Text(
                "num_fields",
                message="How many fields?",
                default="2",
                validate=lambda _, x: x.isdigit() and int(x) >= 1 or "Must have at least 1 field."
            )
        ]
        
        answers = inquirer.prompt(questions)
        num_fields = int(answers["num_fields"])
        
        field_type_options = list(FIELD_TYPES.keys())
        backend_fillable = []
        backend_validations = []
        frontend_fields = []
        
        for i in range(num_fields):
            print(f"\n   Field {i + 1}:")
            
            # Field name
            questions = [
                inquirer.Text(
                    "field_name",
                    message="Field name:",
                    validate=lambda _, x: validate_field_name(x)
                )
            ]
            
            answers = inquirer.prompt(questions)
            field_name = answers["field_name"]
            
            # Field type
            questions = [
                inquirer.List(
                    "field_type",
                    message="Field type:",
                    choices=field_type_options,
                    default="string"
                )
            ]
            
            answers = inquirer.prompt(questions)
            field_type = answers["field_type"]
            field_info = FIELD_TYPES[field_type]
            
            # Add to backend
            backend_fillable.append(field_name)
            backend_validations.append({
                "name": field_name,
                "type": field_type,
                "rule": field_info["validation"],
                "updateRule": field_info["updateValidation"],
                "testValue": field_info["testValue"],
                "updateValue": field_info["updateValue"]
            })
            
            # Frontend field configuration (only if generating frontend)
            if gen_frontend:
                questions = [
                    inquirer.Confirm(
                        "show_in_table",
                        message="Show in table?",
                        default=field_info["tableDisplay"] != "hidden"
                    ),
                    inquirer.Text(
                        "description",
                        message="Field description (optional):",
                        default=""
                    )
                ]
                
                answers = inquirer.prompt(questions)
                show_in_table = answers["show_in_table"]
                description = answers["description"]
                
                frontend_field = {
                    "name": field_name,
                    "type": field_type,
                    "formType": field_info["formType"],
                    "rules": field_info["validation"],
                    "col": field_info["col"],
                    "showInTable": show_in_table,
                    "tableDisplay": field_info["tableDisplay"],
                    "description": description,
                }
                
                # Handle options
                if field_info.get("hasOptions"):
                    questions = [
                        inquirer.List(
                            "options_type",
                            message="Options type:",
                            choices=["Static options", "Dynamic (from API/Module)"],
                            default="Static options"
                        )
                    ]
                    
                    answers = inquirer.prompt(questions)
                    options_type = answers["options_type"]
                    
                    if options_type == "Static options":
                        questions = [
                            inquirer.Text(
                                "num_options",
                                message="How many options?",
                                default="2",
                                validate=lambda _, x: x.isdigit() and int(x) >= 1 or "Must have at least 1 option."
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        num_options = int(answers["num_options"])
                        
                        options = []
                        for j in range(num_options):
                            print(f"\n      Option {j + 1}:")
                            
                            questions = [
                                inquirer.Text("option_id", message=f"Option {j + 1} ID:", default=f"option{j + 1}"),
                                inquirer.Text("option_name", message=f"Option {j + 1} Name:", default=f"Option {j + 1}")
                            ]
                            
                            answers = inquirer.prompt(questions)
                            options.append({
                                "id": answers["option_id"],
                                "name": answers["option_name"]
                            })
                        
                        frontend_field["options"] = options
                    else:
                        questions = [
                            inquirer.Text("custom_type", message="Type property value (e.g., options, user-select):", default="options"),
                            inquirer.Text("module", message="Module/API name:", validate=lambda _, x: validate_model_name(x)),
                            inquirer.Text("option_label", message="Option label field:", default="name"),
                            inquirer.Text("option_value", message='Option value field (leave empty for "id"):', default="")
                        ]
                        
                        answers = inquirer.prompt(questions)
                        
                        frontend_field.update({
                            "isDynamic": True,
                            "moduleName": answers["module"],
                            "optionLabel": answers["option_label"].strip(),
                            "optionValue": answers["option_value"].strip() or None
                        })
                    
                    questions = [
                        inquirer.Confirm(
                            "multiple",
                            message="Allow multiple selection?",
                            default=False
                        )
                    ]
                    
                    answers = inquirer.prompt(questions)
                    frontend_field["multiple"] = answers["multiple"]
                
                frontend_fields.append(frontend_field)
            
            print(f"      ✓ Added: {field_name}")
        
        # Step 6: Relationships
        step_num = 6
        relationships = []
        models_to_create = []
        
        if gen_backend:
            print(f"\n📝 Step {step_num}: Relationships")
            print("─────────────────────────────────────────────────────────")
            print("   ✅ belongsTo    - Many-to-One (stores foreign key)")
            print("   ✅ hasOne       - One-to-One (referenced or embedded)")
            print("   ✅ hasMany      - One-to-Many (foreign key in related model)")
            print("   ✅ belongsToMany- Many-to-Many (array of IDs, NO pivot)")
            
            questions = [
                inquirer.Confirm(
                    "add_relationships",
                    message="Add relationships?",
                    default=False
                )
            ]
            
            answers = inquirer.prompt(questions)
            add_relationships = answers["add_relationships"]
            
            if add_relationships:
                questions = [
                    inquirer.Text(
                        "num_relations",
                        message="How many relationships?",
                        default="0",
                        validate=lambda _, x: x.isdigit() and int(x) >= 0 or "Please enter a valid number"
                    )
                ]
                
                answers = inquirer.prompt(questions)
                num_relations = int(answers["num_relations"])
                
                relationship_type_options = ["belongsTo", "hasOne", "hasMany", "belongsToMany"]
                
                for i in range(num_relations):
                    print(f"\n   Relationship {i + 1}:")
                    
                    questions = [
                        inquirer.List(
                            "relation_type",
                            message="Type:",
                            choices=relationship_type_options
                        ),
                        inquirer.Text(
                            "related_model",
                            message="Related model name:",
                            validate=lambda _, x: validate_model_name(x)
                        )
                    ]
                    
                    answers = inquirer.prompt(questions)
                    relation_type = answers["relation_type"]
                    related_model = answers["related_model"]
                    
                    # Check if related model exists
                    related_model_exists = await check_backend_model_exists(backend_path, related_model)
                    create_related_model = False
                    
                    if not related_model_exists:
                        print(f"      ⚠️  Warning: {related_model} model doesn't exist yet!")
                        
                        questions = [
                            inquirer.Confirm(
                                "create_choice",
                                message=f"Auto-create {related_model} module?",
                                default=True
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        create_choice = answers["create_choice"]
                        
                        if create_choice:
                            create_related_model = True
                            print(f"      ✓ Will create {related_model} model")
                        else:
                            questions = [
                                inquirer.Confirm(
                                    "continue_anyway",
                                    message="Continue anyway?",
                                    default=False
                                )
                            ]
                            
                            answers = inquirer.prompt(questions)
                            if not answers["continue_anyway"]:
                                print(f"      ⏭️  Skipped relationship with {related_model}")
                                continue
                    
                    # Method name
                    questions = [
                        inquirer.Text(
                            "method_name",
                            message="Method name:",
                            default=related_model.lower()
                        )
                    ]
                    
                    answers = inquirer.prompt(questions)
                    method_name = answers["method_name"]
                    
                    rel_info = RELATIONSHIP_TYPES.get(relation_type)
                    if not rel_info:
                        print(f"      ⚠️  Unknown type: {relation_type}, skipping...")
                        continue
                    
                    foreign_key_name = None
                    local_key = None
                    inverse_method_name = None
                    add_inverse_relationship = False
                    
                    if relation_type == "belongsTo":
                        default_fk = f"{related_model.lower()}_id"
                        
                        questions = [
                            inquirer.Text(
                                "foreign_key",
                                message="Foreign key field:",
                                default=default_fk
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        foreign_key_name = answers["foreign_key"]
                        
                        # Add to fillable and validations
                        backend_fillable.append(foreign_key_name)
                        backend_validations.append({
                            "name": foreign_key_name,
                            "type": "string",
                            "rule": "required|string",
                            "updateRule": "required|string",
                            "testValue": '"507f1f77bcf86cd799439011"',
                            "updateValue": '"507f1f77bcf86cd799439012"'
                        })
                        
                        print(f"      ✓ Field: {foreign_key_name} (string)")
                        
                        # Ask about inverse relationship
                        if related_model_exists or create_related_model:
                            questions = [
                                inquirer.Confirm(
                                    "add_inverse",
                                    message=f"Add inverse hasMany to {related_model}?",
                                    default=False
                                )
                            ]
                            
                            answers = inquirer.prompt(questions)
                            add_inverse = answers["add_inverse"]
                            
                            if add_inverse:
                                add_inverse_relationship = True
                                default_inverse = name.lower() + "s"
                                
                                questions = [
                                    inquirer.Text(
                                        "inverse_method",
                                        message=f"Method name in {related_model}:",
                                        default=default_inverse
                                    )
                                ]
                                
                                answers = inquirer.prompt(questions)
                                inverse_method_name = answers["inverse_method"]
                    
                    elif relation_type == "belongsToMany":
                        print("      ℹ️  MongoDB uses array of IDs (no pivot table needed)")
                        default_array_field = f"{related_model.lower()}_ids"
                        
                        questions = [
                            inquirer.Text(
                                "array_field",
                                message="Array field name:",
                                default=default_array_field
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        foreign_key_name = answers["array_field"]
                        
                        # Add to fillable and validations
                        backend_fillable.append(foreign_key_name)
                        backend_validations.append({
                            "name": foreign_key_name,
                            "type": "array",
                            "rule": "nullable|array",
                            "updateRule": "nullable|array",
                            "testValue": "['507f1f77bcf86cd799439011', '507f1f77bcf86cd799439012']",
                            "updateValue": "['507f1f77bcf86cd799439013', '507f1f77bcf86cd799439014']"
                        })
                        
                        print(f"      ✓ Field: {foreign_key_name} (array)")
                        
                        # Ask about inverse relationship
                        questions = [
                            inquirer.Confirm(
                                "add_inverse",
                                message=f"Add inverse belongsToMany to {related_model}?",
                                default=False
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        add_inverse = answers["add_inverse"]
                        
                        if add_inverse:
                            add_inverse_relationship = True
                            default_inverse = name.lower() + "s"
                            
                            questions = [
                                inquirer.Text(
                                    "inverse_method",
                                    message=f"Method name in {related_model}:",
                                    default=default_inverse
                                ),
                                inquirer.Text(
                                    "inverse_field",
                                    message="Array field name in related model:",
                                    default=f"{name.lower()}_ids"
                                )
                            ]
                            
                            answers = inquirer.prompt(questions)
                            inverse_method_name = answers["inverse_method"]
                            inverse_field = answers["inverse_field"]
                            
                            # Add to models_to_create if needed
                            if create_related_model:
                                existing_model = next((m for m in models_to_create if m["name"] == related_model), None)
                                if not existing_model:
                                    models_to_create.append({
                                        "name": related_model,
                                        "fields": [{"name": inverse_field, "type": "array"}],
                                        "relationships": [],
                                        "relatedModels": [name]
                                    })
                                else:
                                    if not any(f["name"] == inverse_field for f in existing_model["fields"]):
                                        existing_model["fields"].append({"name": inverse_field, "type": "array"})
                        
                        # Ask which repository manages the relationship
                        questions = [
                            inquirer.List(
                                "sync_choice",
                                message="Which Repository should manage this relationship (attach/detach)?",
                                choices=[f"{name} Repository", f"{related_model} Repository"],
                                default=f"{name} Repository"
                            )
                        ]
                        
                        answers = inquirer.prompt(questions)
                        sync_choice = answers["sync_choice"]
                        
                        add_sync_methods = sync_choice == f"{name} Repository"
                        add_inverse_sync_methods = sync_choice == f"{related_model} Repository"
                        
                        if add_sync_methods:
                            print(f"      ✓ {name} Repository will manage the relationship")
                        else:
                            print(f"      ✓ {related_model} Repository will manage the relationship")
                        
                        local_key = {
                            "inverseField": inverse_field if add_inverse else f"{name.lower()}_ids",
                            "addSyncMethods": add_sync_methods,
                            "addInverseSyncMethods": add_inverse_sync_methods
                        }
                        
                        if create_related_model and add_inverse_sync_methods:
                            existing_model = next((m for m in models_to_create if m["name"] == related_model), None)
                            if existing_model:
                                existing_model["addSyncMethods"] = True
                    
                    # Add relationship to list
                    relationships.append({
                        "type": relation_type,
                        "relatedModel": related_model,
                        "methodName": method_name,
                        "foreignKey": foreign_key_name,
                        "localKey": local_key,
                        "embedded": False,
                        "createModel": create_related_model,
                        "inverseMethod": inverse_method_name,
                        "addInverse": add_inverse_relationship,
                        "inverseType": rel_info["inverse"]
                    })
                    
                    print(f"      ✓ Added: {method_name} ({relation_type})")
        
        # Step 7: Create missing models
        step_num = 7
        if models_to_create:
            print(f"\n📝 Step {step_num}: Creating Related Models")
            print("─────────────────────────────────────────────────────────")
            
            for model_config in models_to_create:
                model_name = model_config["name"]
                print(f"\n   Creating module for {model_name}...")
                
                model_name_kebab = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', model_name).lower()
                model_fillable = ["name"]
                
                try:
                    # Create basic backend files
                    backend_files = [
                        ("Model", "model.txt"),
                        ("Repository", "repositories.txt"),
                        ("Service", "services.txt"),
                        ("Controller", "controller.txt"),
                        ("Routes", "route.txt")
                    ]
                    
                    for file_type, template in backend_files:
                        await create_backend_file(
                            file_type,
                            backend_path,
                            template,
                            model_name,
                            model_name_kebab,
                            model_fillable
                        )
                        print(f"      ✓ {file_type} created")
                    
                    await register_backend_route(backend_path, model_name)
                    print(f"      ✓ Route registered")
                    
                except Exception as err:
                    print(f"      ❌ Error: {err}")
        
        # Step 8: Generate main module
        step_num = 8 if models_to_create else 7
        print(f"\n📝 Step {step_num}: Generating Module")
        print("─────────────────────────────────────────────────────────")
        
        if gen_backend:
            try:
                # Create all backend files
                backend_files = [
                    ("Model", "model.txt"),
                    ("Repository", "repositories.txt"),
                    ("Service", "services.txt"),
                    ("Controller", "controller.txt"),
                    ("Routes", "route.txt"),
                    ("ControllerTest", "test.txt")
                ]
                
                for file_type, template in backend_files:
                    await create_backend_file(
                        file_type,
                        backend_path,
                        template,
                        name,
                        name_kebab,
                        backend_fillable,
                        backend_validations if file_type in ["Service", "ControllerTest"] else [],
                        relationships if file_type in ["Model", "Repository"] else []
                    )
                    print(f"   ✓ {file_type} created")
                
                await register_backend_route(backend_path, name)
                print("   ✓ Route registered")
                
                # Add inverse relationships
                has_inverse_relationships = any(
                    r.get("addInverse") and r.get("inverseMethod") 
                    for r in relationships
                )
                
                if has_inverse_relationships:
                    print(f"\n   Adding Inverse Relationships...")
                    
                    for rel in relationships:
                        if rel.get("addInverse") and rel.get("inverseMethod"):
                            related_model_exists = await check_backend_model_exists(
                                backend_path,
                                rel["relatedModel"]
                            )
                            
                            if not related_model_exists:
                                print(f"   ❌ Model {rel['relatedModel']} not found!")
                                continue
                            
                            inverse_type = rel.get("inverseType", RELATIONSHIP_TYPES[rel["type"]]["inverse"])
                            if not inverse_type:
                                print(f"   ⚠️  No inverse type defined for {rel['type']}")
                                continue
                            
                            inverse_rel = {
                                "type": inverse_type,
                                "relatedModel": name,
                                "methodName": rel["inverseMethod"],
                                "foreignKey": rel["localKey"]["inverseField"] if rel["type"] == "belongsToMany" else rel["foreignKey"],
                                "localKey": {"inverseField": rel["foreignKey"]} if rel["type"] == "belongsToMany" else None
                            }
                            
                            inverse_code = generate_single_relationship(inverse_rel)
                            
                            try:
                                await add_relationship_to_model(
                                    backend_path,
                                    rel["relatedModel"],
                                    {
                                        "name": rel["inverseMethod"],
                                        "code": inverse_code
                                    },
                                    name
                                )
                            except Exception as error:
                                print(f"   ❌ Error adding relationship: {error}")
                
            except Exception as err:
                print(f"   ❌ Error: {err}")
        
        if gen_frontend:
            try:
                # Create frontend files
                frontend_files = [
                    ("API", "api.txt"),
                    ("Page", "page.txt"),
                    ("Router", "router.txt")
                ]
                
                for file_type, template in frontend_files:
                    await create_frontend_file(
                        file_type,
                        frontend_path,
                        template,
                        name,
                        name_kebab,
                        name_lower,
                        frontend_fields if file_type == "Page" else None,
                        form_type  # Pass form type
                    )
                
                await update_frontend_router_index(frontend_path, name)
                print("   ✓ Router index updated")
                
                # Special note for FormDialog
                if form_type == "page-form-dialog":
                    print("   ℹ️  Note: Using FormDialog component - ensure it's imported in your project")
                    print("   ℹ️  The generated page uses v-model for dialog visibility")
                
                # Generate translations
                translations = generate_translations(name_lower, frontend_fields)
                print("\n📝 Translation Keys (add to your i18n file):")
                print("─────────────────────────────────────────────────────────")
                print(json.dumps(translations, indent=2))
                
            except Exception as err:
                print(f"   ❌ Error: {err}")
        
        # Display completion banner and summary
        display_completion_banner()
        
        print("📊 Summary:")
        print(f"   Module: {name}")
        print(f"   Route: /{name_kebab}")
        if gen_backend:
            print(f"   Backend Fields: {len(backend_fillable)}")
            print(f"   Relationships: {len(relationships)}")
        if gen_frontend:
            print(f"   Frontend Form Type: {'Modal Dialog (FormDialog)' if form_type == 'page-form-dialog' else 'Regular Form (isFlipped)'}")
            print(f"   Frontend Fields: {len(frontend_fields)}")
        print("\n")
        
        questions = [
            inquirer.Confirm(
                "continue_loop",
                message="Would you like to perform another action?",
                default=True
            )
        ]
        
        answers = inquirer.prompt(questions)
        if answers["continue_loop"]:
            await main_menu()
        else:
            print("\nGoodbye! 👋\n")
            sys.exit(0)
        
    except Exception as error:
        print(f"\n❌ Error: {error}")
        sys.exit(1)
        
def log_generation_details(module_name: str, form_type: str, template_path: str, 
                          fields_count: int, has_frontend: bool, has_backend: bool):
    """Log all generation details for debugging."""
    print("\n" + "="*80)
    print("🔍 DEBUG LOG - GENERATION DETAILS")
    print("="*80)
    print(f"📦 Module Name: {module_name}")
    print(f"🎯 Form Type: {form_type}")
    print(f"📁 Template Path: {template_path}")
    print(f"🔤 Template Exists: {Path(template_path).exists()}")
    print(f"📊 Fields Count: {fields_count}")
    print(f"🖥️  Generate Frontend: {has_frontend}")
    print(f"⚙️  Generate Backend: {has_backend}")
    
    # Read and show template content (first 500 chars)
    try:
        if Path(template_path).exists():
            content = Path(template_path).read_text(encoding='utf-8')
            print(f"\n📄 Template Content Preview (500 chars):")
            print("-"*40)
            print(content[:500] + "..." if len(content) > 500 else content)
            print("-"*40)
            
            # Check for key markers
            print(f"\n🔍 Checking Template Markers:")
            print(f"   Contains '@@templateChoice@@': {'@@templateChoice@@' in content}")
            print(f"   Contains 'FormDialog': {'FormDialog' in content}")
            print(f"   Contains 'v-model=\"dialogVisible\"': {'v-model=\"dialogVisible\"' in content}")
            print(f"   Contains 'Form': {'Form' in content}")
        else:
            print(f"❌ Template file does NOT exist!")
    except Exception as e:
        print(f"❌ Error reading template: {e}")
    print("="*80 + "\n")
# ============================================================================
# MAIN MENU
# ============================================================================

async def main_menu():
    """Display main menu and handle user choices."""
    display_banner()
    
    try:
        questions = [
            inquirer.List(
                "action",
                message="What would you like to do?",
                choices=[
                    ("✨ Create new module", "create"),
                    ("📦 Batch mode (from JSON file)", "batch"),
                    ("🤖 AI & ClickUp mode", "ai_clickup"),
                    ("📋 Generate example config", "example"),
                    ("🗑️  Delete module", "delete"),
                    ("❌ Exit", "exit")
                ]
            )
        ]
        
        answers = inquirer.prompt(questions)
        action = answers["action"]
        
        if action == "exit":
            print("\nGoodbye! 👋\n")
            sys.exit(0)
        elif action == "create":
            await wizard()
        elif action == "batch":
            await batch_mode_wizard()
        elif action == "ai_clickup":
            await ai_clickup_mode_wizard()
        elif action == "example":
            await generate_example_config()
            await main_menu()
        elif action == "delete":
            await delete_module_wizard()
    
    except Exception as error:
        print(f"\n❌ Error: {error}")
        sys.exit(1)

# ============================================================================
# MAIN ENTRY POINT
# ============================================================================

if __name__ == "__main__":
    try:
        import asyncio
        asyncio.run(main_menu())
    except KeyboardInterrupt:
        print("\n\n👋 Operation cancelled by user. Goodbye!\n")
        sys.exit(0)
    except Exception as error:
        print(f"\n❌ Fatal error: {error}")
        sys.exit(1)
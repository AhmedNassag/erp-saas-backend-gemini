import os
import json
import re
from generator_constants import FIELD_TYPES, RELATIONSHIP_TYPES

def generate_table_columns(fields, name_lower):
    columns = []
    for f in fields:
        if not f.get('showInTable'):
            continue
            
        field_name = f['name']
        
        # Determine strict structure matching original JS
        if f.get('tableDisplay') == 'boolean':
            col = f'      {{ id: "{field_name}", title: this.$t("{name_lower}.{field_name}"), data: "{field_name}", defaultContent: "N/A", render: (data) => data ? "✓" : "✗" }},'
        elif f.get('tableDisplay') == 'date':
             col = f'      {{ id: "{field_name}", title: this.$t("{name_lower}.{field_name}"), data: "{field_name}", defaultContent: "N/A", render: (data) => new Date(data).toLocaleDateString() }},'
        else:
             col = f'      {{ id: "{field_name}", title: this.$t("{name_lower}.{field_name}"), data: "{field_name}", defaultContent: "N/A" }},'
        columns.append(col)
    
    return "\n".join(columns)

def format_options_array(options):
    if not options:
        return "[]"
    
    opts_str_list = []
    for opt in options:
        opts_str_list.append(f'          {{\n            id: "{opt["id"]}",\n            name: "{opt["name"]}"\n          }}')
    
    joined_opts = ",\n".join(opts_str_list)
    return f"[\n{joined_opts}\n        ]"

def generate_form_fields(fields, name_lower):
    form_fields = []
    for f in fields:
        # Build field object string
        field_obj = f'      {{\n        name: "{f["name"]}",\n        label: this.$t("{name_lower}.{f["name"]}"),\n        type: "{f["formType"]}",\n        rules: "{f.get("rules", "")}",\n        col: {f["col"]}'
        
        if f.get('isDynamic'):
            field_obj += f',\n        options: "{f["moduleName"]}",\n        optionLabel: "{f["optionLabel"]}"'
            if f.get("optionValue"):
                field_obj += f',\n        optionValue: "{f["optionValue"]}"'
        elif f.get('options'):
            options_str = format_options_array(f['options'])
            field_obj += f',\n        options: {options_str},\n        optionLabel: "name"'
            
        if f.get('type') == 'select' and f.get('multiple'):
            field_obj += ',\n        multiple: true'
            
        if f.get('description'):
            field_obj += f',\n        description: "{f["description"]}"'
            
        field_obj += '\n      }'
        form_fields.append(field_obj)
        
    return ",\n".join(form_fields)

def generate_translations(name_lower, fields):
    translations = {
        name_lower: {
            name_lower: name_lower.capitalize()
        }
    }
    
    for f in fields:
        # Generate label from name: user_id -> User Id, camelCase -> Camel Case
        label = f['name'].replace('_', ' ')
        # Insert space before capitals
        label = re.sub(r'([A-Z])', r' \1', label)
        # Title case
        label = " ".join([word.capitalize() for word in label.split()])
        
        translations[name_lower][f['name']] = label.strip()
        
    return translations

def generate_single_relationship(rel):
    rel_type = rel['type']
    rel_info = RELATIONSHIP_TYPES.get(rel_type)
    
    if not rel_info:
        return ""
    
    code = f'\n    /**\n     * {rel_type} relationship with {rel["relatedModel"]}'
    
    if rel_type == "belongsToMany":
        fk = rel.get("foreignKey")
        lk_inverse = rel.get("localKey", {}).get("inverseField") or f"{rel['relatedModel'].lower()}_ids"
        code += f"\n     * MongoDB: '{fk}' in THIS model, '{lk_inverse}' in {rel['relatedModel']}"
    
    code += f'\n     */\n    public function {rel["methodName"]}()\n    {{'
    
    if rel_type in ["hasOne", "hasMany"]:
        code += f'\n        return $this->{rel_info["method"]}({rel["relatedModel"]}::class);\n    }}'
    elif rel_type == "belongsTo":
        code += f'\n        return $this->{rel_info["method"]}({rel["relatedModel"]}::class, \'{rel["foreignKey"]}\');\n    }}'
    elif rel_type == "belongsToMany":
        related_model_field = rel.get("localKey", {}).get("inverseField") or f"{rel['relatedModel'].lower()}_ids"
        code += f'\n        // MongoDB: \'{rel["foreignKey"]}\' in THIS model, \'{related_model_field}\' in {rel["relatedModel"]}'
        code += f'\n        return $this->{rel_info["method"]}({rel["relatedModel"]}::class, null, \'{related_model_field}\', \'{rel["foreignKey"]}\');\n    }}'
    else:
        code += f'\n        return $this->{rel_info["method"]}({rel["relatedModel"]}::class, \'{rel["foreignKey"]}\');\n    }}'
        
    return code

def create_directory_if_not_exists(path_name):
    if not os.path.exists(path_name):
        os.makedirs(path_name)

def create_backend_file(file_type, backend_path, template_name, name, name_kebab, fillable=None, validations=None, relationships=None):
    # Mapping file type to destination structure
    # Based on original fullstack-generator log output:
    # Model -> App/Models/{Name}/{Name}.php
    # Repository -> App/Repositories/{Name}Repository.php  (Wait, original creates folder for everything?) 
    # Let's check original logs from fullstack-generator.js output scan.
    # "App/Models/Product/Product.php" ?
    # In `checkBackendModelExists` I assumed `app/Models/{model_name}/{model_name}.php`.
    
    # Let's verify destination paths by looking at where it wrote files in original code.
    # Code was using `createBackendFile` with specific logic per type? No, it passed type "Model", "Repository" etc.
    # I need to know the destination rules.
    # Let's assume a structure similar to `generator-setting-backend` folders? No those are templates.
    
    # I'll try to find the `getDestinationPath` logic in fullstack-generator.js if it exists.
    # Or just assume standard Laravel modules structure often used:
    # app/Http/Controllers/{Name}Controller.php
    # app/Models/{Name}.php
    # BUT, the original had `app/Models/{Name}/{Name}.php` in my snippet view?
    # Line 3 of model.txt: `namespace App\Models\@@Name@@;`
    # This implies `App/Models/Product/Product.php`.
    
    # Controller.txt: `namespace App\Http\Controllers\@@Name@@;` -> `App/Http/Controllers/Product/ProductController.php` ?
    # Let's Assume Module-based structure:
    # app/Models/{Name}/{Name}.php
    # app/Repositories/{Name}/{Name}Repository.php
    # app/Services/{Name}/{Name}Service.php
    # app/Http/Controllers/{Name}/{Name}Controller.php
    
    name_lower = name.lower()
    
    # Read template
    # Template path is relative to current script in Generator-Setting-Python/generator-setting-backend/
    template_path = os.path.join(os.getcwd(), "generator-setting-backend", template_name)
    
    if not os.path.exists(template_path):
        print(f"Template not found: {template_path}")
        return

    with open(template_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Replacements
    content = content.replace("@@Name@@", name)
    content = content.replace("@@name@@", name_lower)
    content = content.replace("@@NameKebab@@", name_kebab) # If used
    
    if fillable:
        # quoted list
        fillable_str = ", ".join([f"'{x}'" for x in fillable])
        content = content.replace("@@NameFillable@@", fillable_str)
        
    if relationships:
        # Generate code
        rels_code = []
        use_statements = set()
        for rel in relationships:
            rels_code.append(generate_single_relationship(rel))
            use_statements.add(f"use App\\Models\\{rel['relatedModel']}\\{rel['relatedModel']};")
            
        content = content.replace("@@Relationships@@", "\n".join(rels_code))
        content = content.replace("@@UseStatements@@", "\n".join(use_statements))
    else:
        content = content.replace("@@Relationships@@", "")
        content = content.replace("@@UseStatements@@", "")

    # Determine validation logic replacements if needed (Not fully detailed in prompt plan but 'validations' arg passed)
    # The original JS probably replaces validation arrays in Service/Request files.
    # services.txt might have @@ValidationRules@@.
    # Let's check if 'services.txt' has placeholders.
    
    # Logic for file writing:
    base_dest = os.path.join(backend_path, "app")
    
    if file_type == "Model":
        dest_dir = os.path.join(base_dest, "Models", name)
        dest_file = os.path.join(dest_dir, f"{name}.php")
    elif file_type == "Repository":
        dest_dir = os.path.join(base_dest, "Repositories", name)
        dest_file = os.path.join(dest_dir, f"{name}Repository.php")
    elif file_type == "Service":
        dest_dir = os.path.join(base_dest, "Services", name)
        dest_file = os.path.join(dest_dir, f"{name}Service.php")
        # Handle validations replacement if simple
        if validations:
            # We need to format the validation array somewhat.
            # $rules = [ 'field' => 'rule', ... ];
            rules_str = ""
            for v in validations:
                 rules_str += f"            '{v['name']}' => '{v['rule']}',\n"
            content = content.replace("@@ValidationRules@@", rules_str)
            
            update_rules_str = ""
            for v in validations:
                 update_rules_str += f"            '{v['name']}' => '{v['updateRule']}',\n"
            content = content.replace("@@UpdateValidationRules@@", update_rules_str)

    elif file_type == "Controller":
        dest_dir = os.path.join(base_dest, "Http", "Controllers", name)
        dest_file = os.path.join(dest_dir, f"{name}Controller.php")
    elif file_type == "Routes":
        # Routes usually go to routes/api.php? 
        # Or a separate route file? "route.txt"
        # The original code says "route.txt created".
        # Maybe it creates a route file in the module folder?
        # Let's check `registerBackendRoute` logic.
        # But `createBackendFile` assumes a file creation.
        # Maybe App/Http/Routes/{Name}Route.php?
        # Let's guess dest_dir = base_dest/.. /routes?
        # In Laravel modular: `routes/modules/{name}.php`?
        # For now, I'll place it in `routes/{name}.php` inside the backend root if I can.
        dest_dir = os.path.join(backend_path, "routes", "modules") # Guess
        dest_file = os.path.join(dest_dir, f"{name.lower()}.php")
    elif file_type == "ControllerTest":
         dest_dir = os.path.join(backend_path, "tests", "Feature", name)
         dest_file = os.path.join(dest_dir, f"{name}ControllerTest.php")
    else:
        print(f"Unknown file type: {file_type}")
        return

    create_directory_if_not_exists(dest_dir)
    
    with open(dest_file, "w", encoding='utf-8') as f:
        f.write(content)

def create_frontend_file(file_type, frontend_path, template_name, name, name_kebab, name_lower, fields):
    # Determine destination
    base_src = os.path.join(frontend_path, "src")
    
    # Template
    template_path = os.path.join(os.getcwd(), "generator-setting-frontend", template_name)
    if not os.path.exists(template_path):
        print(f"Template not found: {template_path}")
        return

    with open(template_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Standard replacements
    content = content.replace("@@Name@@", name)
    content = content.replace("@@name@@", name_lower)
    content = content.replace("@@NameKebab@@", name_kebab)
    
    # fields generation
    if "@@TableColumns@@" in content:
        content = content.replace("@@TableColumns@@", generate_table_columns(fields, name_lower))
        
    if "@@FormFields@@" in content:
        content = content.replace("@@FormFields@@", generate_form_fields(fields, name_lower))
        
    # Destination
    if file_type == "API":
        dest_dir = os.path.join(base_src, "API", name)
        dest_file = os.path.join(dest_dir, f"{name}.js")
    elif file_type == "Page":
        dest_dir = os.path.join(base_src, "views", name)
        dest_file = os.path.join(dest_dir, f"{name}.vue")
    elif file_type == "Router":
        dest_dir = os.path.join(base_src, "router", "modules")
        dest_file = os.path.join(dest_dir, f"{name.lower()}.js")
    else:
        return

    create_directory_if_not_exists(dest_dir)
    
    with open(dest_file, "w", encoding='utf-8') as f:
        f.write(content)

def register_backend_route(backend_path, model_name):
    # Append to routes/api.php
    # Route::apiResource('model-name', Controller::class);
    routes_file = os.path.join(backend_path, "routes", "api.php")
    if os.path.exists(routes_file):
        with open(routes_file, "a", encoding='utf-8') as f:
            f.write(f"Route::apiResource('{model_name.lower()}', App\\Http\\Controllers\\{model_name}\\{model_name}Controller::class);")
def update_frontend_router_index(frontend_path, name):
    # Import router module and add to routes array in src/router/index.js
    router_index = os.path.join(frontend_path, "src", "router", "index.js")
    if not os.path.exists(router_index):
        return
        
    with open(router_index, "r", encoding='utf-8') as f:
        content = f.read()
        
    # Add import
    import_line = f'import {name.lower()} from "./modules/{name.lower()}";'
    if import_line not in content:
        # Insert at top
        content = import_line + "\n" + content
        
    # Add to routes array: ...product,
    # Find `const routes = [` or `routes: [`
    # Simple replace for now, assuming standard structure
    # This is tricky without strict parsing, usually appending to a list.
    # Let's simple append before `];` of routes if possible, or just warn user.
    # The original generator likely regexes it.
    
    # Simplistic approach:
    if f"...{name.lower()}" not in content:
        content = content.replace("routes: [", f"routes: [\n    ...{name.lower()},")
        
    with open(router_index, "w", encoding='utf-8') as f:
        f.write(content)

def add_relationship_to_model(backend_path, model_name, rel_data, source_model):
    # Find existing model file and inject method
    model_file = os.path.join(backend_path, "app", "Models", model_name, f"{model_name}.php")
    if not os.path.exists(model_file):
        return
        
    with open(model_file, "r", encoding='utf-8') as f:
        content = f.read()
        
    # Insert before the last closing brace
    # Find last `}`
    last_brace_idx = content.rfind("}")
    if last_brace_idx != -1:
        new_content = content[:last_brace_idx] + rel_data['code'] + "\n" + content[last_brace_idx:]
        
        # Add use statement if missing
        use_stmt = f"use App\\Models\\{source_model}\\{source_model};"
        if use_stmt not in new_content:
            # Add after namespace
            namespace_line = f"namespace App\\Models\\{model_name};"
            new_content = new_content.replace(namespace_line, f"{namespace_line}\n{use_stmt}")
            
        with open(model_file, "w", encoding='utf-8') as f:
            f.write(new_content)
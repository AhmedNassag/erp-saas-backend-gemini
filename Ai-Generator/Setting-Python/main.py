import sys
import os
import json
import subprocess
import traceback
from pathlib import Path
from PyQt5.QtWidgets import (QApplication, QMainWindow, QWidget, QVBoxLayout, 
                             QHBoxLayout, QPushButton, QLabel, QTextEdit, 
                             QLineEdit, QTabWidget, QGroupBox, QGridLayout,
                             QMessageBox, QFileDialog, QListWidget, QListWidgetItem,
                             QCheckBox, QComboBox, QSpinBox, QTableWidget, 
                             QTableWidgetItem, QHeaderView, QScrollArea, QSplitter,
                             QDialog, QProgressBar, QDialogButtonBox , QRadioButton, 
                             QTextBrowser, QFrame, QSizePolicy, QStackedWidget)
from PyQt5.QtCore import Qt, pyqtSignal, QThread, pyqtSlot, QTimer
from PyQt5.QtGui import QFont, QIcon, QColor, QTextCursor, QPalette
import fullstack_generator
import logging
from logging.handlers import RotatingFileHandler

import sys, os, io
try:
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')
except Exception:
    try:
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
        sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')
    except Exception:
        os.environ.setdefault('PYTHONIOENCODING', 'utf-8')


def setup_file_logger():
    """Configure a rotating file logger. Returns the logger instance and log file path."""
    try:
        if getattr(sys, 'frozen', False):
            base = Path(sys.executable).parent
        else:
            base = Path(__file__).parent

        logs_dir = base / "logs"
        logs_dir.mkdir(parents=True, exist_ok=True)

        log_file = logs_dir / "gui.log"

        logger = logging.getLogger("generator_gui")
        logger.setLevel(logging.INFO)

        # Avoid adding multiple handlers if called multiple times
        if not any(isinstance(h, RotatingFileHandler) and h.baseFilename == str(log_file) for h in logger.handlers):
            handler = RotatingFileHandler(str(log_file), maxBytes=5 * 1024 * 1024, backupCount=5, encoding='utf-8')
            formatter = logging.Formatter("%(asctime)s [%(levelname)s] %(message)s")
            handler.setFormatter(formatter)
            logger.addHandler(handler)

        return logger, str(log_file)
    except Exception:
        # Fallback to a basic logger that writes to current directory
        logging.basicConfig(level=logging.INFO)
        return logging.getLogger("generator_gui"), "gui.log"


# Initialize file logger at import time so GenerationThread can log early messages
FILE_LOGGER, FILE_LOG_PATH = setup_file_logger()
from loader_manager import LoaderManager

# Try to import ClickUp integration if available
try:
    from ClickUpIntegration import get_task
    CLICKUP_AVAILABLE = True
except ImportError:
    CLICKUP_AVAILABLE = False
    print("Note: ClickUpIntegration module not available")

# Try to import LLM integration if available
try:
    from LLMIntegaration import generate_schema
    LLM_AVAILABLE = True
except ImportError:
    LLM_AVAILABLE = False
    print("Note: LLMIntegaration module not available")

class GenerationThread(QThread):
    """Thread for running generation tasks"""
    log_signal = pyqtSignal(str)
    progress_signal = pyqtSignal(int)
    finished_signal = pyqtSignal(bool, str)
    
    def __init__(self, task_type, **kwargs):
        super().__init__()
        self.task_type = task_type
        self.kwargs = kwargs
        
    def run(self):
        try:
            if self.task_type == "create_module":
                self.create_module()
            elif self.task_type == "batch":
                self.batch_generate()
            elif self.task_type == "ai_clickup":
                self.ai_clickup_generate()
            elif self.task_type == "business_description":
                self.business_description_generate()
            elif self.task_type == "delete":
                self.delete_module()
        except Exception as e:
            self.log_signal.emit(f"❌ Error: {str(e)}")
            self.log_signal.emit(traceback.format_exc())
            self.finished_signal.emit(False, str(e))
            
    def create_module(self):
        """Create a single module"""
        
        self.log_signal.emit("Starting module creation...")
    
        # 🔍 DEBUG: اطبع جميع kwargs
        self.log_signal.emit(f"🔍 [DEBUG] Received kwargs:")
        for key, value in self.kwargs.items():
            self.log_signal.emit(f"   {key}: {value}")
        
        # تحقق من وجود form_type
        form_type = self.kwargs.get("form_type", "page")  # Default to "page" if not provided
        self.log_signal.emit(f"🔍 [DEBUG] Form Type to use: {form_type}")
        
        # Prepare config from GUI data
        config = {
            "modules": [{
                "name": self.kwargs.get("module_name"),
                "fields": self.kwargs.get("fields", []),
                "relationships": self.kwargs.get("relationships", []),
                "formType": form_type
            }]
        }
        
        # Validate
        validation = fullstack_generator.validate_batch_config(config)
        if not validation["isValid"]:
            self.log_signal.emit("❌ Validation failed:")
            for err in validation["errors"]:
                self.log_signal.emit(f"   • {err}")
            self.finished_signal.emit(False, "Validation failed")
            return
        
        # Process module
        self.log_signal.emit(f"Processing module: {self.kwargs.get('module_name')}")
        import inspect, asyncio
        process_func = fullstack_generator.process_batch_module
        args = (
            config["modules"][0],
            self.kwargs.get("backend_path"),
            self.kwargs.get("frontend_path"),
            self.kwargs.get("gen_backend", True),
            self.kwargs.get("gen_frontend", True)
        )
        if inspect.iscoroutinefunction(process_func):
            try:
                loop = asyncio.get_event_loop()
            except RuntimeError:
                loop = asyncio.new_event_loop()
                asyncio.set_event_loop(loop)
            result = loop.run_until_complete(process_func(*args))
        else:
            result = process_func(*args)
        if result["success"]:
            self.log_signal.emit("✅ Module created successfully!")
            self.finished_signal.emit(True, "Module created successfully")
        else:
            self.log_signal.emit("❌ Module creation failed:")
            for err in result["errors"]:
                self.log_signal.emit(f"   • {err}")
            self.finished_signal.emit(False, "Module creation failed")
    
    def batch_generate(self):
        """Process batch generation"""
        self.log_signal.emit("Starting batch generation...")
        
        try:
            config = json.loads(self.kwargs.get("config_json", "{}"))
            validation = fullstack_generator.validate_batch_config(config)
            
            if not validation["isValid"]:
                self.log_signal.emit("❌ Validation failed:")
                for err in validation["errors"]:
                    self.log_signal.emit(f"   • {err}")
                self.finished_signal.emit(False, "Validation failed")
                return
            
            self.log_signal.emit(f"✅ Configuration validated. Found {len(config.get('modules', []))} modules")
            
            results = []
            total = len(config.get("modules", []))
            
            import inspect, asyncio
            process_func = fullstack_generator.process_batch_module
            for i, module in enumerate(config.get("modules", []), 1):
                self.log_signal.emit(f"\n📝 [{i}/{total}] Processing: {module['name']}")
                self.progress_signal.emit(int((i / total) * 100))
                args = (
                    module,
                    self.kwargs.get("backend_path"),
                    self.kwargs.get("frontend_path"),
                    self.kwargs.get("gen_backend", True),
                    self.kwargs.get("gen_frontend", True)
                )
                if inspect.iscoroutinefunction(process_func):
                    try:
                        loop = asyncio.get_event_loop()
                    except RuntimeError:
                        loop = asyncio.new_event_loop()
                        asyncio.set_event_loop(loop)
                    result = loop.run_until_complete(process_func(*args))
                else:
                    result = process_func(*args)
                results.append(result)
                if result["success"]:
                    self.log_signal.emit(f"✅ {module['name']} generated successfully")
                else:
                    self.log_signal.emit(f"❌ {module['name']} generation failed")
            
            # Summary
            success_count = sum(1 for r in results if r["success"])
            self.log_signal.emit(f"\n📊 Summary: {success_count}/{total} modules successful")
            
            if success_count == total:
                self.finished_signal.emit(True, f"All {total} modules generated successfully")
            else:
                self.finished_signal.emit(False, f"{success_count}/{total} modules successful")
                
        except Exception as e:
            self.log_signal.emit(f"❌ Batch generation error: {str(e)}")
            self.finished_signal.emit(False, str(e))

    def business_description_generate(self):
        """Generate modules from AI configuration provided via business description."""
        self.log_signal.emit("Starting Business Description AI generation...")
        try:
            config = json.loads(self.kwargs.get("config_json", "{}"))
            validation = fullstack_generator.validate_batch_config(config)

            if not validation["isValid"]:
                self.log_signal.emit("❌ Validation failed:")
                for err in validation["errors"]:
                    self.log_signal.emit(f"   • {err}")
                self.finished_signal.emit(False, "Validation failed")
                return

            self.log_signal.emit(f"✅ Configuration validated. Found {len(config.get('modules', []))} modules")

            results = []
            total = len(config.get("modules", []))

            import inspect, asyncio
            process_func = fullstack_generator.process_batch_module
            for i, module in enumerate(config.get("modules", []), 1):
                self.log_signal.emit(f"\n📝 [{i}/{total}] Processing: {module['name']}")
                self.progress_signal.emit(int((i / total) * 100))
                args = (
                    module,
                    self.kwargs.get("backend_path"),
                    self.kwargs.get("frontend_path"),
                    True,
                    True
                )
                if inspect.iscoroutinefunction(process_func):
                    try:
                        loop = asyncio.get_event_loop()
                    except RuntimeError:
                        loop = asyncio.new_event_loop()
                        asyncio.set_event_loop(loop)
                    result = loop.run_until_complete(process_func(*args))
                else:
                    result = process_func(*args)
                results.append(result)

            success_count = sum(1 for r in results if r["success"])
            self.log_signal.emit(f"\n📊 AI Generation Summary: {success_count}/{total} modules successful")

            if success_count == total:
                self.finished_signal.emit(True, f"AI generation completed: {success_count}/{total} successful")
            else:
                self.finished_signal.emit(False, f"AI generation partial: {success_count}/{total} successful")

        except Exception as e:
            self.log_signal.emit(f"❌ Business Description AI error: {str(e)}")
            self.log_signal.emit(traceback.format_exc())
            self.finished_signal.emit(False, str(e))
    
    def ai_clickup_generate(self):
        """AI & ClickUp mode generation"""
        self.log_signal.emit("Starting AI & ClickUp mode...")
        
        if not CLICKUP_AVAILABLE:
            self.log_signal.emit("❌ ClickUpIntegration module not available")
            self.finished_signal.emit(False, "ClickUp integration not available")
            return
        
        if not LLM_AVAILABLE:
            self.log_signal.emit("❌ LLMIntegaration module not available")
            self.finished_signal.emit(False, "LLM integration not available")
            return
        
        try:
            task_id = self.kwargs.get("task_id")
            self.log_signal.emit(f"Fetching ClickUp task: {task_id}")
            
            # Fetch from ClickUp
            task_data = get_task(task_id)
            self.log_signal.emit(f"✅ Task fetched: {task_data}")
            
            # Generate schema with AI
            self.log_signal.emit("🤖 Generating configuration with AI...")
            generated_config = generate_schema(task_data)
            self.log_signal.emit("✅ AI configuration generated")
            
            # Parse and process
            parsed_config = json.loads(generated_config)
            
            if "modules" not in parsed_config:
                self.log_signal.emit("❌ Generated config missing 'modules' array")
                self.finished_signal.emit(False, "Invalid AI response")
                return
            
            # Fix module names
            for module in parsed_config["modules"]:
                if "name" in module and " " in module["name"]:
                    old_name = module["name"]
                    module["name"] = module["name"].replace(" ", "")
                    self.log_signal.emit(f"⚠️  Fixed module name: '{old_name}' → '{module['name']}'")
            
            self.log_signal.emit(f"📦 Found {len(parsed_config['modules'])} modules")
            
            # Process each module
            results = []
            total = len(parsed_config["modules"])
            
            import inspect, asyncio
            process_func = fullstack_generator.process_batch_module
            for i, module in enumerate(parsed_config["modules"], 1):
                self.log_signal.emit(f"\n📝 [{i}/{total}] Processing: {module.get('name', 'Unnamed')}")
                self.progress_signal.emit(int((i / total) * 100))
                args = (
                    module,
                    self.kwargs.get("backend_path"),
                    self.kwargs.get("frontend_path"),
                    True,
                    True
                )
                if inspect.iscoroutinefunction(process_func):
                    try:
                        loop = asyncio.get_event_loop()
                    except RuntimeError:
                        loop = asyncio.new_event_loop()
                        asyncio.set_event_loop(loop)
                    result = loop.run_until_complete(process_func(*args))
                else:
                    result = process_func(*args)
                results.append(result)
            
            # Summary
            success_count = sum(1 for r in results if r["success"])
            self.log_signal.emit(f"\n📊 AI Generation Summary: {success_count}/{total} modules successful")
            
            if success_count == total:
                self.finished_signal.emit(True, f"AI generation completed: {success_count}/{total} successful")
            else:
                self.finished_signal.emit(False, f"AI generation partial: {success_count}/{total} successful")
                
        except Exception as e:
            self.log_signal.emit(f"❌ AI & ClickUp error: {str(e)}")
            self.log_signal.emit(traceback.format_exc())
            self.finished_signal.emit(False, str(e))
    
    def delete_module(self):
        """Delete a module"""
        self.log_signal.emit(f"Deleting module: {self.kwargs.get('module_name')}")
        
        try:
            import inspect, asyncio
            
            # احصل على دالة الحذف من الموديول
            delete_func = fullstack_generator.delete_module
            
            # تحقق إذا كانت الدالة async أم لا
            if inspect.iscoroutinefunction(delete_func):
                try:
                    loop = asyncio.get_event_loop()
                except RuntimeError:
                    loop = asyncio.new_event_loop()
                    asyncio.set_event_loop(loop)
                
                # استدعاء الدالة async
                results = loop.run_until_complete(delete_func(
                    self.kwargs.get("backend_path"),
                    self.kwargs.get("frontend_path"),
                    self.kwargs.get("module_name"),
                    self.kwargs.get("delete_backend", True),
                    self.kwargs.get("delete_frontend", True)
                ))
            else:
                # إذا لم تكن async، استدعها بشكل عادي
                results = delete_func(
                    self.kwargs.get("backend_path"),
                    self.kwargs.get("frontend_path"),
                    self.kwargs.get("module_name"),
                    self.kwargs.get("delete_backend", True),
                    self.kwargs.get("delete_frontend", True)
                )
            
            self.log_signal.emit("✅ Module deletion complete!")
            self.finished_signal.emit(True, "Module deleted successfully")
            
        except Exception as e:
            self.log_signal.emit(f"❌ Deletion error: {str(e)}")
            self.finished_signal.emit(False, str(e))

class FieldDialog(QDialog):
    """Dialog for editing a field - Improved Design"""
    def __init__(self, field_data=None, parent=None):
        super().__init__(parent)
        self.field_data = field_data or {}
        self.init_ui()
        
    def init_ui(self):
        self.setWindowTitle("Edit Field" if self.field_data else "Add Field")
        self.setModal(True)
        self.setMinimumWidth(650)
        self.setMinimumHeight(550)
        
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(0, 0, 0, 0)
        main_layout.setSpacing(0)
        
        # Header
        header_frame = QFrame()
        header_frame.setStyleSheet("""
            QFrame {
                background: qlineargradient(x1:0, y1:0, x2:1, y2:0,
                    stop:0 #2196F3, stop:1 #1976D2);
                padding: 20px;
                border-bottom: 1px solid #1976D2;
            }
        """)
        header_layout = QVBoxLayout(header_frame)
        
        title = QLabel("Edit Field" if self.field_data else "Add New Field")
        title.setStyleSheet("""
            QLabel {
                font-size: 20px;
                font-weight: bold;
                color: white;
                padding: 5px;
            }
        """)
        title.setAlignment(Qt.AlignCenter)
        
        subtitle = QLabel("Configure field properties and options")
        subtitle.setStyleSheet("""
            QLabel {
                font-size: 13px;
                color: rgba(255, 255, 255, 0.9);
                padding: 2px;
            }
        """)
        subtitle.setAlignment(Qt.AlignCenter)
        
        header_layout.addWidget(title)
        header_layout.addWidget(subtitle)
        main_layout.addWidget(header_frame)
        
        # Content area with scroll
        scroll_area = QScrollArea()
        scroll_area.setWidgetResizable(True)
        scroll_area.setFrameShape(QFrame.NoFrame)
        
        content_widget = QWidget()
        content_layout = QVBoxLayout(content_widget)
        content_layout.setContentsMargins(25, 25, 25, 25)
        content_layout.setSpacing(20)
        
        # Field configuration grid
        basic_group = QGroupBox("Basic Information")
        basic_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                border: 2px solid #E3F2FD;
                border-radius: 8px;
                margin-top: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 10px;
                padding: 0 10px 0 10px;
                color: #1976D2;
            }
        """)
        grid = QGridLayout()
        grid.setVerticalSpacing(15)
        grid.setHorizontalSpacing(20)
        
        # Field name
        name_label = QLabel("Field Name:")
        name_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(name_label, 0, 0, Qt.AlignRight)
        self.field_name = QLineEdit()
        self.field_name.setText(self.field_data.get("name", ""))
        self.field_name.setPlaceholderText("Enter field name (e.g., title, price)")
        grid.addWidget(self.field_name, 0, 1, 1, 2)
        
        # Field type
        type_label = QLabel("Field Type:")
        type_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(type_label, 1, 0, Qt.AlignRight)
        self.field_type = QComboBox()
        self.field_type.addItem("-- Select a field type --", None)
        self.field_type.addItems(sorted(fullstack_generator.FIELD_TYPES.keys()))
        if "type" in self.field_data:
            self.field_type.setCurrentText(self.field_data["type"])
        else:
            self.field_type.setCurrentIndex(0)
        self.field_type.currentTextChanged.connect(self.on_type_changed)
        grid.addWidget(self.field_type, 1, 1, 1, 2)
        
        # Display settings
        display_label = QLabel("Display Settings:")
        display_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(display_label, 2, 0, Qt.AlignRight)
        
        self.show_in_table = QCheckBox("Show in Table")
        self.show_in_table.setChecked(self.field_data.get("showInTable", True))
        self.show_in_table.setStyleSheet("padding: 5px;")
        grid.addWidget(self.show_in_table, 2, 1)
        
        # Column span
        col_label = QLabel("Column Span:")
        col_label.setStyleSheet("color: #616161;")
        grid.addWidget(col_label, 2, 2, Qt.AlignRight)
        self.col_span = QSpinBox()
        self.col_span.setRange(1, 12)
        self.col_span.setValue(self.field_data.get("col", 6))
        self.col_span.setStyleSheet("padding: 5px;")
        grid.addWidget(self.col_span, 2, 3)
        
        # Description
        desc_label = QLabel("Description:")
        desc_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(desc_label, 3, 0, Qt.AlignRight)
        self.description = QLineEdit()
        self.description.setText(self.field_data.get("description", ""))
        self.description.setPlaceholderText("Optional field description")
        grid.addWidget(self.description, 3, 1, 1, 2)
        
        basic_group.setLayout(grid)
        content_layout.addWidget(basic_group)
        
        # Options section (initially hidden)
        self.options_group = QGroupBox("Field Options")
        self.options_group.setVisible(False)
        self.options_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                border: 2px solid #F3E5F5;
                border-radius: 8px;
                margin-top: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 10px;
                padding: 0 10px 0 10px;
                color: #7B1FA2;
            }
        """)
        options_layout = QVBoxLayout()
        options_layout.setSpacing(15)
        
        # Options type selection
        type_frame = QFrame()
        type_frame.setStyleSheet("background-color: #F8F9FA; border-radius: 6px; padding: 10px;")
        type_layout = QHBoxLayout(type_frame)
        type_label = QLabel("Options Type:")
        type_label.setStyleSheet("font-weight: bold; color: #7B1FA2;")
        type_layout.addWidget(type_label)
        self.options_type = QComboBox()
        self.options_type.addItems(["Static", "Dynamic"])
        self.options_type.currentTextChanged.connect(self.on_options_type_changed)
        self.options_type.setStyleSheet("padding: 8px;")
        type_layout.addWidget(self.options_type)
        type_layout.addStretch()
        options_layout.addWidget(type_frame)
        
        # Static options
        self.static_options_widget = QWidget()
        static_layout = QVBoxLayout(self.static_options_widget)
        static_layout.setSpacing(10)
        
        static_header = QLabel("Static Options")
        static_header.setStyleSheet("font-weight: bold; color: #616161; font-size: 13px;")
        static_layout.addWidget(static_header)
        
        self.static_options_table = QTableWidget(0, 2)
        self.static_options_table.setHorizontalHeaderLabels(["ID", "Display Name"])
        self.static_options_table.horizontalHeader().setSectionResizeMode(QHeaderView.Stretch)
        self.static_options_table.setMinimumHeight(180)
        self.static_options_table.setStyleSheet("""
            QTableWidget {
                border: 1px solid #E0E0E0;
                border-radius: 6px;
                background-color: white;
            }
            QHeaderView::section {
                background-color: #F5F5F5;
                padding: 8px;
                border: none;
                font-weight: bold;
                color: #424242;
            }
        """)
        static_layout.addWidget(self.static_options_table)
        
        static_buttons = QHBoxLayout()
        static_buttons.setSpacing(10)
        btn_add_option = QPushButton("➕ Add Option")
        btn_add_option.setStyleSheet("""
            QPushButton {
                background-color: #4CAF50;
                color: white;
                padding: 8px 15px;
                border-radius: 4px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #43A047;
            }
        """)
        btn_add_option.clicked.connect(self.add_static_option)
        static_buttons.addWidget(btn_add_option)
        
        btn_remove_option = QPushButton("🗑️ Remove Selected")
        btn_remove_option.setStyleSheet("""
            QPushButton {
                background-color: #F44336;
                color: white;
                padding: 8px 15px;
                border-radius: 4px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #E53935;
            }
        """)
        btn_remove_option.clicked.connect(self.remove_static_option)
        static_buttons.addWidget(btn_remove_option)
        
        static_layout.addLayout(static_buttons)
        
        # Dynamic options
        self.dynamic_options_widget = QWidget()
        dynamic_layout = QGridLayout(self.dynamic_options_widget)
        dynamic_layout.setVerticalSpacing(12)
        dynamic_layout.setHorizontalSpacing(15)
        
        dynamic_header = QLabel("Dynamic Options Configuration")
        dynamic_header.setStyleSheet("font-weight: bold; color: #616161; font-size: 13px;")
        dynamic_layout.addWidget(dynamic_header, 0, 0, 1, 2)
        
        dynamic_layout.addWidget(QLabel("Module/API:"), 1, 0, Qt.AlignRight)
        self.dynamic_module = QLineEdit()
        self.dynamic_module.setPlaceholderText("Enter module name for dynamic options")
        dynamic_layout.addWidget(self.dynamic_module, 1, 1)
        
        dynamic_layout.addWidget(QLabel("Display Field:"), 2, 0, Qt.AlignRight)
        self.dynamic_option_label = QLineEdit()
        self.dynamic_option_label.setText("name")
        self.dynamic_option_label.setPlaceholderText("Field to display (e.g., name, title)")
        dynamic_layout.addWidget(self.dynamic_option_label, 2, 1)
        
        dynamic_layout.addWidget(QLabel("Value Field:"), 3, 0, Qt.AlignRight)
        self.dynamic_option_value = QLineEdit()
        self.dynamic_option_value.setText("id")
        self.dynamic_option_value.setPlaceholderText("Field for value (e.g., id)")
        dynamic_layout.addWidget(self.dynamic_option_value, 3, 1)
        
        options_layout.addWidget(self.static_options_widget)
        options_layout.addWidget(self.dynamic_options_widget)
        
        # Multiple selection
        self.multiple_checkbox = QCheckBox("Allow Multiple Selection")
        self.multiple_checkbox.setChecked(self.field_data.get("multiple", False))
        self.multiple_checkbox.setStyleSheet("""
            QCheckBox {
                padding: 10px;
                background-color: #FFF3E0;
                border-radius: 6px;
                font-weight: bold;
                color: #F57C00;
            }
        """)
        options_layout.addWidget(self.multiple_checkbox)
        
        self.options_group.setLayout(options_layout)
        content_layout.addWidget(self.options_group)
        
        content_layout.addStretch()
        
        scroll_area.setWidget(content_widget)
        main_layout.addWidget(scroll_area, 1)
        
        # Footer with buttons
        footer_frame = QFrame()
        footer_frame.setStyleSheet("""
            QFrame {
                background-color: #FAFAFA;
                border-top: 1px solid #E0E0E0;
                padding: 15px;
            }
        """)
        footer_layout = QHBoxLayout(footer_frame)
        footer_layout.addStretch()
        
        btn_cancel = QPushButton("Cancel")
        btn_cancel.setStyleSheet("""
            QPushButton {
                background-color: #757575;
                color: white;
                padding: 10px 25px;
                border-radius: 6px;
                font-weight: bold;
                min-width: 100px;
            }
            QPushButton:hover {
                background-color: #616161;
            }
        """)
        btn_cancel.clicked.connect(self.reject)
        footer_layout.addWidget(btn_cancel)
        
        btn_ok = QPushButton("Save Field")
        btn_ok.setStyleSheet("""
            QPushButton {
                background-color: #2196F3;
                color: white;
                padding: 10px 25px;
                border-radius: 6px;
                font-weight: bold;
                min-width: 120px;
            }
            QPushButton:hover {
                background-color: #1976D2;
            }
        """)
        btn_ok.clicked.connect(self.validate_and_accept)
        footer_layout.addWidget(btn_ok)
        
        main_layout.addWidget(footer_frame)
        
        # Load existing options if any
        if self.field_data:
            self.load_existing_options()
            
        # Set default visibility
        self.on_type_changed(self.field_type.currentText())
    
    def validate_and_accept(self):
        """Validate that a field type is selected before accepting"""
        if self.field_type.currentIndex() == 0:
            QMessageBox.warning(self, "Validation Error", "Please select a field type")
            return
        self.accept()
    
    def on_type_changed(self, field_type):
        """Show/hide options based on field type"""
        field_info = fullstack_generator.FIELD_TYPES.get(field_type, {})
        has_options = field_info.get("hasOptions", False)
        self.options_group.setVisible(has_options)
        self.multiple_checkbox.setVisible(has_options)
        
        # Update column span from field info
        if "col" in field_info:
            self.col_span.setValue(field_info["col"])
    
    def on_options_type_changed(self, options_type):
        """Switch between static and dynamic options"""
        is_static = options_type == "Static"
        self.static_options_widget.setVisible(is_static)
        self.dynamic_options_widget.setVisible(not is_static)
    
    def add_static_option(self):
        """Add a new row to static options table"""
        row = self.static_options_table.rowCount()
        self.static_options_table.insertRow(row)
        self.static_options_table.setItem(row, 0, QTableWidgetItem(f"option{row+1}"))
        self.static_options_table.setItem(row, 1, QTableWidgetItem(f"Option {row+1}"))
    
    def remove_static_option(self):
        """Remove selected rows from static options table"""
        selected = self.static_options_table.selectedItems()
        if selected:
            rows = set(item.row() for item in selected)
            for row in sorted(rows, reverse=True):
                self.static_options_table.removeRow(row)
    
    def load_existing_options(self):
            """Load existing options into the table."""
            self.static_options_table.setRowCount(0)
            
            options = self.field_data.get("options")
            
            if not options:
                return
            
            # إذا كان options سلسلة نصية (مثل "User")
            if isinstance(options, str):
                # تحويل السلسلة إلى قائمة بقاموس واحد
                options = [{"id": options.lower(), "name": options}]
            
            # إذا كان options قائمة
            if isinstance(options, list):
                for row, opt in enumerate(options):
                    self.static_options_table.insertRow(row)
                    
                    # إذا كان العنصر قاموساً
                    if isinstance(opt, dict):
                        self.static_options_table.setItem(row, 0, QTableWidgetItem(str(opt.get("id", ""))))
                        self.static_options_table.setItem(row, 1, QTableWidgetItem(str(opt.get("name", ""))))
                    # إذا كان العنصر سلسلة نصية
                    elif isinstance(opt, str):
                        self.static_options_table.setItem(row, 0, QTableWidgetItem(opt.lower()))
                        self.static_options_table.setItem(row, 1, QTableWidgetItem(opt))
                    # إذا كان العنصر أي نوع آخر
                    else:
                        self.static_options_table.setItem(row, 0, QTableWidgetItem(str(opt)))
                        self.static_options_table.setItem(row, 1, QTableWidgetItem(str(opt)))
                
    def get_field_data(self):
        """Get field configuration from dialog"""
        field_type = self.field_type.currentText()
        field_info = fullstack_generator.FIELD_TYPES.get(field_type, {})
        data = {
            "name": self.field_name.text(),
            "type": field_type,
            "description": self.description.text(),
            "showInTable": self.show_in_table.isChecked(),
            "col": self.col_span.value(),
            "formType": field_info.get("formType", "text")
        }
        # Add options if applicable
        if field_info.get("hasOptions", False):
            data["multiple"] = self.multiple_checkbox.isChecked()
            if self.options_type.currentText() == "Static":
                options = []
                for row in range(self.static_options_table.rowCount()):
                    id_item = self.static_options_table.item(row, 0)
                    name_item = self.static_options_table.item(row, 1)
                    if id_item and name_item:
                        options.append({
                            "id": id_item.text(),
                            "name": name_item.text()
                        })
                if options:
                    data["options"] = options
            else:
                # Dynamic options: set options to moduleName for frontend compatibility
                module_name = self.dynamic_module.text()
                data["isDynamic"] = True
                data["moduleName"] = module_name
                data["optionLabel"] = self.dynamic_option_label.text()
                data["optionValue"] = self.dynamic_option_value.text()
                # For frontend, set 'options' to moduleName if present
                if module_name:
                    data["options"] = module_name
        return data

class RelationshipDialog(QDialog):
    """Dialog for editing a relationship - Improved Design"""
    def __init__(self, relationship_data=None, parent=None):
        super().__init__(parent)
        self.relationship_data = relationship_data or {}
        self.init_ui()
        
    def init_ui(self):
        self.setWindowTitle("Edit Relationship" if self.relationship_data else "Add Relationship")
        self.setModal(True)
        self.setMinimumWidth(600)
        self.setMinimumHeight(550)
        
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(0, 0, 0, 0)
        main_layout.setSpacing(0)
        
        # Header
        header_frame = QFrame()
        header_frame.setStyleSheet("""
            QFrame {
                background: qlineargradient(x1:0, y1:0, x2:1, y2:0,
                    stop:0 #7B1FA2, stop:1 #6A1B9A);
                padding: 20px;
                border-bottom: 1px solid #6A1B9A;
            }
        """)
        header_layout = QVBoxLayout(header_frame)
        
        title = QLabel("Edit Relationship" if self.relationship_data else "Add New Relationship")
        title.setStyleSheet("""
            QLabel {
                font-size: 20px;
                font-weight: bold;
                color: white;
                padding: 5px;
            }
        """)
        title.setAlignment(Qt.AlignCenter)
        
        subtitle = QLabel("Define relationship between modules")
        subtitle.setStyleSheet("""
            QLabel {
                font-size: 13px;
                color: rgba(255, 255, 255, 0.9);
                padding: 2px;
            }
        """)
        subtitle.setAlignment(Qt.AlignCenter)
        
        header_layout.addWidget(title)
        header_layout.addWidget(subtitle)
        main_layout.addWidget(header_frame)
        
        # Content area with scroll
        scroll_area = QScrollArea()
        scroll_area.setWidgetResizable(True)
        scroll_area.setFrameShape(QFrame.NoFrame)
        
        content_widget = QWidget()
        content_layout = QVBoxLayout(content_widget)
        content_layout.setContentsMargins(25, 25, 25, 25)
        content_layout.setSpacing(20)
        
        # Relationship configuration grid
        basic_group = QGroupBox("Relationship Configuration")
        basic_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                border: 2px solid #F3E5F5;
                border-radius: 8px;
                margin-top: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 10px;
                padding: 0 10px 0 10px;
                color: #7B1FA2;
            }
        """)
        grid = QGridLayout()
        grid.setVerticalSpacing(15)
        grid.setHorizontalSpacing(20)
        
        # Relationship type
        type_label = QLabel("Relationship Type:")
        type_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(type_label, 0, 0, Qt.AlignRight)
        self.rel_type = QComboBox()
        self.rel_type.addItems(sorted(fullstack_generator.RELATIONSHIP_TYPES.keys()))
        if "type" in self.relationship_data:
            self.rel_type.setCurrentText(self.relationship_data["type"])
        self.rel_type.currentTextChanged.connect(self.on_type_changed)
        grid.addWidget(self.rel_type, 0, 1)
        
        # Related model
        model_label = QLabel("Related Model:")
        model_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(model_label, 1, 0, Qt.AlignRight)
        self.related_model = QLineEdit()
        self.related_model.setText(self.relationship_data.get("relatedModel", ""))
        self.related_model.setPlaceholderText("Enter model name (e.g., Category, User)")
        grid.addWidget(self.related_model, 1, 1)
        
        # Method name
        method_label = QLabel("Method Name:")
        method_label.setStyleSheet("font-weight: bold; color: #424242;")
        grid.addWidget(method_label, 2, 0, Qt.AlignRight)
        self.method_name = QLineEdit()
        self.method_name.setText(self.relationship_data.get("methodName", ""))
        self.method_name.setPlaceholderText("Method to access relationship")
        grid.addWidget(self.method_name, 2, 1)
        
        # Foreign key (for belongsTo)
        self.fk_widget = QWidget()
        fk_layout = QHBoxLayout(self.fk_widget)
        fk_label = QLabel("Foreign Key:")
        fk_label.setStyleSheet("font-weight: bold; color: #424242;")
        fk_layout.addWidget(fk_label)
        self.foreign_key = QLineEdit()
        self.foreign_key.setText(self.relationship_data.get("foreignKey", ""))
        self.foreign_key.setPlaceholderText("Foreign key column name")
        fk_layout.addWidget(self.foreign_key, 1)
        grid.addWidget(self.fk_widget, 3, 0, 1, 2)
        
        basic_group.setLayout(grid)
        content_layout.addWidget(basic_group)
        
        # Inverse relationship options
        self.inverse_group = QGroupBox("Inverse Relationship")
        self.inverse_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                border: 2px solid #E8F5E9;
                border-radius: 8px;
                margin-top: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 10px;
                padding: 0 10px 0 10px;
                color: #388E3C;
            }
        """)
        inverse_layout = QVBoxLayout()
        inverse_layout.setSpacing(15)
        
        self.add_inverse = QCheckBox("Create Inverse Relationship")
        self.add_inverse.setChecked(self.relationship_data.get("addInverse", False))
        self.add_inverse.setStyleSheet("""
            QCheckBox {
                padding: 10px;
                background-color: #E8F5E9;
                border-radius: 6px;
                font-weight: bold;
                color: #388E3C;
            }
        """)
        inverse_layout.addWidget(self.add_inverse)
        
        inverse_fields = QWidget()
        inverse_fields_layout = QGridLayout(inverse_fields)
        inverse_fields_layout.setVerticalSpacing(12)
        inverse_fields_layout.setHorizontalSpacing(15)
        
        inverse_fields_layout.addWidget(QLabel("Inverse Method:"), 0, 0, Qt.AlignRight)
        self.inverse_method = QLineEdit()
        self.inverse_method.setText(self.relationship_data.get("inverseMethod", ""))
        self.inverse_method.setPlaceholderText("Method name in related model")
        inverse_fields_layout.addWidget(self.inverse_method, 0, 1)
        
        inverse_fields_layout.addWidget(QLabel("Inverse Type:"), 1, 0, Qt.AlignRight)
        self.inverse_type = QComboBox()
        self.inverse_type.addItems(["belongsTo", "hasOne", "hasMany", "belongsToMany"])
        if "inverseType" in self.relationship_data:
            self.inverse_type.setCurrentText(self.relationship_data["inverseType"])
        inverse_fields_layout.addWidget(self.inverse_type, 1, 1)
        
        inverse_layout.addWidget(inverse_fields)
        
        self.inverse_group.setLayout(inverse_layout)
        content_layout.addWidget(self.inverse_group)
        
        # Description
        desc_group = QGroupBox("Description (Optional)")
        desc_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                border: 2px solid #FFF3E0;
                border-radius: 8px;
                margin-top: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 10px;
                padding: 0 10px 0 10px;
                color: #F57C00;
            }
        """)
        desc_layout = QVBoxLayout(desc_group)
        self.description = QTextEdit()
        self.description.setMaximumHeight(80)
        self.description.setText(self.relationship_data.get("description", ""))
        self.description.setPlaceholderText("Optional relationship description")
        self.description.setStyleSheet("""
            QTextEdit {
                border: 1px solid #FFE0B2;
                border-radius: 6px;
                padding: 8px;
            }
        """)
        desc_layout.addWidget(self.description)
        content_layout.addWidget(desc_group)
        
        content_layout.addStretch()
        
        scroll_area.setWidget(content_widget)
        main_layout.addWidget(scroll_area, 1)
        
        # Footer with buttons
        footer_frame = QFrame()
        footer_frame.setStyleSheet("""
            QFrame {
                background-color: #FAFAFA;
                border-top: 1px solid #E0E0E0;
                padding: 15px;
            }
        """)
        footer_layout = QHBoxLayout(footer_frame)
        footer_layout.addStretch()
        
        btn_cancel = QPushButton("Cancel")
        btn_cancel.setStyleSheet("""
            QPushButton {
                background-color: #757575;
                color: white;
                padding: 10px 25px;
                border-radius: 6px;
                font-weight: bold;
                min-width: 100px;
            }
            QPushButton:hover {
                background-color: #616161;
            }
        """)
        btn_cancel.clicked.connect(self.reject)
        footer_layout.addWidget(btn_cancel)
        
        btn_ok = QPushButton("Save Relationship")
        btn_ok.setStyleSheet("""
            QPushButton {
                background-color: #7B1FA2;
                color: white;
                padding: 10px 25px;
                border-radius: 6px;
                font-weight: bold;
                min-width: 120px;
            }
            QPushButton:hover {
                background-color: #6A1B9A;
            }
        """)
        btn_ok.clicked.connect(self.accept)
        footer_layout.addWidget(btn_ok)
        
        main_layout.addWidget(footer_frame)
        
        # Initial state
        self.on_type_changed(self.rel_type.currentText())
    
    def on_type_changed(self, rel_type):
        """Update UI based on relationship type"""
        # Show/hide foreign key field
        show_fk = rel_type == "belongsTo"
        self.fk_widget.setVisible(show_fk)
        
        # Set default foreign key if empty
        if show_fk and not self.foreign_key.text():
            related = self.related_model.text()
            if related:
                self.foreign_key.setText(f"{related.lower()}_id")
        
        # Set default method name if empty
        if not self.method_name.text():
            related = self.related_model.text()
            if related:
                self.method_name.setText(related.lower())
        
        # Set default inverse method if empty and inverse is checked
        if not self.inverse_method.text() and self.add_inverse.isChecked():
            self.inverse_method.setText(f"{self.parent().module_name.lower()}s")
    
    def get_relationship_data(self):
        """Get relationship configuration from dialog"""
        data = {
            "type": self.rel_type.currentText(),
            "relatedModel": self.related_model.text(),
            "methodName": self.method_name.text(),
            "description": self.description.toPlainText()
        }
        
        if self.rel_type.currentText() == "belongsTo":
            data["foreignKey"] = self.foreign_key.text()
        
        if self.add_inverse.isChecked():
            data["addInverse"] = True
            data["inverseMethod"] = self.inverse_method.text()
            data["inverseType"] = self.inverse_type.currentText()
        
        return data

class ModuleCreationTab(QWidget):
    """Tab for creating individual modules"""
    def __init__(self, parent=None):
        super().__init__(parent)
        self.fields = []
        self.relationships = []
        self.form_type = "page"  # Default to regular form
        self.init_ui()
        
    def init_ui(self):
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(20)
        
        # Module name
        name_group = QGroupBox("Module Information")
        name_layout = QGridLayout()
        name_layout.setVerticalSpacing(12)
        name_layout.setHorizontalSpacing(20)
        
        name_layout.addWidget(QLabel("Module Name:"), 0, 0, Qt.AlignRight)
        self.module_name = QLineEdit()
        self.module_name.textChanged.connect(self.on_module_name_changed)
        self.module_name.setPlaceholderText("Enter module name (e.g., Product, User)")
        name_layout.addWidget(self.module_name, 0, 1)
        
        name_group.setLayout(name_layout)
        main_layout.addWidget(name_group)
        
        # Paths and Generation Options in two columns
        options_frame = QFrame()
        options_layout = QHBoxLayout(options_frame)
        options_layout.setSpacing(20)
        
        # Left column - Paths
        paths_group = QGroupBox("Paths Configuration")
        paths_layout = QGridLayout()
        paths_layout.setVerticalSpacing(12)
        paths_layout.setHorizontalSpacing(15)
        
        paths_layout.addWidget(QLabel("Backend Path:"), 0, 0, Qt.AlignRight)
        self.backend_path = QLineEdit()
        self.backend_path.setText("../Backend")
        paths_layout.addWidget(self.backend_path, 0, 1)
        btn_backend = QPushButton("📁 Browse")
        btn_backend.clicked.connect(self.browse_backend)
        paths_layout.addWidget(btn_backend, 0, 2)
        
        paths_layout.addWidget(QLabel("Frontend Path:"), 1, 0, Qt.AlignRight)
        self.frontend_path = QLineEdit()
        self.frontend_path.setText("../Frontend")
        paths_layout.addWidget(self.frontend_path, 1, 1)
        btn_frontend = QPushButton("📁 Browse")
        btn_frontend.clicked.connect(self.browse_frontend)
        paths_layout.addWidget(btn_frontend, 1, 2)
        
        paths_group.setLayout(paths_layout)
        options_layout.addWidget(paths_group)
        
        # Right column - Generation Options
        gen_group = QGroupBox("Generation Options")
        gen_layout = QVBoxLayout()
        gen_layout.setSpacing(15)
        
        # Stack the two options vertically
        backend_check = QWidget()
        backend_layout = QHBoxLayout(backend_check)
        backend_layout.setContentsMargins(0, 0, 0, 0)
        self.gen_backend = QCheckBox("Generate Backend")
        self.gen_backend.setChecked(True)
        self.gen_backend.toggled.connect(self.on_generation_changed)
        backend_layout.addWidget(self.gen_backend)
        backend_layout.addStretch()
        gen_layout.addWidget(backend_check)
        
        frontend_check = QWidget()
        frontend_layout = QHBoxLayout(frontend_check)
        frontend_layout.setContentsMargins(0, 0, 0, 0)
        self.gen_frontend = QCheckBox("Generate Frontend")
        self.gen_frontend.setChecked(True)
        self.gen_frontend.toggled.connect(self.on_generation_changed)
        frontend_layout.addWidget(self.gen_frontend)
        frontend_layout.addStretch()
        gen_layout.addWidget(frontend_check)
        
        # Form Type Selection
        self.form_type_group = QGroupBox("Frontend Form Type")
        self.form_type_group.setVisible(True)
        form_type_layout = QVBoxLayout()
        form_type_layout.setSpacing(10)
        
        # Radio buttons for form type
        radio_layout = QHBoxLayout()
        self.form_type_regular = QRadioButton("📄 Regular Form")
        self.form_type_regular.setChecked(True)
        self.form_type_regular.toggled.connect(self.on_form_type_changed)
        
        self.form_type_dialog = QRadioButton("💬 Modal Dialog")
        self.form_type_dialog.toggled.connect(self.on_form_type_changed)
        
        radio_layout.addWidget(self.form_type_regular)
        radio_layout.addWidget(self.form_type_dialog)
        radio_layout.addStretch()
        
        form_type_layout.addLayout(radio_layout)
        self.form_type_group.setLayout(form_type_layout)
        gen_layout.addWidget(self.form_type_group)
        
        gen_group.setLayout(gen_layout)
        options_layout.addWidget(gen_group)
        
        main_layout.addWidget(options_frame)
        
        # Fields and Relationships in splitter
        splitter = QSplitter(Qt.Horizontal)
        splitter.setHandleWidth(2)
        
        # Fields section
        fields_widget = QWidget()
        fields_layout = QVBoxLayout(fields_widget)
        fields_layout.setContentsMargins(0, 0, 0, 0)
        fields_layout.setSpacing(10)
        
        fields_header = QFrame()
        fields_header.setStyleSheet("background-color: #2196F3; border-radius: 6px; padding: 12px;")
        header_layout = QHBoxLayout(fields_header)
        fields_title = QLabel("📋 Fields")
        fields_title.setStyleSheet("font-weight: bold; font-size: 16px; color: white;")
        header_layout.addWidget(fields_title)
        header_layout.addStretch()
        count_label = QLabel("0")
        count_label.setStyleSheet("font-weight: bold; font-size: 16px; color: white; background-color: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 12px;")
        header_layout.addWidget(count_label)
        self.fields_count_label = count_label
        fields_layout.addWidget(fields_header)
        
        self.fields_list = QListWidget()
        self.fields_list.itemDoubleClicked.connect(self.edit_field)
        self.fields_list.setMinimumHeight(250)
        self.fields_list.setStyleSheet("""
            QListWidget {
                border: 2px solid #E3F2FD;
                border-radius: 6px;
                padding: 5px;
            }
            QListWidget::item {
                padding: 10px;
                border-bottom: 1px solid #F5F5F5;
            }
            QListWidget::item:selected {
                background-color: #E3F2FD;
                color: #1976D2;
                border-radius: 4px;
            }
        """)
        fields_layout.addWidget(self.fields_list, 1)
        
        fields_buttons = QHBoxLayout()
        fields_buttons.setSpacing(10)
        btn_add_field = QPushButton("➕ Add Field")
        btn_add_field.setStyleSheet("""
            QPushButton {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #43A047;
            }
        """)
        btn_add_field.clicked.connect(self.add_field)
        fields_buttons.addWidget(btn_add_field)
        
        btn_edit_field = QPushButton("✏️ Edit")
        btn_edit_field.setStyleSheet("""
            QPushButton {
                background-color: #FF9800;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #F57C00;
            }
        """)
        btn_edit_field.clicked.connect(self.edit_field)
        fields_buttons.addWidget(btn_edit_field)
        
        btn_remove_field = QPushButton("🗑️ Remove")
        btn_remove_field.setStyleSheet("""
            QPushButton {
                background-color: #F44336;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #E53935;
            }
        """)
        btn_remove_field.clicked.connect(self.remove_field)
        fields_buttons.addWidget(btn_remove_field)
        
        fields_layout.addLayout(fields_buttons)
        splitter.addWidget(fields_widget)
        
        # Relationships section
        rel_widget = QWidget()
        rel_layout = QVBoxLayout(rel_widget)
        rel_layout.setContentsMargins(0, 0, 0, 0)
        rel_layout.setSpacing(10)
        
        rel_header = QFrame()
        rel_header.setStyleSheet("background-color: #7B1FA2; border-radius: 6px; padding: 12px;")
        rel_header_layout = QHBoxLayout(rel_header)
        rel_title = QLabel("🔗 Relationships")
        rel_title.setStyleSheet("font-weight: bold; font-size: 16px; color: white;")
        rel_header_layout.addWidget(rel_title)
        rel_header_layout.addStretch()
        rel_count_label = QLabel("0")
        rel_count_label.setStyleSheet("font-weight: bold; font-size: 16px; color: white; background-color: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 12px;")
        rel_header_layout.addWidget(rel_count_label)
        self.relationships_count_label = rel_count_label
        rel_layout.addWidget(rel_header)
        
        self.relationships_list = QListWidget()
        self.relationships_list.itemDoubleClicked.connect(self.edit_relationship)
        self.relationships_list.setMinimumHeight(250)
        self.relationships_list.setStyleSheet("""
            QListWidget {
                border: 2px solid #F3E5F5;
                border-radius: 6px;
                padding: 5px;
            }
            QListWidget::item {
                padding: 10px;
                border-bottom: 1px solid #F5F5F5;
            }
            QListWidget::item:selected {
                background-color: #F3E5F5;
                color: #7B1FA2;
                border-radius: 4px;
            }
        """)
        rel_layout.addWidget(self.relationships_list, 1)
        
        rel_buttons = QHBoxLayout()
        rel_buttons.setSpacing(10)
        btn_add_rel = QPushButton("➕ Add Relationship")
        btn_add_rel.setStyleSheet("""
            QPushButton {
                background-color: #7B1FA2;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #6A1B9A;
            }
        """)
        btn_add_rel.clicked.connect(self.add_relationship)
        rel_buttons.addWidget(btn_add_rel)
        
        btn_edit_rel = QPushButton("✏️ Edit")
        btn_edit_rel.setStyleSheet("""
            QPushButton {
                background-color: #FF9800;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #F57C00;
            }
        """)
        btn_edit_rel.clicked.connect(self.edit_relationship)
        rel_buttons.addWidget(btn_edit_rel)
        
        btn_remove_rel = QPushButton("🗑️ Remove")
        btn_remove_rel.setStyleSheet("""
            QPushButton {
                background-color: #F44336;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: bold;
            }
            QPushButton:hover {
                background-color: #E53935;
            }
        """)
        btn_remove_rel.clicked.connect(self.remove_relationship)
        rel_buttons.addWidget(btn_remove_rel)
        
        rel_layout.addLayout(rel_buttons)
        splitter.addWidget(rel_widget)
        
        splitter.setSizes([350, 350])
        main_layout.addWidget(splitter, 1)
        
        # Generate button
        self.btn_generate = QPushButton("🚀 Generate Module")
        self.btn_generate.clicked.connect(self.generate_module)
        self.btn_generate.setEnabled(False)
        self.btn_generate.setMinimumHeight(50)
        self.btn_generate.setStyleSheet("""
            QPushButton {
                background: qlineargradient(x1:0, y1:0, x2:1, y2:0,
                    stop:0 #2196F3, stop:1 #1976D2);
                color: white;
                font-weight: bold;
                font-size: 16px;
                padding: 12px;
                border-radius: 8px;
                border: none;
            }
            QPushButton:hover {
                background: qlineargradient(x1:0, y1:0, x2:1, y2:0,
                    stop:0 #1976D2, stop:1 #1565C0);
            }
            QPushButton:disabled {
                background: #BDBDBD;
                color: #757575;
            }
        """)
        main_layout.addWidget(self.btn_generate)
        
        # Initial state
        self.on_generation_changed()
        
    def on_module_name_changed(self, text):
        """Enable/disable generate button based on module name"""
        has_name = bool(text.strip())
        has_fields = len(self.fields) > 0
        self.btn_generate.setEnabled(has_name and has_fields)
        
    def on_generation_changed(self):
        """Show/hide form type section based on frontend generation"""
        show_form_type = self.gen_frontend.isChecked()
        self.form_type_group.setVisible(show_form_type)
        
    def on_form_type_changed(self):
        """Update form type based on selection"""
        if self.form_type_dialog.isChecked():
            self.form_type = "page-form-dialog"
        else:
            self.form_type = "page"
        
    def browse_backend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Backend Directory")
        if path:
            self.backend_path.setText(path)
            
    def browse_frontend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Frontend Directory")
        if path:
            self.frontend_path.setText(path)
            
    def add_field(self):
        dialog = FieldDialog(parent=self)
        if dialog.exec_() == QDialog.Accepted:
            field_data = dialog.get_field_data()
            if field_data["name"]:
                self.fields.append(field_data)
                self.update_fields_list()
                
    def edit_field(self):
        current_row = self.fields_list.currentRow()
        if current_row >= 0 and current_row < len(self.fields):
            dialog = FieldDialog(self.fields[current_row], self)
            if dialog.exec_() == QDialog.Accepted:
                field_data = dialog.get_field_data()
                if field_data["name"]:
                    self.fields[current_row] = field_data
                    self.update_fields_list()
                    
    def remove_field(self):
        current_row = self.fields_list.currentRow()
        if current_row >= 0:
            self.fields.pop(current_row)
            self.update_fields_list()
            
    def update_fields_list(self):
        self.fields_list.clear()
        for field in self.fields:
            desc = field.get('description', '')
            if desc:
                item_text = f"• {field['name']} ({field['type']}) - {desc}"
            else:
                item_text = f"• {field['name']} ({field['type']})"
            item = QListWidgetItem(item_text)
            self.fields_list.addItem(item)
        
        # Update count
        self.fields_count_label.setText(str(len(self.fields)))
        self.on_module_name_changed(self.module_name.text())
        
    def add_relationship(self):
        dialog = RelationshipDialog(parent=self)
        if dialog.exec_() == QDialog.Accepted:
            rel_data = dialog.get_relationship_data()
            if rel_data["relatedModel"]:
                self.relationships.append(rel_data)
                self.update_relationships_list()
                
    def edit_relationship(self):
        current_row = self.relationships_list.currentRow()
        if current_row >= 0 and current_row < len(self.relationships):
            dialog = RelationshipDialog(self.relationships[current_row], self)
            if dialog.exec_() == QDialog.Accepted:
                rel_data = dialog.get_relationship_data()
                if rel_data["relatedModel"]:
                    self.relationships[current_row] = rel_data
                    self.update_relationships_list()
                    
    def remove_relationship(self):
        current_row = self.relationships_list.currentRow()
        if current_row >= 0:
            self.relationships.pop(current_row)
            self.update_relationships_list()
            
    def update_relationships_list(self):
        self.relationships_list.clear()
        for rel in self.relationships:
            desc = rel.get('description', '')
            if desc:
                item_text = f"• {rel['methodName']}: {rel['type']} → {rel['relatedModel']} - {desc}"
            else:
                item_text = f"• {rel['methodName']}: {rel['type']} → {rel['relatedModel']}"
            item = QListWidgetItem(item_text)
            self.relationships_list.addItem(item)
        
        # Update count
        self.relationships_count_label.setText(str(len(self.relationships)))
        
    def generate_module(self):
        """Start module generation"""
        if not self.module_name.text().strip():
            QMessageBox.warning(self, "Warning", "Please enter a module name!")
            return
            
        if not self.fields:
            QMessageBox.warning(self, "Warning", "Please add at least one field!")
            return
        
        # Get form type
        if hasattr(self, 'form_type_dialog') and self.form_type_dialog.isChecked():
            form_type = "page-form-dialog"
            form_type_name = "Modal Dialog"
        elif hasattr(self, 'form_type_regular') and self.form_type_regular.isChecked():
            form_type = "page"
            form_type_name = "Regular Form"
        else:
            form_type = "page"
            form_type_name = "Regular Form"
        
        print(f"\n🔍 [GUI DEBUG] Module Generation Details:")
        print(f"   Module Name: {self.module_name.text()}")
        print(f"   Form Type Selected: {form_type} ({form_type_name})")
        print(f"   Generate Backend: {self.gen_backend.isChecked()}")
        print(f"   Generate Frontend: {self.gen_frontend.isChecked()}")
        print(f"   Fields Count: {len(self.fields)}")
        print(f"   Relationships Count: {len(self.relationships)}")
        print(f"   Backend Path: {self.backend_path.text()}")
        print(f"   Frontend Path: {self.frontend_path.text()}")
        
        # Confirm with user
        confirm_msg = f"""
        Generate module '{self.module_name.text()}'?
        
        • Backend: {'Yes' if self.gen_backend.isChecked() else 'No'}
        • Frontend: {'Yes' if self.gen_frontend.isChecked() else 'No'}
        • Form Type: {form_type_name}
        • Fields: {len(self.fields)}
        • Relationships: {len(self.relationships)}
        
        Continue?
        """
        
        reply = QMessageBox.question(
            self, 
            "Confirm Generation",
            confirm_msg,
            QMessageBox.Yes | QMessageBox.No,
            QMessageBox.Yes
        )
        
        if reply != QMessageBox.Yes:
            return
        
        LoaderManager.show_loader(self.window(), f"Creating {form_type_name} module...")   
        
        # Get main window
        main_window = self.window()
        if hasattr(main_window, 'start_generation'):
            main_window.start_generation(
                task_type="create_module",
                module_name=self.module_name.text(),
                fields=self.fields,
                relationships=self.relationships,
                backend_path=self.backend_path.text(),
                frontend_path=self.frontend_path.text(),
                gen_backend=self.gen_backend.isChecked(),
                gen_frontend=self.gen_frontend.isChecked(),
                form_type=form_type  # Pass form type
            )

class BatchModeTab(QWidget):
    """Tab for batch mode using JSON configuration"""
    def __init__(self, parent=None):
        super().__init__(parent)
        self.init_ui()
        
    def init_ui(self):
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(20)
        
        # Paths
        paths_group = QGroupBox("Paths")
        paths_layout = QGridLayout()
        paths_layout.setVerticalSpacing(12)
        paths_layout.setHorizontalSpacing(20)
        
        paths_layout.addWidget(QLabel("Backend Path:"), 0, 0, Qt.AlignRight)
        self.backend_path = QLineEdit()
        self.backend_path.setText("../Backend")
        paths_layout.addWidget(self.backend_path, 0, 1)
        btn_backend = QPushButton("📁 Browse")
        btn_backend.clicked.connect(self.browse_backend)
        paths_layout.addWidget(btn_backend, 0, 2)
        
        paths_layout.addWidget(QLabel("Frontend Path:"), 1, 0, Qt.AlignRight)
        self.frontend_path = QLineEdit()
        self.frontend_path.setText("../Frontend")
        paths_layout.addWidget(self.frontend_path, 1, 1)
        btn_frontend = QPushButton("📁 Browse")
        btn_frontend.clicked.connect(self.browse_frontend)
        paths_layout.addWidget(btn_frontend, 1, 2)
        
        paths_group.setLayout(paths_layout)
        main_layout.addWidget(paths_group)
        
        # JSON configuration
        config_group = QGroupBox("JSON Configuration")
        config_layout = QVBoxLayout()
        config_layout.setSpacing(15)
        
        self.json_editor = QTextEdit()
        self.json_editor.setPlaceholderText("Paste your JSON configuration here or load from file...")
        self.json_editor.setMinimumHeight(350)
        self.json_editor.setStyleSheet("""
            QTextEdit {
                font-family: 'Consolas', 'Monaco', monospace;
                font-size: 12px;
                border: 2px solid #E0E0E0;
                border-radius: 6px;
                padding: 10px;
            }
        """)
        config_layout.addWidget(self.json_editor)
        
        json_buttons = QHBoxLayout()
        json_buttons.setSpacing(10)
        btn_load = QPushButton("📂 Load JSON File...")
        btn_load.clicked.connect(self.load_json)
        json_buttons.addWidget(btn_load)
        
        btn_validate = QPushButton("✓ Validate JSON")
        btn_validate.clicked.connect(self.validate_json)
        json_buttons.addWidget(btn_validate)
        
        btn_example = QPushButton("📋 Load Example")
        btn_example.clicked.connect(self.load_example)
        json_buttons.addWidget(btn_example)
        
        btn_save = QPushButton("💾 Save JSON...")
        btn_save.clicked.connect(self.save_json)
        json_buttons.addWidget(btn_save)
        
        config_layout.addLayout(json_buttons)
        config_group.setLayout(config_layout)
        main_layout.addWidget(config_group, 1)
        
        # Stats and Generate area
        bottom_frame = QFrame()
        bottom_layout = QVBoxLayout(bottom_frame)
        bottom_layout.setSpacing(10)
        
        # Stats label
        self.stats_label = QLabel("No configuration loaded")
        self.stats_label.setStyleSheet("font-style: italic; color: #666; padding: 10px; background-color: #F5F5F5; border-radius: 6px;")
        bottom_layout.addWidget(self.stats_label)
        
        # Generate button
        self.btn_generate = QPushButton("🚀 Generate Batch")
        self.btn_generate.clicked.connect(self.generate_batch)
        self.btn_generate.setEnabled(False)
        self.btn_generate.setMinimumHeight(45)
        bottom_layout.addWidget(self.btn_generate)
        
        main_layout.addWidget(bottom_frame)
        
    def browse_backend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Backend Directory")
        if path:
            self.backend_path.setText(path)
            
    def browse_frontend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Frontend Directory")
        if path:
            self.frontend_path.setText(path)
            
    def load_json(self):
        file_path, _ = QFileDialog.getOpenFileName(
            self, "Open JSON File", "", 
            "JSON Files (*.json);;All Files (*)"
        )
        if file_path:
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                    self.json_editor.setText(content)
                    self.update_stats()
            except Exception as e:
                QMessageBox.critical(self, "Error", f"Failed to load file:\n{str(e)}")
                
    def save_json(self):
        if not self.json_editor.toPlainText().strip():
            QMessageBox.warning(self, "Warning", "No JSON content to save!")
            return
            
        file_path, _ = QFileDialog.getSaveFileName(
            self, "Save JSON File", "batch-config.json",
            "JSON Files (*.json);;All Files (*)"
        )
        if file_path:
            try:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(self.json_editor.toPlainText())
                QMessageBox.information(self, "Success", f"Configuration saved to:\n{file_path}")
            except Exception as e:
                QMessageBox.critical(self, "Error", f"Failed to save file:\n{str(e)}")
                
    def validate_json(self):
        try:
            config = json.loads(self.json_editor.toPlainText())
            validation = fullstack_generator.validate_batch_config(config)
            
            if validation["isValid"]:
                self.update_stats()
                QMessageBox.information(self, "Validation", "✅ JSON configuration is valid!")
                self.btn_generate.setEnabled(True)
            else:
                errors = "\n".join(validation["errors"])
                QMessageBox.warning(self, "Validation Errors", f"❌ Found errors:\n{errors}")
                self.btn_generate.setEnabled(False)
        except json.JSONDecodeError as e:
            QMessageBox.critical(self, "Invalid JSON", f"❌ JSON parsing error:\n{str(e)}")
            self.btn_generate.setEnabled(False)
        except Exception as e:
            QMessageBox.critical(self, "Error", f"❌ Validation error:\n{str(e)}")
            self.btn_generate.setEnabled(False)
            
    def load_example(self):
        example_config = {
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
        
        self.json_editor.setText(json.dumps(example_config, indent=2))
        self.update_stats()
        
    def update_stats(self):
        """Update statistics label based on JSON content"""
        try:
            config = json.loads(self.json_editor.toPlainText())
            modules = config.get("modules", [])
            total_fields = sum(len(m.get("fields", [])) for m in modules)
            total_relationships = sum(len(m.get("relationships", [])) for m in modules)
            
            self.stats_label.setText(
                f"📊 Statistics: {len(modules)} modules, "
                f"{total_fields} fields, "
                f"{total_relationships} relationships"
            )
            self.btn_generate.setEnabled(len(modules) > 0)
        except:
            self.stats_label.setText("Invalid JSON configuration")
            self.btn_generate.setEnabled(False)
            
    def generate_batch(self):
        """Start batch generation"""
        if not self.json_editor.toPlainText().strip():
            QMessageBox.warning(self, "Warning", "Please provide JSON configuration!")
            return
            
        LoaderManager.show_loader(self.window(), "Processing batch generation...")
        
        # Get main window
        main_window = self.window()
        if hasattr(main_window, 'start_generation'):
            main_window.start_generation(
                task_type="batch",
                config_json=self.json_editor.toPlainText(),
                backend_path=self.backend_path.text(),
                frontend_path=self.frontend_path.text(),
                gen_backend=True,
                gen_frontend=True
            )

class AIClickUpTab(QWidget):
    """Tab for AI & ClickUp mode"""
    def __init__(self, parent=None):
        super().__init__(parent)
        self.init_ui()
        
    def init_ui(self):
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(20)
        
        # Status indicators
        status_group = QGroupBox("Integration Status")
        status_layout = QGridLayout()
        status_layout.setVerticalSpacing(10)
        status_layout.setHorizontalSpacing(20)
        
        self.clickup_status = QLabel("❌ ClickUp: Not available")
        if CLICKUP_AVAILABLE:
            self.clickup_status.setText("✅ ClickUp: Available")
            self.clickup_status.setStyleSheet("color: #2e7d32; font-weight: bold;")
        status_layout.addWidget(self.clickup_status, 0, 0)
        
        self.llm_status = QLabel("❌ AI: Not available")
        if LLM_AVAILABLE:
            self.llm_status.setText("✅ AI: Available")
            self.llm_status.setStyleSheet("color: #2e7d32; font-weight: bold;")
        status_layout.addWidget(self.llm_status, 0, 1)
        
        status_group.setLayout(status_layout)
        main_layout.addWidget(status_group)
        
        # Paths
        paths_group = QGroupBox("Paths")
        paths_layout = QGridLayout()
        paths_layout.setVerticalSpacing(12)
        paths_layout.setHorizontalSpacing(20)
        
        paths_layout.addWidget(QLabel("Backend Path:"), 0, 0, Qt.AlignRight)
        self.backend_path = QLineEdit()
        self.backend_path.setText("../Backend")
        paths_layout.addWidget(self.backend_path, 0, 1)
        btn_backend = QPushButton("📁 Browse")
        btn_backend.clicked.connect(self.browse_backend)
        paths_layout.addWidget(btn_backend, 0, 2)
        
        paths_layout.addWidget(QLabel("Frontend Path:"), 1, 0, Qt.AlignRight)
        self.frontend_path = QLineEdit()
        self.frontend_path.setText("../Frontend")
        paths_layout.addWidget(self.frontend_path, 1, 1)
        btn_frontend = QPushButton("📁 Browse")
        btn_frontend.clicked.connect(self.browse_frontend)
        paths_layout.addWidget(btn_frontend, 1, 2)
        
        paths_group.setLayout(paths_layout)
        main_layout.addWidget(paths_group)
        
        # ClickUp Task
        task_group = QGroupBox("ClickUp Task")
        task_layout = QVBoxLayout()
        task_layout.setSpacing(12)
        
        task_layout.addWidget(QLabel("ClickUp Task ID:"))
        self.task_id = QLineEdit()
        self.task_id.setPlaceholderText("Enter ClickUp Task ID (e.g., 123abc)")
        task_layout.addWidget(self.task_id)
        
        task_buttons = QHBoxLayout()
        task_buttons.setSpacing(10)
        self.btn_fetch = QPushButton("🔍 Fetch Task")
        self.btn_fetch.clicked.connect(self.fetch_task)
        self.btn_fetch.setEnabled(CLICKUP_AVAILABLE)
        task_buttons.addWidget(self.btn_fetch)
        
        task_layout.addLayout(task_buttons)
        
        # Task preview
        task_layout.addWidget(QLabel("Task Preview:"))
        self.task_preview = QTextEdit()
        self.task_preview.setMaximumHeight(120)
        self.task_preview.setReadOnly(True)
        task_layout.addWidget(self.task_preview)
        
        task_group.setLayout(task_layout)
        main_layout.addWidget(task_group)
        
        # AI Configuration Preview
        config_group = QGroupBox("AI Configuration Preview")
        config_layout = QVBoxLayout()
        config_layout.setSpacing(12)
        
        self.config_preview = QTextEdit()
        self.config_preview.setReadOnly(True)
        self.config_preview.setMinimumHeight(200)
        config_layout.addWidget(self.config_preview)
        
        config_buttons = QHBoxLayout()
        config_buttons.setSpacing(10)
        self.btn_generate_config = QPushButton("🤖 Generate with AI")
        self.btn_generate_config.clicked.connect(self.generate_ai_config)
        self.btn_generate_config.setEnabled(LLM_AVAILABLE and CLICKUP_AVAILABLE)
        config_buttons.addWidget(self.btn_generate_config)
        
        btn_save_config = QPushButton("💾 Save Config...")
        btn_save_config.clicked.connect(self.save_config)
        config_buttons.addWidget(btn_save_config)
        
        config_layout.addLayout(config_buttons)
        config_group.setLayout(config_layout)
        main_layout.addWidget(config_group, 1)
        
        # Generate button
        self.btn_generate = QPushButton("🚀 Generate from AI Configuration")
        self.btn_generate.clicked.connect(self.generate_from_ai)
        self.btn_generate.setEnabled(False)
        self.btn_generate.setMinimumHeight(45)
        main_layout.addWidget(self.btn_generate)

    def browse_backend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Backend Directory")
        if path:
            self.backend_path.setText(path)
            
    def browse_frontend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Frontend Directory")
        if path:
            self.frontend_path.setText(path)
            
    def fetch_task(self):
        """Fetch task from ClickUp"""
        task_id = self.task_id.text().strip()
        if not task_id:
            QMessageBox.warning(self, "Warning", "Please enter a ClickUp Task ID!")
            return
           
        LoaderManager.show_loader(self.window(), "Fetching ClickUp task...")
         
        # Get main window to log
        main_window = self.window()
        if hasattr(main_window, 'log_output'):
            main_window.log_output.append(f"Fetching ClickUp task: {task_id}")
        
        try:
            task_data = get_task(task_id)
            self.task_preview.setText(str(task_data))
            self.btn_generate_config.setEnabled(True)
            
            if hasattr(main_window, 'log_output'):
                main_window.log_output.append(f"✅ Task fetched successfully")
                LoaderManager.hide_loader()
                
        except Exception as e:
            QMessageBox.critical(self, "Error", f"Failed to fetch task:\n{str(e)}")
            LoaderManager.hide_loader()
            if hasattr(main_window, 'log_output'):
                LoaderManager.hide_loader()
                main_window.log_output.append(f"❌ Failed to fetch task: {str(e)}")
                
    def generate_ai_config(self):
        """Generate configuration using AI"""
        task_data = self.task_preview.toPlainText()
        if not task_data:
            QMessageBox.warning(self, "Warning", "No task data to process!")
            return
            
        LoaderManager.show_loader(self.window(), "Generating AI configuration...")
        
        # Get main window to log
        main_window = self.window()
        if hasattr(main_window, 'log_output'):
            main_window.log_output.append("🤖 Generating configuration with AI...")
        
        try:
            generated_config = generate_schema(task_data)
            
            # Pretty print the JSON
            parsed = json.loads(generated_config)
            pretty_config = json.dumps(parsed, indent=2)
            self.config_preview.setText(pretty_config)
            self.btn_generate.setEnabled(True)
            
            if hasattr(main_window, 'log_output'):
                main_window.log_output.append("✅ AI configuration generated successfully")
                
        except Exception as e:
            QMessageBox.critical(self, "Error", f"AI generation failed:\n{str(e)}")
            if hasattr(main_window, 'log_output'):
                main_window.log_output.append(f"❌ AI generation failed: {str(e)}")
        
        LoaderManager.hide_loader()
        
    def save_config(self):
        """Save AI-generated configuration to file"""
        config_text = self.config_preview.toPlainText()
        if not config_text.strip():
            QMessageBox.warning(self, "Warning", "No configuration to save!")
            return
            
        file_path, _ = QFileDialog.getSaveFileName(
            self, "Save AI Configuration", "ai-config.json",
            "JSON Files (*.json);;All Files (*)"
        )
        if file_path:
            try:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(config_text)
                QMessageBox.information(self, "Success", f"Configuration saved to:\n{file_path}")
            except Exception as e:
                QMessageBox.critical(self, "Error", f"Failed to save file:\n{str(e)}")
                
    def generate_from_ai(self):
        """Start generation from AI configuration"""
        config_text = self.config_preview.toPlainText()
        if not config_text.strip():
            QMessageBox.warning(self, "Warning", "No AI configuration available!")
            return
            
        LoaderManager.show_loader(self.window(), "Starting AI generation...")
        
        # Get main window
        main_window = self.window()
        if hasattr(main_window, 'start_generation'):
            main_window.start_generation(
                task_type="ai_clickup",
                task_id=self.task_id.text(),
                backend_path=self.backend_path.text(),
                frontend_path=self.frontend_path.text()
            )

class BusinessDescriptionTab(QWidget):
    """Tab for Business Description -> AI generation"""
    def __init__(self, parent=None):
        super().__init__(parent)
        self.init_ui()

    def init_ui(self):
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(20)

        # Status
        status_group = QGroupBox("AI Status")
        status_layout = QHBoxLayout()
        self.llm_status = QLabel("❌ AI: Not available")
        if LLM_AVAILABLE:
            self.llm_status.setText("✅ AI: Available")
            self.llm_status.setStyleSheet("color: #2e7d32; font-weight: bold;")
        status_layout.addWidget(self.llm_status)
        status_group.setLayout(status_layout)
        main_layout.addWidget(status_group)

        # Paths
        paths_group = QGroupBox("Paths")
        paths_layout = QGridLayout()
        paths_layout.setVerticalSpacing(12)
        paths_layout.setHorizontalSpacing(20)
        paths_layout.addWidget(QLabel("Backend Path:"), 0, 0, Qt.AlignRight)
        self.backend_path = QLineEdit()
        self.backend_path.setText("../Backend")
        paths_layout.addWidget(self.backend_path, 0, 1)
        btn_backend = QPushButton("📁 Browse")
        btn_backend.clicked.connect(self.browse_backend)
        paths_layout.addWidget(btn_backend, 0, 2)

        paths_layout.addWidget(QLabel("Frontend Path:"), 1, 0, Qt.AlignRight)
        self.frontend_path = QLineEdit()
        self.frontend_path.setText("../Frontend")
        paths_layout.addWidget(self.frontend_path, 1, 1)
        btn_frontend = QPushButton("📁 Browse")
        btn_frontend.clicked.connect(self.browse_frontend)
        paths_layout.addWidget(btn_frontend, 1, 2)

        paths_group.setLayout(paths_layout)
        main_layout.addWidget(paths_group)

        # Business Description input
        desc_group = QGroupBox("Business Description")
        desc_layout = QVBoxLayout()
        desc_layout.setSpacing(12)
        desc_layout.addWidget(QLabel("Enter the business/task description:"))
        self.business_description = QTextEdit()
        self.business_description.setMinimumHeight(150)
        self.business_description.setPlaceholderText("Describe your business requirements or task details...")
        desc_layout.addWidget(self.business_description)
        desc_group.setLayout(desc_layout)
        main_layout.addWidget(desc_group)

        # AI Configuration Preview
        config_group = QGroupBox("AI Configuration Preview")
        config_layout = QVBoxLayout()
        config_layout.setSpacing(12)
        self.config_preview = QTextEdit()
        self.config_preview.setReadOnly(False)
        self.config_preview.setMinimumHeight(200)
        config_layout.addWidget(self.config_preview)

        config_buttons = QHBoxLayout()
        config_buttons.setSpacing(10)
        self.btn_generate_config = QPushButton("🤖 Generate with AI")
        self.btn_generate_config.clicked.connect(self.generate_ai_config)
        self.btn_generate_config.setEnabled(LLM_AVAILABLE)
        config_buttons.addWidget(self.btn_generate_config)

        btn_save_config = QPushButton("💾 Save Config...")
        btn_save_config.clicked.connect(self.save_config)
        config_buttons.addWidget(btn_save_config)

        config_group.setLayout(config_layout)
        config_layout.addLayout(config_buttons)
        main_layout.addWidget(config_group, 1)

        # Generate button
        self.btn_generate = QPushButton("🚀 Generate from AI Configuration")
        self.btn_generate.clicked.connect(self.generate_from_ai)
        self.btn_generate.setEnabled(False)
        self.btn_generate.setMinimumHeight(45)
        main_layout.addWidget(self.btn_generate)

    def browse_backend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Backend Directory")
        if path:
            self.backend_path.setText(path)

    def browse_frontend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Frontend Directory")
        if path:
            self.frontend_path.setText(path)

    def generate_ai_config(self):
        desc = self.business_description.toPlainText().strip()
        if not desc:
            QMessageBox.warning(self, "Warning", "Please enter a business description first!")
            return

        LoaderManager.show_loader(self.window(), "Generating AI configuration...")
        main_window = self.window()
        if hasattr(main_window, 'log_output'):
            main_window.log_output.append("🤖 Generating configuration with AI from business description...")

        try:
            generated_config = generate_schema(desc)
            parsed = json.loads(generated_config)
            pretty = json.dumps(parsed, indent=2)
            self.config_preview.setText(pretty)
            self.btn_generate.setEnabled(True)
            if hasattr(main_window, 'log_output'):
                main_window.log_output.append("✅ AI configuration generated successfully")
        except Exception as e:
            QMessageBox.critical(self, "Error", f"AI generation failed:\n{str(e)}")
            if hasattr(main_window, 'log_output'):
                main_window.log_output.append(f"❌ AI generation failed: {str(e)}")
        LoaderManager.hide_loader()

    def save_config(self):
        config_text = self.config_preview.toPlainText()
        if not config_text.strip():
            QMessageBox.warning(self, "Warning", "No configuration to save!")
            return
        file_path, _ = QFileDialog.getSaveFileName(self, "Save AI Configuration", "ai-config.json", "JSON Files (*.json);;All Files (*)")
        if file_path:
            try:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(config_text)
                QMessageBox.information(self, "Success", f"Configuration saved to:\n{file_path}")
            except Exception as e:
                QMessageBox.critical(self, "Error", f"Failed to save file:\n{str(e)}")

    def generate_from_ai(self):
        config_text = self.config_preview.toPlainText()
        if not config_text.strip():
            QMessageBox.warning(self, "Warning", "No AI configuration available!")
            return
        LoaderManager.show_loader(self.window(), "Starting AI generation...")
        main_window = self.window()
        if hasattr(main_window, 'start_generation'):
            main_window.start_generation(
                task_type="business_description",
                config_json=config_text,
                backend_path=self.backend_path.text(),
                frontend_path=self.frontend_path.text()
            )

class DeleteModuleTab(QWidget):
    """Tab for deleting modules"""
    def __init__(self, parent=None):
        super().__init__(parent)
        self.init_ui()
        
    def init_ui(self):
        main_layout = QVBoxLayout(self)
        main_layout.setContentsMargins(20, 20, 20, 20)
        main_layout.setSpacing(20)
        
        # Paths
        paths_group = QGroupBox("Paths")
        paths_layout = QGridLayout()
        paths_layout.setVerticalSpacing(12)
        paths_layout.setHorizontalSpacing(20)
        
        paths_layout.addWidget(QLabel("Backend Path:"), 0, 0, Qt.AlignRight)
        self.backend_path = QLineEdit()
        self.backend_path.setText("../Backend")
        paths_layout.addWidget(self.backend_path, 0, 1)
        btn_backend = QPushButton("📁 Browse")
        btn_backend.clicked.connect(self.browse_backend)
        paths_layout.addWidget(btn_backend, 0, 2)
        
        paths_layout.addWidget(QLabel("Frontend Path:"), 1, 0, Qt.AlignRight)
        self.frontend_path = QLineEdit()
        self.frontend_path.setText("../Frontend")
        paths_layout.addWidget(self.frontend_path, 1, 1)
        btn_frontend = QPushButton("📁 Browse")
        btn_frontend.clicked.connect(self.browse_frontend)
        paths_layout.addWidget(btn_frontend, 1, 2)
        
        paths_group.setLayout(paths_layout)
        main_layout.addWidget(paths_group)
        
        # Module to delete
        module_group = QGroupBox("Module Selection")
        module_layout = QVBoxLayout()
        module_layout.setSpacing(15)
        
        module_layout.addWidget(QLabel("Module Name to Delete:"))
        self.module_name = QLineEdit()
        self.module_name.setPlaceholderText("Enter module name to delete")
        module_layout.addWidget(self.module_name)
        
        # Options
        options_layout = QHBoxLayout()
        options_layout.setSpacing(30)
        self.delete_backend = QCheckBox("Delete Backend Files")
        self.delete_backend.setChecked(True)
        options_layout.addWidget(self.delete_backend)
        
        self.delete_frontend = QCheckBox("Delete Frontend Files")
        self.delete_frontend.setChecked(True)
        options_layout.addWidget(self.delete_frontend)
        
        module_layout.addLayout(options_layout)
        module_group.setLayout(module_layout)
        main_layout.addWidget(module_group)
        
        # Warning
        warning_frame = QFrame()
        warning_frame.setStyleSheet("""
            QFrame {
                background-color: #FFEBEE;
                border: 2px solid #F44336;
                border-radius: 8px;
                padding: 20px;
            }
        """)
        warning_layout = QVBoxLayout(warning_frame)
        
        warning_label = QLabel("⚠️  WARNING: This action is permanent and cannot be undone!")
        warning_label.setStyleSheet("color: #d32f2f; font-weight: bold; font-size: 14px;")
        warning_label.setAlignment(Qt.AlignCenter)
        
        warning_details = QLabel("All selected files will be permanently deleted from the system.")
        warning_details.setStyleSheet("color: #d32f2f; font-size: 12px;")
        warning_details.setAlignment(Qt.AlignCenter)
        
        warning_layout.addWidget(warning_label)
        warning_layout.addWidget(warning_details)
        main_layout.addWidget(warning_frame)
        
        # Delete button
        self.btn_delete = QPushButton("🗑️  Delete Module")
        self.btn_delete.clicked.connect(self.delete_module)
        self.btn_delete.setEnabled(False)
        self.btn_delete.setMinimumHeight(50)
        self.btn_delete.setStyleSheet("""
            QPushButton {
                background-color: #F44336;
                color: white;
                font-weight: bold;
                font-size: 16px;
                padding: 12px;
                border-radius: 8px;
                border: none;
            }
            QPushButton:hover {
                background-color: #E53935;
            }
            QPushButton:disabled {
                background-color: #BDBDBD;
                color: #757575;
            }
        """)
        main_layout.addWidget(self.btn_delete)
        
        # Connect module name change to enable/disable button
        self.module_name.textChanged.connect(self.on_module_name_changed)
        
    def on_module_name_changed(self, text):
        """Enable/disable delete button based on module name"""
        self.btn_delete.setEnabled(bool(text.strip()))
        
    def browse_backend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Backend Directory")
        if path:
            self.backend_path.setText(path)
            
    def browse_frontend(self):
        path = QFileDialog.getExistingDirectory(self, "Select Frontend Directory")
        if path:
            self.frontend_path.setText(path)
            
    def delete_module(self):
        """Confirm and start module deletion"""
        module_name = self.module_name.text().strip()
        if not module_name:
            QMessageBox.warning(self, "Warning", "Please enter a module name!")
            return
            
        # Confirmation dialog
        reply = QMessageBox.question(
            self, 
            "Confirm Deletion",
            f"Are you ABSOLUTELY sure you want to delete module '{module_name}'?\n\n"
            f"This will permanently delete:\n"
            f"• Backend files: {'YES' if self.delete_backend.isChecked() else 'NO'}\n"
            f"• Frontend files: {'YES' if self.delete_frontend.isChecked() else 'NO'}\n\n"
            f"This action cannot be undone!",
            QMessageBox.Yes | QMessageBox.No,
            QMessageBox.No
        )
        
        if reply == QMessageBox.Yes:
            # Get main window
            LoaderManager.show_loader(self.window(), "Deleting module...")
            
            main_window = self.window()
            if hasattr(main_window, 'start_generation'):
                main_window.start_generation(
                    task_type="delete",
                    module_name=module_name,
                    backend_path=self.backend_path.text(),
                    frontend_path=self.frontend_path.text(),
                    delete_backend=self.delete_backend.isChecked(),
                    delete_frontend=self.delete_frontend.isChecked()
                )

class MainWindow(QMainWindow):
    """Main application window with fixed header"""
    def __init__(self):
        super().__init__()
        self.generation_thread = None
        self.init_ui()
        
    def init_ui(self):
        self.setWindowTitle("🚀 Full Stack Module Generator GUI")
        self.setGeometry(100, 100, 1200, 800)
        
        # Create central widget
        central_widget = QWidget()
        self.setCentralWidget(central_widget)
        
        # Main layout
        main_layout = QVBoxLayout(central_widget)
        main_layout.setContentsMargins(0, 0, 0, 0)
        main_layout.setSpacing(0)
        
        # Fixed Header Section
        header_frame = QFrame()
        header_frame.setFixedHeight(180)
        header_frame.setStyleSheet("""
            QFrame {
                background: qlineargradient(x1:0, y1:0, x2:1, y2:0,
                    stop:0 #2196F3, stop:0.5 #9C27B0, stop:1 #2196F3);
            }
        """)
        header_layout = QVBoxLayout(header_frame)
        header_layout.setContentsMargins(30, 20, 30, 20)
        # header_layout.setSpacing(15)
        
        # Title
        title = QLabel("🚀 Full Stack Module Generator")
        title.setStyleSheet("""
            QLabel {
                font-size: 32px;
                font-weight: bold;
                color: white;
                padding: 10px;
            }
        """)
        title.setAlignment(Qt.AlignCenter)
        
        # Subtitle
        subtitle = QLabel("Create modules for your full-stack application with ease")
        subtitle.setStyleSheet("""
            QLabel {
                font-size: 16px;
                color: rgba(255, 255, 255, 0.9);
                padding: 5px;
            }
        """)
        subtitle.setAlignment(Qt.AlignCenter)
        
        header_layout.addWidget(title)
        header_layout.addWidget(subtitle)
        
        # Add some spacing
        # header_layout.addSpacing(10)
        
        # Navigation tabs (simulated as buttons for fixed header)
        nav_frame = QFrame()
        nav_frame.setStyleSheet("""
            QFrame {
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                padding: 5px;
            }
        """)
        # nav_layout = QHBoxLayout(nav_frame)
        # nav_layout.setSpacing(5)
        
        # We'll use a QTabWidget for the actual tabs, but show navigation in header
        # nav_label = QLabel("Navigate:")
        # nav_label.setStyleSheet("color: white; font-weight: bold; padding: 8px;")
        # nav_layout.addWidget(nav_label)
        # nav_layout.addStretch()
        
        # header_layout.addWidget(nav_frame)
        main_layout.addWidget(header_frame)
        
        # Main content area with scroll
        content_widget = QWidget()
        content_layout = QVBoxLayout(content_widget)
        content_layout.setContentsMargins(0, 0, 0, 0)
        content_layout.setSpacing(0)
        
        # Create tab widget for main content
        self.tabs = QTabWidget()
        self.tabs.setDocumentMode(True)
        self.tabs.setTabPosition(QTabWidget.North)
        
        # Add tabs
        self.tabs.addTab(ModuleCreationTab(), "✨ Create Module")
        self.tabs.addTab(BatchModeTab(), "📦 Batch Mode")
        self.tabs.addTab(AIClickUpTab(), "🤖 AI & ClickUp")
        self.tabs.addTab(BusinessDescriptionTab(), "💼 Business Description")
        self.tabs.addTab(DeleteModuleTab(), "🗑️ Delete Module")
        
        content_layout.addWidget(self.tabs)
        
        # Progress bar
        self.progress_bar = QProgressBar()
        self.progress_bar.setVisible(False)
        self.progress_bar.setTextVisible(True)
        content_layout.addWidget(self.progress_bar)
        
        # Log output area
        log_group = QGroupBox("📝 Log Output")
        log_group.setStyleSheet("""
            QGroupBox {
                font-weight: bold;
                font-size: 14px;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin: 10px;
                padding-top: 15px;
                background-color: white;
            }
            QGroupBox::title {
                subcontrol-origin: margin;
                left: 12px;
                padding: 0 8px 0 8px;
                color: #424242;
            }
        """)
        log_layout = QVBoxLayout()
        log_layout.setSpacing(10)
        
        self.log_output = QTextEdit()
        self.log_output.setReadOnly(True)
        self.log_output.setMaximumHeight(180)
        self.log_output.setStyleSheet("""
            QTextEdit {
                background-color: #fafafa;
                font-family: 'Consolas', 'Monaco', monospace;
                font-size: 11px;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                padding: 10px;
                margin: 5px;
            }
        """)
        log_layout.addWidget(self.log_output)
        
        log_buttons = QHBoxLayout()
        log_buttons.setSpacing(10)
        btn_clear_log = QPushButton("🗑️ Clear Log")
        btn_clear_log.clicked.connect(lambda: self.log_output.clear())
        log_buttons.addWidget(btn_clear_log)
        
        btn_copy_log = QPushButton("📋 Copy Log")
        btn_copy_log.clicked.connect(self.copy_log)
        log_buttons.addWidget(btn_copy_log)
        
        btn_save_log = QPushButton("💾 Save Log...")
        btn_save_log.clicked.connect(self.save_log)
        log_buttons.addWidget(btn_save_log)
        
        log_layout.addLayout(log_buttons)
        log_group.setLayout(log_layout)
        content_layout.addWidget(log_group)
        
        # Create scroll area for main content
        scroll_area = QScrollArea()
        scroll_area.setWidgetResizable(True)
        scroll_area.setFrameShape(QFrame.NoFrame)
        scroll_area.setWidget(content_widget)
        
        main_layout.addWidget(scroll_area, 1)
        
        # Status bar
        self.statusBar().showMessage("Ready")
        
        # Apply stylesheet
        self.setStyleSheet("""
            QMainWindow {
                background-color: #f5f5f5;
            }
            QTabWidget::pane {
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                background-color: white;
                margin: 10px;
            }
            QTabBar::tab {
                padding: 12px 24px;
                margin-right: 2px;
                background-color: #f0f0f0;
                border: 1px solid #e0e0e0;
                border-bottom: none;
                border-top-left-radius: 6px;
                border-top-right-radius: 6px;
                font-weight: 500;
                color: #616161;
                font-size: 13px;
            }
            QTabBar::tab:selected {
                background-color: white;
                color: #2196F3;
                border-bottom: 3px solid #2196F3;
                font-weight: bold;
            }
            QTabBar::tab:hover {
                background-color: #f8f8f8;
            }
            QPushButton {
                padding: 8px 16px;
                border-radius: 6px;
                border: 1px solid #e0e0e0;
                background-color: #f8f8f8;
                font-weight: 500;
                color: #424242;
                min-height: 32px;
            }
            QPushButton:hover {
                background-color: #e0e0e0;
                border-color: #d0d0d0;
            }
            QPushButton:pressed {
                background-color: #d0d0d0;
            }
            QPushButton:disabled {
                background-color: #f0f0f0;
                color: #b0b0b0;
            }
            QLineEdit, QSpinBox {
                padding: 8px;
                border-radius: 6px;
                border: 1px solid #e0e0e0;
                background-color: white;
                font-size: 13px;
                min-height: 32px;
            }

            /* ComboBox styling */
            QComboBox {
                padding: 8px;
                border-radius: 6px;
                border: 1px solid #e0e0e0;
                background-color: white;
                font-size: 13px;
                min-height: 32px;
                color: #424242;
            }
            QComboBox::drop-down {
                subcontrol-origin: padding;
                subcontrol-position: top right;
                width: 28px;
                border-left: 1px solid #e0e0e0;
            }
            QComboBox::down-arrow {
                width: 0; height: 0;
            }
            /* Popup view (options list) */
            QComboBox QAbstractItemView {
                background-color: white;
                border: 1px solid #e0e0e0;
                selection-background-color: #2196F3; /* selected item background */
                selection-color: #ffffff; /* selected item text */
                color: #424242; /* default item text */
                outline: none;
            }
            QComboBox QAbstractItemView::item {
                padding: 6px 10px;
            }
            QComboBox QAbstractItemView::item:hover {
                background-color: #E3F2FD; /* hover background */
                color: #0D47A1; /* hover text color */
            }
            QTextEdit {
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                padding: 8px;
                background-color: white;
                font-size: 13px;
            }
            QListWidget, QTableWidget {
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                padding: 5px;
                background-color: white;
                font-size: 13px;
            }
            QProgressBar {
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                text-align: center;
                background-color: white;
                height: 24px;
                margin: 10px;
            }
            QProgressBar::chunk {
                background-color: #4CAF50;
                border-radius: 3px;
            }
            QCheckBox, QRadioButton {
                spacing: 8px;
                font-size: 13px;
                color: #424242;
            }
            QCheckBox::indicator, QRadioButton::indicator {
                width: 18px;
                height: 18px;
            }
        """)
        
    def log_message(self, message):
        """Add a message to the log"""
        # Append to UI log
        self.log_output.append(message)
        self.log_output.moveCursor(QTextCursor.End)
        QApplication.processEvents()  # Update GUI

        # Also write to file logger
        try:
            if FILE_LOGGER:
                # Normalize message to single-line entries in file
                FILE_LOGGER.info(str(message))
        except Exception:
            pass
        
    def copy_log(self):
        """Copy log contents to clipboard"""
        clipboard = QApplication.clipboard()
        clipboard.setText(self.log_output.toPlainText())
        self.statusBar().showMessage("Log copied to clipboard", 3000)
        
    def save_log(self):
        """Save log to file"""
        log_text = self.log_output.toPlainText()
        if not log_text.strip():
            QMessageBox.warning(self, "Warning", "No log content to save!")
            return
            
        file_path, _ = QFileDialog.getSaveFileName(
            self, "Save Log File", "generator-log.txt",
            "Text Files (*.txt);;All Files (*)"
        )
        if file_path:
            try:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(log_text)
                QMessageBox.information(self, "Success", f"Log saved to:\n{file_path}")
            except Exception as e:
                QMessageBox.critical(self, "Error", f"Failed to save log:\n{str(e)}")
    
    def start_generation(self, task_type, **kwargs):
        """Start a generation task in a separate thread"""
        # Show loader
        LoaderManager.show_loader(self, "Generating...")
        # Disable UI elements
        self.tabs.setEnabled(False)
        self.progress_bar.setVisible(True)
        self.progress_bar.setValue(0)
        
        # Clear log
        self.log_output.clear()
        self.log_message(f"🚀 Starting {task_type.replace('_', ' ')}...")
        
        # Create and start thread
        self.generation_thread = GenerationThread(task_type, **kwargs)
        self.generation_thread.log_signal.connect(self.log_message)
        self.generation_thread.progress_signal.connect(self.on_progress_update)
        self.generation_thread.progress_signal.connect(self.progress_bar.setValue)
        self.generation_thread.finished_signal.connect(self.generation_finished)
        self.generation_thread.start()
        
    def on_progress_update(self, value):
        """Update progress in both progress bar and loader"""
        self.progress_bar.setValue(value)
        LoaderManager.update_progress(value)

    def generation_finished(self, success, message):
        """Handle generation completion"""
        from PyQt5.QtCore import QTimer
        QTimer.singleShot(500, LoaderManager.hide_loader)

        # Re-enable UI
        self.tabs.setEnabled(True)
        self.progress_bar.setVisible(False)

        # Log & status
        if success:
            self.log_message(f"✅ {message}")
            self.statusBar().showMessage("Generation completed successfully", 5000)
        else:
            self.log_message(f"❌ {message}")
            self.statusBar().showMessage("Generation failed", 5000)

        # Show sweet alert style popup after loader hidden
        def _show_result():
            try:
                dialog = QDialog(self)
                dialog.setWindowTitle("Generator Result" if success else "Generation Failed")
                dialog.setModal(True)
                dialog.setMinimumWidth(500)
                dialog.setMinimumHeight(300)
                dialog.setWindowFlags(dialog.windowFlags() & ~Qt.WindowContextHelpButtonHint)

                layout = QVBoxLayout(dialog)
                layout.setContentsMargins(0, 0, 0, 0)
                layout.setSpacing(0)

                # Icon and title area
                header = QFrame()
                if success:
                    header.setStyleSheet("""
                        QFrame {
                            background: qlineargradient(x1:0, y1:0, x2:1, y2:1,
                                stop:0 #4CAF50, stop:1 #45a049);
                            padding: 30px;
                        }
                    """)
                else:
                    header.setStyleSheet("""
                        QFrame {
                            background: qlineargradient(x1:0, y1:0, x2:1, y2:1,
                                stop:0 #F44336, stop:1 #E53935);
                            padding: 30px;
                        }
                    """)

                header_layout = QVBoxLayout(header)
                header_layout.setSpacing(10)
                header_layout.setAlignment(Qt.AlignCenter)

                # Icon
                icon_label = QLabel("✓" if success else "✕")
                icon_label.setStyleSheet("""
                    QLabel {
                        font-size: 60px;
                        color: white;
                        text-align: center;
                    }
                """)
                icon_label.setAlignment(Qt.AlignCenter)
                header_layout.addWidget(icon_label)

                # Title
                title = QLabel("Success!" if success else "Error!")
                title.setStyleSheet("""
                    QLabel {
                        font-size: 24px;
                        font-weight: bold;
                        color: white;
                        text-align: center;
                    }
                """)
                title.setAlignment(Qt.AlignCenter)
                header_layout.addWidget(title)

                layout.addWidget(header)

                # Message area
                content_frame = QFrame()
                content_frame.setStyleSheet("background-color: white; padding: 30px;")
                content_layout = QVBoxLayout(content_frame)

                message_text = QLabel(str(message or ("Action completed successfully" if success else "Action failed. Check log for details.")))
                message_text.setWordWrap(True)
                message_text.setAlignment(Qt.AlignCenter)
                message_text.setStyleSheet("""
                    QLabel {
                        font-size: 14px;
                        color: #333;
                        line-height: 1.6;
                    }
                """)
                content_layout.addWidget(message_text)
                content_layout.addStretch()

                layout.addWidget(content_frame, 1)

                # Button area
                button_frame = QFrame()
                button_frame.setStyleSheet("background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0;")
                button_layout = QHBoxLayout(button_frame)
                button_layout.addStretch()

                btn_ok = QPushButton("OK")
                btn_ok.setMinimumWidth(120)
                btn_ok.setMinimumHeight(40)
                if success:
                    btn_ok.setStyleSheet("""
                        QPushButton {
                            background-color: #4CAF50;
                            color: white;
                            font-weight: bold;
                            font-size: 14px;
                            padding: 10px 30px;
                            border-radius: 6px;
                            border: none;
                        }
                        QPushButton:hover {
                            background-color: #45a049;
                        }
                    """)
                else:
                    btn_ok.setStyleSheet("""
                        QPushButton {
                            background-color: #F44336;
                            color: white;
                            font-weight: bold;
                            font-size: 14px;
                            padding: 10px 30px;
                            border-radius: 6px;
                            border: none;
                        }
                        QPushButton:hover {
                            background-color: #E53935;
                        }
                    """)
                btn_ok.clicked.connect(dialog.accept)
                button_layout.addWidget(btn_ok)

                layout.addWidget(button_frame)

                dialog.exec_()

            except Exception as e:
                # Fallback to basic message box
                if success:
                    QMessageBox.information(self, "Success", str(message or "Action completed successfully"))
                else:
                    QMessageBox.critical(self, "Error", str(message or "Action failed. Check log for details."))

        QTimer.singleShot(650, _show_result)

        # Clean up thread
        self.generation_thread = None


def main():
    app = QApplication(sys.argv)

    # Set application style
    app.setStyle('Fusion')

    # Set application font
    font = QFont("Segoe UI", 10)
    app.setFont(font)

    window = MainWindow()
    window.show()
    try:
        FILE_LOGGER.info("Application started")
        FILE_LOGGER.info(f"Log file: {FILE_LOG_PATH}")
    except Exception:
        pass
    try:
        window.statusBar().showMessage(f"Log file: {FILE_LOG_PATH}", 8000)
    except Exception:
        pass
    sys.exit(app.exec_())


if __name__ == '__main__':
    # Install excepthook to log uncaught exceptions to file
    def handle_exception(exc_type, exc_value, exc_traceback):
        # Print to stderr as usual
        sys.__excepthook__(exc_type, exc_value, exc_traceback)
        try:
            import traceback as _tb
            FILE_LOGGER.error("Uncaught exception:\n" + _tb.format_exc())
        except Exception:
            pass

    sys.excepthook = handle_exception
    main()
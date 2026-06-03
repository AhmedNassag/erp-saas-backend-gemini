import os
import sys
import threading
import subprocess
import tkinter as tk
from tkinter import scrolledtext


# Handle running as both script and exe
if getattr(sys, 'frozen', False):
    # Running as exe: go up from dist folder to Setting-Python
    SCRIPT_DIR = os.path.dirname(os.path.dirname(os.path.abspath(sys.executable)))
else:
    # Running as script
    SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

VENV_DIR = os.path.join(SCRIPT_DIR, "venv")
VENV_PYTHON = os.path.join(VENV_DIR, "Scripts", "python.exe")
VENV_ACTIVATE = os.path.join(VENV_DIR, "Scripts", "activate.bat")


def append_log(text_widget, text):
    text_widget.configure(state="normal")
    text_widget.insert(tk.END, text)
    text_widget.see(tk.END)
    text_widget.configure(state="disabled")


def stream_process(proc, text_widget):
    for line in proc.stdout:
        append_log(text_widget, line)
    proc.wait()
    append_log(text_widget, f"\nProcess exited with code {proc.returncode}\n")


def find_script(script_name="main.py"):
    """Search for script starting from SCRIPT_DIR and going up parent directories."""
    current = SCRIPT_DIR
    for _ in range(5):  # Search up to 5 levels up
        script_path = os.path.join(current, script_name)
        if os.path.exists(script_path):
            return script_path
        parent = os.path.dirname(current)
        if parent == current:  # Reached root
            break
        current = parent
    return None


def run_script(text_widget, script_name="main.py"):
    script_path = find_script(script_name)
    if not script_path:
        append_log(text_widget, f"Script not found: {script_name}\n")
        append_log(text_widget, f"Searched in: {SCRIPT_DIR}\n")
        return

    if os.path.exists(VENV_PYTHON):
        cmd = [VENV_PYTHON, script_path]
    else:
        append_log(text_widget, "Venv python not found, using system Python.\n")
        cmd = [sys.executable, script_path]

    try:
        proc = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=os.path.dirname(script_path), text=True)
        threading.Thread(target=stream_process, args=(proc, text_widget), daemon=True).start()
    except Exception as e:
        append_log(text_widget, f"Failed to start process: {e}\n")




def build_gui():
    root = tk.Tk()
    root.title("Project Launcher")
    root.geometry("750x500")
    root.resizable(False, False)
    
    # Color scheme - modern dark theme
    PRIMARY_COLOR = "#2E3440"
    SECONDARY_COLOR = "#3B4252"
    ACCENT_COLOR = "#88C0D0"
    TEXT_COLOR = "#ECEFF4"
    BUTTON_BG = "#5E81AC"
    BUTTON_HOVER = "#81A1C1"
    
    root.configure(bg=PRIMARY_COLOR)
    
    # Header frame with title and icon
    header_frame = tk.Frame(root, bg=SECONDARY_COLOR, height=100)
    header_frame.pack(fill=tk.X, padx=0, pady=0)
    header_frame.pack_propagate(False)
    
    title_label = tk.Label(
        header_frame, 
        text="🚀 Project Launcher",
        font=("Segoe UI", 24, "bold"),
        bg=SECONDARY_COLOR,
        fg=ACCENT_COLOR
    )
    title_label.pack(pady=15)
    
    subtitle_label = tk.Label(
        header_frame,
        text=f"Project: {os.path.basename(SCRIPT_DIR)}",
        font=("Segoe UI", 10),
        bg=SECONDARY_COLOR,
        fg=TEXT_COLOR
    )
    subtitle_label.pack(pady=(0, 10))
    
    # Button frame
    button_frame = tk.Frame(root, bg=PRIMARY_COLOR)
    button_frame.pack(fill=tk.X, padx=15, pady=15)
    
    def create_button(text, command, bg_color=BUTTON_BG):
        btn = tk.Button(
            button_frame,
            text=text,
            command=command,
            font=("Segoe UI", 11, "bold"),
            bg=bg_color,
            fg=TEXT_COLOR,
            activebackground=BUTTON_HOVER,
            activeforeground=TEXT_COLOR,
            relief=tk.FLAT,
            padx=20,
            pady=10,
            cursor="hand2",
            bd=0
        )
        return btn
    
    btn_run = create_button("▶ Run Main Script", lambda: run_script(log_box, "main.py"))
    btn_run.pack(side=tk.LEFT, padx=8)
    
    btn_quit = create_button("⊗ Quit", root.quit, bg_color="#BF616A")
    btn_quit.pack(side=tk.RIGHT, padx=8)
    
    # Log frame with title
    log_frame = tk.Frame(root, bg=PRIMARY_COLOR)
    log_frame.pack(fill=tk.BOTH, expand=True, padx=15, pady=(0, 15))
    
    log_title = tk.Label(
        log_frame,
        text="Output Log",
        font=("Segoe UI", 10, "bold"),
        bg=PRIMARY_COLOR,
        fg=ACCENT_COLOR
    )
    log_title.pack(anchor=tk.W, pady=(0, 5))
    
    # Scrolled text widget with custom styling
    log_box = scrolledtext.ScrolledText(
        log_frame,
        state="disabled",
        wrap=tk.WORD,
        bg=SECONDARY_COLOR,
        fg=TEXT_COLOR,
        font=("Consolas", 10),
        insertbackground=ACCENT_COLOR,
        relief=tk.FLAT,
        bd=1,
        highlightthickness=0
    )
    log_box.pack(fill=tk.BOTH, expand=True)
    
    # Configure scrollbar styling
    scrollbar = log_box.vbar
    scrollbar.config(bg=SECONDARY_COLOR, troughcolor=PRIMARY_COLOR, activebackground=ACCENT_COLOR)
    
    append_log(log_box, f"✓ Launcher started\n")
    append_log(log_box, f"📁 Location: {SCRIPT_DIR}\n")
    append_log(log_box, f"{'─' * 60}\n\n")
    
    if not os.path.exists(VENV_DIR):
        append_log(log_box, "⚠ Warning: no 'venv' directory found in this folder.\n")
    
    root.mainloop()


if __name__ == "__main__":
    build_gui()

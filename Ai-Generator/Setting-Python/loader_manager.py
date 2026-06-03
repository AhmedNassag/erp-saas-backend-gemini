from PyQt5.QtWidgets import QApplication, QDesktopWidget, QWidget
from PyQt5.QtCore import Qt, QTimer
from PyQt5.QtGui import QPainter, QColor, QPen, QBrush, QFont
import math

class CSSLoader(QWidget):    
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setAttribute(Qt.WA_TranslucentBackground)
        self.setWindowFlags(Qt.FramelessWindowHint | Qt.WindowStaysOnTopHint)
        
        # Set size
        self.setFixedSize(180, 220)
        
        # Animation values
        self._angle = 0
        self._progress = 0
        self._message = "Processing"
        self._dots = ""
        
        # Setup timers
        self.rotation_timer = QTimer()
        self.rotation_timer.timeout.connect(self._update_rotation)
        self.rotation_timer.setInterval(30)  # 30ms = ~33 FPS
        
        self.dots_timer = QTimer()
        self.dots_timer.timeout.connect(self._update_dots)
        self.dots_timer.setInterval(500)
        
        # Colors
        self.colors = [
            QColor("#2196F3"),  # Blue
            QColor("#9C27B0"),  # Purple
            QColor("#FF4081"),  # Pink
            QColor("#00BCD4"),  # Cyan
            QColor("#4CAF50"),  # Green
        ]
        
    def _update_rotation(self):
        self._angle = (self._angle + 6) % 360
        self.update()
    
    def _update_dots(self):
        self._dots = "." * ((len(self._dots) + 1) % 4)
        self.update()
    
    def paintEvent(self, event):
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        
        # Draw background with rounded corners
        painter.setBrush(QColor(255, 255, 255, 230))
        painter.setPen(Qt.NoPen)
        painter.drawRoundedRect(0, 0, self.width(), self.height(), 10, 10)
        
        # Center coordinates
        center_x = self.width() // 2
        center_y = self.height() // 2 - 20
        
        # Draw rotating circles (simple spinner)
        painter.save()
        painter.translate(center_x, center_y)
        painter.rotate(self._angle)
        
        # Draw 8 circles around center
        radius = 40
        for i in range(8):
            angle_rad = i * 45 * math.pi / 180
            x = radius * math.cos(angle_rad)
            y = radius * math.sin(angle_rad)
            
            # Vary opacity for wave effect
            opacity = 0.3 + 0.7 * ((i + int(self._angle / 45)) % 2)
            painter.setOpacity(opacity)
            
            # Use rotating colors
            color = self.colors[(i + int(self._angle / 90)) % len(self.colors)]
            painter.setBrush(color)
            painter.setPen(Qt.NoPen)
            
            # Draw circle
            painter.drawEllipse(int(x - 6), int(y - 6), 12, 12)
        
        painter.restore()
        
        # Draw inner pulsing circle
        pulse_size = 4 + 2 * math.sin(self._angle * math.pi / 180)
        painter.setBrush(QColor(33, 150, 243, 100))
        painter.setPen(Qt.NoPen)
        painter.drawEllipse(center_x - 15, center_y - 15, 30, 30)
        
        # Draw progress circle if progress > 0
        if self._progress > 0:
            painter.setPen(QPen(QColor("#4CAF50"), 3))
            painter.setBrush(Qt.NoBrush)
            
            # Draw progress arc
            span_angle = -self._progress * 3.6  # 360 * progress/100
            painter.drawArc(
                center_x - 30, center_y - 30, 
                60, 60, 
                90 * 16, int(span_angle * 16)
            )
            
            # Draw progress text
            painter.setPen(QColor("#333333"))
            font = QFont("Segoe UI", 9, QFont.Bold)
            painter.setFont(font)
            painter.drawText(
                center_x - 15, center_y - 10, 
                30, 20, 
                Qt.AlignCenter, 
                f"{self._progress}%"
            )
        
        # Draw message with animated dots
        painter.setPen(QColor("#333333"))
        font = QFont("Segoe UI", 10, QFont.Medium)
        painter.setFont(font)
        painter.drawText(
            0, center_y + 50, 
            self.width(), 30, 
            Qt.AlignCenter, 
            f"{self._message}{self._dots}"
        )
        
        # Draw status based on progress
        if self._progress > 0:
            if self._progress < 30:
                status = "Starting..."
                color = QColor("#F44336")
            elif self._progress < 70:
                status = "Processing..."
                color = QColor("#FF9800")
            else:
                status = "Finishing..."
                color = QColor("#4CAF50")
            
            painter.setPen(color)
            font = QFont("Segoe UI", 8)
            painter.setFont(font)
            painter.drawText(
                0, center_y + 75, 
                self.width(), 25, 
                Qt.AlignCenter, 
                status
            )
        
        # Draw border
        painter.setPen(QPen(QColor(200, 200, 200), 1))
        painter.setBrush(Qt.NoBrush)
        painter.drawRoundedRect(0, 0, self.width()-1, self.height()-1, 10, 10)
    
    def start(self):
        self.rotation_timer.start()
        self.dots_timer.start()
        self.show()
    
    def stop(self):
        self.rotation_timer.stop()
        self.dots_timer.stop()
        self.hide()
    
    def set_progress(self, value):
        self._progress = min(100, max(0, int(value)))
        self.update()
    
    def set_message(self, message):
        self._message = message
        self.update()
    
    def isVisible(self):
        return super().isVisible()


class LoaderManager:    
    _instance = None
    _loader = None
    
    def __new__(cls):
        if cls._instance is None:
            cls._instance = super(LoaderManager, cls).__new__(cls)
        return cls._instance
    
    @classmethod
    def show_loader(cls, parent=None, message="Processing"):
        try:
            # Close existing loader if any
            if cls._loader is not None:
                try:
                    cls._loader.stop()
                    cls._loader.hide()
                    cls._loader.deleteLater()
                except:
                    pass
                cls._loader = None
            
            # Create new loader
            cls._loader = CSSLoader(parent)
            
            # Set message
            cls._loader.set_message(message)
            
            # Center on parent or screen
            if parent and parent.isVisible():
                parent_rect = parent.frameGeometry()
                loader_size = cls._loader.size()
                x = parent_rect.x() + (parent_rect.width() - loader_size.width()) // 2
                y = parent_rect.y() + (parent_rect.height() - loader_size.height()) // 2
                cls._loader.move(x, y)
            else:
                # Center on screen
                screen = QDesktopWidget().screenGeometry()
                loader_size = cls._loader.size()
                x = (screen.width() - loader_size.width()) // 2
                y = (screen.height() - loader_size.height()) // 2
                cls._loader.move(x, y)
            
            # Show and start
            cls._loader.show()
            cls._loader.start()
            
            # Force update
            QApplication.processEvents()
            
        except Exception as e:
            print(f"Error showing loader: {e}")
            import traceback
            traceback.print_exc()
    
    @classmethod
    def hide_loader(cls):
        try:
            if cls._loader:
                cls._loader.stop()
                QTimer.singleShot(200, lambda: cls._loader.hide() if cls._loader else None)
        except Exception as e:
            print(f"Error hiding loader: {e}")
    
    @classmethod
    def update_progress(cls, value):
        try:
            if cls._loader:
                cls._loader.set_progress(value)
                cls._loader.update()
                QApplication.processEvents()
        except Exception as e:
            print(f"Error updating progress: {e}")
    
    @classmethod
    def is_visible(cls):
        return cls._loader and cls._loader.isVisible()
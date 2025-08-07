/**
 * Unified Notification System
 * Hệ thống thông báo thống nhất cho toàn bộ website
 */
class NotificationManager {
	constructor() {
		this.init();
	}

	init() {
		this.addCSS();
		this.createContainer();
	}

	/**
	 * Add CSS styles
	 */
	addCSS() {
		if (document.querySelector('#unified-notification-styles')) {
			return; // Already added
		}

		const style = document.createElement('style');
		style.id = 'unified-notification-styles';
		style.textContent = `
            .notification-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                pointer-events: none;
            }

            .notification {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                margin-bottom: 10px;
                overflow: hidden;
                transform: translateX(100%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: auto;
                min-width: 320px;
                max-width: 400px;
                opacity: 0;
            }

            .notification.show {
                transform: translateX(0);
                opacity: 1;
            }

            .notification.hide {
                transform: translateX(100%);
                opacity: 0;
            }

            .notification-header {
                padding: 12px 16px;
                color: white;
                font-weight: 600;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .notification-success .notification-header {
                background: linear-gradient(135deg, #28a745, #20c997);
            }

            .notification-error .notification-header {
                background: linear-gradient(135deg, #dc3545, #e83e8c);
            }

            .notification-warning .notification-header {
                background: linear-gradient(135deg, #ffc107, #fd7e14);
                color: #333;
            }

            .notification-info .notification-header {
                background: linear-gradient(135deg, #17a2b8, #007bff);
            }

            .notification-content {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .notification-icon {
                font-size: 16px;
            }

            .notification-close {
                background: none;
                border: none;
                color: currentColor;
                cursor: pointer;
                padding: 2px;
                opacity: 0.8;
                transition: opacity 0.2s;
            }

            .notification-close:hover {
                opacity: 1;
            }

            .notification-body {
                padding: 12px 16px;
                color: #333;
                font-size: 14px;
                line-height: 1.4;
            }

            /* Animation for mobile */
            @media (max-width: 768px) {
                .notification-container {
                    left: 10px;
                    right: 10px;
                    top: 10px;
                }

                .notification {
                    min-width: auto;
                    max-width: none;
                }

                .notification,
                .notification.show {
                    transform: translateY(-100%);
                }

                .notification.show {
                    transform: translateY(0);
                }

                .notification.hide {
                    transform: translateY(-100%);
                }
            }
        `;
		document.head.appendChild(style);
	}

	/**
	 * Create notification container
	 */
	createContainer() {
		if (document.querySelector('.notification-container')) {
			return; // Already exists
		}

		const container = document.createElement('div');
		container.className = 'notification-container';
		container.id = 'notification-container';
		document.body.appendChild(container);
	}

	/**
	 * Show success notification
	 */
	success(message, title = 'Thành công') {
		this.show(message, 'success', title);
	}

	/**
	 * Show error notification
	 */
	error(message, title = 'Lỗi') {
		this.show(message, 'error', title);
	}

	/**
	 * Show warning notification
	 */
	warning(message, title = 'Cảnh báo') {
		this.show(message, 'warning', title);
	}

	/**
	 * Show info notification
	 */
	info(message, title = 'Thông tin') {
		this.show(message, 'info', title);
	}

	/**
	 * Show notification
	 */
	show(message, type = 'info', title = '', duration = 4000) {
		const container = document.getElementById('notification-container');
		if (!container) {
			console.error('Notification container not found');
			return;
		}

		// Create notification element
		const notification = document.createElement('div');
		notification.className = `notification notification-${type}`;

		// Get icon based on type
		const icons = {
			success: 'fas fa-check-circle',
			error: 'fas fa-exclamation-triangle',
			warning: 'fas fa-exclamation-circle',
			info: 'fas fa-info-circle',
		};

		notification.innerHTML = `
            <div class="notification-header">
                <div class="notification-content">
                    <i class="notification-icon ${icons[type]}"></i>
                    <span>${title}</span>
                </div>
                <button class="notification-close" onclick="this.closest('.notification').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="notification-body">
                ${message}
            </div>
        `;

		// Add to container
		container.appendChild(notification);

		// Show with animation
		setTimeout(() => {
			notification.classList.add('show');
		}, 100);

		// Auto remove after duration
		if (duration > 0) {
			setTimeout(() => {
				this.hide(notification);
			}, duration);
		}

		return notification;
	}

	/**
	 * Hide notification
	 */
	hide(notification) {
		if (!notification || !notification.parentNode) return;

		notification.classList.add('hide');
		setTimeout(() => {
			if (notification.parentNode) {
				notification.parentNode.removeChild(notification);
			}
		}, 300);
	}

	/**
	 * Clear all notifications
	 */
	clear() {
		const container = document.getElementById('notification-container');
		if (container) {
			container.innerHTML = '';
		}
	}
}

// Create global instance
window.notifications = new NotificationManager();

// Global convenience functions
window.showNotification = (message, type = 'info', title = '') => {
	return window.notifications.show(message, type, title);
};

window.showSuccess = (message, title = 'Thành công') => {
	return window.notifications.success(message, title);
};

window.showError = (message, title = 'Lỗi') => {
	return window.notifications.error(message, title);
};

window.showWarning = (message, title = 'Cảnh báo') => {
	return window.notifications.warning(message, title);
};

window.showInfo = (message, title = 'Thông tin') => {
	return window.notifications.info(message, title);
};

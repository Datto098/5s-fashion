/**
 * 5S Fashion Chatbot
 * Client-side JavaScript for chatbot functionality
 * Cập nhật: Kết nối với API backend mới, hiển thị sản phẩm đẹp mắt hơn
 */

class FSFashionChatbot {
	constructor() {
		this.baseUrl = window.location.origin + '/5s-fashion';
		this.apiUrl = this.baseUrl + '/public/chatbot-api.php'; // Đường dẫn tới API mới (đường dẫn trực tiếp)
		this.isOpen = false;
		this.isTyping = false;
		this.conversation = []; // Lưu lịch sử hội thoại
		this.currentProductType = null; // Loại sản phẩm hiện tại (best_selling, discounted, newest)

		this.init();
	}

	init() {
		// Kiểm tra hội thoại đã lưu trong local storage
		const savedChat = localStorage.getItem('5s_fashion_chatbot_history');
		if (savedChat) {
			try {
				const parsedChat = JSON.parse(savedChat);
				if (Array.isArray(parsedChat) && parsedChat.length > 0) {
					this.conversation = parsedChat;

					// Hiển thị 3 tin nhắn cuối cùng nếu có
					const lastMessages = parsedChat.slice(-3);
					lastMessages.forEach((msg) => {
						this.addMessage(msg.message, msg.type, false);
					});

					this.scrollToBottom();
					console.log('Chatbot: Loaded saved conversation');
				}
			} catch (e) {
				console.error('Chatbot: Error loading saved conversation', e);
				localStorage.removeItem('5s_fashion_chatbot_history');
			}
		} else {
			// Add welcome message
			this.addMessage(
				'Xin chào! Tôi là trợ lý ảo của 5S Fashion. Tôi có thể giúp bạn tìm sản phẩm bán chạy, khuyến mãi, hàng mới, hoặc hỗ trợ đơn hàng. Bạn cần hỗ trợ gì?',
				'bot'
			);
		}

		// Bind events
		this.bindEvents();

		console.log('FSFashionChatbot initialized');
	}

	bindEvents() {
		const toggleBtn = document.getElementById('chatbot-toggle');
		const closeBtn = document.getElementById('chatbot-close');
		const sendBtn = document.getElementById('chatbot-send');
		const input = document.getElementById('chatbot-input');

		if (toggleBtn) {
			toggleBtn.addEventListener('click', () => this.toggle());
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', () => this.close());
		}

		if (sendBtn) {
			sendBtn.addEventListener('click', () => this.sendMessage());
		}

		if (input) {
			input.addEventListener('keypress', (e) => {
				if (e.key === 'Enter') {
					this.sendMessage();
				}
			});
		}

		// Quick action buttons - xử lý cả click vào button và icon trong button
		document.querySelectorAll('.quick-action').forEach((btn) => {
			btn.addEventListener('click', (e) => {
				const message = btn.dataset.message;
				if (message) {
					this.sendUserMessage(message);
				}
			});

			// Xử lý click vào icon trong button
			const icon = btn.querySelector('i');
			if (icon) {
				icon.addEventListener('click', (e) => {
					e.stopPropagation(); // Ngăn event bubbling
					const message = btn.dataset.message;
					if (message) {
						this.sendUserMessage(message);
					}
				});
			}
		});
	}

	toggle() {
		if (this.isOpen) {
			this.close();
		} else {
			this.open();
		}
	}

	open() {
		const container = document.getElementById('chatbot-container');
		const toggle = document.getElementById('chatbot-toggle');

		if (container && toggle) {
			container.classList.add('active');
			toggle.classList.add('active');
			this.isOpen = true;

			// Focus on input
			const input = document.getElementById('chatbot-input');
			if (input) {
				setTimeout(() => input.focus(), 300);
			}

			// Đảm bảo cuộn xuống cuối
			this.scrollToBottom();
		}
	}

	close() {
		const container = document.getElementById('chatbot-container');
		const toggle = document.getElementById('chatbot-toggle');

		if (container && toggle) {
			container.classList.remove('active');
			toggle.classList.remove('active');
			this.isOpen = false;
		}
	}

	sendMessage() {
		const input = document.getElementById('chatbot-input');
		if (!input) return;

		const message = input.value.trim();
		if (!message) return;

		// Add user message to chat
		this.addMessage(message, 'user');

		// Clear input
		input.value = '';

		// Send to API
		this.sendToAPI(message);
	}

	sendUserMessage(message) {
		this.addMessage(message, 'user');
		this.sendToAPI(message);
	}

	async sendToAPI(message) {
		try {
			this.showTyping();

			const response = await fetch(this.apiUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Accept: 'application/json',
				},
				body: JSON.stringify({ message: message }),
			});

			const data = await response.json();

			this.hideTyping();

			if (data.success) {
				// Add bot response
				this.addMessage(data.data.message, 'bot');

				// Lưu loại sản phẩm hiện tại
				if (data.data.type) {
					this.currentProductType = data.data.type;
				}

				// Add products if any
				if (data.data.products && data.data.products.length > 0) {
					this.addProductMessage(data.data.products);
				}
			} else {
				this.addMessage(
					'Xin lỗi, tôi gặp sự cố kỹ thuật. Vui lòng thử lại sau.',
					'bot'
				);
			}
		} catch (error) {
			console.error('Chatbot API Error:', error);
			this.hideTyping();
			this.addMessage(
				'Xin lỗi, không thể kết nối đến server. Vui lòng thử lại sau.',
				'bot'
			);
		}
	}

	addMessage(message, type, save = true) {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (!messagesContainer) return;

		const messageDiv = document.createElement('div');
		messageDiv.className = `message ${type}-message`;

		// Nhận dạng URL và chuyển đổi thành liên kết có thể click
		const linkedMessage = this.linkify(message);

		if (type === 'bot') {
			messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">${linkedMessage}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
		} else {
			messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-text">${linkedMessage}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
            `;
		}

		messagesContainer.appendChild(messageDiv);

		// Lưu tin nhắn vào lịch sử nếu cần
		if (save) {
			this.conversation.push({
				message: message,
				type: type,
				timestamp: new Date().toISOString(),
			});

			// Lưu vào local storage, chỉ giữ 20 tin nhắn gần nhất
			if (this.conversation.length > 20) {
				this.conversation = this.conversation.slice(-20);
			}
			localStorage.setItem(
				'5s_fashion_chatbot_history',
				JSON.stringify(this.conversation)
			);
		}

		this.scrollToBottom();
	}

	addProductMessage(products) {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (!messagesContainer) return;

		const productDiv = document.createElement('div');
		productDiv.className = 'message bot-message product-message';

		let productsHtml = '<div class="products-grid">';
		products.forEach((product) => {
			// Xác định giá và giá giảm giá
			const finalPrice = product.final_price || product.price;
			const originalPrice =
				product.discount_percentage > 0 ? product.price : null;
			const discountBadge =
				product.discount_percentage > 0
					? `<span class="discount-badge">-${product.discount_percentage}%</span>`
					: '';

			productsHtml += `
                <div class="product-card-mini">
                    ${discountBadge}
                    <a href="${
						product.url
					}" class="product-link" target="_blank">
                        <div class="product-image">
                            <img src="${
								product.image ||
								'/5s-fashion/serve-file.php?file=products/no-image.jpg'
							}" alt="${product.name}"
                                onerror="this.src='/5s-fashion/serve-file.php?file=products/no-image.jpg'"
                                class="product-image img-fluid">
                        </div>
                        <div class="product-info">
                            <h6 class="product-name">${product.name}</h6>
                            <div class="product-price">
                                <span class="current-price">${this.formatPrice(
									finalPrice
								)}</span>
                                ${
									originalPrice
										? `<span class="original-price">${this.formatPrice(
												originalPrice
										  )}</span>`
										: ''
								}
                            </div>
                        </div>
                    </a>
                </div>
            `;
		});
		productsHtml += '</div>';

		// Tùy chỉnh tiêu đề dựa vào loại sản phẩm
		let productTitle = 'Đây là một số sản phẩm phù hợp với bạn:';
		if (this.currentProductType === 'best_selling') {
			productTitle = 'Đây là những sản phẩm bán chạy nhất hiện tại:';
		} else if (this.currentProductType === 'discounted') {
			productTitle = 'Đây là các sản phẩm đang được giảm giá sốc:';
		} else if (this.currentProductType === 'newest') {
			productTitle = 'Đây là những sản phẩm mới nhất của chúng tôi:';
		}

		productDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="message-text">${productTitle}</div>
                ${productsHtml}
                <div class="message-time">${this.getCurrentTime()}</div>
            </div>
        `;

		messagesContainer.appendChild(productDiv);
		this.scrollToBottom();
	}

	showTyping() {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (!messagesContainer) return;

		const typingDiv = document.createElement('div');
		typingDiv.className = 'message bot-message typing-message';
		typingDiv.id = 'typing-indicator';
		typingDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;

		messagesContainer.appendChild(typingDiv);
		this.scrollToBottom();
		this.isTyping = true;
	}

	hideTyping() {
		const typingIndicator = document.getElementById('typing-indicator');
		if (typingIndicator) {
			typingIndicator.remove();
		}
		this.isTyping = false;
	}

	scrollToBottom() {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (messagesContainer) {
			messagesContainer.scrollTop = messagesContainer.scrollHeight;
		}
	}

	getCurrentTime() {
		const now = new Date();
		return now.toLocaleTimeString('vi-VN', {
			hour: '2-digit',
			minute: '2-digit',
		});
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(price);
	}

	// Hàm nhận dạng URL và chuyển đổi thành liên kết có thể click
	linkify(text) {
		if (!text) return '';

		// URL pattern
		const urlPattern =
			/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
		// www. without http:// or https://
		const pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
		// Email pattern
		const emailPattern = /[\w.]+@[a-zA-Z_-]+?(?:\.[a-zA-Z]{2,6})+/gim;

		return (
			text
				// Chuyển đổi URL thành liên kết
				.replace(urlPattern, '<a href="$1" target="_blank">$1</a>')
				// Chuyển đổi www. URLs thành liên kết
				.replace(
					pseudoUrlPattern,
					'$1<a href="http://$2" target="_blank">$2</a>'
				)
				// Chuyển đổi email thành liên kết mailto
				.replace(emailPattern, '<a href="mailto:$&">$&</a>')
				// Thay thế dấu xuống dòng bằng <br>
				.replace(/\n/g, '<br>')
		);
	}
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	// Check if chatbot elements exist
	if (document.getElementById('chatbot-toggle')) {
		window.chatbot = new FSFashionChatbot();
	}
});

// Thêm hàm toàn cục để hiển thị chatbot từ các nơi khác
window.openChatbot = function () {
	if (window.chatbot) {
		window.chatbot.open();
	}
};

// Thêm hàm để gửi tin nhắn từ bên ngoài chatbot
window.sendToChatbot = function (message) {
	if (window.chatbot) {
		window.chatbot.open();
		setTimeout(() => {
			window.chatbot.sendUserMessage(message);
		}, 500);
	}
};

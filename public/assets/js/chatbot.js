/**
 * 5S Fashion Chatbot
 * Client-side JavaScript for chatbot functionality
 */

class FSFashionChatbot {
	constructor() {
		this.baseUrl = window.location.origin + '/5s-fashion';
		this.apiUrl = this.baseUrl + '/api/chatbot/chat';
		this.isOpen = false;
		this.isTyping = false;

		this.init();
	}

	init() {
		// Bind events
		this.bindEvents();

		// Add welcome message
		this.addMessage(
			'Xin chào! Tôi là trợ lý ảo của 5S Fashion. Tôi có thể giúp bạn tìm sản phẩm, tư vấn thời trang, hoặc hỗ trợ mua hàng. Bạn cần hỗ trợ gì?',
			'bot'
		);

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

		// Quick action buttons
		document.querySelectorAll('.quick-action').forEach((btn) => {
			btn.addEventListener('click', (e) => {
				const message = e.target.dataset.message;
				if (message) {
					this.sendUserMessage(message);
				}
			});
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

	addMessage(message, type) {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (!messagesContainer) return;

		const messageDiv = document.createElement('div');
		messageDiv.className = `message ${type}-message`;

		if (type === 'bot') {
			messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">${message}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
		} else {
			messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-text">${message}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
            `;
		}

		messagesContainer.appendChild(messageDiv);
		this.scrollToBottom();
	}

	addProductMessage(products) {
		const messagesContainer = document.getElementById('chatbot-messages');
		if (!messagesContainer) return;

		const productDiv = document.createElement('div');
		productDiv.className = 'message bot-message product-message';

		let productsHtml = '<div class="products-grid">';
		products.forEach((product) => {
			const price = product.sale_price || product.price;
			const originalPrice = product.sale_price ? product.price : null;

			productsHtml += `
                <div class="product-card-mini">
                    <div class="product-image">
                        <img src="${product.image_url}" alt="${
				product.name
			}" onerror="this.src='/5s-fashion/public/assets/images/no-image.jpg'">
                    </div>
                    <div class="product-info">
                        <h6 class="product-name">${product.name}</h6>
                        <div class="product-price">
                            <span class="current-price">${this.formatPrice(
								price
							)}</span>
                            ${
								originalPrice
									? `<span class="original-price">${this.formatPrice(
											originalPrice
									  )}</span>`
									: ''
							}
                        </div>
                        <a href="${this.baseUrl}/product/${
				product.slug
			}" class="btn btn-primary btn-sm">Xem chi tiết</a>
                    </div>
                </div>
            `;
		});
		productsHtml += '</div>';

		productDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="message-text">Đây là một số sản phẩm phù hợp với bạn:</div>
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
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	// Check if chatbot elements exist
	if (document.getElementById('chatbot-toggle')) {
		window.chatbot = new FSFashionChatbot();
	}
});

// Additional chatbot functionality can be added here if needed

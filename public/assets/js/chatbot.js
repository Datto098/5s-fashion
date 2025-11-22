/**
 * zone Fashion Chatbot
 * Client-side JavaScript for chatbot functionality
 * Cập nhật: Kết nối với API backend mới, hiển thị sản phẩm đẹp mắt hơn
 */

class FSFashionChatbot {
	constructor() {
		this.baseUrl = window.location.origin + '/zone-fashion';
		this.apiUrl = this.baseUrl + '/public/chatbot-api.php'; // Đường dẫn tới API mới (đường dẫn trực tiếp)
		this.isOpen = false;
		this.isTyping = false;
		this.conversation = []; // Lưu lịch sử hội thoại
		this.currentProductType = null; // Loại sản phẩm hiện tại (best_selling, discounted, newest)
		this.sessionId = this.generateSessionId(); // Session ID for conversation tracking

		this.init();
	}

	// Generate a random session ID
	generateSessionId() {
		return (
			'chatbot_' +
			Math.random().toString(36).substring(2, 15) +
			Math.random().toString(36).substring(2, 15)
		);
	}

	init() {
		// Prevent multiple initialization
		if (this.initialized) {
			console.log('Chatbot already initialized');
			return;
		}
		
		// Thêm CSS cho các thành phần đặc biệt như bảng size
		this.injectCustomCSS();

		// Kiểm tra hội thoại đã lưu trong local storage
		const savedChat = localStorage.getItem('zone_fashion_chatbot_history');
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
				localStorage.removeItem('zone_fashion_chatbot_history');
			}
		} else {
			// Add welcome message
			this.addMessage(
				'Xin chào! Tôi là trợ lý ảo của zone Fashion. Tôi có thể giúp bạn tìm sản phẩm bán chạy, khuyến mãi, hàng mới, hoặc hỗ trợ đơn hàng. Bạn cần hỗ trợ gì?',
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

		// Quick action buttons - use simple forEach but with flag check
		document.querySelectorAll('.quick-action').forEach((btn) => {
			// Skip if already has our listener
			if (btn.hasAttribute('data-chatbot-bound')) {
				return;
			}
			
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				const message = btn.dataset.message;
				if (message) {
					this.sendUserMessage(message);
				}
			});
			
			// Mark as bound to prevent duplicate
			btn.setAttribute('data-chatbot-bound', 'true');
		});

		// Mark as initialized
		this.initialized = true;
		console.log('FSFashionChatbot initialized');
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
				body: JSON.stringify({
					message: message,
					sessionId: this.sessionId, // Send session ID for context tracking
				}),
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

				// Handle different types of responses
				if (data.data.products && data.data.products.length > 0) {
					// Products data available
					this.addProductMessage(data.data.products);
				}

				// If it's a search result, remember for context
				if (
					data.data.type === 'product_search' ||
					data.data.type === 'price_range_search'
				) {
					// Could add specific UI elements for searches
					console.log('Search results displayed');
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

		// Kiểm tra xem message có chứa HTML hay không
		// Nếu chứa HTML, sử dụng trực tiếp, ngược lại xử lý URL
		let processedMessage = message;

		// Kiểm tra xem message có phải là HTML không
		const containsHTML = /<[a-z][\s\S]*>/i.test(message);

		if (!containsHTML) {
			// Chỉ xử lý linkify nếu không phải HTML
			processedMessage = this.linkify(message);
		}

		if (type === 'bot') {
			messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">${processedMessage}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
		} else {
			messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-text">${processedMessage}</div>
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
				'zone_fashion_chatbot_history',
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
                            <img src="${product.image}" alt="${product.name}"
                                onerror="this.src=window.location.origin + '/zone-fashion/public/assets/images/no-image.jpg'"
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

	// Thêm CSS tùy chỉnh để hiển thị bảng size và các thành phần khác đẹp hơn
	injectCustomCSS() {
		const styleElement = document.createElement('style');
		styleElement.innerHTML = `
			/* CSS cho bảng size trong chatbot */
			.size-guide-table {
				margin: 8px 0;
				padding: 10px;
				border-radius: 8px;
				background-color: #f7f8fc;
			}

			.size-guide-table h4 {
				margin-top: 0;
				margin-bottom: 10px;
				color: #333;
				font-size: 16px;
				font-weight: 600;
			}

			.size-guide-table table {
				width: 100%;
				border-collapse: collapse;
				margin-bottom: 10px;
				border: none;
			}

			.size-guide-table th {
				background-color: #e3e6f3;
				color: #333;
				text-align: left;
				padding: 6px 8px;
				font-size: 14px;
				font-weight: 600;
				border: 1px solid #c6cde5;
			}

			.size-guide-table td {
				padding: 6px 8px;
				border: 1px solid #e3e6f3;
				font-size: 13px;
			}

			.size-guide-table tr:nth-child(even) {
				background-color: #f2f4fa;
			}

			.size-guide-table p {
				margin: 8px 0 0 0;
				font-size: 13px;
				font-style: italic;
				color: #666;
			}

			/* CSS cho thông tin cửa hàng */
			.store-info-card {
				margin: 8px 0;
				padding: 12px;
				border-radius: 8px;
				background-color: #f8f9ff;
				border: 1px solid #e3e6f3;
			}

			.store-info-header {
				display: flex;
				align-items: center;
				margin-bottom: 10px;
				border-bottom: 1px solid #e3e6f3;
				padding-bottom: 8px;
			}

			.store-info-header i {
				font-size: 18px;
				color: #4a60a1;
				margin-right: 8px;
			}

			.store-info-header h4 {
				margin: 0;
				color: #333;
				font-size: 16px;
				font-weight: 600;
			}

			.store-branches {
				margin-bottom: 10px;
			}

			.branch-item {
				margin-bottom: 8px;
				padding: 6px 8px;
				border-radius: 6px;
				background-color: white;
				border-left: 3px solid #4a60a1;
			}

			.branch-name {
				font-weight: 600;
				color: #333;
				font-size: 14px;
				margin-bottom: 2px;
			}

			.branch-name i {
				color: #e74c3c;
				margin-right: 5px;
			}

			.branch-address {
				font-size: 13px;
				color: #555;
				padding-left: 20px;
			}

			.store-info-footer {
				margin-top: 10px;
				padding-top: 8px;
				border-top: 1px solid #e3e6f3;
			}

			.info-item {
				display: flex;
				align-items: center;
				margin-bottom: 5px;
				font-size: 13px;
				color: #444;
			}

			.info-item i {
				width: 16px;
				margin-right: 8px;
				color: #4a60a1;
			}
		`;
		document.head.appendChild(styleElement);
	}
}

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

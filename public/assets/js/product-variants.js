/**
 * Product Variants JavaScript
 * zone Fashion E-commerce Platform
 */

class ProductVariantManager {
	constructor(productId, variants, attributes) {
		this.productId = productId;
		this.variants = variants || [];
		this.attributes = attributes || [];
		this.selectedVariant = null;
		this.selectedAttributes = {};

		this.init();
	}

	init() {
		this.setupAttributeSelectors();
		this.updateVariantDisplay();
		this.bindEvents();
	}

	setupAttributeSelectors() {
		// Create attribute selectors based on available attributes
		const attributeContainer =
			document.getElementById('product-attributes');
		if (!attributeContainer) return;

		let html = '';

		this.attributes.forEach((attribute) => {
			html += `
                <div class="attribute-group mb-3">
                    <label class="form-label fw-bold">${attribute.name}:</label>
                    <div class="attribute-values" data-attribute-type="${
						attribute.type
					}">
                        ${this.renderAttributeValues(attribute)}
                    </div>
                </div>
            `;
		});

		attributeContainer.innerHTML = html;
	}

	renderAttributeValues(attribute) {
		let html = '';

		if (attribute.type === 'color') {
			// Render color swatches
			attribute.values.forEach((value) => {
				const isSelected =
					this.selectedAttributes[attribute.type] === value.id;
				html += `
                    <div class="color-option ${isSelected ? 'selected' : ''}"
                         data-attribute-id="${attribute.id}"
                         data-value-id="${value.id}"
                         title="${value.value}">
                        <div class="color-swatch"
                             style="background-color: ${
									value.color_code || '#ccc'
								}"></div>
                        <span class="color-name">${value.value}</span>
                    </div>
                `;
			});
		} else if (attribute.type === 'size') {
			// Render size buttons
			attribute.values.forEach((value) => {
				const isSelected =
					this.selectedAttributes[attribute.type] === value.id;
				const isAvailable = this.isValueAvailable(
					attribute.type,
					value.id
				);
				html += `
                    <button type="button"
                            class="btn btn-outline-primary size-option ${
								isSelected ? 'active' : ''
							} ${!isAvailable ? 'disabled' : ''}"
                            data-attribute-id="${attribute.id}"
                            data-value-id="${value.id}"
                            ${!isAvailable ? 'disabled' : ''}>
                        ${value.value}
                    </button>
                `;
			});
		} else {
			// Render as dropdown for other attributes
			html = `
                <select class="form-select attribute-select"
                        data-attribute-id="${attribute.id}"
                        data-attribute-type="${attribute.type}">
                    <option value="">Chọn ${attribute.name.toLowerCase()}</option>
                    ${attribute.values
						.map(
							(value) => `
                        <option value="${value.id}"
                                ${
									this.selectedAttributes[attribute.type] ===
									value.id
										? 'selected'
										: ''
								}
                                ${
									!this.isValueAvailable(
										attribute.type,
										value.id
									)
										? 'disabled'
										: ''
								}>
                            ${value.value}
                        </option>
                    `
						)
						.join('')}
                </select>
            `;
		}

		return html;
	}

	bindEvents() {
		// Color selection
		document.addEventListener('click', (e) => {
			if (e.target.closest('.color-option')) {
				const colorOption = e.target.closest('.color-option');
				const attributeType =
					colorOption.closest('.attribute-values').dataset
						.attributeType;
				const valueId = parseInt(colorOption.dataset.valueId);

				this.selectAttributeValue(attributeType, valueId);
			}
		});

		// Size selection
		document.addEventListener('click', (e) => {
			if (e.target.classList.contains('size-option')) {
				const attributeType =
					e.target.closest('.attribute-values').dataset.attributeType;
				const valueId = parseInt(e.target.dataset.valueId);

				this.selectAttributeValue(attributeType, valueId);
			}
		});

		// Dropdown selection
		document.addEventListener('change', (e) => {
			if (e.target.classList.contains('attribute-select')) {
				const attributeType = e.target.dataset.attributeType;
				const valueId = e.target.value
					? parseInt(e.target.value)
					: null;

				this.selectAttributeValue(attributeType, valueId);
			}
		});
	}

	selectAttributeValue(attributeType, valueId) {
		// Update selected attributes
		if (valueId) {
			this.selectedAttributes[attributeType] = valueId;
		} else {
			delete this.selectedAttributes[attributeType];
		}

		// Update UI
		this.updateAttributeSelectors();
		this.updateVariantDisplay();
		this.checkVariantAvailability();
	}

	updateAttributeSelectors() {
		// Update color options
		document.querySelectorAll('.color-option').forEach((option) => {
			const attributeType =
				option.closest('.attribute-values').dataset.attributeType;
			const valueId = parseInt(option.dataset.valueId);

			option.classList.toggle(
				'selected',
				this.selectedAttributes[attributeType] === valueId
			);
		});

		// Update size buttons
		document.querySelectorAll('.size-option').forEach((button) => {
			const attributeType =
				button.closest('.attribute-values').dataset.attributeType;
			const valueId = parseInt(button.dataset.valueId);

			button.classList.toggle(
				'active',
				this.selectedAttributes[attributeType] === valueId
			);
		});

		// Update available options based on selections
		this.updateAvailableOptions();
	}

	updateAvailableOptions() {
		// Disable unavailable combinations
		this.attributes.forEach((attribute) => {
			attribute.values.forEach((value) => {
				const isAvailable = this.isValueAvailable(
					attribute.type,
					value.id
				);

				// Update color options
				const colorOption = document.querySelector(
					`.color-option[data-value-id="${value.id}"]`
				);
				if (colorOption) {
					colorOption.classList.toggle('unavailable', !isAvailable);
				}

				// Update size buttons
				const sizeButton = document.querySelector(
					`.size-option[data-value-id="${value.id}"]`
				);
				if (sizeButton) {
					sizeButton.disabled = !isAvailable;
					sizeButton.classList.toggle('disabled', !isAvailable);
				}

				// Update dropdown options
				const selectOption = document.querySelector(
					`.attribute-select option[value="${value.id}"]`
				);
				if (selectOption) {
					selectOption.disabled = !isAvailable;
				}
			});
		});
	}

	isValueAvailable(attributeType, valueId) {
		// Check if this value is available given current selections
		const testAttributes = { ...this.selectedAttributes };
		testAttributes[attributeType] = valueId;

		// Find variants that match all selected attributes
		return this.variants.some((variant) => {
			return Object.entries(testAttributes).every(([type, id]) => {
				// Check if variant has attributes array
				if (!variant.attributes || !Array.isArray(variant.attributes)) {
					return false;
				}
				return variant.attributes.some(
					(attr) =>
						attr.attribute_type === type && attr.value_id === id
				);
			});
		});
	}

	checkVariantAvailability() {
		// Find matching variant
		this.selectedVariant = this.findMatchingVariant();

		if (this.selectedVariant) {
			this.showVariantDetails(this.selectedVariant);
			this.enableAddToCart();
		} else {
			this.showDefaultDetails();
			this.disableAddToCart();
		}
	}

	findMatchingVariant() {
		const selectedValueIds = Object.values(this.selectedAttributes);

		if (selectedValueIds.length === 0) {
			return null;
		}

		return this.variants.find((variant) => {
			// Check if variant has all selected attributes
			return selectedValueIds.every((valueId) => {
				// Check if variant has attributes array
				if (!variant.attributes || !Array.isArray(variant.attributes)) {
					return false;
				}
				return variant.attributes.some(
					(attr) => attr.value_id === valueId
				);
			});
		});
	}

	showVariantDetails(variant) {
		// Update price
		const priceElement = document.getElementById('product-price');
		if (priceElement) {
			const price = variant.sale_price || variant.price;
			priceElement.innerHTML = this.formatPrice(price);

			if (variant.sale_price) {
				priceElement.innerHTML += ` <span class="text-muted text-decoration-line-through ms-2">${this.formatPrice(
					variant.price
				)}</span>`;
			}
		}

		// Update stock
		const stockElement = document.getElementById('product-stock');
		if (stockElement) {
			const availableStock =
				variant.stock_quantity - (variant.reserved_quantity || 0);
			stockElement.textContent = `Còn ${availableStock} sản phẩm`;
			stockElement.className =
				availableStock > 0 ? 'text-success' : 'text-danger';
		}

		// Update SKU
		const skuElement = document.getElementById('product-sku');
		if (skuElement) {
			skuElement.textContent = variant.sku;
		}

		// Update image if variant has specific image
		if (variant.image) {
			this.updateProductImage(variant.image);
		}

		// Update variant info in add to cart form
		const variantInput = document.getElementById('selected-variant-id');
		if (variantInput) {
			variantInput.value = variant.id;
		}
	}

	showDefaultDetails() {
		// Show default product details when no variant is selected
		const priceElement = document.getElementById('product-price');
		if (priceElement && window.productData) {
			const price =
				window.productData.sale_price || window.productData.price;
			priceElement.innerHTML = this.formatPrice(price);

			if (window.productData.sale_price) {
				priceElement.innerHTML += ` <span class="text-muted text-decoration-line-through ms-2">${this.formatPrice(
					window.productData.price
				)}</span>`;
			}
		}

		// Clear variant input
		const variantInput = document.getElementById('selected-variant-id');
		if (variantInput) {
			variantInput.value = '';
		}
	}

	updateProductImage(imagePath) {
		const mainImage = document.querySelector(
			'.product-gallery .swiper-slide img'
		);
		if (mainImage) {
			mainImage.src = this.getImageUrl(imagePath);
		}
	}

	enableAddToCart() {
		const addToCartBtn = document.getElementById('add-to-cart-btn');
		if (addToCartBtn) {
			addToCartBtn.disabled = false;
			addToCartBtn.removeAttribute('aria-disabled');
			addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
		}
	}

	disableAddToCart() {
		const addToCartBtn = document.getElementById('add-to-cart-btn');
		if (addToCartBtn) {
			addToCartBtn.disabled = true;
			addToCartBtn.setAttribute('aria-disabled', 'true');
			// If no variant selected because of availability, show 'Hết Hàng'
			addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
		}
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		})
			.format(price)
			.replace('₫', '₫');
	}

	getImageUrl(imagePath) {
		// Use the same image URL logic as in client.js
		if (typeof getImageUrl === 'function') {
			return getImageUrl(imagePath);
		}
		return imagePath;
	}

	updateVariantDisplay() {
		// Update variant summary display
		const variantSummary = document.getElementById('variant-summary');
		if (variantSummary) {
			const selectedValues = [];

			Object.entries(this.selectedAttributes).forEach(
				([type, valueId]) => {
					const attribute = this.attributes.find(
						(attr) => attr.type === type
					);
					if (attribute) {
						const value = attribute.values.find(
							(val) => val.id === valueId
						);
						if (value) {
							selectedValues.push(
								`${attribute.name}: ${value.value}`
							);
						}
					}
				}
			);

			if (selectedValues.length > 0) {
				variantSummary.innerHTML = `
                    <div class="selected-variant-info">
                        <small class="text-muted">Đã chọn:</small><br>
                        ${selectedValues.join(' • ')}
                    </div>
                `;
			} else {
				variantSummary.innerHTML = '';
			}
		}
	}
}

// Initialize variant manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	if (window.productVariants && window.productAttributes) {
		new ProductVariantManager(
			window.productId,
			window.productVariants,
			window.productAttributes
		);
	}
});

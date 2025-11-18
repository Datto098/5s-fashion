/**
 * Search Modal JavaScript
 * Handles AJAX search functionality with modal interface
 */

class SearchModal {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.searchButton = document.getElementById('searchButton');
        this.searchResults = document.getElementById('searchResults');
        this.searchResultsList = document.getElementById('searchResultsList');
        this.noResults = document.getElementById('noResults');
        this.popularSearches = document.getElementById('popularSearches');
        this.searchLoading = document.querySelector('.search-loading');
        this.viewAllResults = document.getElementById('viewAllResults');
        this.searchModal = document.getElementById('searchModal');
        
        this.currentQuery = '';
        this.searchTimeout = null;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        // Search input events
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
            
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch(e.target.value);
                }
            });
        }
        
        // Search button click
        if (this.searchButton) {
            this.searchButton.addEventListener('click', () => {
                this.performSearch(this.searchInput.value);
            });
        }
        
        // View all results button
        if (this.viewAllResults) {
            this.viewAllResults.addEventListener('click', () => {
                this.goToSearchPage(this.currentQuery);
            });
        }
        
        // Popular search tags
        const searchTags = document.querySelectorAll('.search-tag');
        searchTags.forEach(tag => {
            tag.addEventListener('click', () => {
                const searchTerm = tag.getAttribute('data-search');
                this.searchInput.value = searchTerm;
                this.handleSearch(searchTerm);
            });
        });
        
        // Modal events
        if (this.searchModal) {
            this.searchModal.addEventListener('shown.bs.modal', () => {
                this.searchInput.focus();
                this.showInitialState();
            });
            
            this.searchModal.addEventListener('hidden.bs.modal', () => {
                this.clearSearch();
            });
        }
        
        // Click outside modal to close
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('search-box-trigger') || 
                e.target.closest('.search-box-trigger')) {
                e.preventDefault();
            }
        });
    }
    
    handleSearch(query) {
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        query = query.trim();
        
        if (query.length === 0) {
            this.showInitialState();
            return;
        }
        
        if (query.length < 2) {
            return; // Don't search for single characters
        }
        
        // Debounce search
        this.searchTimeout = setTimeout(() => {
            this.performAjaxSearch(query);
        }, 300);
    }
    
    async performAjaxSearch(query) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.currentQuery = query;
        this.showLoading();
        
        try {
            // Use API endpoint
            const url = `${window.baseUrl}/api/search/suggestions?q=${encodeURIComponent(query)}`;
            console.log('Search URL:', url);
            
            const response = await fetch(url);
            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Search data:', data);
            
            // Debug: Log each product's image data
            if (data.success && data.data.suggestions) {
                console.log('Raw suggestions:', data.data.suggestions);
                data.data.suggestions.forEach((product, index) => {
                    console.log(`Product ${index + 1}:`, {
                        name: product.name,
                        image: product.image,
                        imageUrl: this.getProductImageUrl(product.image)
                    });
                });
            }
            
            if (data.success && data.data.suggestions && data.data.suggestions.length > 0) {
                this.displayResults(data.data.suggestions, data.data.total);
            } else {
                this.showNoResults();
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showNoResults();
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }
    
    displayResults(suggestions, total) {
        this.hideInitialState();
        this.hideNoResults();
        this.showResults();
        
        // Update view all button
        if (total > suggestions.length) {
            this.viewAllResults.style.display = 'inline-block';
            this.viewAllResults.textContent = `Xem tất cả (${total})`;
        } else {
            this.viewAllResults.style.display = 'none';
        }
        
        // Generate HTML for suggestions
        const html = suggestions.map(product => this.createProductHTML(product)).join('');
        this.searchResultsList.innerHTML = html;
    }
    
    createProductHTML(product) {
        const discountHTML = product.has_discount ? 
            `<span class="discount-badge">-${product.discount_percent}%</span>` : '';
        
        const priceHTML = product.has_discount ? 
            `<div class="search-product-price">
                ${this.formatPrice(product.price)}đ
                <span class="original-price ms-1">${this.formatPrice(product.original_price)}đ</span>
            </div>` :
            `<div class="search-product-price">${this.formatPrice(product.price)}đ</div>`;
        
        return `
            <div class="col-12 mb-2">
                <a href="${product.url}" class="search-result-item d-block p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="product-image-container">
                                <img src="${this.getProductImageUrl(product.image)}" 
                                     alt="${product.name}" 
                                     class="product-image"
                                     onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\'no-image-placeholder\'>No Image</div>'"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="category-name text-muted small">${product.category}</div>
                            <div class="product-name text-truncate">${product.name}</div>
                            ${priceHTML}
                        </div>
                        <div class="flex-shrink-0">
                            ${discountHTML}
                        </div>
                    </div>
                </a>
            </div>
        `;
    }
    
    getProductImageUrl(imagePath) {
        console.log('Original image path:', imagePath);
        console.log('Base URL:', window.baseUrl);
        
        // Kiểm tra các trường hợp null/empty
        if (!imagePath || imagePath === 'null' || imagePath === '' || imagePath === 'NULL' || imagePath === null) {
            const noImageUrl = `${window.baseUrl}/assets/images/no-image.jpg`;
            console.log('Using no-image:', noImageUrl);
            return noImageUrl;
        }
        
        if (imagePath.startsWith('http')) {
            return imagePath;
        }
        
        // Remove leading slash if present  
        let cleanPath = imagePath.replace(/^\/+/, '');
        
        let fileName;
        
        // Nếu đường dẫn đã có 'uploads/products/' thì lấy tên file
        if (cleanPath.startsWith('uploads/products/')) {
            fileName = cleanPath.replace('uploads/products/', '');
        } 
        // Nếu đường dẫn bắt đầu với 'products/' thì lấy tên file
        else if (cleanPath.startsWith('products/')) {
            fileName = cleanPath.replace('products/', '');
        }
        // Nếu chỉ là tên file
        else {
            fileName = cleanPath;
        }
        
        // Sử dụng serve-file.php theo format: serve-file.php?file=products%2F[filename]
        const fullImageUrl = `${window.baseUrl}/serve-file.php?file=products%2F${encodeURIComponent(fileName)}`;
        
        console.log('Clean path:', cleanPath);
        console.log('File name:', fileName);
        console.log('Final image URL:', fullImageUrl);
        
        return fullImageUrl;
    }
    
    formatPrice(price) {
        return parseInt(price).toLocaleString('vi-VN');
    }
    
    performSearch(query) {
        if (query.trim()) {
            this.goToSearchPage(query.trim());
        }
    }
    
    goToSearchPage(query) {
        window.location.href = `${window.baseUrl}/search?q=${encodeURIComponent(query)}`;
    }
    
    showLoading() {
        this.searchLoading.classList.remove('d-none');
        this.hideResults();
        this.hideNoResults();
        this.hideInitialState();
    }
    
    hideLoading() {
        this.searchLoading.classList.add('d-none');
    }
    
    showResults() {
        this.searchResults.classList.remove('d-none');
    }
    
    hideResults() {
        this.searchResults.classList.add('d-none');
    }
    
    showNoResults() {
        this.hideInitialState();
        this.hideResults();
        this.noResults.classList.remove('d-none');
    }
    
    hideNoResults() {
        this.noResults.classList.add('d-none');
    }
    
    showInitialState() {
        this.hideResults();
        this.hideNoResults();
        this.popularSearches.classList.remove('d-none');
    }
    
    hideInitialState() {
        this.popularSearches.classList.add('d-none');
    }
    
    clearSearch() {
        this.searchInput.value = '';
        this.currentQuery = '';
        this.hideLoading();
        this.hideResults();
        this.hideNoResults();
        this.showInitialState();
        
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Set base URL for AJAX requests
    window.baseUrl = window.baseUrl || '';
    
    // Initialize search modal
    new SearchModal();
});
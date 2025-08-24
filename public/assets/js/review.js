// Xử lý nút Like và Delete cho các đánh giá
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện khi nhấn nút Like
    document.querySelectorAll('.like-review-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            likeReview(reviewId, this);
        });
    });

    // Xử lý sự kiện khi nhấn nút Delete
    document.querySelectorAll('.delete-review-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            if (confirm('Bạn có chắc chắn muốn xóa đánh giá này không?')) {
                deleteReview(reviewId, this);
            }
        });
    });

    // Xử lý sự kiện khi nhấn nút Sửa
    document.querySelectorAll('.btn-edit-review').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const reviewItem = this.closest('.review-item');
            if (!reviewItem) return;

            // Lấy tiêu đề và nội dung cũ
            let title = '';
            const titleElem = reviewItem.querySelector('.review-title');
            if (titleElem) {
                title = titleElem.innerText.trim();
            } else {
                // Nếu không có class review-title, thử lấy thẳng từ thẻ strong/h6 đầu đoạn nội dung
                const strong = reviewItem.querySelector('.review-content strong');
                if (strong) title = strong.innerText.trim();
            }
            const content = reviewItem.querySelector('.review-content p')?.innerText || '';
            const rating = reviewItem.querySelectorAll('.review-rating .fa-star.text-warning').length;
            // Scroll tới form
            const reviewFormDiv = document.getElementById('reviewForm');
            if (reviewFormDiv && reviewFormDiv.style.display === 'none') {
                reviewFormDiv.style.display = '';
            }
            reviewFormDiv?.scrollIntoView({ behavior: 'smooth' });

            // Điền lại nội dung và rating vào form (form id submitReviewForm, textarea name=content, input name=rating)
            setTimeout(function() {
                const form = document.getElementById('submitReviewForm');
                if (form) {
                    // Điền tiêu đề
                    const titleInput = form.querySelector('input[name="title"]');
                    if (titleInput) titleInput.value = title;
                    // Điền nội dung
                    const textarea = form.querySelector('textarea[name="content"]');
                    if (textarea) textarea.value = content;
                    // Nếu có input hidden review_id thì set để biết là đang sửa
                    let reviewIdInput = form.querySelector('input[name="review_id"]');
                    if (!reviewIdInput) {
                        reviewIdInput = document.createElement('input');
                        reviewIdInput.type = 'hidden';
                        reviewIdInput.name = 'review_id';
                        form.appendChild(reviewIdInput);
                    }
                    reviewIdInput.value = reviewId;
                    // Set rating nếu có
                    if (typeof setReviewFormRating === 'function') {
                        setReviewFormRating(rating);
                    } else {
                        // Nếu không có hàm, thử set input radio
                        const ratingInput = form.querySelector('input[name="rating"][value="' + rating + '"]');
                        if (ratingInput) ratingInput.checked = true;
                    }
                }
            }, 300);
        });
    });
});

// Hàm xử lý like review với toggle functionality
function likeReview(reviewId, button) {
    // Vô hiệu hóa nút trong khi đang xử lý
    button.disabled = true;
    
    fetch(`/5s-fashion/ajax/review/like/${reviewId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượt thích
            const countElement = button.querySelector('.helpful-count');
            if (countElement) {
                countElement.textContent = data.helpfulCount;
            }
            
            // Toggle trạng thái liked/unliked dựa vào phản hồi từ server
            if (data.action === 'liked') {
                button.classList.add('liked');
            } else if (data.action === 'unliked') {
                button.classList.remove('liked');
            }
            
            // Hiển thị thông báo
            showToast('success', data.message);
        } else {
            showToast('warning', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Đã xảy ra lỗi khi xử lý yêu cầu');
    })
    .finally(() => {
        // Kích hoạt lại nút sau khi xử lý xong
        button.disabled = false;
    });
}

// Hàm xử lý xóa đánh giá
function deleteReview(reviewId, button) {
    // Vô hiệu hóa nút trong khi đang xử lý
    button.disabled = true;
    
    fetch(`/5s-fashion/ajax/review/delete/${reviewId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Xóa review khỏi DOM
            
            // Xóa phần tử đánh giá khỏi DOM
            const reviewItem = button.closest('.review-item');
            if (reviewItem) {
                reviewItem.remove();
            }
            
            // Hiển thị thông báo
            showToast('success', data.message);
            
            // Kiểm tra xem còn đánh giá nào không
            const reviewList = document.querySelector('.review-list');
            if (reviewList) {
                if (reviewList.children.length === 0 || data.canAddReview) {
                    // Nếu không còn đánh giá nào hoặc server trả về canAddReview = true
                    
                    // Ẩn thông báo "Bạn đã đánh giá sản phẩm này rồi" nếu có
                    const reviewedAlerts = document.querySelectorAll('.alert-info strong');
                    reviewedAlerts.forEach(alert => {
                        if (alert.textContent.includes('Bạn đã đánh giá sản phẩm này rồi')) {
                            const alertDiv = alert.closest('.alert-info');
                            if (alertDiv) alertDiv.style.display = 'none';
                        }
                    });
                    
                    // Hiển thị form đánh giá nếu có
                    const reviewForm = document.getElementById('review-form');
                    if (reviewForm) {
                        // Kiểm tra xem có form input không
                        const formInput = reviewForm.querySelector('form');
                        if (!formInput) {
                            // Nếu không có form thì tạo form mới bằng cách reload trang
                            location.reload();
                            return;
                        } else {
                            // Hiển thị form nếu đang ẩn
                            const existingForm = reviewForm.querySelector('form');
                            if (existingForm) {
                                existingForm.style.display = '';
                            }
                        }
                    }
                    
                    // Hiển thị thông báo "không có đánh giá"
                    if (reviewList.children.length === 0) {
                        const emptyMessage = `
                            <div class="text-center py-4 border rounded bg-light">
                                <i class="fas fa-star fs-1 text-muted mb-3"></i>
                                <p>Sản phẩm này chưa có đánh giá nào.</p>
                                <button class="btn btn-outline-primary" onclick="scrollToReviewForm()">
                                    <i class="fas fa-edit me-2"></i>Viết đánh giá đầu tiên
                                </button>
                            </div>
                        `;
                        reviewList.innerHTML = emptyMessage;
                    }
                }
            }
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Đã xảy ra lỗi khi xử lý yêu cầu');
    })
    .finally(() => {
        // Kích hoạt lại nút sau khi xử lý xong
        button.disabled = false;
    });
}

// Hiển thị thông báo toast
function showToast(type, message) {
    // Kiểm tra xem đã có container cho toast chưa
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Tạo ID duy nhất cho toast
    const toastId = 'toast-' + new Date().getTime();
    
    // Xác định class dựa trên loại thông báo
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    
    // Tạo HTML cho toast
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Thêm toast vào container
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Khởi tạo và hiển thị toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
    toast.show();
    
    // Xóa toast khỏi DOM sau khi ẩn
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Cuộn đến form đánh giá
function scrollToReviewForm() {
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.scrollIntoView({ behavior: 'smooth' });
    }
}
console.log('Review script loaded - Version 1.0');

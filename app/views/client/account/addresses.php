
<?php
// Start output buffering for content
ob_start();
?>

<div class="account-container py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="account-sidebar">
                    <div class="user-info text-center mb-4">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle fa-4x text-danger"></i>
                        </div>
                        <h5 class="mt-2"><?= htmlspecialchars(getUser()['name'] ?? getUser()['full_name'] ?? 'User') ?></h5>
                        <p class="text-muted"><?= htmlspecialchars(getUser()['email'] ?? '') ?></p>
                    </div>

                    <nav class="account-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account/profile') ?>">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('orders') ?>">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= url('addresses') ?>">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('wishlist') ?>">
                                    <i class="fas fa-heart me-2"></i>Sản phẩm yêu thích
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account/password') ?>">
                                    <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="<?= url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <div class="account-content">
                    <div class="content-header mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="content-title">Địa chỉ của tôi</h2>
                            <p class="content-subtitle">Quản lý địa chỉ giao hàng</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                        </button>
                    </div>

                    <!-- Address List -->
                    <?php if (empty($addresses)): ?>
                        <div class="empty-addresses text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <h4>Chưa có địa chỉ nào</h4>
                            <p class="text-muted mb-4">Thêm địa chỉ giao hàng để mua sắm thuận tiện hơn!</p>
                            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>Thêm địa chỉ đầu tiên
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="addresses-list">
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <div class="address-info">
                                            <h6 class="address-name">
                                                <?= htmlspecialchars($address['name']) ?>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="badge bg-primary ms-2">Mặc định</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="address-phone"><?= htmlspecialchars($address['phone']) ?></p>
                                        </div>
                                        <div class="address-actions">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </div>

                                    <div class="address-body">
                                        <p class="address-full">
                                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                            <?= htmlspecialchars($address['address']) ?>
                                        </p>
                                    </div>

                                    <?php if (!$address['is_default']): ?>
                                        <div class="address-footer">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="setDefaultAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-star me-1"></i>Đặt làm mặc định
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm" action="<?= url('account/addAddress') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
<div class="mb-3">
    <label for="address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ, ví dụ: 123 Lê Lợi, Quận 1, TP.HCM" autocomplete="off" required>
        <button class="btn btn-outline-secondary" type="button" id="searchAddressBtn" title="Tìm trên bản đồ">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                <input type="hidden" id="lat" name="lat">
                                <input type="hidden" id="lng" name="lng">
                            </div>
                        </div>
                    </div>

<div class="mb-3">
    <label for="note" class="form-label">Ghi chú địa chỉ (nếu có)</label>
    <textarea class="form-control" id="note" name="note" rows="3"
              placeholder="Ghi chú thêm về địa chỉ, ví dụ: Gần trường học, tầng 2..."></textarea>
</div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu địa chỉ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa địa chỉ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAddressForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_address_id">
                    <input type="hidden" id="edit_lat" >
                    <input type="hidden" id="edit_lng" >
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_address" name="address" required>
                                    <button class="btn btn-outline-secondary" type="button" id="edit_searchAddressBtn" title="Tìm trên bản đồ">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div id="edit_map" style="height: 300px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_note" class="form-label">Ghi chú địa chỉ (nếu có)</label>
                        <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_default" name="is_default">
                        <label class="form-check-label" for="edit_is_default">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Leaflet map custom style */
#map {
    width: 100%;
    margin-bottom: 10px;
}
.account-container {
    background: #f8f9fa;
    min-height: 100vh;
}

.account-sidebar {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.user-avatar {
    margin-bottom: 15px;
}

.account-nav .nav-link {
    color: #6c757d;
    border: none;
    text-align: left;
    margin-bottom: 5px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.account-nav .nav-link:hover,
.account-nav .nav-link.active {
    background: #dc3545;
    color: white;
}

.account-nav .nav-link.text-danger:hover {
    background: #dc3545;
    color: white;
}

.account-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.content-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 5px;
}

.content-subtitle {
    color: #6c757d;
    margin: 0;
}

.address-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.address-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.address-header {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.address-name {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.address-phone {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.address-body {
    padding: 15px 20px;
}

.address-full {
    margin: 0;
    color: #333;
}

.address-footer {
    background: #f8f9fa;
    padding: 10px 20px;
    border-top: 1px solid #e9ecef;
}

.address-actions .btn {
    margin-left: 5px;
}

.empty-addresses {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 40px;
    margin: 20px 0;
}

@media (max-width: 768px) {
    .account-container {
        padding: 20px 0;
    }

    .account-content {
        padding: 20px;
    }

    .content-header {
        flex-direction: column;
        text-align: center;
    }

    .content-header .btn {
        margin-top: 15px;
    }

    .address-header {
        flex-direction: column;
        text-align: center;
    }

    .address-actions {
        margin-top: 10px;
    }
}
</style>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
// Khởi tạo lại map khi mở modal sửa địa chỉ
let editMap, editMarker;
const editModal = document.getElementById('editAddressModal');
editModal.addEventListener('shown.bs.modal', function () {
    // Xóa map cũ nếu có
    if (editMap) {
        editMap.remove();
        editMap = null;
    }
    setTimeout(function() {
        editMap = L.map('edit_map').setView([21.0285, 105.8542], 13); // Mặc định: Hà Nội
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(editMap);
        editMap.on('click', function(e) {
            setEditMarker(e.latlng.lat, e.latlng.lng);
        });
        // Nếu đã có lat/lng thì set lại marker
        const lat = document.getElementById('edit_lat').value;
        const lng = document.getElementById('edit_lng').value;
        if (lat && lng) setEditMarker(lat, lng);
    }, 200);
});

function setEditMarker(lat, lng, retry = 0) {
    if (!editMap) {
        // Nếu map chưa sẵn sàng, thử lại sau 100ms (tối đa 10 lần)
        if (retry < 10) setTimeout(() => setEditMarker(lat, lng, retry + 1), 100);
        return;
    }
    if (editMarker) editMarker.remove();
    editMarker = L.marker([lat, lng]).addTo(editMap);
    document.getElementById('edit_lat').value = lat;
    document.getElementById('edit_lng').value = lng;
    // Đảm bảo map luôn zoom đến marker
    editMap.setView([lat, lng], 17, { animate: true });
    // Lấy địa chỉ từ lat/lng và điền vào input address
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('edit_address').value = data.display_name;
            }
        });
}

// Tìm kiếm địa chỉ với Nominatim cho modal sửa
function searchEditAddressOnMap() {
    let query = document.getElementById('edit_address').value;
    if (!query) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                let lat = parseFloat(data[0].lat);
                let lon = parseFloat(data[0].lon);
                setEditMarker(lat, lon);
            } else {
                alert('Không tìm thấy địa chỉ phù hợp!');
            }
        });
}
document.getElementById('edit_address').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchEditAddressOnMap();
    }
});
document.getElementById('edit_searchAddressBtn').addEventListener('click', function() {
    searchEditAddressOnMap();
});
// AJAX submit add address
document.getElementById('addAddressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Response data:', data); // Debug response
        if (data.success) {
            location.reload();
        } else {
            console.log('Error details:', data.debug); // Debug error details
            alert(data.message || 'Có lỗi xảy ra khi thêm địa chỉ!');
        }
    })
    .catch(() => {
        alert('Có lỗi xảy ra khi thêm địa chỉ!');
    });
});

function editAddress(addressId) {
    // Lấy dữ liệu địa chỉ từ danh sách (tránh thêm AJAX, dùng sẵn trên trang)
    const address = window.addressesList.find(a => a.id == addressId);
    if (!address) {
        alert('Không tìm thấy địa chỉ!');
        return;
    }
    document.getElementById('edit_address_id').value = address.id;
    document.getElementById('edit_name').value = address.name;
    document.getElementById('edit_phone').value = address.phone;
    document.getElementById('edit_address').value = address.address;
    document.getElementById('edit_note').value = address.note || '';
    document.getElementById('edit_is_default').checked = address.is_default == 1;
    document.getElementById('edit_lat').value = address.lat || '';
    document.getElementById('edit_lng').value = address.lng || '';
    var modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
    modal.show();
    // Đảm bảo dot (marker) xuất hiện ngay khi mở modal
    setTimeout(function() {
        const lat = address.lat;
        const lng = address.lng;
        if (lat && lng && editMap) {
            setEditMarker(lat, lng);
        } else if (address.address && editMap) {
            // Nếu chưa có lat/lng, dùng Nominatim để lấy tọa độ từ địa chỉ
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address.address)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        setEditMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
                    }
                });
        }
    }, 350); // Đợi modal và map render xong
}

// Submit sửa địa chỉ
document.getElementById('editAddressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const addressId = document.getElementById('edit_address_id').value;
    const formData = new FormData(form);
    fetch('<?= url('account/editAddress/') ?>/' + addressId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch (e) {
            alert('Lỗi parse JSON khi sửa! Xem console để biết chi tiết.');
            return;
        }
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Không thể sửa địa chỉ!');
        }
    })
    .catch(() => {
        alert('Có lỗi xảy ra khi sửa địa chỉ!');
    });
});
// Lưu danh sách địa chỉ vào JS để dùng cho editAddress
window.addressesList = <?php echo json_encode(array_map(function($a) {
    $a['id'] = (int)$a['id'];
    $a['is_default'] = (int)$a['is_default'];
    $a['lat'] = isset($a['lat']) ? $a['lat'] : '';
    $a['lng'] = isset($a['lng']) ? $a['lng'] : '';
    return $a;
}, $addresses ?? [])); ?>;

function deleteAddress(addressId) {
    if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) return;
    fetch('<?= url('account/deleteAddress') ?>/' + addressId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(text => {
        console.log('Raw response:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            alert('Lỗi parse JSON khi xóa! Xem console để biết chi tiết.');
            return;
        }
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Không thể xóa địa chỉ!');
        }
    })
    .catch(() => {
        alert('Có lỗi xảy ra khi xóa địa chỉ!');
    });
}

function setDefaultAddress(addressId) {
    fetch('<?= url('account/setDefaultAddress') ?>/' + addressId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(text => {
        console.log('Raw response:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            alert('Lỗi parse JSON! Xem console để biết chi tiết.');
            return;
        }
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Không thể đặt địa chỉ mặc định!');
        }
    })
    .catch(() => {
        alert('Có lỗi xảy ra!');
    });
}
// Sau khi modal đóng (Bootstrap 5)
document.getElementById('addAddressModal').addEventListener('hidden.bs.modal', function () {
    // Chuyển focus về nút mở modal (hoặc body)
    document.body.focus();
});

// Khởi tạo lại map mỗi lần mở modal để tránh lỗi hiển thị
let map, marker;
const modal = document.getElementById('addAddressModal');
modal.addEventListener('shown.bs.modal', function () {
    // Xóa map cũ nếu có
    if (map) {
        map.remove();
        map = null;
    }
    setTimeout(function() {
        map = L.map('map').setView([21.0285, 105.8542], 13); // Mặc định: Hà Nội
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
        map.on('click', function(e) {
            setMarker(e.latlng.lat, e.latlng.lng);
        });
        // Nếu đã có lat/lng thì set lại marker
        const lat = document.getElementById('lat').value;
        const lng = document.getElementById('lng').value;
        if (lat && lng) setMarker(lat, lng);
    }, 200);
});

function setMarker(lat, lng) {
    if (marker) marker.remove();
    marker = L.marker([lat, lng]).addTo(map);
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    map.setView([lat, lng], 17);
    // Lấy địa chỉ từ lat/lng và điền vào input address
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('address').value = data.display_name;
            }
        });
}

// Tìm kiếm địa chỉ với Nominatim
function searchAddressOnMap() {
    let query = document.getElementById('address').value;
    if (!query) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                let lat = parseFloat(data[0].lat);
                let lon = parseFloat(data[0].lon);
                setMarker(lat, lon);
            } else {
                alert('Không tìm thấy địa chỉ phù hợp!');
            }
        });
}
document.getElementById('address').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchAddressOnMap();
    }
});
document.getElementById('searchAddressBtn').addEventListener('click', function() {
    searchAddressOnMap();
});
</script>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>

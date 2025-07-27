<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Cài đặt hệ thống
                    </h1>
                    <p class="text-muted mb-0">Quản lý các cài đặt và cấu hình của website</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Settings Navigation -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Nhóm cài đặt
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="nav nav-pills flex-column" id="settings-nav" role="tablist">
                        <?php
                        $groupNames = [
                            'general' => ['name' => 'Tổng quát', 'icon' => 'fas fa-globe'],
                            'contact' => ['name' => 'Liên hệ', 'icon' => 'fas fa-address-book'],
                            'social' => ['name' => 'Mạng xã hội', 'icon' => 'fas fa-share-alt'],
                            'ecommerce' => ['name' => 'Thương mại', 'icon' => 'fas fa-shopping-cart']
                        ];
                        $first = true;
                        foreach ($settings as $group => $groupSettings):
                            $groupInfo = $groupNames[$group] ?? ['name' => ucfirst($group), 'icon' => 'fas fa-cog'];
                        ?>
                            <a class="nav-link <?= $first ? 'active' : '' ?>"
                               id="<?= $group ?>-tab"
                               data-bs-toggle="pill"
                               href="#<?= $group ?>"
                               role="tab">
                                <i class="<?= $groupInfo['icon'] ?> me-2"></i>
                                <?= $groupInfo['name'] ?>
                                <span class="badge bg-secondary ms-auto"><?= count($groupSettings) ?></span>
                            </a>
                        <?php
                            $first = false;
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="settings-content">
                <?php
                $first = true;
                foreach ($settings as $group => $groupSettings):
                    $groupInfo = $groupNames[$group] ?? ['name' => ucfirst($group), 'icon' => 'fas fa-cog'];
                ?>
                    <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                         id="<?= $group ?>"
                         role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="<?= $groupInfo['icon'] ?> me-2"></i>
                                    <?= $groupInfo['name'] ?>
                                </h5>
                                <button type="button" class="btn btn-outline-warning btn-sm"
                                        onclick="resetGroup('<?= $group ?>')">
                                    <i class="fas fa-undo me-1"></i>
                                    Khôi phục mặc định
                                </button>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="/5s-fashion/admin/settings/update" enctype="multipart/form-data">
                                    <input type="hidden" name="group" value="<?= $group ?>">

                                    <?php foreach ($groupSettings as $setting): ?>
                                        <div class="mb-3">
                                            <label for="setting_<?= $setting['key'] ?>" class="form-label">
                                                <?= ucwords(str_replace('_', ' ', $setting['key'])) ?>
                                            </label>

                                            <?php if ($setting['type'] === 'text'): ?>
                                                <textarea name="setting_<?= $setting['key'] ?>"
                                                          id="setting_<?= $setting['key'] ?>"
                                                          class="form-control"
                                                          rows="3"><?= htmlspecialchars($setting['value'] ?? '') ?></textarea>
                                            <?php else: ?>
                                                <input type="<?= $setting['type'] === 'string' ? 'text' : $setting['type'] ?>"
                                                       name="setting_<?= $setting['key'] ?>"
                                                       id="setting_<?= $setting['key'] ?>"
                                                       class="form-control"
                                                       value="<?= htmlspecialchars($setting['value'] ?? '') ?>">
                                            <?php endif; ?>

                                            <div class="form-text">
                                                Key: <code><?= $setting['key'] ?></code>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Lưu cài đặt
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    $first = false;
                endforeach;
                ?>
            </div>
        </div>
    </div>

    <?php if (empty($settings)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                <h4>Chưa có cài đặt nào</h4>
                <p class="text-muted mb-3">Hệ thống chưa có cài đặt nào. Bạn có thể khởi tạo cài đặt mặc định.</p>
                <button type="button" class="btn btn-primary" onclick="initializeDefaults()">
                    <i class="fas fa-plus me-1"></i>
                    Khởi tạo cài đặt mặc định
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function resetGroup(group) {
    if (confirm(`Bạn có chắc muốn khôi phục tất cả cài đặt trong nhóm "${group}" về giá trị mặc định?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/5s-fashion/admin/settings/reset';

        const groupInput = document.createElement('input');
        groupInput.type = 'hidden';
        groupInput.name = 'group';
        groupInput.value = group;

        form.appendChild(groupInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function initializeDefaults() {
    if (confirm('Bạn có chắc muốn khởi tạo tất cả cài đặt mặc định?')) {
        fetch('/5s-fashion/admin/settings/initialize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể khởi tạo'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi khởi tạo cài đặt');
        });
    }
}

// Auto-save functionality (optional)
document.addEventListener('DOMContentLoaded', function() {
    // Add change event listeners to form inputs for auto-save indication
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add visual indication that settings need to be saved
            const submitBtn = this.closest('form').querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('btn-warning')) {
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-warning');
                submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Có thay đổi - Lưu ngay';
            }
        });
    });
});
</script>

<style>
.nav-pills .nav-link {
    border-radius: 0;
    color: #6c757d;
    border-bottom: 1px solid #dee2e6;
}

.nav-pills .nav-link:first-child {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-pills .nav-link:last-child {
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-bottom: none;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}

.nav-pills .nav-link:hover:not(.active) {
    background-color: #f8f9fa;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.form-text code {
    font-size: 0.8em;
    color: #6c757d;
}

.badge {
    font-size: 0.75em;
}
</style>

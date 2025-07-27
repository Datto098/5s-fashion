<?php
/**
 * Professional Customers Controller with Sidebar
 */

class CustomersController extends BaseController
{
    public function __construct()
    {
        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    public function index()
    {
        // Sample customers data (in real app, get from database)
        $customers = [
            [
                'id' => 1,
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@email.com',
                'phone' => '0901234567',
                'status' => 'active',
                'orders' => 5,
                'total_spent' => 2500000,
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'name' => 'Trần Thị B',
                'email' => 'tranthib@email.com',
                'phone' => '0902345678',
                'status' => 'active',
                'orders' => 8,
                'total_spent' => 4200000,
                'created_at' => '2024-02-20'
            ],
            [
                'id' => 3,
                'name' => 'Lê Văn C',
                'email' => 'levanc@email.com',
                'phone' => '0903456789',
                'status' => 'inactive',
                'orders' => 2,
                'total_spent' => 800000,
                'created_at' => '2024-03-10'
            ]
        ];

        $data = [
            'title' => 'Quản lý khách hàng - 5S Fashion Admin',
            'customers' => $customers,
            'totalCustomers' => count($customers),
            'activeCustomers' => count(array_filter($customers, fn($c) => $c['status'] === 'active'))
        ];

        $this->render('admin/customers/index', $data, 'admin/layouts/main-inline');
                            <span class="nav-icon icon-orders"></span>
                            <span class="nav-text">Đơn hàng</span>
                            <span class="nav-badge">43</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Khách hàng</div>
                    <div class="nav-item">
                        <a href="/5s-fashion/admin/customers" class="nav-link active">
                            <span class="nav-icon icon-customers"></span>
                            <span class="nav-text">Quản lý khách hàng</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">☰</button>
                    <nav class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <a href="/5s-fashion/admin" class="breadcrumb-link">Dashboard</a>
                        </div>
                        <div class="breadcrumb-item">Khách hàng</div>
                    </nav>
                </div>

                <div class="topbar-right">
                    <div class="topbar-search">
                        <input type="text" class="search-input" placeholder="Tìm kiếm khách hàng...">
                        <span class="search-icon icon-search"></span>
                    </div>

                    <div class="topbar-actions">
                        <button class="action-btn">
                            <span class="icon-notifications"></span>
                            <span class="notification-badge">3</span>
                        </button>

                        <div class="user-dropdown">
                            <button class="user-btn">
                                <div class="user-avatar">AD</div>
                                <div class="user-info">
                                    <div class="user-name">Admin</div>
                                    <div class="user-role">Quản trị viên</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="admin-content">
                <!-- Page Header -->
                <div class="admin-header">
                    <h1 class="admin-title">Quản lý khách hàng</h1>
                    <div class="admin-breadcrumb">
                        Theo dõi và quản lý thông tin khách hàng
                    </div>
                </div>

                <!-- Statistics -->
                <div class="admin-stats">
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">1,247</div>
                        <div class="admin-stat-label">Tổng khách hàng</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">89</div>
                        <div class="admin-stat-label">Khách hàng mới</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">156</div>
                        <div class="admin-stat-label">Khách VIP</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">₫2,450,000</div>
                        <div class="admin-stat-label">Giá trị trung bình</div>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Đơn hàng</th>
                                <th>Tổng chi tiêu</th>
                                <th>Nhóm</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 40px; height: 40px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                            NA
                                        </div>
                                        <div>
                                            <div class="font-medium">Nguyễn Văn A</div>
                                            <div class="text-muted" style="font-size: 0.875rem;">ID: #CUS001</div>
                                        </div>
                                    </div>
                                </td>
                                <td>nguyen.a@email.com</td>
                                <td>0901234567</td>
                                <td class="text-center">12</td>
                                <td class="font-semibold">₫8,450,000</td>
                                <td>
                                    <span class="status-badge status-warning">VIP</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="/5s-fashion/admin/customers/1" class="btn btn-sm btn-primary">Xem</a>
                                        <button class="btn btn-sm btn-outline">Sửa</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 40px; height: 40px; background: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                            TB
                                        </div>
                                        <div>
                                            <div class="font-medium">Trần Thị B</div>
                                            <div class="text-muted" style="font-size: 0.875rem;">ID: #CUS002</div>
                                        </div>
                                    </div>
                                </td>
                                <td>tran.b@email.com</td>
                                <td>0907654321</td>
                                <td class="text-center">8</td>
                                <td class="font-semibold">₫5,230,000</td>
                                <td>
                                    <span class="status-badge status-active">Thường</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="/5s-fashion/admin/customers/2" class="btn btn-sm btn-primary">Xem</a>
                                        <button class="btn btn-sm btn-outline">Sửa</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script>
        // Sidebar functionality
        const sidebarToggle = document.getElementById("sidebarToggle");
        const sidebar = document.getElementById("sidebar");
        const sidebarOverlay = document.getElementById("sidebarOverlay");

        sidebarToggle.addEventListener("click", function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle("show");
                sidebarOverlay.classList.toggle("show");
            }
        });

        sidebarOverlay.addEventListener("click", function() {
            sidebar.classList.remove("show");
            sidebarOverlay.classList.remove("show");
        });
    </script>
</body>
</html>';
    }

    public function show($id)
    {
    }
}

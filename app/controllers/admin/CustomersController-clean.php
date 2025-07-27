<?php
/**
 * Professional Customers Controller
 * Business-grade customer management interface
 * Clean MVC structure - all HTML in views
 */

class CustomersController extends BaseController
{
    public function __construct()
    {
        // Session đã được start từ index.php

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }
    }

    public function index()
    {
        // Sample customers data (in real app, get from database)
        $customers = [
            [
                'id' => 1,
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@email.com',
                'phone' => '0987654321',
                'address' => 'Hà Nội',
                'total_orders' => 5,
                'total_spent' => 2500000,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@email.com',
                'phone' => '0976543210',
                'address' => 'TP.HCM',
                'total_orders' => 8,
                'total_spent' => 4200000,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'created_at' => '2024-01-12'
            ],
            [
                'id' => 3,
                'name' => 'Lê Minh Cảnh',
                'email' => 'leminhcanh@email.com',
                'phone' => '0965432109',
                'address' => 'Đà Nẵng',
                'total_orders' => 3,
                'total_spent' => 1800000,
                'status' => 'inactive',
                'status_text' => 'Không hoạt động',
                'created_at' => '2024-01-10'
            ]
        ];

        $data = [
            'pageTitle' => 'Quản lý khách hàng',
            'customers' => $customers,
            'breadcrumb' => ['Khách hàng']
        ];

        $this->render('admin/customers/index', $data, 'admin/layouts/main');
    }
}

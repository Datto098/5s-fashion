<?php
/**
 * Professional Orders Controller
 * Business-grade order management interface
 * Clean MVC structure - all HTML in views
 */

class OrdersController extends BaseController
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
        // Sample orders data (in real app, get from database)
        $orders = [
            [
                'id' => 1,
                'order_number' => 'ORD-2024-001',
                'customer_name' => 'Nguyễn Văn An',
                'customer_email' => 'nguyenvanan@email.com',
                'total_amount' => 599000,
                'status' => 'pending',
                'status_text' => 'Chờ xử lý',
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'created_at' => '2024-01-15 10:30:00'
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-2024-002',
                'customer_name' => 'Trần Thị Bình',
                'customer_email' => 'tranthibinh@email.com',
                'total_amount' => 1200000,
                'status' => 'processing',
                'status_text' => 'Đang xử lý',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'created_at' => '2024-01-14 15:45:00'
            ],
            [
                'id' => 3,
                'order_number' => 'ORD-2024-003',
                'customer_name' => 'Lê Minh Cảnh',
                'customer_email' => 'leminhcanh@email.com',
                'total_amount' => 750000,
                'status' => 'shipped',
                'status_text' => 'Đã giao hàng',
                'payment_status' => 'paid',
                'payment_method' => 'cod',
                'created_at' => '2024-01-13 09:20:00'
            ]
        ];

        $data = [
            'pageTitle' => 'Quản lý đơn hàng',
            'orders' => $orders,
            'breadcrumb' => ['Đơn hàng']
        ];

        $this->render('admin/orders/index', $data, 'admin/layouts/main-inline');
    }
}

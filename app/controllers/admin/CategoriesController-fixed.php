<?php
/**
 * Professional Categories Controller
 * Business-grade category management interface
 * Clean MVC structure - all HTML in views
 */

class CategoriesController extends BaseController
{
    public function __construct()
    {
        session_start();

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }
    }

    public function index()
    {
        // Sample categories data (in real app, get from database)
        $categories = [
            [
                'id' => 1,
                'name' => 'Áo thun nam',
                'slug' => 'ao-thun-nam',
                'description' => 'Áo thun nam các loại',
                'products_count' => 25,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'name' => 'Áo sơ mi',
                'slug' => 'ao-so-mi',
                'description' => 'Áo sơ mi công sở và thời trang',
                'products_count' => 18,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'created_at' => '2024-01-12'
            ],
            [
                'id' => 3,
                'name' => 'Quần jean',
                'slug' => 'quan-jean',
                'description' => 'Quần jean nam nữ',
                'products_count' => 32,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'created_at' => '2024-01-10'
            ]
        ];

        $data = [
            'pageTitle' => 'Quản lý danh mục',
            'categories' => $categories,
            'breadcrumb' => ['Danh mục']
        ];

        $this->render('admin/categories/index', $data, 'admin/layouts/main');
    }
}

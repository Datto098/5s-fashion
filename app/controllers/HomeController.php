<?php
/**
 * Home Controller (Client)
 * 5S Fashion E-commerce Platform
 */

require_once __DIR__ . '/../models/ProductVariant.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Order.php';

class HomeController extends Controller
{
    private $productModel;
    private $categoryModel;
    private $couponModel;
    private $reviewModel;
    private $orderModel;

    public function __construct()
    {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->couponModel = $this->model('Coupon');
        $this->reviewModel = new Review();
        $this->orderModel = new Order();
    }

    public function index()
    {
        // Get featured categories
        $featuredCategories = $this->categoryModel->getFeaturedCategories(6);

        // Get featured products
        $featuredProducts = $this->productModel->getFeaturedProducts(8);

        // Get new arrivals
        $newArrivals = $this->productModel->getNewArrivals(8);

        // Get best sellers
        $bestSellers = $this->productModel->getBestSellers(8);

        // Get sale products
        $saleProducts = $this->productModel->getSaleProducts(8);

        // Get featured vouchers for homepage
        $featuredVouchers = $this->couponModel->getFeaturedVouchers(2);

        $data = [
            'title' => '5S Fashion - Thời trang nam nữ cao cấp',
            'featured_categories' => $featuredCategories,
            'featured_products' => $featuredProducts,
            'new_arrivals' => $newArrivals,
            'best_sellers' => $bestSellers,
            'sale_products' => $saleProducts,
            'featured_vouchers' => $featuredVouchers
        ];

        $this->view('client/home/index', $data);
    }

    public function shop()
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 12;
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $sort = $_GET['sort'] ?? 'latest';
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $brand = $_GET['brand'] ?? null;

        // Get filters
        $filters = [
            'category' => $category,
            'search' => $search,
            'sort' => $sort,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'brand' => $brand
        ];

        // Get products with pagination
        $result = $this->productModel->getProductsWithFilters($filters, $page, $limit);

        // Get categories for filter
        $categories = $this->categoryModel->getActiveCategories();

        // Get brands for filter
        $brands = $this->productModel->getAllBrands();

        // Calculate pagination data
        $totalProducts = $result['total'];
        $totalPages = ceil($totalProducts / $limit);
        $currentPage = (int)$page;

        // Build query string for pagination
        $queryParams = array_filter([
            'category' => $category,
            'brand' => $brand,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'search' => $search,
            'sort' => $sort
        ]);
        $queryString = $queryParams ? '&' . http_build_query($queryParams) : '';

        $data = [
            'title' => 'Shop - 5S Fashion',
            'products' => $result['products'],
            'totalProducts' => $totalProducts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'queryString' => $queryString
        ];

        $this->view('client/shop/index', $data);
    }

    public function product($slug = null)
    {
        if (!$slug) {
            redirect('/shop');
            return;
        }

        $product = $this->productModel->getProductBySlug($slug);

        if (!$product) {
            $this->view('errors/404');
            return;
        }

        // Get product variants with attributes if product has variants
        $variants = [];
        $attributes = [];

        if (isset($product['has_variants']) && $product['has_variants']) {
            $variants = ProductVariant::getProductVariantsWithDetails($product['id']);

            // Get available attributes for this product
            $db = Database::getInstance();
            $sql = "
                SELECT DISTINCT pa.id, pa.name, pa.type, pa.slug,
                       JSON_ARRAYAGG(
                           JSON_OBJECT(
                               'id', pav.id,
                               'value', pav.value,
                               'color_code', pav.color_code,
                               'image', pav.image
                           )
                       ) as `values`
                FROM product_variants pv
                JOIN product_variant_attributes pva ON pv.id = pva.variant_id
                JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
                JOIN product_attributes pa ON pav.attribute_id = pa.id
                WHERE pv.product_id = :product_id AND pv.status = 'active'
                GROUP BY pa.id, pa.name, pa.type, pa.slug
                ORDER BY pa.sort_order
            ";
            $attributes = $db->fetchAll($sql, ['product_id' => $product['id']]);

            // Decode JSON values
            foreach ($attributes as &$attribute) {
                $attribute['values'] = json_decode($attribute['values'], true) ?? [];
            }
        }

        // Get related products
        $relatedProducts = $this->productModel->getRelatedProducts($product['id'], $product['category_id'], 8);

        // Get product reviews from database
        $userId = isLoggedIn() ? $_SESSION['user']['id'] : null;
        $reviews = $this->reviewModel->getProductReviews($product['id'], 10, $userId);
        $reviewCount = count($reviews);
        
        // Debug: Log reviews
        error_log("Reviews for product {$product['id']}: " . print_r($reviews, true));
        error_log("Review count: " . $reviewCount);
        
        // Check if the current user can review this product
        $canReview = false;
        $userId = null;
        $hasOrderedProduct = false;
        $hasCompletedOrders = false;
        $hasReviewed = false;
        
        if (isLoggedIn()) {
            $userId = $_SESSION['user']['id'];
            
            // Kiểm tra xem người dùng đã đặt hàng sản phẩm này chưa (bất kể trạng thái)
            $hasOrderedProduct = $this->orderModel->hasUserPurchasedProduct(
                $userId, 
                $product['id'], 
                ['processing', 'shipped', 'delivered', 'completed']
            );
            
            // Kiểm tra xem người dùng đã mua và nhận sản phẩm này chưa
            $hasCompletedOrders = $this->orderModel->hasUserPurchasedProduct(
                $userId, 
                $product['id'], 
                ['delivered', 'completed']
            );
            
            // Kiểm tra xem người dùng đã đánh giá sản phẩm này chưa
            $hasReviewed = $this->reviewModel->hasUserReviewedProduct($userId, $product['id']);
            
            // Người dùng có thể đánh giá nếu đã mua và nhận sản phẩm, và chưa đánh giá
            $canReview = $hasCompletedOrders && !$hasReviewed;
        }

        $data = [
            'title' => $product['name'] . ' - 5S Fashion',
            'product' => $product,
            'variants' => $variants,
            'attributes' => $attributes,
            'related_products' => $relatedProducts,
            'reviews' => $reviews,
            'reviewCount' => $reviewCount,
            'canReview' => $canReview,
            'hasOrderedProduct' => $hasOrderedProduct,
            'hasCompletedOrders' => $hasCompletedOrders,
            'hasReviewed' => $hasReviewed,
            'userId' => $userId
        ];

        $this->view('client/product/detail', $data);
    }

    public function cart()
    {
        // Check if user is authenticated (using helper function)
        if (!isLoggedIn()) {
            // Store intended URL for redirect after login
            $_SESSION['redirect_after_login'] = '/5s-fashion/cart';

            // Redirect to login with message
            $_SESSION['info_message'] = 'Bạn cần đăng nhập để xem giỏ hàng';
            header('Location: /5s-fashion/login');
            exit;
        }

        // Check if user is admin - admins shouldn't use client cart
        $user = getUser();
        if ($user && $user['role'] === 'admin') {
            // Show a proper message instead of redirecting to dashboard
            $data = [
                'title' => 'Giỏ hàng - 5S Fashion',
                'error_message' => 'Tài khoản admin không thể sử dụng giỏ hàng khách hàng. Để mua hàng, vui lòng đăng xuất và đăng nhập bằng tài khoản khách hàng.',
                'cartItems' => [],
                'cartTotal' => 0,
                'cartCount' => 0
            ];

            $this->view('client/cart/index', $data);
            return;
        }        // Initialize Cart model
        $cartModel = $this->model('Cart');

        $cartItems = $cartModel->getCartItems();
        $cartTotal = $cartModel->getCartTotal();
        $cartCount = $cartModel->getCartCount();

        $data = [
            'title' => 'Giỏ hàng - 5S Fashion',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount
        ];

        $this->view('client/cart/index', $data);
    }

    public function checkout()
    {
        // Initialize Cart model
        $cartModel = $this->model('Cart');

        // Get cart data
        $cartItems = $cartModel->getCartItems();
        $cartTotal = $cartModel->getCartTotal();
        $cartCount = $cartModel->getCartCount();

        // Format cart items for JavaScript compatibility
        $formattedCartItems = array_map(function($item) {
            return [
                'cart_key' => $item['id'] ?? $item['cart_key'] ?? null,
                'product_id' => $item['product_id'],
                'name' => $item['product_name'], // Map product_name to name
                'image' => $item['product_image'], // Map product_image to image
                'price' => floatval($item['price']),
                'quantity' => intval($item['quantity']),
                'variant' => $item['variant_name'] ?? null
            ];
        }, $cartItems);

        $addresses = [];

        // Load user addresses if logged in
        if (isset($_SESSION['user'])) {
            $customerModel = $this->model('Customer');
            $addresses = $customerModel->getAddressesByUserId($_SESSION['user']['id']);
        }

        $data = [
            'title' => 'Thanh Toán - 5S Fashion',
            'cartItems' => $formattedCartItems, // Use formatted items
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
            'addresses' => $addresses
        ];

        $this->view('client/checkout/index', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Liên Hệ - 5S Fashion'
        ];

        $this->view('client/contact/index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'Về Chúng Tôi - 5S Fashion'
        ];

        $this->view('client/about/index', $data);
    }
}
?>

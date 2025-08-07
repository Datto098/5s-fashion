<?php
/**
 * Global Helper Functions
 * 5S Fashion E-commerce Platform
 */

require_once __DIR__ . '/../models/Cart.php';

/**
 * Redirect to URL
 */
function redirect($url)
{
    // If URL doesn't start with http, make it relative to base URL
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = BASE_URL . '/' . ltrim($url, '/');
    }

    // Check if headers already sent
    if (headers_sent($file, $line)) {
        echo "<script>window.location.href = '{$url}';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0; url={$url}'></noscript>";
        exit;
    }

    header("Location: {$url}");
    exit;
}

/**
 * Generate URL
 */
function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get asset URL
 */
function asset($path)
{
    return ASSET_URL . '/' . ltrim($path, '/');
}

/**
 * Get upload URL
 */
function upload($path)
{
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

/**
 * Format currency
 */
function formatCurrency($amount)
{
    return number_format($amount, 0, ',', '.') . ' ₫';
}

/**
 * Format date
 */
function formatDate($date, $format = 'd/m/Y')
{
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

/**
 * Get user from session
 */
function getUser()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user']);
}

/**
 * Check user role
 */
function hasRole($role)
{
    $user = getUser();
    return $user && $user['role'] === $role;
}

/**
 * Generate slug from string
 */
function createSlug($string)
{
    // Convert to lowercase
    $slug = strtolower($string);

    // Replace Vietnamese characters
    $vietnamese = [
        'á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
        'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
        'í', 'ì', 'ỉ', 'ĩ', 'ị',
        'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
        'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
        'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
        'đ'
    ];

    $ascii = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd'
    ];

    $slug = str_replace($vietnamese, $ascii, $slug);

    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\-\s]/', '', $slug);

    // Replace spaces and multiple hyphens with single hyphen
    $slug = preg_replace('/[\s\-]+/', '-', $slug);

    // Trim hyphens from start and end
    return trim($slug, '-');
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $ending = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length - strlen($ending)) . $ending;
}

/**
 * Get cart from session
 */
function getCart()
{
    return $_SESSION['cart'] ?? [];
}

/**
 * Get cart count
 */
function getCartCount()
{
    $cartModel = new Cart();
    return $cartModel->getCartCount();
}

/**
 * Get cart total
 */
function getCartTotal()
{
    $cart = getCart();
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return $total;
}

/**
 * Add item to cart
 */
function addToCart($productId, $quantity = 1, $variant = null)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $key = $productId . ($variant ? '_' . $variant : '');

    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant' => $variant
        ];
    }
}

/**
 * Remove item from cart
 */
function removeFromCart($key)
{
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
}

/**
 * Clear cart
 */
function clearCart()
{
    $_SESSION['cart'] = [];
}

/**
 * Flash message functions
 */
function setFlash($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type = null)
{
    if ($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function hasFlash($type = null)
{
    if ($type) {
        return isset($_SESSION['flash'][$type]);
    }

    return !empty($_SESSION['flash']);
}

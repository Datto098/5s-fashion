<?php
/**
 * Wishlist Controller (Client)
 * 5S Fashion E-commerce Platform
 */

class WishlistController extends Controller
{
    private $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = $this->model('Wishlist');
    }

    public function index()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('login');
            exit;
        }

        $user = getUser();

        // Get wishlist items from database
        try {
            $wishlist = $this->wishlistModel->getUserWishlist($user['id']);
        } catch (Exception $e) {
            $wishlist = [];
            error_log('Wishlist error: ' . $e->getMessage());
        }

        // Pass data to view
        $data = [
            'title' => 'Sản phẩm yêu thích',
            'wishlist' => $wishlist,
            'user' => $user
        ];

        $this->view('client/account/wishlist', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = getUser();
            $productId = $_POST['product_id'] ?? null;

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                return;
            }

            // Add to wishlist using model
            try {
                $result = $this->wishlistModel->addToWishlist($user['id'], $productId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong danh sách yêu thích']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
                error_log('Add to wishlist error: ' . $e->getMessage());
            }
        }
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                return;
            }

            $user = getUser();
            $wishlistId = $_POST['wishlist_id'] ?? null;

            if (!$wishlistId) {
                echo json_encode(['success' => false, 'message' => 'Wishlist ID is required']);
                return;
            }

            // Remove from wishlist using model
            try {
                $result = $this->wishlistModel->removeFromWishlistById($user['id'], $wishlistId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể xóa khỏi danh sách yêu thích']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
                error_log('Remove from wishlist error: ' . $e->getMessage());
            }
        }
    }

    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = getUser();

            // Clear all wishlist items for user
            $result = $this->clearWishlist($user['id']);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa tất cả sản phẩm khỏi danh sách yêu thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh sách yêu thích']);
            }
        }
    }

    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = getUser();
            $productId = $_POST['product_id'] ?? null;

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                return;
            }

            // Check if product is in wishlist
            $inWishlist = $this->isInWishlist($user['id'], $productId);

            if ($inWishlist) {
                $result = $this->removeFromWishlist($user['id'], $productId);
                $message = $result ? 'Đã xóa khỏi danh sách yêu thích' : 'Không thể xóa khỏi danh sách yêu thích';
                $action = 'removed';
            } else {
                $result = $this->addToWishlist($user['id'], $productId);
                $message = $result ? 'Đã thêm vào danh sách yêu thích' : 'Không thể thêm vào danh sách yêu thích';
                $action = 'added';
            }

            echo json_encode([
                'success' => $result,
                'message' => $message,
                'action' => $action,
                'in_wishlist' => !$inWishlist
            ]);
        }
    }

    public function count()
    {
        $user = getUser();

        if (!$user) {
            echo json_encode(['count' => 0]);
            return;
        }

        $count = $this->getWishlistCount($user['id']);
        echo json_encode(['count' => $count]);
    }

    private function getWishlistItems($userId)
    {
        // TODO: Implement actual database query when Wishlist model is created
        // For now, return sample data
        return [
            [
                'id' => 1,
                'name' => 'Áo thun nam basic',
                'slug' => 'ao-thun-nam-basic',
                'price' => 299000,
                'sale_price' => 199000,
                'image' => '/assets/images/products/product1.jpg',
                'rating' => 4,
                'reviews_count' => 25
            ],
            [
                'id' => 2,
                'name' => 'Quần jeans nữ skinny',
                'slug' => 'quan-jeans-nu-skinny',
                'price' => 599000,
                'sale_price' => null,
                'image' => '/assets/images/products/product2.jpg',
                'rating' => 5,
                'reviews_count' => 18
            ],
            [
                'id' => 3,
                'name' => 'Áo khoác bomber unisex',
                'slug' => 'ao-khoac-bomber-unisex',
                'price' => 899000,
                'sale_price' => 699000,
                'image' => '/assets/images/products/product3.jpg',
                'rating' => 4,
                'reviews_count' => 32
            ]
        ];
    }

    private function addToWishlist($userId, $productId)
    {
        // TODO: Implement actual database insertion
        // For now, return true to simulate success
        return true;
    }

    private function removeFromWishlist($userId, $productId)
    {
        // TODO: Implement actual database deletion
        // For now, return true to simulate success
        return true;
    }

    private function clearWishlist($userId)
    {
        // TODO: Implement actual database clear
        // For now, return true to simulate success
        return true;
    }

    private function isInWishlist($userId, $productId)
    {
        // TODO: Implement actual database check
        // For now, return false to simulate not in wishlist
        return false;
    }

    private function getWishlistCount($userId)
    {
        // TODO: Implement actual database count
        // For now, return sample count
        return 3;
    }
}

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
        $this->wishlistModel = $this->model("Wishlist");
    }

    public function index()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect("login");
            exit;
        }

        $user = getUser();

        // Get wishlist items from database
        try {
            $wishlist = $this->wishlistModel->getUserWishlist($user["id"]);
        } catch (Exception $e) {
            $wishlist = [];
            error_log("Wishlist error: " . $e->getMessage());
        }

        // Pass data to view
        $data = [
            "title" => "Sản phẩm yêu thích - 5S Fashion",
            "wishlist" => $wishlist,
            "user" => $user
        ];

        $this->view("client/account/wishlist", $data);
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập"]);
                return;
            }

            $user = getUser();
            $productId = $_POST["product_id"] ?? null;

            if (!$productId) {
                echo json_encode(["success" => false, "message" => "Product ID is required"]);
                return;
            }

            // Add to wishlist using model
            try {
                $result = $this->wishlistModel->addToWishlist($user["id"], $productId);
                
                if ($result) {
                    echo json_encode(["success" => true, "message" => "Đã thêm vào danh sách yêu thích"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Sản phẩm đã có trong danh sách yêu thích"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Có lỗi xảy ra"]);
                error_log("Add to wishlist error: " . $e->getMessage());
            }
        }
    }

    public function remove()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập"]);
                return;
            }

            $user = getUser();
            $productId = $_POST["product_id"] ?? null;

            if (!$productId) {
                echo json_encode(["success" => false, "message" => "Product ID is required"]);
                return;
            }

            // Remove from wishlist using model
            try {
                $result = $this->wishlistModel->removeFromWishlist($user["id"], $productId);
                
                if ($result) {
                    echo json_encode(["success" => true, "message" => "Đã xóa khỏi danh sách yêu thích"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Không thể xóa khỏi danh sách yêu thích"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Có lỗi xảy ra"]);
                error_log("Remove from wishlist error: " . $e->getMessage());
            }
        }
    }

    public function clear()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập"]);
                return;
            }

            $user = getUser();

            // Clear wishlist using model
            try {
                $result = $this->wishlistModel->clearWishlist($user["id"]);
                
                if ($result) {
                    echo json_encode(["success" => true, "message" => "Đã xóa toàn bộ danh sách yêu thích"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Không thể xóa danh sách yêu thích"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Có lỗi xảy ra"]);
                error_log("Clear wishlist error: " . $e->getMessage());
            }
        }
    }

    public function toggle()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập"]);
                return;
            }

            $user = getUser();
            $productId = $_POST["product_id"] ?? null;

            if (!$productId) {
                echo json_encode(["success" => false, "message" => "Product ID is required"]);
                return;
            }

            // Toggle wishlist using model
            try {
                $result = $this->wishlistModel->toggleWishlist($user["id"], $productId);
                $isInWishlist = $this->wishlistModel->isInWishlist($user["id"], $productId);
                
                if ($result) {
                    $message = $isInWishlist ? "Đã thêm vào danh sách yêu thích" : "Đã xóa khỏi danh sách yêu thích";
                    $action = $isInWishlist ? "added" : "removed";
                    
                    echo json_encode([
                        "success" => true,
                        "message" => $message,
                        "action" => $action,
                        "in_wishlist" => $isInWishlist
                    ]);
                } else {
                    echo json_encode(["success" => false, "message" => "Không thể thực hiện thao tác"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Có lỗi xảy ra"]);
                error_log("Toggle wishlist error: " . $e->getMessage());
            }
        }
    }

    public function count()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            echo json_encode(["count" => 0]);
            return;
        }

        $user = getUser();
        
        try {
            $count = $this->wishlistModel->getWishlistCount($user["id"]);
            echo json_encode(["count" => $count]);
        } catch (Exception $e) {
            echo json_encode(["count" => 0]);
            error_log("Get wishlist count error: " . $e->getMessage());
        }
    }
}

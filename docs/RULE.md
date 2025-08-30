# Zone Fashion - CODING RULES & STANDARDS

## 📋 Tổng quan

Tài liệu này định nghĩa các quy tắc và chuẩn code cho dự án **Zone Fashion E-commerce Platform**. Tuân thủ các quy tắc này để đảm bảo code sạch sẽ, nhất quán và dễ bảo trì.

---

## 🏗️ KIẾN TRÚC & STRUCTURE

### 1. MVC Pattern - BẮT BUỘC

```
📁 app/
├── 📁 controllers/        # Controllers (Logic xử lý)
│   ├── 📁 admin/         # Admin controllers
│   ├── 📁 api/          # API controllers
│   └── BaseController.php # Base controller
├── 📁 models/           # Models (Data layer)
├── 📁 views/            # Views (Presentation layer)
│   ├── 📁 admin/        # Admin views
│   ├── 📁 client/       # Client views
│   └── 📁 layouts/      # Layout templates
└── 📁 core/             # Core system files
```

### 2. Naming Convention

#### File Names
- **Controllers**: `PascalCase + Controller.php`
  ```php
  // ✅ ĐÚNG
  ProductsController.php
  OrdersController.php

  // ❌ SAI
  products_controller.php
  product.php
  ```

- **Models**: `PascalCase.php`
  ```php
  // ✅ ĐÚNG
  Product.php
  Order.php

  // ❌ SAI
  product_model.php
  products.php
  ```

- **Views**: `snake_case.php`
  ```php
  // ✅ ĐÚNG
  product_detail.php
  order_success.php

  // ❌ SAI
  ProductDetail.php
  orderSuccess.php
  ```

#### Class Names
```php
// ✅ ĐÚNG
class ProductsController extends BaseController
class Product extends BaseModel
class OrderTrackingManager

// ❌ SAI
class products_controller
class product_model
```

#### Method Names
```php
// ✅ ĐÚNG
public function index()
public function createProduct()
public function getProductDetails()

// ❌ SAI
public function Index()
public function create_product()
public function GetProductDetails()
```

#### Variable Names
```php
// ✅ ĐÚNG
$productData
$orderItems
$customerId

// ❌ SAI
$product_data
$ProductData
$customerid
```

---

## 🎯 CONTROLLERS

### 1. Structure chuẩn

```php
<?php
/**
 * Controller Description
 * Zone Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../models/ModelName.php';

class ControllerName extends BaseController
{
    private $model;

    public function __construct()
    {
        // Session management
        $this->ensureSessionStarted();

        // Authentication check (if needed)
        $this->checkAuthentication();

        // Initialize models
        $this->model = new ModelName();
    }

    /**
     * Action description
     */
    public function index()
    {
        // Implementation
    }
}
```

### 2. Admin Controller Rules

```php
public function __construct()
{
    // Session đã được start từ index.php - KHÔNG start lại

    // Check admin authentication - BẮT BUỘC
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /zone-fashion/admin/login');
        exit;
    }

    // Initialize models
    $this->model = new Model();
}
```

### 3. API Controller Rules

```php
class ApiController extends BaseApiController
{
    /**
     * Check HTTP methods
     */
    protected function checkMethod($allowedMethods = ['GET'])
    {
        // Implementation
    }

    /**
     * Always return JSON response
     */
    protected function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
```

---

## 🗄️ MODELS

### 1. Structure chuẩn

```php
<?php
/**
 * Model Name
 * Zone Fashion E-commerce Platform
 */

require_once __DIR__ . '/../core/Database.php';

class ModelName
{
    private $db;
    protected $table = 'table_name';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Method description
     */
    public function methodName($params)
    {
        // Implementation
    }
}
```

### 2. Database Query Rules

```php
// ✅ ĐÚNG - Sử dụng prepared statements
public function getProduct($id)
{
    $sql = "SELECT * FROM products WHERE id = :id AND status = 'active'";
    return $this->db->fetchOne($sql, ['id' => $id]);
}

// ❌ SAI - String concatenation (SQL Injection risk)
public function getProduct($id)
{
    $sql = "SELECT * FROM products WHERE id = " . $id;
    return $this->db->query($sql);
}
```

### 3. Error Handling

```php
public function createProduct($data)
{
    try {
        $sql = "INSERT INTO products (name, price, description) VALUES (:name, :price, :description)";
        $result = $this->db->execute($sql, $data);

        if ($result) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }

        return ['success' => false, 'message' => 'Failed to create product'];

    } catch (Exception $e) {
        error_log('Product creation error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}
```

---

## 🎨 VIEWS

### 1. Client Views - Layout Pattern

```php
<?php
// Page Description - Using standard layout
// Start output buffering for content
ob_start();
?>

<!-- Page Content HTML -->
<section class="page-section">
    <div class="container">
        <!-- Content here -->
    </div>
</section>

<?php
// Get content from buffer
$content = ob_get_clean();

// Set page variables for layout
$title = 'Page Title - Zone Fashion';
$meta_description = 'Page description';

// Custom CSS for this page
$custom_css = [
    'css/page-specific.css'
];

// Custom JavaScript for this page
$custom_js = [
    'js/page-specific.js'
];

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
```

### 2. Admin Views - Layout Pattern

```php
<?php
// Admin Page - Using standard admin layout
ob_start();
?>

<div class="admin-page-content">
    <!-- Admin content here -->
</div>

<?php
$content = ob_get_clean();

// Admin layout data
$data = [
    'title' => 'Admin Page Title - Zone Fashion Admin',
    'content' => $content,
    'breadcrumbs' => [
    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
        ['title' => 'Current Page']
    ]
];

// Render admin layout
$this->render('admin/layouts/main-inline', $data);
?>
```

### 3. HTML/CSS Rules

```html
<!-- ✅ ĐÚNG - Bootstrap classes, semantic HTML -->
<section class="product-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ❌ SAI - Inline styles, non-semantic -->
<div style="padding: 50px;">
    <div style="margin: 20px;">
        <!-- Content -->
    </div>
</div>
```

---

## ⚡ JAVASCRIPT

### 1. File Structure

```javascript
/**
 * JavaScript Module Name
 * Zone Fashion E-commerce Platform
 */

class ManagerName {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadInitialData();
    }

    setupEventListeners() {
        // Event bindings
    }

    // Other methods
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.managerInstance = new ManagerName();
});
```

### 2. KHÔNG ĐƯỢC inline JavaScript

```php
<!-- ❌ SAI - JavaScript inline -->
$inline_js = '
    document.addEventListener("DOMContentLoaded", function() {
        // Code here
    });
';

<!-- ✅ ĐÚNG - JavaScript trong file riêng -->
$custom_js = [
    'js/page-specific.js'
];
```

### 3. Error Handling & API Calls

```javascript
async loadData() {
    try {
        const response = await fetch('/api/endpoint');
        const data = await response.json();

        if (data.success) {
            this.updateUI(data.data);
        } else {
            this.showError(data.message);
        }

    } catch (error) {
        console.error('API Error:', error);
        this.showError('Có lỗi xảy ra. Vui lòng thử lại.');
    }
}
```

---

## 🎨 CSS

### 1. File Organization

```css
/**
 * CSS Module Name
 * Zone Fashion E-commerce Platform
 */

/* Variables */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #e74c3c;
    --border-radius: 8px;
    --transition: all 0.3s ease;
}

/* Base Styles */
.component-name {
    /* Styles */
}

/* States */
.component-name:hover {
    /* Hover styles */
}

.component-name.active {
    /* Active styles */
}

/* Responsive */
@media (max-width: 768px) {
    .component-name {
        /* Mobile styles */
    }
}
```

### 2. Naming Convention

```css
/* ✅ ĐÚNG - BEM methodology */
.product-card {}
.product-card__image {}
.product-card__title {}
.product-card--featured {}

/* ❌ SAI */
.productCard {}
.product_card {}
#product-1 {}
```

---

## 🔒 SECURITY

### 1. Input Validation

```php
// ✅ ĐÚNG
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Database queries with prepared statements
$sql = "SELECT * FROM users WHERE email = :email";
$user = $db->fetchOne($sql, ['email' => $email]);
```

### 2. Authentication Check

```php
// ✅ ĐÚNG - Always check authentication
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('Location: /login');
    exit;
}
```

### 3. Error Logging

```php
// ✅ ĐÚNG - Log errors, don't expose to user
try {
    // Code
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    return ['success' => false, 'message' => 'Có lỗi xảy ra'];
}
```

---

## 📁 FILE MANAGEMENT

### 1. KHÔNG ĐƯỢC tạo file test/debug

```
❌ SAI - Các file này không được tồn tại:
test_*.php
debug_*.php
*_test.php
*_debug.php
temp_*.php
```

### 2. File Upload Handling

```php
// ✅ ĐÚNG
public function uploadFile($file) {
    // Validate file type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array(strtolower($fileType), $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }

    // Generate unique filename
    $fileName = time() . '_' . uniqid() . '.' . $fileType;

    // Upload to designated directory
    $uploadPath = UPLOAD_PATH . '/products/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $fileName];
    }

    return ['success' => false, 'message' => 'Upload failed'];
}
```

---

## 📚 COMMENTS & DOCUMENTATION

### 1. Class Documentation

```php
/**
 * Product Management Controller
 *
 * Handles CRUD operations for products including:
 * - Product listing with filters and pagination
 * - Product creation and editing
 * - Product variant management
 * - Product image handling
 *
 * @author Zone Fashion Team
 * @version 1.0
 */
class ProductsController extends BaseController
```

### 2. Method Documentation

```php
/**
 * Get products with filtering and pagination
 *
 * @param array $filters Search and filter criteria
 * @param int $page Current page number
 * @param int $limit Products per page
 * @return array Products data with pagination info
 * @throws Exception When database query fails
 */
public function getProductsWithFilters($filters, $page = 1, $limit = 10)
```

### 3. Inline Comments

```php
// Check user authentication before proceeding
if (!$this->isAuthenticated()) {
    return $this->redirectToLogin();
}

// Calculate total price including tax and discounts
$totalPrice = ($basePrice + $tax) - $discount;

// TODO: Implement caching for frequently accessed products
// FIXME: Handle edge case when product has no variants
```

---

## ✅ TESTING & VALIDATION

### 1. Code Quality Checklist

- [ ] **MVC Structure**: Controller → Model → View pattern tuân thủ
- [ ] **Naming Convention**: Class, method, variable names chuẩn
- [ ] **Security**: Input validation, prepared statements, authentication
- [ ] **Error Handling**: Try-catch blocks, proper error messages
- [ ] **Documentation**: Class và method comments đầy đủ
- [ ] **Layout Consistency**: Client views sử dụng standard layout
- [ ] **No Debug Files**: Không có file test/debug dư thừa

### 2. Pre-commit Validation

```bash
# Check for debug/test files
find . -name "*test*.php" -o -name "*debug*.php"

# Check for inline JavaScript
grep -r "\$inline_js" app/views/

# Check for direct SQL queries (non-prepared)
grep -r "SELECT.*\$" app/models/
```

---

## 🚀 DEPLOYMENT RULES

### 1. Production Checklist

- [ ] Remove all debug/test files
- [ ] Disable error display: `ini_set('display_errors', 0)`
- [ ] Enable error logging: `ini_set('log_errors', 1)`
- [ ] Validate all file permissions
- [ ] Check database connection security
- [ ] Verify SSL/HTTPS configuration

### 2. Git Rules

```bash
# ✅ ĐÚNG - Descriptive commit messages
git commit -m "feat: Add order tracking functionality"
git commit -m "fix: Resolve cart pricing calculation bug"
git commit -m "refactor: Improve admin layout consistency"

# ❌ SAI
git commit -m "update"
git commit -m "fix bug"
git commit -m "changes"
```

---

## 🔧 DEVELOPMENT WORKFLOW

### 1. Feature Development

1. **Tạo branch**: `feature/feature-name`
2. **Code theo chuẩn**: Tuân thủ tất cả rules trên
3. **Test locally**: Verify functionality
4. **Clean up**: Remove debug files, comments dư thừa
5. **Commit**: Descriptive commit messages
6. **Merge**: Via pull request với code review

### 2. Bug Fixing

1. **Identify issue**: Log error details
2. **Create fix**: Minimal code changes
3. **Test thoroughly**: Ensure no regression
4. **Document**: Update comments if needed
5. **Deploy**: Follow deployment checklist

---

## ⚠️ COMMON MISTAKES TO AVOID

### ❌ TUYỆT ĐỐI KHÔNG ĐƯỢC LÀM

1. **Inline JavaScript trong views**
2. **Tạo file test/debug trên production**
3. **SQL injection vulnerabilities**
4. **Hardcode passwords/API keys**
5. **Skip authentication checks**
6. **Expose sensitive error details**
7. **Mix Vietnamese and English inconsistently**
8. **Use deprecated PHP functions**
9. **Ignore responsive design**
10. **Commit code without testing**

---

## 📞 SUPPORT

**Khi có thắc mắc về coding standards:**

1. **Check documentation**: README.md và file này
2. **Review existing code**: Tìm pattern tương tự
3. **Ask team lead**: Khi không chắc chắn
4. **Document decisions**: Update RULE.md khi cần thiết

---

**📝 Document Version**: 1.0
**📅 Last Updated**: August 2025
**👥 Maintained by**: Zone Fashion Development Team

---

> **Nhớ rằng**: Code quality không chỉ là về functionality mà còn về maintainability, security, và team collaboration. Clean code là investment cho tương lai! 🚀

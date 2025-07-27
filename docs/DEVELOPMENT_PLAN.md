# 🚀 5S FASHION - DEVELOPMENT PLAN CHI TIẾT

## 📋 CHIẾN LƯỢC PHÁT TRIỂN
**Mục tiêu**: Xây dựng từ Admin → API → Client để có dữ liệu thật từ đầu

---

## 🏗️ PHASE 1: XÂY DỰNG ADMIN PANEL HOÀN CHỈNH (Tuần 1-3)

### 🔧 1.1 Setup Foundation & Database (Ngày 1-3)

#### 📅 Ngày 1: Cấu trúc dự án & MVC Foundation
**Mục tiêu**: Tạo skeleton MVC hoàn chỉnh

**Tasks:**
- [ ] **Tạo cấu trúc thư mục MVC**
  ```
  📁 app/
  ├── 📁 config/
  │   ├── database.php
  │   ├── app.php
  │   └── constants.php
  ├── 📁 core/
  │   ├── App.php (Main application)
  │   ├── Controller.php (Base controller)
  │   ├── Model.php (Base model)
  │   └── Database.php (DB connection)
  ├── 📁 controllers/
  │   ├── BaseController.php
  │   └── 📁 admin/
  ├── 📁 models/
  ├── 📁 views/
  │   └── 📁 admin/
  ├── 📁 helpers/
  └── 📁 middleware/
  ```

- [ ] **Setup Autoloader**
  - PSR-4 autoloading
  - Composer integration
  - Class mapping

- [ ] **Routing System**
  - URL rewriting (.htaccess)
  - Route parsing
  - Controller resolution
  - Admin route protection

- [ ] **Environment Configuration**
  - .env file setup
  - Environment variables
  - Config loading system

**Deliverables:**
- Functional MVC structure
- Basic routing working
- Autoloader functional

---

#### 📅 Ngày 2: Database Design & Schema
**Mục tiêu**: Thiết kế database hoàn chỉnh cho e-commerce thời trang

**Tasks:**
- [ ] **Core Tables Design**
  ```sql
  -- Users table (admin/customer)
  CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```

- [ ] **Categories Table** (Nested categories)
  ```sql
  CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
  );
  ```

- [ ] **Brands Table**
  ```sql
  CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    website VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```

- [ ] **Products Table** (Main products)
  ```sql
  CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    cost_price DECIMAL(10,2) NULL,
    category_id INT NOT NULL,
    brand_id INT NULL,
    featured_image VARCHAR(255),
    gallery JSON,
    status ENUM('draft', 'published', 'out_of_stock') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    weight DECIMAL(8,2) NULL,
    dimensions VARCHAR(100) NULL,
    material VARCHAR(100) NULL,
    care_instructions TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
  );
  ```

- [ ] **Product Variants Table** (Colors, Sizes)
  ```sql
  CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(50),
    size VARCHAR(20),
    price_adjustment DECIMAL(10,2) DEFAULT 0,
    stock_quantity INT DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
  );
  ```

- [ ] **Additional Tables**
  - `product_images` (Gallery images)
  - `product_attributes` (Custom attributes)
  - `orders` & `order_items`
  - `customers`
  - `coupons`
  - `settings`

**Deliverables:**
- Complete database schema
- All tables created with proper relationships
- Indexes for performance
- Sample data for testing

---

#### 📅 Ngày 3: Base Classes & Core System
**Mục tiêu**: Xây dựng foundation classes cho MVC

**Tasks:**
- [ ] **Database Class**
  ```php
  class Database {
    private $host, $username, $password, $database;
    private $connection;

    public function connect();
    public function query($sql, $params = []);
    public function fetchAll($sql, $params = []);
    public function fetchOne($sql, $params = []);
    public function execute($sql, $params = []);
    public function lastInsertId();
    public function beginTransaction();
    public function commit();
    public function rollback();
  }
  ```

- [ ] **BaseModel Class**
  ```php
  abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function find($id);
    public function findBy($column, $value);
    public function all();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function paginate($page, $limit);
    protected function validate($data);
  }
  ```

- [ ] **BaseController Class**
  ```php
  abstract class BaseController {
    protected function view($viewPath, $data = []);
    protected function redirect($url);
    protected function json($data, $status = 200);
    protected function validateRequest($rules);
    protected function flashMessage($type, $message);
    protected function isAuthenticated();
    protected function requireAuth();
  }
  ```

- [ ] **Authentication Middleware**
  ```php
  class AuthMiddleware {
    public function handle();
    public function login($email, $password);
    public function logout();
    public function check();
    public function user();
    protected function hash($password);
    protected function verify($password, $hash);
  }
  ```

**Deliverables:**
- Functional base classes
- Database connection working
- Authentication system ready

---

### 🎨 1.2 Admin UI Framework (Ngày 4-6)

#### 📅 Ngày 4: Admin Layout & Template System
**Mục tiêu**: Tạo admin template đẹp và responsive

**Tasks:**
- [ ] **Admin Layout Structure**
  ```php
  📁 views/admin/
  ├── 📁 layouts/
  │   ├── app.php (Main layout)
  │   ├── header.php
  │   ├── sidebar.php
  │   └── footer.php
  ├── 📁 auth/
  │   ├── login.php
  │   └── forgot-password.php
  ├── 📁 dashboard/
  │   └── index.php
  └── 📁 components/
      ├── breadcrumb.php
      ├── pagination.php
      └── alerts.php
  ```

- [ ] **Responsive Sidebar Navigation**
  - Dashboard
  - Products → List, Add, Categories, Brands
  - Orders → All Orders, Order Details
  - Customers → List, Customer Details
  - Marketing → Coupons, Banners
  - Settings → General, Email, Payment
  - Profile → Account, Logout

- [ ] **Header Components**
  - Admin profile dropdown
  - Notifications (optional)
  - Quick search (optional)
  - Mobile toggle button

- [ ] **CSS Framework Integration**
  - Bootstrap 5 / AdminLTE / Custom
  - Responsive design
  - Dark/Light theme support (optional)

**Deliverables:**
- Responsive admin layout
- Navigation system
- Mobile-friendly design

---

#### 📅 Ngày 5: Dashboard & Analytics Components
**Mục tiêu**: Tạo dashboard với thống kê và charts

**Tasks:**
- [ ] **Dashboard Controller**
  ```php
  class DashboardController extends BaseController {
    public function index() {
      $stats = [
        'total_products' => $this->getTotalProducts(),
        'total_orders' => $this->getTotalOrders(),
        'total_customers' => $this->getTotalCustomers(),
        'revenue' => $this->getTotalRevenue()
      ];

      $this->view('admin/dashboard/index', compact('stats'));
    }
  }
  ```

- [ ] **Statistics Cards**
  - Total Products
  - Total Orders (Today/This Month)
  - Total Customers
  - Revenue (Today/This Month)
  - Low Stock Alerts

- [ ] **Charts Integration**
  - Sales chart (Chart.js)
  - Top selling products
  - Order status distribution
  - Monthly revenue trend

- [ ] **Recent Activities**
  - Recent orders
  - Recent customers
  - Recent products added
  - Low stock products

**Deliverables:**
- Functional dashboard
- Real-time statistics
- Interactive charts

---

#### 📅 Ngày 6: Common UI Components & JavaScript
**Mục tiêu**: Tạo các component UI tái sử dụng

**Tasks:**
- [ ] **DataTables Integration**
  ```javascript
  // Common DataTable setup
  $('.data-table').DataTable({
    responsive: true,
    pageLength: 25,
    order: [[0, 'desc']],
    language: {
      url: 'assets/js/datatable-vi.json'
    }
  });
  ```

- [ ] **Form Validation**
  - Client-side validation (jQuery Validate)
  - Server-side validation
  - Error display system
  - Success messages

- [ ] **Image Upload Component**
  - Drag & drop upload
  - Preview functionality
  - Multiple file upload
  - Image cropping (optional)

- [ ] **Modal System**
  - Confirmation modals
  - Form modals
  - Image preview modals

- [ ] **Toast Notifications**
  - Success/Error/Warning/Info messages
  - Auto-dismiss functionality
  - Queue system

**Deliverables:**
- Reusable UI components
- Form validation system
- File upload functionality

---

### 🛠️ 1.3 Core Admin Features (Ngày 7-15)

#### 📅 Ngày 7-8: Authentication System
**Mục tiêu**: Hoàn thành hệ thống đăng nhập admin an toàn

**Tasks Day 7:**
- [ ] **Login Controller**
  ```php
  class AuthController extends BaseController {
    public function showLogin();
    public function login();
    public function logout();
    public function showForgotPassword();
    public function sendResetLink();
    public function showResetForm($token);
    public function resetPassword();
  }
  ```

- [ ] **Login Form**
  - Email/Username field
  - Password field
  - Remember me checkbox
  - CSRF protection
  - Rate limiting (login attempts)

- [ ] **Session Management**
  - Secure session handling
  - Session timeout
  - Remember me functionality
  - Session hijacking protection

**Tasks Day 8:**
- [ ] **Password Security**
  - bcrypt hashing
  - Password strength requirements
  - Password reset via email
  - Token-based reset system

- [ ] **Admin User Management**
  - Create admin users
  - Role-based permissions (optional)
  - Admin profile management
  - Change password functionality

**Deliverables:**
- Secure admin authentication
- Password reset system
- Session management

---

#### 📅 Ngày 9-10: Category Management System
**Mục tiêu**: CRUD hoàn chỉnh cho danh mục với nested categories

**Tasks Day 9:**
- [ ] **Category Model**
  ```php
  class Category extends BaseModel {
    protected $table = 'categories';
    protected $fillable = ['name', 'slug', 'description', 'image', 'parent_id', 'status'];

    public function children();
    public function parent();
    public function getTree();
    public function getAllWithHierarchy();
    public function generateSlug($name);
  }
  ```

- [ ] **Category Controller**
  ```php
  class CategoryController extends BaseController {
    public function index();      // List categories
    public function create();     // Show create form
    public function store();      // Save new category
    public function edit($id);    // Show edit form
    public function update($id);  // Update category
    public function destroy($id); // Delete category
    public function bulkAction(); // Bulk operations
  }
  ```

**Tasks Day 10:**
- [ ] **Category Views**
  - List view with parent-child hierarchy
  - Create/Edit forms
  - Image upload for category
  - Parent category selection (dropdown tree)
  - SEO meta fields

- [ ] **Category Features**
  - Drag & drop reordering (optional)
  - Bulk delete/activate
  - Category tree visualization
  - Search and filter
  - Pagination

**Deliverables:**
- Complete category CRUD
- Nested category support
- Admin interface for categories

---

#### 📅 Ngày 11-12: Brand Management System
**Mục tiêu**: Quản lý thương hiệu hoàn chỉnh

**Tasks Day 11:**
- [ ] **Brand Model & Controller**
  ```php
  class Brand extends BaseModel {
    protected $table = 'brands';
    protected $fillable = ['name', 'slug', 'description', 'logo', 'website', 'status'];

    public function products();
    public function generateSlug($name);
  }
  ```

- [ ] **Brand CRUD Operations**
  - Create brand with logo upload
  - List brands with search/filter
  - Edit brand information
  - Delete with product check
  - Bulk operations

**Tasks Day 12:**
- [ ] **Brand Interface**
  - DataTable listing
  - Form validation
  - Logo image upload
  - Status management
  - Products count per brand

**Deliverables:**
- Brand management system
- Logo upload functionality
- Brand-product relationships

---

#### 📅 Ngày 13-15: Product Management (Core Features)
**Mục tiêu**: Xây dựng hệ thống quản lý sản phẩm mạnh mẽ

**Tasks Day 13:**
- [ ] **Product Model**
  ```php
  class Product extends BaseModel {
    protected $table = 'products';
    protected $fillable = [
      'name', 'slug', 'sku', 'description', 'short_description',
      'price', 'sale_price', 'category_id', 'brand_id', 'featured_image',
      'status', 'featured', 'weight', 'dimensions', 'material'
    ];

    public function category();
    public function brand();
    public function variants();
    public function images();
    public function generateSKU();
    public function updateStock($quantity);
  }
  ```

- [ ] **Product Controller Foundation**
  ```php
  class ProductController extends BaseController {
    public function index();           // Product listing
    public function create();          // Create form
    public function store();           // Save product
    public function show($id);         // Product detail
    public function edit($id);         // Edit form
    public function update($id);       // Update product
    public function destroy($id);      // Delete product
    public function bulkAction();      // Bulk operations
    public function quickEdit();       // Quick edit modal
  }
  ```

**Tasks Day 14:**
- [ ] **Product Create/Edit Form**
  - Basic information (name, SKU, description)
  - Category selection (AJAX dropdown)
  - Brand selection
  - Pricing (regular price, sale price)
  - Stock management
  - Product status
  - Featured product toggle
  - SEO meta fields

- [ ] **Rich Text Editor**
  - TinyMCE integration for description
  - Image insertion capability
  - HTML content sanitization

**Tasks Day 15:**
- [ ] **Product Listing & Management**
  - DataTable with advanced filtering
  - Product search functionality
  - Filter by category, brand, status
  - Bulk actions (delete, update status)
  - Quick edit functionality
  - Export products to CSV

- [ ] **Product Images System**
  - Featured image upload
  - Multiple gallery images
  - Image preview and cropping
  - Drag & drop reordering
  - Alt text for SEO

**Deliverables:**
- Complete product CRUD
- Image management system
- Product listing with filters

---

### 🚀 1.4 Advanced Product Features (Ngày 16-21)

#### 📅 Ngày 16-17: Product Variants & Attributes
**Mục tiêu**: Hệ thống biến thể sản phẩm (màu sắc, size)

**Tasks Day 16:**
- [ ] **Product Variant Model**
  ```php
  class ProductVariant extends BaseModel {
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'sku', 'color', 'size', 'stock_quantity', 'image'];

    public function product();
    public function updateStock($quantity);
    public function isInStock();
  }
  ```

- [ ] **Variant Management Interface**
  - Add variants to product
  - Color picker for colors
  - Size selection (S, M, L, XL, XXL)
  - Stock per variant
  - Individual pricing (if different)
  - Variant images

**Tasks Day 17:**
- [ ] **Product Attributes System**
  - Material selection
  - Gender (Nam, Nữ, Unisex)
  - Season (Spring, Summer, Fall, Winter)
  - Style (Casual, Formal, Sport)
  - Custom attributes
  - Attribute groups

- [ ] **Size Chart Management**
  - Size chart upload
  - Size guide modal
  - Measurements table

**Deliverables:**
- Product variants system
- Attributes management
- Size chart functionality

---

#### 📅 Ngày 18-19: Inventory Management
**Mục tiêu**: Quản lý kho hàng chuyên nghiệp

**Tasks Day 18:**
- [ ] **Stock Tracking System**
  ```php
  class StockMovement extends BaseModel {
    protected $table = 'stock_movements';
    protected $fillable = ['product_id', 'variant_id', 'type', 'quantity', 'reason', 'reference'];

    public function product();
    public function variant();
  }
  ```

- [ ] **Inventory Features**
  - Real-time stock updates
  - Stock movement history
  - Low stock alerts
  - Stock adjustment forms
  - Inventory reports

**Tasks Day 19:**
- [ ] **Bulk Import/Export**
  - CSV template generation
  - Product import functionality
  - Data validation
  - Import preview
  - Error handling and reporting
  - Export products to CSV/Excel

**Deliverables:**
- Inventory tracking system
- Import/Export functionality
- Stock management tools

---

#### 📅 Ngày 20-21: SEO & Product Relations
**Mục tiêu**: Tối ưu SEO và quan hệ sản phẩm

**Tasks Day 20:**
- [ ] **SEO Optimization**
  - Meta title/description per product
  - URL slug optimization
  - Open Graph tags
  - JSON-LD schema markup
  - SEO analysis tools

- [ ] **URL Management**
  - Friendly URLs
  - Canonical URLs
  - 301 redirects for slug changes

**Tasks Day 21:**
- [ ] **Product Relations**
  - Related products selection
  - Cross-sell products
  - Up-sell products
  - Product collections/sets
  - Recently viewed products

- [ ] **Product Tags**
  - Tag system implementation
  - Tag-based product grouping
  - Tag cloud display

**Deliverables:**
- SEO optimization tools
- Product relationship system
- Tag management

---

### 📊 1.5 Order Management System (Ngày 22-28)

#### 📅 Ngày 22-23: Order Dashboard & Analytics
**Mục tiêu**: Tổng quan đơn hàng và thống kê

**Tasks Day 22:**
- [ ] **Order Model & Relationships**
  ```php
  class Order extends BaseModel {
    protected $table = 'orders';
    protected $fillable = [
      'user_id', 'order_code', 'total_amount', 'status',
      'payment_method', 'shipping_address', 'billing_address'
    ];

    public function user();
    public function items();
    public function generateOrderCode();
    public function calculateTotal();
    public function updateStatus($status);
  }

  class OrderItem extends BaseModel {
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'variant_id', 'quantity', 'price'];

    public function order();
    public function product();
    public function variant();
  }
  ```

**Tasks Day 23:**
- [ ] **Order Dashboard**
  - Order statistics cards
  - Recent orders table
  - Order status distribution chart
  - Revenue analytics
  - Filter by date range
  - Export order reports

- [ ] **Order Listing**
  - DataTable with advanced filtering
  - Search by order ID, customer email
  - Filter by status, date, payment method
  - Bulk status updates
  - Order export functionality

**Deliverables:**
- Order dashboard with analytics
- Order listing interface
- Order statistics

---

#### 📅 Ngày 24-25: Order Details & Status Management
**Mục tiêu**: Chi tiết đơn hàng và workflow quản lý

**Tasks Day 24:**
- [ ] **Order Detail View**
  - Order information summary
  - Customer details
  - Order items with product info
  - Shipping & billing addresses
  - Payment information
  - Order timeline/history

- [ ] **Order Status Workflow**
  - Pending → Processing → Shipped → Delivered
  - Cancelled/Refunded states
  - Email notifications on status change
  - Status change logging

**Tasks Day 25:**
- [ ] **Order Management Actions**
  - Edit order items (add/remove/update)
  - Apply discounts/coupons
  - Calculate shipping costs
  - Generate invoices (PDF)
  - Print shipping labels
  - Order notes & internal comments

- [ ] **Payment Management**
  - Payment status tracking
  - Refund processing
  - Payment method display
  - Transaction history

**Deliverables:**
- Order detail interface
- Status management system
- Order editing capabilities

---

#### 📅 Ngày 26-27: Customer Management
**Mục tiêu**: Quản lý khách hàng hoàn chỉnh

**Tasks Day 26:**
- [ ] **Customer Model & Controller**
  ```php
  class Customer extends BaseModel {
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'full_name', 'phone', 'status'];

    public function orders();
    public function totalSpent();
    public function averageOrderValue();
    public function orderCount();
  }
  ```

- [ ] **Customer Listing**
  - Customer DataTable
  - Search by name, email, phone
  - Customer registration date
  - Total orders and spent amount
  - Customer status management

**Tasks Day 27:**
- [ ] **Customer Detail View**
  - Customer profile information
  - Order history
  - Address book
  - Customer notes
  - Customer analytics (LTV, frequency)

- [ ] **Customer Segmentation**
  - VIP customers
  - New customers
  - Inactive customers
  - Customer groups/tags

**Deliverables:**
- Customer management system
- Customer analytics
- Customer segmentation

---

#### 📅 Ngày 28: Reports & Advanced Analytics
**Mục tiêu**: Báo cáo và phân tích nâng cao

**Tasks:**
- [ ] **Sales Reports**
  - Daily/Weekly/Monthly/Yearly sales
  - Revenue by product/category
  - Best selling products
  - Sales trends and forecasting

- [ ] **Inventory Reports**
  - Current stock levels
  - Low stock alerts
  - Stock movement history
  - Product performance (fast/slow moving)
  - Stock valuation report

- [ ] **Customer Reports**
  - New customer acquisition
  - Customer lifetime value
  - Customer retention rate
  - Geographic distribution

- [ ] **Export Functionality**
  - Export all reports to CSV/Excel
  - Scheduled reports (optional)
  - Email reports to admin

**Deliverables:**
- Comprehensive reporting system
- Analytics dashboard
- Export capabilities

---

## 🔌 PHASE 2: XÂY DỰNG API SYSTEM (Tuần 4-5)

### 🛠️ 2.1 API Foundation (Ngày 29-31)

#### 📅 Ngày 29: API Architecture Setup
**Mục tiêu**: Tạo foundation cho REST API

**Tasks:**
- [ ] **API Routing System**
  ```php
  // api/routes.php
  $router->group(['prefix' => 'api/v1'], function($router) {
    // Products
    $router->get('/products', 'Api\ProductController@index');
    $router->get('/products/{id}', 'Api\ProductController@show');

    // Categories
    $router->get('/categories', 'Api\CategoryController@index');
    $router->get('/categories/{id}/products', 'Api\CategoryController@products');

    // Cart
    $router->post('/cart/add', 'Api\CartController@add');
    $router->get('/cart', 'Api\CartController@index');
  });
  ```

- [ ] **API Response Format**
  ```php
  class ApiResponse {
    public static function success($data, $message = 'Success', $code = 200);
    public static function error($message, $code = 400, $errors = []);
    public static function paginated($data, $pagination, $message = 'Success');
  }
  ```

- [ ] **API Authentication**
  - JWT token implementation
  - API key authentication
  - Rate limiting middleware
  - CORS handling

#### 📅 Ngày 30-31: Core API Endpoints
**Mục tiêu**: Phát triển API endpoints cơ bản

**Tasks Day 30:**
- [ ] **Products API**
  ```php
  class ProductApiController {
    public function index();           // GET /api/products
    public function show($id);         // GET /api/products/{id}
    public function featured();        // GET /api/products/featured
    public function byCategory($id);   // GET /api/categories/{id}/products
    public function search();          // GET /api/products/search?q=query
  }
  ```

- [ ] **Categories API**
  ```php
  class CategoryApiController {
    public function index();           // GET /api/categories
    public function show($id);         // GET /api/categories/{id}
    public function tree();            // GET /api/categories/tree
    public function products($id);     // GET /api/categories/{id}/products
  }
  ```

**Tasks Day 31:**
- [ ] **Product Filtering & Search**
  - Filter by price range
  - Filter by category, brand
  - Filter by color, size
  - Search functionality
  - Sorting options
  - Pagination

**Deliverables:**
- Functional API foundation
- Core product/category endpoints
- API documentation

---

### 🛍️ 2.2 E-commerce APIs (Ngày 32-35)

#### 📅 Ngày 32-33: Cart & Wishlist APIs
**Mục tiêu**: Giỏ hàng và wishlist API

**Tasks Day 32:**
- [ ] **Cart Management API**
  ```php
  class CartApiController {
    public function index();           // GET /api/cart
    public function add();             // POST /api/cart/add
    public function update();          // PUT /api/cart/update
    public function remove();          // DELETE /api/cart/remove
    public function clear();           // DELETE /api/cart/clear
    public function count();           // GET /api/cart/count
  }
  ```

- [ ] **Cart Session/Storage**
  - Session-based cart for guests
  - Database cart for logged users
  - Cart persistence
  - Cart expiration

**Tasks Day 33:**
- [ ] **Wishlist API**
  ```php
  class WishlistApiController {
    public function index();           // GET /api/wishlist
    public function add();             // POST /api/wishlist/add
    public function remove();          // DELETE /api/wishlist/remove
    public function toggle();          // POST /api/wishlist/toggle
  }
  ```

**Deliverables:**
- Cart management API
- Wishlist functionality
- Session handling

---

#### 📅 Ngày 34-35: Order & Checkout APIs
**Mục tiêu**: Quy trình đặt hàng API

**Tasks Day 34:**
- [ ] **Checkout API**
  ```php
  class CheckoutApiController {
    public function validateOrder();    // POST /api/checkout/validate
    public function calculateTotals();  // POST /api/checkout/calculate
    public function applyCoupon();      // POST /api/checkout/coupon
    public function createOrder();      // POST /api/orders
  }
  ```

- [ ] **Order Validation**
  - Product availability check
  - Stock verification
  - Price validation
  - Shipping calculation

**Tasks Day 35:**
- [ ] **Order Management API**
  ```php
  class OrderApiController {
    public function store();           // POST /api/orders
    public function show($id);         // GET /api/orders/{id}
    public function userOrders();      // GET /api/user/orders
    public function updateStatus();    // PUT /api/orders/{id}/status
  }
  ```

- [ ] **Payment Integration**
  - Payment gateway setup (VNPay, MoMo)
  - Payment webhook handling
  - Payment status updates
  - Transaction logging

**Deliverables:**
- Checkout process API
- Order management API
- Payment integration

---

### 👤 2.3 User & Authentication APIs (Ngày 36-38)

#### 📅 Ngày 36-37: Authentication APIs
**Mục tiêu**: Hệ thống xác thực người dùng

**Tasks Day 36:**
- [ ] **Auth API Endpoints**
  ```php
  class AuthApiController {
    public function register();        // POST /api/auth/register
    public function login();           // POST /api/auth/login
    public function logout();          // POST /api/auth/logout
    public function refresh();         // POST /api/auth/refresh
    public function forgotPassword();  // POST /api/auth/forgot-password
    public function resetPassword();   // POST /api/auth/reset-password
  }
  ```

- [ ] **JWT Implementation**
  - Token generation
  - Token validation
  - Token refresh
  - Token blacklisting

**Tasks Day 37:**
- [ ] **User Registration & Validation**
  - Email uniqueness check
  - Password strength validation
  - Email verification
  - Phone verification (optional)

**Deliverables:**
- Authentication API
- JWT token system
- User registration

---

#### 📅 Ngày 38: User Profile APIs
**Mục tiêu**: Quản lý thông tin người dùng

**Tasks:**
- [ ] **User Profile API**
  ```php
  class UserApiController {
    public function profile();         // GET /api/user/profile
    public function updateProfile();   // PUT /api/user/profile
    public function changePassword();  // PUT /api/user/password
    public function uploadAvatar();    // POST /api/user/avatar
    public function orders();          // GET /api/user/orders
    public function wishlist();        // GET /api/user/wishlist
  }
  ```

- [ ] **Address Management**
  - Get user addresses
  - Add new address
  - Update address
  - Set default address
  - Delete address

**Deliverables:**
- User profile management API
- Address management
- Complete API documentation

---

## 🎨 PHASE 3: XÂY DỰNG CLIENT WEBSITE (Tuần 6-8)

### 🏠 3.1 Frontend Foundation (Ngày 39-42)

#### 📅 Ngày 39-40: Layout & Navigation
**Mục tiêu**: Tạo layout chính và navigation

**Tasks Day 39:**
- [ ] **Main Layout Structure**
  ```php
  📁 views/
  ├── 📁 layouts/
  │   ├── app.php (Main layout)
  │   ├── header.php
  │   ├── footer.php
  │   └── navigation.php
  ├── 📁 pages/
  │   ├── home.php
  │   ├── products.php
  │   ├── product-detail.php
  │   ├── cart.php
  │   └── checkout.php
  └── 📁 partials/
      ├── product-card.php
      ├── breadcrumb.php
      └── pagination.php
  ```

- [ ] **Header Components**
  - Logo và branding
  - Main navigation menu
  - Search bar
  - Cart icon với counter
  - User account dropdown
  - Mobile hamburger menu

**Tasks Day 40:**
- [ ] **Navigation System**
  - Multi-level dropdown menu
  - Category navigation
  - Mega menu (optional)
  - Mobile-responsive navigation
  - Search functionality

- [ ] **Footer Design**
  - Company information
  - Quick links
  - Social media links
  - Newsletter signup
  - Payment methods display

**Deliverables:**
- Complete layout structure
- Responsive navigation
- Header/Footer components

---

#### 📅 Ngày 41-42: Homepage Development
**Mục tiêu**: Xây dựng trang chủ ấn tượng

**Tasks Day 41:**
- [ ] **Homepage Sections**
  - Hero banner/slider
  - Featured categories grid
  - Featured products carousel
  - Special offers section
  - Brand showcase
  - Newsletter signup

- [ ] **Hero Slider**
  - Multiple slide support
  - Auto-advance functionality
  - Navigation dots/arrows
  - Mobile-responsive
  - Call-to-action buttons

**Tasks Day 42:**
- [ ] **Product Sections**
  - New arrivals
  - Best sellers
  - Sale items
  - Product carousels
  - "Load more" functionality

- [ ] **Category Grid**
  - Category images
  - Hover effects
  - Category links
  - Responsive grid layout

**Deliverables:**
- Functional homepage
- Product display sections
- Category navigation

---

### 🛒 3.2 Product Catalog (Ngày 43-47)

#### 📅 Ngày 43-44: Product Listing Page
**Mục tiêu**: Trang danh sách sản phẩm với filtering

**Tasks Day 43:**
- [ ] **Product Listing Layout**
  - Grid/List view toggle
  - Product cards design
  - Pagination
  - Products per page selector
  - Sort options dropdown

- [ ] **Product Filtering Sidebar**
  - Category filter
  - Price range slider
  - Brand checkboxes
  - Color swatches
  - Size options
  - Clear filters button

**Tasks Day 44:**
- [ ] **Product Card Design**
  - Product image với hover effect
  - Product name và price
  - Sale badge/discount
  - Quick view button
  - Add to cart button
  - Wishlist heart icon

- [ ] **AJAX Filtering**
  - Filter without page reload
  - URL updates for SEO
  - Loading states
  - Filter combinations

**Deliverables:**
- Product listing page
- Advanced filtering system
- Responsive product cards

---

#### 📅 Ngày 45-47: Product Detail Page
**Mục tiêu**: Trang chi tiết sản phẩm đầy đủ tính năng

**Tasks Day 45:**
- [ ] **Product Detail Layout**
  - Image gallery với zoom
  - Product information section
  - Add to cart form
  - Product tabs (description, reviews, etc.)
  - Related products

- [ ] **Image Gallery**
  - Multiple product images
  - Thumbnail navigation
  - Zoom on hover/click
  - Lightbox functionality
  - Mobile swipe support

**Tasks Day 46:**
- [ ] **Product Variants**
  - Color selection
  - Size selection
  - Stock availability display
  - Price updates
  - Add to cart functionality

- [ ] **Product Information**
  - Product description
  - Specifications table
  - Size chart modal
  - Care instructions
  - Shipping information

**Tasks Day 47:**
- [ ] **Product Tabs**
  - Description tab
  - Reviews tab (future)
  - Shipping tab
  - Size guide tab

- [ ] **Related Products**
  - Similar products carousel
  - Recently viewed products
  - Cross-sell recommendations

**Deliverables:**
- Complete product detail page
- Image gallery with zoom
- Variant selection system

---

### 🛍️ 3.3 Shopping Experience (Ngày 48-52)

#### 📅 Ngày 48-49: Shopping Cart
**Mục tiêu**: Giỏ hàng và cart management

**Tasks Day 48:**
- [ ] **Mini Cart Dropdown**
  - Cart items preview
  - Quantity display
  - Total amount
  - Quick remove option
  - "View Cart" và "Checkout" buttons

- [ ] **Cart Page**
  - Cart items table
  - Quantity update controls
  - Remove item functionality
  - Subtotal calculation
  - Continue shopping button

**Tasks Day 49:**
- [ ] **Cart Functionality**
  - Add to cart animation
  - AJAX cart updates
  - Cart persistence
  - Stock validation
  - Price calculations

- [ ] **Coupon System**
  - Coupon code input
  - Discount application
  - Coupon validation
  - Discount display

**Deliverables:**
- Shopping cart system
- Mini cart component
- Coupon functionality

---

#### 📅 Ngày 50-52: Checkout Process
**Mục tiêu**: Quy trình thanh toán hoàn chỉnh

**Tasks Day 50:**
- [ ] **Checkout Layout**
  - Multi-step checkout form
  - Order summary sidebar
  - Progress indicator
  - Guest checkout option

- [ ] **Shipping Information**
  - Billing/shipping address forms
  - Address validation
  - Shipping method selection
  - Shipping cost calculation

**Tasks Day 51:**
- [ ] **Payment Methods**
  - Payment option selection
  - Credit card form
  - E-wallet options (MoMo, ZaloPay)
  - COD option
  - Payment security

**Tasks Day 52:**
- [ ] **Order Review & Confirmation**
  - Order summary display
  - Terms and conditions
  - Place order functionality
  - Order confirmation page
  - Email confirmation

**Deliverables:**
- Complete checkout process
- Payment integration
- Order confirmation system

---

### 👤 3.4 User Account System (Ngày 53-56)

#### 📅 Ngày 53-54: Authentication Pages
**Mục tiêu**: Đăng nhập và đăng ký

**Tasks Day 53:**
- [ ] **Login/Register Forms**
  - Login form design
  - Registration form
  - Form validation
  - Error handling
  - Social login buttons (optional)

- [ ] **Password Management**
  - Forgot password form
  - Password reset flow
  - Password strength indicator
  - Show/hide password toggle

**Tasks Day 54:**
- [ ] **User Authentication Flow**
  - Login/logout functionality
  - Registration process
  - Email verification
  - Remember me feature
  - Redirect after login

**Deliverables:**
- Authentication system
- User registration/login
- Password management

---

#### 📅 Ngày 55-56: User Dashboard
**Mục tiêu**: Trang quản lý tài khoản người dùng

**Tasks Day 55:**
- [ ] **Dashboard Layout**
  - User sidebar navigation
  - Account overview
  - Quick stats display
  - Recent orders

- [ ] **Profile Management**
  - Edit profile form
  - Avatar upload
  - Personal information
  - Change password

**Tasks Day 56:**
- [ ] **Order History**
  - Order listing table
  - Order status display
  - Order details modal
  - Reorder functionality
  - Download invoice

- [ ] **Wishlist Management**
  - Wishlist items grid
  - Remove from wishlist
  - Move to cart
  - Wishlist sharing (optional)

**Deliverables:**
- User dashboard
- Profile management
- Order history interface

---

## 🎨 PHASE 4: UI/UX STYLING & FINALIZATION (Tuần 9-10)

### 🖌️ 4.1 Visual Design Implementation (Ngày 57-60)

#### 📅 Ngày 57-58: Design System & Branding
**Mục tiêu**: Implement thiết kế nhất quán

**Tasks Day 57:**
- [ ] **Color Palette Implementation**
  ```css
  :root {
    --primary-color: #2c3e50;
    --secondary-color: #e74c3c;
    --accent-color: #f39c12;
    --success-color: #27ae60;
    --text-color: #2c3e50;
    --background-color: #ecf0f1;
    --white: #ffffff;
    --gray-light: #bdc3c7;
    --gray-dark: #34495e;
  }
  ```

- [ ] **Typography System**
  ```css
  /* Font families */
  --font-heading: 'Montserrat', sans-serif;
  --font-body: 'Open Sans', sans-serif;
  --font-accent: 'Playfair Display', serif;

  /* Font sizes */
  --text-xs: 0.75rem;
  --text-sm: 0.875rem;
  --text-base: 1rem;
  --text-lg: 1.125rem;
  --text-xl: 1.25rem;
  --text-2xl: 1.5rem;
  --text-3xl: 1.875rem;
  --text-4xl: 2.25rem;
  ```

**Tasks Day 58:**
- [ ] **Component Styling**
  - Button styles (primary, secondary, outline)
  - Form element styling
  - Card components
  - Modal designs
  - Alert/notification styles

- [ ] **Layout Styling**
  - Grid system
  - Spacing utilities
  - Container widths
  - Responsive breakpoints

**Deliverables:**
- Consistent design system
- Typography implementation
- Component library

---

#### 📅 Ngày 59-60: Page-Specific Styling
**Mục tiêu**: Polish từng trang web

**Tasks Day 59:**
- [ ] **Homepage Styling**
  - Hero section design
  - Product carousel styling
  - Category grid design
  - Animation effects

- [ ] **Product Pages Styling**
  - Product card hover effects
  - Filter sidebar design
  - Product gallery styling
  - Responsive product grids

**Tasks Day 60:**
- [ ] **Cart & Checkout Styling**
  - Cart table design
  - Checkout form styling
  - Payment method cards
  - Order summary design

- [ ] **User Account Styling**
  - Dashboard layout
  - Profile form design
  - Order history table
  - Navigation styling

**Deliverables:**
- Polished page designs
- Consistent styling
- Professional appearance

---

### 📱 4.2 Responsive & Mobile Optimization (Ngày 61-63)

#### 📅 Ngày 61-62: Mobile-First Implementation
**Mục tiêu**: Tối ưu cho mobile

**Tasks Day 61:**
- [ ] **Mobile Navigation**
  - Hamburger menu
  - Mobile search
  - Touch-friendly buttons
  - Swipe gestures

- [ ] **Mobile Product Experience**
  - Mobile product cards
  - Touch-friendly filters
  - Mobile image gallery
  - Quick add to cart

**Tasks Day 62:**
- [ ] **Mobile Checkout**
  - Mobile-optimized forms
  - One-page checkout (mobile)
  - Touch-friendly payment
  - Mobile confirmations

- [ ] **Performance Optimization**
  - Image optimization
  - CSS/JS minification
  - Lazy loading
  - Critical CSS inlining

**Deliverables:**
- Mobile-optimized experience
- Touch-friendly interface
- Performance improvements

---

#### 📅 Ngày 63: Cross-Platform Testing
**Mục tiêu**: Đảm bảo compatibility

**Tasks:**
- [ ] **Browser Testing**
  - Chrome, Firefox, Safari, Edge
  - Mobile browsers (iOS Safari, Chrome Mobile)
  - Tablet browsers
  - Fallbacks for older browsers

- [ ] **Device Testing**
  - iPhone (various sizes)
  - Android devices
  - Tablets (iPad, Android)
  - Desktop resolutions

- [ ] **Performance Testing**
  - Page load times
  - Core Web Vitals
  - Lighthouse scores
  - Mobile page speed

**Deliverables:**
- Cross-browser compatibility
- Multi-device support
- Performance benchmarks

---

### 🚀 4.3 Final Polish & Launch Preparation (Ngày 64-70)

#### 📅 Ngày 64-65: Animations & Micro-interactions
**Mục tiêu**: Thêm animation và polish cuối cùng

**Tasks Day 64:**
- [ ] **Scroll Animations (AOS)**
  ```javascript
  // Animate on scroll
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
  });
  ```

- [ ] **Hover Effects**
  - Product card hovers
  - Button hover states
  - Image hover effects
  - Link hover animations

**Tasks Day 65:**
- [ ] **Loading States**
  - Page loading animations
  - AJAX loading indicators
  - Skeleton loading screens
  - Button loading states

- [ ] **Micro-interactions**
  - Add to cart animations
  - Form validation feedback
  - Toast notifications
  - Smooth transitions

**Deliverables:**
- Polished animations
- Professional micro-interactions
- Smooth user experience

---

#### 📅 Ngày 66-68: Testing & Quality Assurance
**Mục tiêu**: Comprehensive testing

**Tasks Day 66:**
- [ ] **Functionality Testing**
  - All forms working
  - CRUD operations
  - Payment flow
  - Email notifications

- [ ] **User Experience Testing**
  - Navigation flow
  - Shopping experience
  - Checkout process
  - Account management

**Tasks Day 67:**
- [ ] **Security Testing**
  - SQL injection prevention
  - XSS protection
  - CSRF tokens
  - Input validation

- [ ] **Performance Testing**
  - Page load times
  - Database queries
  - Image optimization
  - Caching effectiveness

**Tasks Day 68:**
- [ ] **Bug Fixes & Optimization**
  - Fix identified issues
  - Code optimization
  - Database optimization
  - Security patches

**Deliverables:**
- Fully tested application
- Bug-free experience
- Optimized performance

---

#### 📅 Ngày 69-70: SEO & Production Deployment
**Mục tiêu**: SEO optimization và deploy

**Tasks Day 69:**
- [ ] **SEO Implementation**
  - Meta tags optimization
  - Schema markup
  - Sitemap generation
  - robots.txt
  - Open Graph tags

- [ ] **Analytics Setup**
  - Google Analytics integration
  - E-commerce tracking
  - Goal setup
  - Search Console

**Tasks Day 70:**
- [ ] **Production Deployment**
  - Server configuration
  - SSL certificate
  - Database migration
  - Environment setup
  - Performance monitoring

- [ ] **Launch Checklist**
  - All features working
  - Security measures active
  - Backups configured
  - Monitoring setup
  - Documentation complete

**Deliverables:**
- SEO-optimized website
- Production deployment
- Monitoring & analytics

---

## ✅ COMPLETION CHECKLIST

### Phase 1 - Admin Panel ✅
- [ ] Complete MVC architecture
- [ ] Database schema implemented
- [ ] Admin authentication system
- [ ] Category management (CRUD)
- [ ] Brand management (CRUD)
- [ ] Product management with variants
- [ ] Image upload system
- [ ] Order management system
- [ ] Customer management
- [ ] Dashboard with analytics
- [ ] Inventory tracking
- [ ] Reports and exports

### Phase 2 - API System ✅
- [ ] REST API architecture
- [ ] Authentication APIs (JWT)
- [ ] Product APIs
- [ ] Category APIs
- [ ] Cart APIs
- [ ] Order APIs
- [ ] User management APIs
- [ ] Payment integration
- [ ] API documentation

### Phase 3 - Client Website ✅
- [ ] Responsive layout
- [ ] Homepage with hero/products
- [ ] Product catalog with filtering
- [ ] Product detail pages
- [ ] Shopping cart system
- [ ] Checkout process
- [ ] User authentication
- [ ] User dashboard
- [ ] Order management

### Phase 4 - UI/UX & Polish ✅
- [ ] Professional design implementation
- [ ] Mobile-responsive design
- [ ] Cross-browser compatibility
- [ ] Performance optimization
- [ ] SEO optimization
- [ ] Animations and micro-interactions
- [ ] Testing and bug fixes
- [ ] Production deployment

---

## 🎯 SUCCESS METRICS

### Technical Metrics
- Page load time < 3 seconds
- Mobile-friendly (Google Mobile-Friendly Test)
- Lighthouse score > 90
- Cross-browser compatibility
- Security best practices implemented

### Business Metrics
- Admin can manage full product catalog
- Complete order processing workflow
- Customer registration and login
- Shopping cart and checkout flow
- Payment integration working
- Email notifications functional

### User Experience Metrics
- Intuitive navigation
- Professional appearance
- Mobile-optimized experience
- Fast and responsive interface
- Error-free functionality

---

**🚀 FINAL GOAL: Một trang web bán quần áo hoàn chỉnh, chuyên nghiệp với admin panel mạnh mẽ để quản lý toàn bộ business!**

*Estimated Timeline: 10 weeks (70 days)*
*Team Size: 1-2 developers*
*Complexity: Intermediate to Advanced*

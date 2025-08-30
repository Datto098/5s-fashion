# UI Design Guidelines - Zone Fashion

> **Tài liệu chuẩn thiết kế giao diện cho dự án zone Fashion**
> Phân tích dựa trên homepage hiện tại để đảm bảo tính nhất quán trong thiết kế

## 📚 Table of Contents

1. [🎨 Brand Colors & Color Scheme](#brand-colors--color-scheme)
2. [📐 Layout Structure](#layout-structure)
3. [🎯 Component Design Standards](#component-design-standards)
4. [🖋️ Typography Standards](#typography-standards)
5. [🎭 Animation & Effects](#animation--effects)
6. [🔲 Button Styles](#button-styles)
7. [📱 Responsive Design Rules](#responsive-design-rules)
8. [🎪 Background Patterns](#background-patterns)
9. [🌟 Interactive Elements](#interactive-elements)
10. [📋 Component Usage Guidelines](#component-usage-guidelines)
11. [🛠️ Development Guidelines](#development-guidelines)
12. [🚨 Current Issues & Fixes](#current-issues--fixes-needed)
13. [✅ Quality Checklist](#quality-checklist)## 🎨 Brand Colors & Color Scheme

### Primary Colors
```css
:root {
  --primary-color: #007bff;    /* Blue - Chính */
  --secondary-color: #6c757d;  /* Gray - Phụ */
  --success-color: #28a745;    /* Green - Thành công */
  --danger-color: #dc3545;     /* Red - Lỗi/Xóa */
  --warning-color: #ffc107;    /* Yellow - Cảnh báo */
  --info-color: #17a2b8;       /* Cyan - Thông tin */
  --light-color: #f8f9fa;      /* Light Gray - Nền nhẹ */
  --dark-color: #343a40;       /* Dark Gray - Text chính */
}
```

### ⚠️ **NGHIÊM CẤM** sử dụng colors không nằm trong brand palette:
```css
/* ❌ KHÔNG được sử dụng */
#2c3e50, #34495e  /* Dark blue-gray - không thuộc brand */
#ecf0f1, #bdc3c7  /* Custom grays - sử dụng --light-color instead */
#95a5a6, #7f8c8d  /* Custom grays - sử dụng --secondary-color instead */
```

### Brand Color Usage Rules
```css
/* Text Colors - PHẢI sử dụng */
.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--secondary-color) !important; }
.text-dark { color: var(--dark-color) !important; }
.text-muted { color: var(--secondary-color); opacity: 0.75; }

/* Background Colors - PHẢI sử dụng */
.bg-primary { background-color: var(--primary-color) !important; }
.bg-light { background-color: var(--light-color) !important; }
.bg-dark { background-color: var(--dark-color) !important; }
```

### Gradient Colors (Voucher System)
```css
/* Orange Gradient - Voucher chính */
background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 50%, #ffa726 100%);

/* Green Gradient - Voucher fixed amount */
background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 50%, #81C784 100%);

/* Feature Section Background */
background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);

/* Section Title Underline */
background: linear-gradient(45deg, var(--primary-color), var(--info-color));
```

## 📐 Layout Structure

### Grid System
- **Framework**: Bootstrap 5 Grid System
- **Breakpoints**:
  - `xs`: <576px (Mobile)
  - `sm`: ≥576px (Small tablets)
  - `md`: ≥768px (Tablets)
  - `lg`: ≥992px (Desktops)
  - `xl`: ≥1200px (Large desktops)
  - `xxl`: ≥1400px (Extra large)

### ⚠️ **CHUẨN MỚI** - Standard Page Layout Pattern

#### Main Content Wrapper
```php
<?php
// PHẢI có ở đầu mọi page
ob_start();
?>

<!-- Page content here -->

<?php
// PHẢI có ở cuối mọi page
$content = ob_get_clean();

// Set page variables
$title = 'Page Title - zone Fashion';
$meta_description = 'Page description';
$custom_css = ['css/page-specific.css'];
$custom_js = ['js/page-specific.js'];

// Include layout
include VIEW_PATH . '/client/layouts/app.php';
?>
```

#### Section Structure - CHUẨN
```html
<section class="[page-name]-section py-5 [optional-bg-class]">
    <div class="container">
        <!-- Breadcrumb (nếu cần) -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item active">Current Page</li>
            </ol>
        </nav>

        <!-- Section Header (nếu cần) -->
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h1 class="section-title">[Page Title]</h1>
                    <p class="section-subtitle">[Page Subtitle]</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="row">
            <!-- Content columns -->
        </div>
    </div>
</section>
```

#### ❌ **NGHIÊM CẤM** - Layout Anti-Patterns
```html
<!-- KHÔNG sử dụng inline styles -->
<div style="padding: 2rem 0; background: #ecf0f1;">

<!-- KHÔNG sử dụng custom CSS trong HTML -->
<style>
.custom-section { background: #2c3e50; }
</style>

<!-- KHÔNG sử dụng non-Bootstrap spacing -->
<div class="custom-margin-top">
```

### Standard Page Types

#### 1. **Cart Page Pattern**
```css
.cart-section {
    padding: 3rem 0;
    background: var(--light-color);  /* PHẢI dùng brand color */
    min-height: 60vh;
}

.cart-card {
    border: none;
    border-radius: 15px;  /* CHUẨN */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);  /* CHUẨN */
}

.cart-header {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);  /* Brand gradient */
    color: white;
    padding: 1.5rem;
    border-radius: 15px 15px 0 0;
}
```

#### 2. **Product Detail Page Pattern**
```css
.product-detail-section {
    padding: 3rem 0;
    background: white;
}

.product-images {
    position: sticky;
    top: 20px;
}

.main-image-container {
    border-radius: 15px;  /* CHUẨN */
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}
```

#### 3. **Success/Confirmation Page Pattern**
```css
.success-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, var(--light-color) 0%, #e9ecef 100%);
    text-align: center;
}

.success-animation {
    margin-bottom: 3rem;
}

.success-title {
    font-size: 2.5rem;  /* Follow typography hierarchy */
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 1rem;
}
```## 🎯 Component Design Standards

### 1. Hero Section
```css
.hero-section {
    height: 80vh;
    min-height: 600px;
    position: relative;
}

.hero-slide {
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
    font-size: 1.3rem;
    line-height: 1.6;
    opacity: 0.95;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}
```

### 2. Section Headers
```css
.section-title {
    font-size: 2.8rem;
    font-weight: 700;
    color: var(--dark-color);
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(45deg, var(--primary-color), var(--info-color));
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.2rem;
    color: var(--secondary-color);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}
```

### 3. Feature Cards
```css
.feature-item {
    padding: 2rem;
    background: white;
    border-radius: 15px;  /* CHUẨN: 15px cho feature cards */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    text-align: center;
}

.feature-item:hover {
    transform: translateY(-10px);  /* CHUẨN: -10px hover lift */
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    margin-bottom: 1.5rem;
    font-size: 3rem;
    color: var(--primary-color);
}
```

### ⚠️ **CHUẨN MỚI** - Universal Card Design System

#### Standard Card Pattern
```css
.card {
    background: white;
    border: none;  /* KHÔNG sử dụng border */
    border-radius: 15px;  /* CHUẨN: 15px cho tất cả cards */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);  /* CHUẨN shadow */
    transition: all 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-8px);  /* CHUẨN hover effect */
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    font-weight: 600;
}

.card-body {
    padding: 2rem;
}
```

#### Product Card Pattern
```css
.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.product-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}
```

#### ❌ **NGHIÊM CẤM** - Card Anti-Patterns
```css
/* KHÔNG được sử dụng */
border-radius: 0.5rem;           /* Phải dùng 15px */
box-shadow: 0 0.125rem 0.25rem;  /* Phải dùng chuẩn shadow */
border: 1px solid #xxx;          /* KHÔNG dùng border */
background: #2c3e50;             /* Phải dùng brand colors */
```

### 4. Voucher Cards
```css
.voucher-card {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 50%, #ffa726 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    box-shadow: 0 15px 35px rgba(255, 107, 107, 0.3);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    min-height: 180px;
    position: relative;
    overflow: hidden;
}

.voucher-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px rgba(255, 107, 107, 0.4);
}

/* Decorative Perforations */
.voucher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 0% 50%, transparent 12px, white 13px, white 15px, transparent 16px),
                radial-gradient(circle at 100% 50%, transparent 12px, white 13px, white 15px, transparent 16px);
    background-size: 25px 25px;
    background-position: 0% 50%, 100% 50%;
    background-repeat: repeat-y;
    border-radius: 20px;
}
```

### 5. Category Cards
```css
.category-card {
    border-radius: 15px;
    overflow: hidden;
    height: 350px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.4));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card:hover .category-overlay {
    opacity: 1;
}
```

## 🖋️ Typography Standards

### Font Hierarchy
```css
/* Main Headings */
h1, .h1 { font-size: 3.5rem; font-weight: 700; }  /* Hero titles */
h2, .h2 { font-size: 2.8rem; font-weight: 700; }  /* Section titles */
h3, .h3 { font-size: 2.2rem; font-weight: 600; }  /* Sub-section titles */
h4, .h4 { font-size: 1.8rem; font-weight: 600; }  /* Card titles */
h5, .h5 { font-size: 1.4rem; font-weight: 600; }  /* Component titles */
h6, .h6 { font-size: 1.2rem; font-weight: 600; }  /* Small titles */

/* Body Text */
.lead { font-size: 1.3rem; line-height: 1.6; }    /* Hero subtitles */
p     { font-size: 1rem; line-height: 1.6; }      /* Standard text */
.text-sm { font-size: 0.875rem; }                 /* Small text */
.text-xs { font-size: 0.75rem; }                  /* Extra small text */
```

### Text Colors
```css
.text-primary { color: var(--primary-color); }
.text-secondary { color: var(--secondary-color); }
.text-dark { color: var(--dark-color); }
.text-light { color: var(--light-color); }
.text-white { color: white; }
.text-muted { color: #6c757d; opacity: 0.8; }
```

## 🎭 Animation & Effects

### Standard Hover Effects
```css
/* Card Hover */
.card-hover {
    transition: all 0.3s ease;
}
.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

/* Button Hover */
.btn {
    transition: all 0.3s ease;
    border-radius: 50px;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* Icon Hover */
.icon-hover {
    transition: all 0.3s ease;
}
.icon-hover:hover {
    transform: scale(1.1);
}
```

### Keyframe Animations
```css
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Usage */
.hero-title { animation: fadeInUp 1s ease; }
.hero-subtitle { animation: fadeInUp 1s ease 0.2s both; }
.hero-actions { animation: fadeInUp 1s ease 0.4s both; }
```

## 🔲 Button Styles

### ⚠️ **CHUẨN MỚI** - Button Design System

#### Primary Buttons
```css
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    border: none;
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;  /* PHẢI là 50px cho rounded buttons */
    transition: all 0.3s ease;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
}
```

#### Danger/Remove Buttons
```css
.btn-danger {
    background: linear-gradient(135deg, var(--danger-color), #c82333);
    border: none;
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
}
```

#### Success Buttons
```css
.btn-success {
    background: linear-gradient(135deg, var(--success-color), #20c997);
    border: none;
    padding: 12px 30px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
    color: white;
}
```

#### Outline Buttons
```css
.btn-outline-primary {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    padding: 10px 28px;  /* Adjust for border */
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.25);
}
```

#### ❌ **NGHIÊM CẤM** - Button Anti-Patterns
```css
/* KHÔNG được sử dụng */
border-radius: 0.5rem;      /* Phải dùng 50px */
border-radius: 0.375rem;    /* Phải dùng 50px */
background: #2c3e50;        /* Phải dùng brand colors */
padding: 1rem 2rem;         /* Sử dụng 12px 30px */
```

### Voucher Action Buttons
```css
.btn-save-voucher {
    padding: 10px 20px;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.btn-copy-code {
    padding: 10px 20px;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
```

## 📱 Responsive Design Rules

### Mobile-First Approach
- Thiết kế ưu tiên mobile (≤576px) trước
- Sử dụng Bootstrap breakpoints
- Test trên tất cả device sizes

### Key Responsive Patterns
```css
/* Mobile Adjustments */
@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .section-title { font-size: 2rem; }
    .feature-item { padding: 1.5rem; }
    .voucher-card { min-height: 160px; padding: 20px; }
}

@media (max-width: 576px) {
    .hero-title { font-size: 2rem; }
    .section-title { font-size: 1.5rem; }
    .feature-item { padding: 1rem; }
    .voucher-card { min-height: 140px; padding: 18px; }
}
```

## 🎪 Background Patterns

### Section Backgrounds
```css
/* Default section */
.section-default { background: white; }

/* Light section */
.section-light { background: #f8f9fa; }

/* Gradient section */
.section-gradient {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Image section với overlay */
.section-image {
    background-size: cover;
    background-position: center;
    position: relative;
}
.section-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}
```

## 🌟 Interactive Elements

### Toast Notifications
```css
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateX(100%);
    transition: transform 0.3s ease;
    font-size: 14px;
    max-width: 300px;
}

/* Toast Colors */
.toast-success { background: #28a745; color: white; }
.toast-error { background: #dc3545; color: white; }
.toast-warning { background: #ffc107; color: #333; }
.toast-info { background: #17a2b8; color: white; }
```

### Loading States
```css
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
```

## 📋 Component Usage Guidelines

### 1. **Hero Section** (Trang chủ, Landing pages)
- Sử dụng Swiper.js cho slider
- Tối thiểu 600px height
- Overlay gradient cho text readability
- Animation fadeInUp cho elements

### 2. **Section Headers** (Mọi section)
- Title + Subtitle structure
- Underline gradient decoration
- Center alignment
- Margin bottom 4rem

### 3. **Feature Cards** (Trang chủ, About)
- 3 cột trên desktop, responsive
- Icon + Title + Description
- Hover effect: translateY + shadow
- White background với subtle shadow

### 4. **Product Cards** (Shop, Categories)
- Sử dụng partial: `/client/partials/product-card.php`
- 4 cột desktop, 2 cột tablet, 1 cột mobile
- Image + Title + Price + Actions
- Hover effects cho image và buttons

### 5. **Category Cards** (Trang chủ, Categories)
- Image background với overlay
- Hover reveal content
- Fixed height 350px
- Rounded corners 15px

## 🛠️ Development Guidelines

### CSS Organization
```
/public/assets/css/
├── main.css          # Global styles, variables
├── homepage.css      # Homepage specific
├── [page].css        # Page specific styles
└── components/       # Component styles
    ├── hero.css
    ├── cards.css
    ├── forms.css
    └── buttons.css
```

### JavaScript Pattern
```javascript
// Component initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initializeSliders();
    initializeTooltips();
    initializeAnimations();
});

// Event delegation pattern
document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-copy-code')) {
        handleCopyCode(e);
    }
});
```

## 🚨 **CURRENT ISSUES & FIXES NEEDED**

### 📋 Analysis of Current Client Pages

#### 1. **Cart Page Issues** (`/client/cart/index.php`)
```css
/* ❌ VIOLATIONS FOUND */
.cart-section {
    background: #ecf0f1;  /* Should use var(--light-color) */
}

.cart-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);  /* Non-brand colors */
}

.cart-card {
    border-radius: 0.5rem;  /* Should be 15px */
}

/* ✅ CORRECT VERSION */
.cart-section {
    background: var(--light-color);
}

.cart-header {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
}

.cart-card {
    border-radius: 15px;
}
```

#### 2. **Product Detail Issues** (`/client/product/detail.php`)
```css
/* ❌ VIOLATIONS FOUND */
.product-price {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);  /* Should use CSS variables */
}

/* ✅ CORRECT VERSION */
.product-price {
    background: linear-gradient(135deg, var(--danger-color), #c82333);
}
```

#### 3. **Success/Tracking Pages** (`/client/order/`)
**Status**: ✅ **COMPLIANT** - These pages follow the layout standards correctly

### 🔧 **IMMEDIATE ACTION REQUIRED**

#### Priority 1: Color Standardization
```bash
# Search and replace these patterns:
Find: #2c3e50
Replace: var(--primary-color)

Find: #34495e
Replace: #0056b3

Find: #ecf0f1
Replace: var(--light-color)

Find: #bdc3c7
Replace: #dee2e6

Find: #7f8c8d
Replace: var(--secondary-color)
```

#### Priority 2: Border Radius Standardization
```bash
# Search and replace:
Find: border-radius: 0.5rem
Replace: border-radius: 15px

Find: border-radius: 0.375rem
Replace: border-radius: 15px

Find: border-radius: 8px
Replace: border-radius: 15px
```

#### Priority 3: Button Standardization
```bash
# All buttons should use:
padding: 12px 30px;
border-radius: 50px;
font-weight: 600;
transition: all 0.3s ease;
```

### 📝 **COMPLIANCE REPORT**

| Page | Layout ✓ | Colors ❌ | Components ❌ | Overall |
|------|----------|-----------|---------------|---------|
| Cart | ✅ | ❌ | ❌ | 33% |
| Product Detail | ✅ | ❌ | ❌ | 33% |
| Order Success | ✅ | ✅ | ✅ | 100% |
| Order Tracking | ✅ | ✅ | ✅ | 100% |

**Overall Compliance: 66%** - Needs improvement on Cart and Product Detail pages

### Icon Usage
- **FontAwesome 6**: Chính cho UI icons
- **Bootstrap Icons**: Backup option
- **Custom SVG**: Cho brand icons

### Image Guidelines
- **Hero**: 1920x1080 minimum
- **Category**: 800x600 recommended
- **Product**: 800x800 square
- **Format**: JPG cho photos, PNG cho graphics
- **Optimization**: WebP preferred, fallback JPG/PNG

## ✅ Quality Checklist

### Before Deploy
- [ ] Mobile responsiveness tested
- [ ] Cross-browser compatibility
- [ ] Loading performance optimized
- [ ] Accessibility standards met
- [ ] Color contrast ratios acceptable
- [ ] Animation performance smooth
- [ ] Images optimized and responsive
- [ ] JavaScript error-free
- [ ] CSS validated
- [ ] SEO meta tags included

### ⚠️ **CHUẨN MỚI** - UI Compliance Checklist

#### Color Compliance
- [ ] Only using CSS variables from brand palette
- [ ] No hardcoded colors outside brand palette
- [ ] Consistent color usage across all pages
- [ ] Proper contrast ratios for accessibility

#### Component Compliance
- [ ] All buttons using 50px border-radius
- [ ] All cards using 15px border-radius and standard shadow
- [ ] Consistent hover effects (translateY + shadow)
- [ ] Standard transition timing (0.3s ease)

#### Layout Compliance
- [ ] Using ob_start()/ob_get_clean() pattern
- [ ] Proper section structure with container/row/col
- [ ] Standard breadcrumb implementation
- [ ] Consistent spacing using Bootstrap classes

#### Code Quality
- [ ] No inline styles in HTML
- [ ] CSS organized in separate files
- [ ] JavaScript properly separated
- [ ] Following naming conventions

### Common Violations to Check

#### ❌ **Color Violations**
```css
/* Search for these patterns and replace */
#2c3e50, #34495e  /* Replace with var(--primary-color) */
#ecf0f1, #bdc3c7  /* Replace with var(--light-color) */
#95a5a6, #7f8c8d  /* Replace with var(--secondary-color) */
```

#### ❌ **Layout Violations**
```html
<!-- Replace custom styles with Bootstrap classes -->
<div style="padding: 2rem 0;">  <!-- Use class="py-4" -->
<div style="margin-bottom: 1rem;">  <!-- Use class="mb-3" -->
```

#### ❌ **Component Violations**
```css
/* Replace non-standard values -->
border-radius: 0.5rem;     /* Use 15px */
border-radius: 8px;        /* Use 15px */
padding: 1rem 2rem;        /* Use 12px 30px for buttons */
```

### Performance Standards

#### Image Optimization
```html
<!-- CHUẨN: Responsive images -->
<img src="image.jpg"
     alt="Description"
     class="img-fluid"
     loading="lazy"
     width="800"
     height="600">
```

#### CSS Loading
```php
// CHUẨN: Page-specific CSS
$custom_css = [
    'css/page-specific.css'  // Only load what's needed
];
```

#### JavaScript Loading
```php
// CHUẨN: Page-specific JS
$custom_js = [
    'js/page-specific.js'
];

// CHUẨN: Inline JS for small scripts
$inline_js = "
// Small page-specific functionality
";
```

---

**Lưu ý quan trọng**:
- ⚠️ **KHÔNG được vi phạm brand colors** - Tất cả màu sắc phải từ CSS variables
- ⚠️ **KHÔNG được sử dụng inline styles** - Tất cả styles phải trong CSS files
- ⚠️ **PHẢI tuân thủ component standards** - Border-radius, shadows, transitions
- ⚠️ **PHẢI test responsive** - Mobile-first approach
- ⚠️ **PHẢI follow layout patterns** - Consistent structure across pages
- Luôn maintain consistency với brand colors
- Prioritize user experience over fancy effects
- Keep accessibility in mind
- Document any new patterns added

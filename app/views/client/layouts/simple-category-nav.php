<?php
// Alternative simple category navigation for the header
// This uses simple links without relying on dropdowns

// Get the current request URL and parameters
$currentUrl = $_SERVER['REQUEST_URI'];
$currentCategory = $_GET['category'] ?? '';

// Function to check if a category is active
function isActiveCategory($slug) {
    global $currentCategory;
    if (empty($currentCategory)) return '';

    // Check exact match
    if ($slug === $currentCategory) return 'active';

    // Check if this is a parent of the current category
    // This requires knowing parent-child relationships
    // Can be expanded with more sophisticated checks if needed

    return '';
}
?>

<!-- Simple Category Navigation -->
<div class="simple-category-nav p-3 bg-white border-top">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-start">
                    <?php if (isset($navCategories) && !empty($navCategories)): ?>
                        <?php foreach ($navCategories as $category): ?>
                            <div class="category-item me-4 mb-3">
                                <a href="<?= url('shop?category=' . $category['slug']) ?>"
                                   class="category-link fw-bold <?= isActiveCategory($category['slug']) ?>">
                                    <i class="fas <?= $category['slug'] === 'nam' ? 'fa-tshirt' : ($category['slug'] === 'nu' ? 'fa-female' : 'fa-tag') ?> me-2"></i>
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>

                                <?php if (!empty($category['children'])): ?>
                                <div class="subcategories mt-2 ms-3">
                                    <?php foreach ($category['children'] as $child): ?>
                                    <div class="mb-1">
                                        <a href="<?= url('shop?category=' . $child['slug']) ?>"
                                           class="subcategory-link <?= isActiveCategory($child['slug']) ?>">
                                            <?= htmlspecialchars($child['name']) ?>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple Category Navigation Styles */
.simple-category-nav {
    border-bottom: 1px solid #eee;
}

.simple-category-nav .category-link {
    color: #333;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.2s;
}

.simple-category-nav .category-link:hover,
.simple-category-nav .category-link.active {
    color: var(--bs-primary);
}

.simple-category-nav .subcategory-link {
    color: #666;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}

.simple-category-nav .subcategory-link:hover,
.simple-category-nav .subcategory-link.active {
    color: var(--bs-primary);
}
</style>

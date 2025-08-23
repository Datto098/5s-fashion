<?php
// Admin panel to toggle between menu types
// This file should be included in an admin settings page

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menu_type'])) {
    // Load current config
    $configPath = APP_PATH . '/config/site.php';
    $siteConfig = require $configPath;

    // Update menu type setting
    $siteConfig['use_simple_menu'] = isset($_POST['use_simple_menu']) ? true : false;

    // Save config back to file
    file_put_contents($configPath, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($siteConfig, true) . ';');

    // Set success message
    $_SESSION['success_message'] = 'Menu type settings updated successfully!';

    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Load current config
$siteConfig = require APP_PATH . '/config/site.php';
$useSimpleMenu = $siteConfig['use_simple_menu'] ?? false;
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Menu Navigation Settings</h5>
    </div>
    <div class="card-body">
        <!-- Display success message if available -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Navigation Menu Type</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="use_simple_menu" value="0" id="megaMenu" <?= !$useSimpleMenu ? 'checked' : '' ?>>
                    <label class="form-check-label" for="megaMenu">
                        Mega Menu (Dropdown style)
                    </label>
                    <small class="d-block text-muted mb-2">The traditional mega menu with dropdowns and advanced styling.</small>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="use_simple_menu" value="1" id="simpleMenu" <?= $useSimpleMenu ? 'checked' : '' ?>>
                    <label class="form-check-label" for="simpleMenu">
                        Simple Menu (Accessible style)
                    </label>
                    <small class="d-block text-muted">A simplified navigation menu that works better on all devices without JavaScript complexity.</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="update_menu_type" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

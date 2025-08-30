<?php
/**
 * Menu Settings Controller
 * zone Fashion E-commerce Platform
 */

class MenuSettingsController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            redirect('admin/login');
        }

        // Load current config
        $siteConfig = require APP_PATH . '/config/site.php';
        $useSimpleMenu = $siteConfig['use_simple_menu'] ?? false;

        // Display settings page
        $this->view('admin/settings/menu_settings', [
            'title' => 'Menu Settings - Admin Dashboard',
            'useSimpleMenu' => $useSimpleMenu
        ]);
    }

    public function update()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            redirect('admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Load current config
            $configPath = APP_PATH . '/config/site.php';
            $siteConfig = require $configPath;

            // Update menu type setting
            $siteConfig['use_simple_menu'] = isset($_POST['use_simple_menu']) && $_POST['use_simple_menu'] == '1';

            // Save config back to file
            file_put_contents($configPath, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($siteConfig, true) . ';');

            // Set success message
            $_SESSION['success_message'] = 'Menu settings updated successfully!';

            // Redirect back to settings page
            redirect('admin/settings/menu');
        }
    }

    private function isAdmin()
    {
        return isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }
}

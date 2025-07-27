<?php
/**
 * Debug Controller
 * For testing routes and system info
 */

class DebugController
{
    public function index()
    {
        require_once VIEW_PATH . '/debug/routes.php';
    }

    public function routes()
    {
        require_once VIEW_PATH . '/debug/routes.php';
    }

    public function info()
    {
        phpinfo();
    }
}

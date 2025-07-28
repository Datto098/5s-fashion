<?php
// Debug API routing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>API Debug Info</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . ($_SERVER['PATH_INFO'] ?? 'Not set') . "</p>";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "<p><strong>Parsed path:</strong> " . $path . "</p>";

// Remove /api prefix if present
if (strpos($path, '/api') === 0) {
    $path = substr($path, 4);
}
echo "<p><strong>After removing /api:</strong> " . $path . "</p>";

// Remove project folder from path
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
echo "<p><strong>Script dir:</strong> " . $scriptName . "</p>";

if (strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
}
echo "<p><strong>Final path:</strong> " . ($path ?: '/') . "</p>";
?>

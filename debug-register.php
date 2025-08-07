<?php
require_once __DIR__ . '/app/core/App.php';

// Load models
require_once __DIR__ . '/app/models/User.php';

echo "<h2>Debug Registration Process</h2>";

// Test User model
$userModel = new User();

// Test data
$userData = [
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
    'full_name' => 'Test User',
    'phone' => '0123456789',
    'role' => 'customer',
    'status' => 'active'
];

echo "<h3>Test Data:</h3>";
echo "<pre>" . print_r($userData, true) . "</pre>";

echo "<h3>Testing User Creation:</h3>";

try {
    $result = $userModel->createUser($userData);
    
    if ($result) {
        echo "<div style='color: green;'>✓ User created successfully!</div>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "<div style='color: red;'>✗ User creation failed!</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Exception: " . $e->getMessage() . "</div>";
    echo "<div>Stack trace: " . $e->getTraceAsString() . "</div>";
}

// Test database connection
echo "<h3>Testing Database Connection:</h3>";
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "<div style='color: green;'>✓ Database connected successfully!</div>";
        
        // Test query
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM users");
        echo "<div>Current users count: " . $result['count'] . "</div>";
        
    } else {
        echo "<div style='color: red;'>✗ Database connection failed!</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Database Exception: " . $e->getMessage() . "</div>";
}
?>

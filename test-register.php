<?php
// Test registration process
require_once 'app/core/App.php';

echo "<h2>Testing Registration Process</h2>";

// Simulate form data from your screenshot
$_POST = [
    'first_name' => 'Do Ngoc',
    'last_name' => 'Hieu',
    'email' => 'test_' . time() . '@gmail.com', // Use unique email
    'phone' => '0384946973',
    'password' => '123456',
    'password_confirmation' => '123456',
    'terms' => 'on'
];

echo "<h3>Form Data:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Test the AuthController logic
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$name = trim($firstName . ' ' . $lastName);
$username = $lastName; // This is the problem!

echo "<h3>Processed Data:</h3>";
echo "firstName: " . $firstName . "<br>";
echo "lastName: " . $lastName . "<br>";
echo "name: " . $name . "<br>";
echo "username: " . $username . "<br>";

// Test if username is valid
if (strlen($username) < 3) {
    echo "<div style='color: red;'>❌ Username '$username' is too short (less than 3 characters)</div>";
} else {
    echo "<div style='color: green;'>✅ Username '$username' is valid</div>";
}

// Test User model
try {
    require_once 'app/models/User.php';
    $userModel = new User();
    
    // Generate a better username
    $betterUsername = strtolower($firstName . $lastName);
    $betterUsername = preg_replace('/[^a-zA-Z0-9]/', '', $betterUsername);
    
    echo "<h3>Better Username Generation:</h3>";
    echo "Generated username: " . $betterUsername . "<br>";
    
    // Test creating user with better data
    $userData = [
        'username' => $betterUsername,
        'email' => $_POST['email'],
        'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'full_name' => $name,
        'phone' => $_POST['phone'],
        'role' => 'customer',
        'status' => 'active'
    ];
    
    echo "<h3>User Data to Create:</h3>";
    echo "<pre>";
    print_r($userData);
    echo "</pre>";
    
    $result = $userModel->createUser($userData);
    
    if ($result) {
        echo "<div style='color: green;'>✅ User created successfully!</div>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create user</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Exception: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

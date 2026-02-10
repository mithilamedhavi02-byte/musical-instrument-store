<?php
// test_login.php
require_once "includes/db.php";

echo "<h2>Testing Login System</h2>";

// Test database connection
echo "<h3>1. Database Connection:</h3>";
if ($conn) {
    echo "✅ Database connected successfully!<br>";
} else {
    echo "❌ Database connection failed!<br>";
    exit();
}

// Check users table
echo "<h3>2. Users in Database:</h3>";
$result = query("SELECT user_id, name, email, role, password FROM users");
if ($result) {
    $users = fetch_all($result);
    if (empty($users)) {
        echo "❌ No users found in database!<br>";
    } else {
        echo "✅ Found " . count($users) . " users:<br>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password Hash</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td style='font-size:10px;'>{$user['password']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Test password verification
echo "<h3>3. Password Verification Test:</h3>";
$test_password = "admin123";
$hashed_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($test_password, $hashed_password)) {
    echo "✅ Password verification works!<br>";
    echo "Test password: $test_password<br>";
    echo "Matches hash: Yes<br>";
} else {
    echo "❌ Password verification failed!<br>";
    echo "Test password: $test_password<br>";
    echo "Hash: $hashed_password<br>";
}

// Test direct query
echo "<h3>4. Test Admin Login:</h3>";
$email = "admin@musicshop.lk";
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = query($sql);
if ($result && mysqli_num_rows($result) > 0) {
    $user = fetch_one($result);
    echo "✅ Admin user found!<br>";
    echo "Name: {$user['name']}<br>";
    echo "Email: {$user['email']}<br>";
    echo "Password in DB: {$user['password']}<br>";
} else {
    echo "❌ Admin user not found!<br>";
}

echo "<hr>";
echo "<h3>Login Instructions:</h3>";
echo "<p><strong>Admin Login:</strong><br>";
echo "Email: admin@musicshop.lk<br>";
echo "Password: admin123</p>";

echo "<p><strong>Customer Login:</strong><br>";
echo "Email: john@example.com<br>";
echo "Password: customer123</p>";

echo "<p><strong>Test Login:</strong><br>";
echo "Email: test@example.com<br>";
echo "Password: test123</p>";
?>
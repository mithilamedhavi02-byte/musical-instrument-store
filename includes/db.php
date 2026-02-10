<?php
// includes/db.php - COMPLETE DATABASE CONNECTION
// NO session_start() HERE

$host = "localhost";
$user = "root";
$password = "";
$database = "music_shop";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// SIMPLE HELPER FUNCTIONS
function query($sql) {
    global $conn;
    
    // Log queries for debugging
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['debug']) && $_SESSION['debug']) {
        error_log("SQL Query: " . $sql);
    }
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        $error_msg = "Query failed: " . mysqli_error($conn) . " | SQL: " . $sql;
        error_log($error_msg);
        
        // Show error in development mode
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['debug']) && $_SESSION['debug']) {
            die($error_msg);
        }
        
        return false;
    }
    
    return $result;
}

function fetch_all($result) {
    if (!$result) {
        return [];
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    mysqli_data_seek($result, 0); // Reset pointer
    return $rows;
}

function fetch_one($result) {
    if (!$result) {
        return null;
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_data_seek($result, 0); // Reset pointer
    return $row;
}

function escape($str) {
    global $conn;
    
    if (is_null($str)) {
        return 'NULL';
    }
    
    if (is_array($str)) {
        foreach ($str as $key => $value) {
            $str[$key] = escape($value);
        }
        return $str;
    }
    
    return mysqli_real_escape_string($conn, trim($str));
}

function last_id() {
    global $conn;
    return mysqli_insert_id($conn);
}

function num_rows($result) {
    if (!$result) {
        return 0;
    }
    return mysqli_num_rows($result);
}

function affected_rows() {
    global $conn;
    return mysqli_affected_rows($conn);
}

// Database initialization function
function init_database() {
    global $conn;
    
    // Check if tables exist, create if not
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            user_id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            phone VARCHAR(20),
            address TEXT,
            user_role ENUM('user', 'admin') DEFAULT 'user',
            active BOOLEAN DEFAULT 1,
            remember_token VARCHAR(100),
            token_expiry DATETIME NULL,
            last_login DATETIME NULL,
            last_ip VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'login_attempts' => "CREATE TABLE IF NOT EXISTS login_attempts (
            attempt_id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(100),
            ip_address VARCHAR(45),
            attempt_time INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'categories' => "CREATE TABLE IF NOT EXISTS categories (
            category_id INT PRIMARY KEY AUTO_INCREMENT,
            category_name VARCHAR(100) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'products' => "CREATE TABLE IF NOT EXISTS products (
            product_id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(200) NOT NULL,
            description TEXT NOT NULL,
            short_description VARCHAR(500),
            price DECIMAL(10,2) NOT NULL,
            compare_price DECIMAL(10,2),
            category_id INT,
            brand VARCHAR(100),
            model VARCHAR(100),
            spec_1 VARCHAR(200),
            spec_2 VARCHAR(200),
            spec_3 VARCHAR(200),
            spec_4 VARCHAR(200),
            spec_5 VARCHAR(200),
            stock_quantity INT DEFAULT 0,
            low_stock_threshold INT DEFAULT 5,
            image_url VARCHAR(255),
            gallery_images TEXT,
            active BOOLEAN DEFAULT 1,
            featured BOOLEAN DEFAULT 0,
            views_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
        )",
        
        'orders' => "CREATE TABLE IF NOT EXISTS orders (
            order_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            order_number VARCHAR(50) UNIQUE,
            total_amount DECIMAL(10,2) NOT NULL,
            shipping_address TEXT,
            billing_address TEXT,
            phone VARCHAR(20),
            payment_method VARCHAR(50),
            payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
            order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
        )",
        
        'order_items' => "CREATE TABLE IF NOT EXISTS order_items (
            order_item_id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT,
            product_id INT,
            product_name VARCHAR(200),
            product_price DECIMAL(10,2),
            quantity INT NOT NULL,
            subtotal DECIMAL(10,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
        )",
        
        'reviews' => "CREATE TABLE IF NOT EXISTS reviews (
            review_id INT PRIMARY KEY AUTO_INCREMENT,
            product_id INT,
            user_id INT,
            rating INT CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            approved BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )"
    ];
    
    // Create tables
    foreach ($tables as $table_name => $sql) {
        query($sql);
    }
    
    // Insert default categories
    $categories = query("SELECT COUNT(*) as count FROM categories");
    $count = fetch_one($categories)['count'];
    
    if ($count == 0) {
        $default_categories = [
            ['Guitars', 'Acoustic, electric, bass and classical guitars'],
            ['Keyboards & Pianos', 'Digital pianos, synthesizers, MIDI controllers'],
            ['Drums & Percussion', 'Drum sets, cymbals, percussion instruments'],
            ['Wind Instruments', 'Flutes, saxophones, trumpets, clarinets'],
            ['String Instruments', 'Violins, cellos, violas, double basses'],
            ['Accessories', 'Cases, strings, picks, stands, tuners'],
            ['Recording Equipment', 'Microphones, audio interfaces, studio monitors']
        ];
        
        foreach ($default_categories as $category) {
            $name = escape($category[0]);
            $desc = escape($category[1]);
            query("INSERT INTO categories (category_name, description) VALUES ('$name', '$desc')");
        }
    }
    
    // Insert default admin user if not exists
    $admin_check = query("SELECT COUNT(*) as count FROM users WHERE user_role = 'admin'");
    $admin_count = fetch_one($admin_check)['count'];
    
    if ($admin_count == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        query("INSERT INTO users (username, email, password, full_name, user_role) 
               VALUES ('admin', 'admin@musicshop.lk', '$admin_password', 'Admin User', 'admin')");
    }
    
    // Insert default customer user if not exists
    $user_check = query("SELECT COUNT(*) as count FROM users WHERE user_role = 'user'");
    $user_count = fetch_one($user_check)['count'];
    
    if ($user_count == 0) {
        $user_password = password_hash('customer123', PASSWORD_DEFAULT);
        query("INSERT INTO users (username, email, password, full_name, user_role) 
               VALUES ('john', 'john@example.com', '$user_password', 'John Doe', 'user')");
        
        $user_password2 = password_hash('customer123', PASSWORD_DEFAULT);
        query("INSERT INTO users (username, email, password, full_name, user_role) 
               VALUES ('sarah', 'sarah@example.com', '$user_password2', 'Sarah Smith', 'user')");
    }
    
    // Insert sample products if none exist
    $products_check = query("SELECT COUNT(*) as count FROM products");
    $products_count = fetch_one($products_check)['count'];
    
    if ($products_count == 0) {
        $guitar_category = fetch_one(query("SELECT category_id FROM categories WHERE category_name = 'Guitars' LIMIT 1"));
        $keyboard_category = fetch_one(query("SELECT category_id FROM categories WHERE category_name = 'Keyboards & Pianos' LIMIT 1"));
        $drum_category = fetch_one(query("SELECT category_id FROM categories WHERE category_name = 'Drums & Percussion' LIMIT 1"));
        
        if ($guitar_category) {
            $sample_products = [
                [
                    'name' => 'Fender Stratocaster Electric Guitar',
                    'description' => 'American Professional II Stratocaster with maple neck and alder body',
                    'price' => 145000.00,
                    'category_id' => $guitar_category['category_id'],
                    'brand' => 'Fender',
                    'spec_1' => 'Alder Body',
                    'spec_2' => 'Maple Neck',
                    'spec_3' => '3 Single-coil Pickups',
                    'stock_quantity' => 10
                ],
                [
                    'name' => 'Taylor 314ce Acoustic Guitar',
                    'description' => 'Grand Auditorium acoustic-electric guitar with ES2 electronics',
                    'price' => 185000.00,
                    'category_id' => $guitar_category['category_id'],
                    'brand' => 'Taylor',
                    'spec_1' => 'Sitka Spruce Top',
                    'spec_2' => 'Sapele Back & Sides',
                    'spec_3' => 'ES2 Electronics',
                    'stock_quantity' => 5
                ],
                [
                    'name' => 'Yamaha P-125 Digital Piano',
                    'description' => '88-key digital piano with GHS weighted action and Pure CF sound engine',
                    'price' => 85000.00,
                    'category_id' => $keyboard_category['category_id'],
                    'brand' => 'Yamaha',
                    'spec_1' => '88 Weighted Keys',
                    'spec_2' => '24 Voices',
                    'spec_3' => 'Built-in Speakers',
                    'stock_quantity' => 8
                ],
                [
                    'name' => 'Pearl Export Drum Set',
                    'description' => '5-piece drum set with hardware and cymbals included',
                    'price' => 125000.00,
                    'category_id' => $drum_category['category_id'],
                    'brand' => 'Pearl',
                    'spec_1' => '5-Piece Setup',
                    'spec_2' => 'Including Cymbals',
                    'spec_3' => 'Hardware Included',
                    'stock_quantity' => 3
                ]
            ];
            
            foreach ($sample_products as $product) {
                $name = escape($product['name']);
                $desc = escape($product['description']);
                $price = $product['price'];
                $cat_id = $product['category_id'];
                $brand = escape($product['brand']);
                $spec1 = escape($product['spec_1']);
                $spec2 = escape($product['spec_2']);
                $spec3 = escape($product['spec_3']);
                $stock = $product['stock_quantity'];
                
                query("INSERT INTO products (name, description, price, category_id, brand, spec_1, spec_2, spec_3, stock_quantity) 
                       VALUES ('$name', '$desc', $price, $cat_id, '$brand', '$spec1', '$spec2', '$spec3', $stock)");
            }
        }
    }
}

// Initialize database on first run (only in development)
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Check if initialized flag exists in database or file
    $check_table = query("SELECT 1 FROM users LIMIT 1");
    if (!$check_table) {
        init_database();
    }
}

// Debug function
function debug($data, $label = '') {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['debug']) && $_SESSION['debug']) {
        echo '<div style="background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin: 10px 0;">';
        if ($label) {
            echo '<strong>' . htmlspecialchars($label) . ':</strong><br>';
        }
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        echo '</div>';
    }
}

// Function to check if user is logged in
function is_logged_in() {
    return session_status() === PHP_SESSION_ACTIVE && 
           isset($_SESSION['user_id']) && 
           $_SESSION['logged_in'] === true;
}

// Function to check if user is admin
function is_admin() {
    return session_status() === PHP_SESSION_ACTIVE && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'admin';
}

// Function to require login
function require_login($redirect_to = 'login.php') {
    if (!is_logged_in()) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: $redirect_to");
        exit();
    }
}

// Function to require admin access
function require_admin($redirect_to = 'login.php') {
    require_login($redirect_to);
    
    if (!is_admin()) {
        header("Location: index.php");
        exit();
    }
}

// Function to get database connection
function get_db_connection() {
    global $conn;
    return $conn;
}

// Function to execute prepared statement
function execute_prepared($sql, $params = [], $types = '') {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return false;
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b'; // blob
                }
            }
        }
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    $executed = mysqli_stmt_execute($stmt);
    if (!$executed) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    return $stmt;
}

// Function to fetch results from prepared statement
function fetch_prepared($sql, $params = [], $types = '') {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return [];
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b'; // blob
                }
            }
        }
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        error_log("Get result failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return [];
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $rows;
}

// Function to fetch single row from prepared statement
function fetch_one_prepared($sql, $params = [], $types = '') {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return null;
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b'; // blob
                }
            }
        }
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        error_log("Get result failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return null;
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $row ?: null;
}

// Function to execute prepared statement without fetching
function execute_prepared_no_fetch($sql, $params = [], $types = '') {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return false;
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b'; // blob
                }
            }
        }
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    $executed = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $executed;
}

// Close database connection on script end
register_shutdown_function(function() use ($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
});
?>
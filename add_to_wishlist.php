<?php
// add_to_wishlist.php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first to add items to wishlist!',
        'redirect' => 'login.php',
        'requires_login' => true
    ]);
    exit();
}

// Initialize wishlist if not exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'toggle';

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID'
    ]);
    exit();
}

if ($action === 'toggle') {
    // Check if product is already in wishlist
    $index = array_search($product_id, $_SESSION['wishlist']);
    
    if ($index !== false) {
        // Remove from wishlist
        array_splice($_SESSION['wishlist'], $index, 1);
        $added = false;
        $message = 'Product removed from wishlist';
    } else {
        // Add to wishlist
        $_SESSION['wishlist'][] = $product_id;
        $added = true;
        $message = 'Product added to wishlist';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'added' => $added,
        'wishlist_count' => count($_SESSION['wishlist'])
    ]);
    
} elseif ($action === 'add') {
    // Add to wishlist (don't remove if already exists)
    if (!in_array($product_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $product_id;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to wishlist',
        'wishlist_count' => count($_SESSION['wishlist'])
    ]);
    
} elseif ($action === 'remove') {
    // Remove from wishlist
    $index = array_search($product_id, $_SESSION['wishlist']);
    if ($index !== false) {
        array_splice($_SESSION['wishlist'], $index, 1);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product removed from wishlist',
        'wishlist_count' => count($_SESSION['wishlist'])
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>
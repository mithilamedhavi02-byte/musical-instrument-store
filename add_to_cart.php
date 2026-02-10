<?php
// add_to_cart.php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first to add items to cart!',
        'redirect' => 'login.php',
        'requires_login' => true
    ]);
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID'
    ]);
    exit();
}

if ($action === 'add') {
    // Add product to cart or increase quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'quantity' => 1,
            'added_at' => time()
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart_count' => count($_SESSION['cart'])
    ]);
    
} elseif ($action === 'remove') {
    // Remove from cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product removed from cart',
        'cart_count' => count($_SESSION['cart'])
    ]);
    
} elseif ($action === 'update') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'cart_count' => count($_SESSION['cart'])
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>
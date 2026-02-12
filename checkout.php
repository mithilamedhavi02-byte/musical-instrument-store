<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? header('location:login.php');

if(isset($_POST['order_btn'])){
    $total_amount = 0;
    // Cart eke total eka ganeema
    if(isset($_SESSION['cart'])){
        foreach($_SESSION['cart'] as $item){
            $total_amount += ($item['price']);
        }
    }

    // Oyage orders table ekata data damma
    $insert_order = mysqli_query($conn, "INSERT INTO `orders`(user_id, total_amount, shipping_cost, status) 
                    VALUES('$user_id', '$total_amount', '0.00', 'Pending')");
    
    $order_id = mysqli_insert_id($conn);

    if($insert_order){
        foreach($_SESSION['cart'] as $item){
            $product_id = $item['id']; // Product id eka ganeema
            $price = $item['price'];
            $qty = 1; // Default quantity 1 kiyala damma

            // Oyage order_items table ekata data damma
            mysqli_query($conn, "INSERT INTO `order_items`(order_id, product_id, quantity, price_at_purchase) 
                        VALUES('$order_id', '$product_id', '$qty', '$price')");
        }
        unset($_SESSION['cart']); 
        echo "<script>alert('Order Placed Successfully!'); window.location.href='index.php';</script>";
    }
}
?>

<?php include 'header.php'; ?>
<div class="container py-5 text-center">
    <div class="card p-5 shadow-sm border-0 mx-auto" style="max-width: 500px;">
        <h2 class="fw-bold mb-4">Checkout</h2>
        <h4 class="mb-4 text-muted">Total Amount: £<?php 
            $total = 0;
            if(isset($_SESSION['cart'])){ foreach($_SESSION['cart'] as $i){ $total += $i['price']; } }
            echo number_format($total, 2);
        ?></h4>
        <form action="" method="post">
            <button type="submit" name="order_btn" class="btn btn-warning btn-lg w-100 fw-bold">Confirm & Place Order</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
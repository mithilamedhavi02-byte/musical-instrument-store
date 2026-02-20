<?php
include 'config.php';

// Session eka active neththan wetharak start karanna kiyala meka danna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Update Logic
if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$qty' WHERE id = '$cart_id'");
   $message[] = 'Cart updated successfully!';
}

// Delete Logic
if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
   exit(); // header ekakata passe exit danna eka hodai
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
    
    <div class="table-responsive">
        <table class="table bg-white shadow-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
                
                if(mysqli_num_rows($cart_query) > 0){
                    while($fetch_cart = mysqli_fetch_assoc($cart_query)){
                        $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                        $grand_total += $sub_total;
                ?>
                <tr>
                    <td class="fw-bold"><?php echo $fetch_cart['name']; ?></td>
                    <td>£<?php echo number_format($fetch_cart['price'], 2); ?></td>
                    <td>
                        <form action="" method="post" class="d-flex align-items-center">
                            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                            <input type="number" name="cart_quantity" min="1" class="form-control form-control-sm me-2" style="width: 70px;" value="<?php echo $fetch_cart['quantity']; ?>">
                            <button type="submit" name="update_cart" class="btn btn-warning btn-sm">Update</button>
                        </form>
                    </td>
                    <td class="fw-bold">£<?php echo number_format($sub_total, 2); ?></td>
                    <td>
                        <a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="text-danger" onclick="return confirm('Remove this item?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center py-4">Your cart is empty!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if($grand_total > 0): 
        // BUSINESS RULE: £100 rule for shipping
        $shipping = ($grand_total > 100) ? 0 : 10;
    ?>
    <div class="card p-4 shadow-sm float-end border-0" style="width: 350px; background: #fdfdfd;">
        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <span>£<?php echo number_format($grand_total, 2); ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2 <?php echo ($shipping == 0)? 'text-success fw-bold':''; ?>">
            <span>Shipping:</span>
            <span><?php echo ($shipping == 0)? 'FREE' : '£10.00'; ?></span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-4">
            <h4 class="fw-bold">Total:</h4>
            <h4 class="fw-bold text-warning">£<?php echo number_format($grand_total + $shipping, 2); ?></h4>
        </div>
        <a href="checkout.php" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">PROCEED TO CHECKOUT</a>
    </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
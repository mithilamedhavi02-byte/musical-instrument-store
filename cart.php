<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Quantity Update logic
if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'");
}

// Remove Item logic
if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
}
?>

<?php include 'header.php'; ?>

<div class="py-5 text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
    <h1 class="fw-bold text-warning">Your Shopping Cart</h1>
</div>

<div class="container py-5">
    <div class="table-responsive shadow-sm rounded">
        <table class="table align-middle bg-white">
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
                // Query eka variable ekakata gannawa
                $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");

                // Meka thamai FIX eka: Query eka wada karanawada kiyala check kireema
                if($cart_query && mysqli_num_rows($cart_query) > 0){
                    while($fetch_cart = mysqli_fetch_assoc($cart_query)){
                        $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                        $grand_total += $sub_total;
                ?>
                <tr>
                    <td>
                        <img src="uploads/<?php echo $fetch_cart['image']; ?>" height="50" class="me-2">
                        <?php echo $fetch_cart['name']; ?>
                    </td>
                    <td>£<?php echo number_format($fetch_cart['price'], 2); ?></td>
                    <td>
                        <form action="" method="post" class="d-flex">
                            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                            <input type="number" name="cart_quantity" min="1" value="<?php echo $fetch_cart['quantity']; ?>" class="form-control form-control-sm" style="width:60px;">
                            <button type="submit" name="update_cart" class="btn btn-sm btn-warning ms-1"><i class="fa-solid fa-sync"></i></button>
                        </form>
                    </td>
                    <td class="fw-bold">£<?php echo number_format($sub_total, 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center py-4">Your cart is empty or Database error!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
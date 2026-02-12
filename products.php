<?php
include 'config.php';

// Session eka check karala start kireema
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// 1. Wishlist Logic
if(isset($_POST['add_to_wishlist'])){
   if($user_id == 0){
      header('location:login.php');
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = $_POST['product_price'];
   $p_image = $_POST['product_image'];

   $check_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$p_name' AND user_id = '$user_id'");

   if($check_wishlist && mysqli_num_rows($check_wishlist) > 0){
      echo "<script>alert('Already in wishlist!');</script>";
   } elseif($check_wishlist) {
      mysqli_query($conn, "INSERT INTO `wishlist`(user_id, name, price, image) VALUES('$user_id', '$p_name', '$p_price', '$p_image')");
      echo "<script>alert('Added to wishlist!');</script>";
   } else {
      die('Wishlist Query Failed: ' . mysqli_error($conn));
   }
}

// 2. Cart Logic
if(isset($_POST['add_to_cart'])){
   if($user_id == 0){
      header('location:login.php');
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = $_POST['product_price'];
   $p_image = $_POST['product_image'];
   $p_qty = $_POST['product_quantity'];

   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$p_name' AND user_id = '$user_id'");

   if($check_cart && mysqli_num_rows($check_cart) > 0){
      echo "<script>alert('Already in cart!');</script>";
   } elseif($check_cart) {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$p_name', '$p_price', '$p_qty', '$p_image')");
      header('location:cart.php');
      exit();
   }
}
?>

<?php include 'header.php'; ?>

<div class="py-5 text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1514119412350-e174d90d280e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
    <h1 class="fw-bold text-warning display-4">Our Instruments</h1>
    <p class="lead">Premium gear for your musical journey</p>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php
            // Database eken badu gannawa
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed');
            if(mysqli_num_rows($select_products) > 0){
                while($fetch_products = mysqli_fetch_assoc($select_products)){
                    // Image eka naththan default image ekak pennanna
                    $img_file = "uploads/" . $fetch_products['image_url'];
                    $display_img = (empty($fetch_products['image_url']) || !file_exists($img_file)) ? 'https://via.placeholder.com/300x200?text=Instrument' : $img_file;
        ?>
        <div class="col-md-3">
            <form action="" method="post" class="card h-100 shadow-sm border-0 p-3 rounded-3">
                <img src="<?php echo $display_img; ?>" class="card-img-top rounded" style="height:200px; object-fit:cover;">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-2"><?php echo $fetch_products['name']; ?></h5>
                    <h5 class="text-warning fw-bold mb-3">£<?php echo number_format($fetch_products['price'], 2); ?></h5>
                    
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_products['image_url']; ?>">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light small fw-bold">Qty</span>
                        <input type="number" name="product_quantity" value="1" min="1" class="form-control text-center">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-warning flex-grow-1 fw-bold shadow-sm">
                            <i class="fa-solid fa-cart-plus me-1"></i> Add
                        </button>
                        <button type="submit" name="add_to_wishlist" class="btn btn-outline-danger shadow-sm">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
                }
            } else {
                echo '<p class="text-center w-100 py-5">No products found in the database!</p>';
            }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>
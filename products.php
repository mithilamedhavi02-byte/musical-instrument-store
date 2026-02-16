<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$message = []; // Alerts pennanna array ekak

// ==========================================
// 1. ADD TO CART LOGIC
// ==========================================
if(isset($_POST['add_to_cart'])){
   if($user_id == 0){
      header('location:login.php'); 
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $p_qty = mysqli_real_escape_string($conn, $_POST['product_quantity']);

   // Check if already in cart
   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Instrument already in your cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$p_name', '$p_price', '$p_qty', '$p_image')") or die('query failed');
      $message[] = 'Added to cart successfully!';
   }
}

// ==========================================
// 2. ADD TO WISHLIST LOGIC
// ==========================================
if(isset($_POST['add_to_wishlist'])){
   if($user_id == 0){
      header('location:login.php');
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);

   // Check if already in wishlist
   $check_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_wishlist) > 0){
      $message[] = 'Instrument already in your wishlist!';
   } else {
      mysqli_query($conn, "INSERT INTO `wishlist`(user_id, name, price, image) VALUES('$user_id', '$p_name', '$p_price', '$p_image')") or die('query failed');
      $message[] = 'Added to wishlist!';
   }
}

$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
?>

<?php include 'header.php'; ?>

<?php if(!empty($message)): ?>
    <div class="container mt-3">
        <?php foreach($message as $msg): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-info-circle me-2"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="py-5 text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1514119412350-e174d90d280e?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
    <h1 class="fw-bold text-warning display-4">Our Instruments</h1>
    <p class="lead">Premium gear for your musical journey</p>
</div>

<div class="bg-dark py-3 border-bottom border-warning sticky-top" style="top: 70px; z-index: 100;">
   <div class="container text-center">
      <div class="d-flex flex-wrap justify-content-center gap-2">
         <a href="products.php" class="btn btn-outline-warning btn-sm rounded-pill <?php echo ($category_filter == '') ? 'active' : ''; ?>">All Instruments</a>
         
         <?php
            $get_nav_cats = mysqli_query($conn, "SELECT DISTINCT category FROM `products` WHERE category != ''") or die('query failed');
            while($nav_row = mysqli_fetch_assoc($get_nav_cats)){
               $cat_name = $nav_row['category'];
               $active_class = ($category_filter == $cat_name) ? 'active' : '';
               echo "<a href='products.php?category=$cat_name' class='btn btn-outline-warning btn-sm rounded-pill $active_class'>$cat_name</a>";
            }
         ?>
      </div>
   </div>
</div>

<div class="container py-5">
    <h3 class="fw-bold mb-4 text-center text-uppercase tracking-wider">
        <?php echo ($category_filter != '') ? 'Category: ' . $category_filter : 'Explore All Products'; ?>
    </h3>

    <div class="row g-4">
        <?php
            $query = "SELECT * FROM `products`";
            if($category_filter != '') {
                $query .= " WHERE category = '$category_filter'";
            }
            
            $select_products = mysqli_query($conn, $query) or die('Query failed');
            
            if(mysqli_num_rows($select_products) > 0){
                while($fetch_products = mysqli_fetch_assoc($select_products)){
                    $img_path = "uploaded_img/" . $fetch_products['image_url'];
                    $display_img = (!empty($fetch_products['image_url']) && file_exists($img_path)) ? $img_path : 'https://via.placeholder.com/300x200?text=Melody+Masters';
        ?>
        <div class="col-md-3 col-sm-6">
            <form action="" method="post" class="card h-100 shadow-sm border-0 p-3 rounded-4 transition-hover">
                <div class="position-relative">
                    <img src="<?php echo $display_img; ?>" class="card-img-top rounded-3" style="height:200px; object-fit:cover;">
                    <span class="badge bg-dark position-absolute top-0 start-0 m-2 opacity-75 small">
                        <?php echo $fetch_products['category']; ?>
                    </span>
                </div>
                
                <div class="card-body text-center px-0 pb-0">
                    <h6 class="fw-bold text-dark mb-1 text-truncate"><?php echo $fetch_products['name']; ?></h6>
                    <h5 class="text-warning fw-bold mb-3">£<?php echo number_format($fetch_products['price'], 2); ?></h5>
                    
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_products['image_url']; ?>">
                    
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text bg-light border-0">Qty</span>
                        <input type="number" name="product_quantity" value="1" min="1" class="form-control border-0 text-center bg-light">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-warning flex-grow-1 fw-bold shadow-sm rounded-3">
                            <i class="fa-solid fa-cart-plus me-1"></i> Add
                        </button>
                        <button type="submit" name="add_to_wishlist" class="btn btn-outline-danger shadow-sm rounded-3">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5"><p class="lead text-muted">No instruments found here yet.</p></div>';
            }
        ?>
    </div>
</div>

<style>
.transition-hover:hover { 
    transform: translateY(-5px); 
    transition: 0.3s ease-in-out; 
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; 
}
.btn-outline-warning.active { 
    background-color: #ffc107 !important; 
    color: #000 !important; 
}
.tracking-wider { letter-spacing: 1px; }
</style>

<?php include 'footer.php'; ?>
<?php
include 'config.php';

// Session Notice fix
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Remove Item Logic
if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE id = '$remove_id'");
   header('location:wishlist.php');
   exit();
}

// Clear All Logic
if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE user_id = '$user_id'");
   header('location:wishlist.php');
   exit();
}
?>

<?php include 'header.php'; ?>

<div class="py-5 text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1459749411177-042180ce673c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
    <h1 class="fw-bold text-warning display-4">My Wishlist</h1>
    <p class="lead">Your favorite instruments saved for later</p>
</div>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold m-0 border-start border-warning border-4 ps-3">Saved Items</h2>
        <?php 
           $check_items = mysqli_query($conn, "SELECT id FROM `wishlist` WHERE user_id = '$user_id'");
           if($check_items && mysqli_num_rows($check_items) > 0): 
        ?>
            <a href="wishlist.php?delete_all" class="btn btn-outline-danger btn-sm fw-bold" onclick="return confirm('Clear all items from wishlist?');">
                <i class="fa-solid fa-trash-can me-1"></i> Clear Wishlist
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php
            $wishlist_query = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE user_id = '$user_id'");
            
            if($wishlist_query){
                if(mysqli_num_rows($wishlist_query) > 0){
                    while($fetch_wishlist = mysqli_fetch_assoc($wishlist_query)){
                        $img_file = "uploads/" . $fetch_wishlist['image'];
                        $display_img = (empty($fetch_wishlist['image']) || !file_exists($img_file)) ? 'https://via.placeholder.com/300x200?text=No+Image' : $img_file;
        ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 p-3 text-center rounded-3 position-relative">
                <img src="<?php echo $display_img; ?>" class="card-img-top rounded mb-3" style="height:180px; object-fit:cover;">
                
                <h6 class="fw-bold mb-1"><?php echo $fetch_wishlist['name']; ?></h6>
                <p class="text-warning fw-bold mb-3">£<?php echo number_format($fetch_wishlist['price'], 2); ?></p>
                
                <div class="d-grid gap-2">
                    <form action="products.php" method="post">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_wishlist['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_wishlist['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $fetch_wishlist['image']; ?>">
                        <input type="hidden" name="product_quantity" value="1">
                        <button type="submit" name="add_to_cart" class="btn btn-warning btn-sm w-100 fw-bold shadow-sm">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Move to Cart
                        </button>
                    </form>
                    <a href="wishlist.php?remove=<?php echo $fetch_wishlist['id']; ?>" class="btn btn-light btn-sm text-danger border fw-bold" onclick="return confirm('Remove this item?');">
                        <i class="fa-solid fa-xmark me-1"></i> Remove
                    </a>
                </div>
            </div>
        </div>
        <?php
                    }
                } else {
                    echo '<div class="col-12 text-center py-5">
                            <i class="fa-regular fa-heart mb-3 text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h4 class="text-muted">Your wishlist is currently empty.</h4>
                            <p class="text-secondary mb-4">Explore our shop and save your favorite gear here!</p>
                            <a href="products.php" class="btn btn-warning px-5 fw-bold">Browse Products</a>
                          </div>';
                }
            } else {
                echo '<div class="alert alert-danger shadow-sm">Query Error: ' . mysqli_error($conn) . '</div>';
            }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>
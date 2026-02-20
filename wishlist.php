<?php
include 'config.php';

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

<style>
    /* Hero Section - Matching Products & About */
    .wishlist-hero {
        background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                    url('https://images.unsplash.com/photo-1459749411177-042180ce673c?auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    
    .wishlist-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 50 L30 40 L40 50 L50 30 L60 50 L70 35 L80 50" stroke="%23ffc107" fill="none" stroke-width="2"/><circle cx="25" cy="60" r="3" fill="%23ffc107"/><circle cx="45" cy="60" r="3" fill="%23ffc107"/><circle cx="65" cy="60" r="3" fill="%23ffc107"/></svg>');
        background-size: 200px 200px;
        animation: floatNotes 20s linear infinite;
    }
    
    @keyframes floatNotes {
        from { transform: translateY(0); }
        to { transform: translateY(-100%); }
    }
    
    .wishlist-hero h1 {
        font-size: 4rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #ffc107;
    }

    /* Wishlist Cards - Matching Product Cards */
    .wishlist-card {
        background: white;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        overflow: hidden;
    }
    
    .wishlist-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 30px rgba(0,0,0,0.15) !important;
    }

    .wishlist-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        border-radius: 15px;
        margin: 15px;
    }

    .wishlist-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .wishlist-card:hover .wishlist-img-wrapper img {
        transform: scale(1.1);
    }

    .price-tag {
        color: #ffc107;
        font-size: 1.3rem;
        font-weight: 800;
    }

    /* Action Buttons */
    .btn-move-cart {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        border: none;
        color: #000;
        font-weight: 700;
        border-radius: 10px;
        transition: 0.3s;
    }

    .btn-move-cart:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }

    .btn-remove {
        background: #f8f9fa;
        color: #dc3545;
        border: 1px solid #eee;
        border-radius: 10px;
        transition: 0.3s;
    }

    .btn-remove:hover {
        background: #dc3545;
        color: white;
    }

    .empty-wishlist i {
        font-size: 5rem;
        color: #ffc107;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 0.5; }
    }
</style>

<div class="wishlist-hero text-center text-white">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold" data-aos="zoom-in">My Wishlist</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="200">Your favorite instruments, ready to be yours.</p>
    </div>
</div>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-right">
        <div>
            <h2 class="fw-bold m-0 text-dark">Saved Gear</h2>
            <div style="width: 50px; height: 4px; background: #ffc107; border-radius: 2px;"></div>
        </div>
        <?php 
           $check_items = mysqli_query($conn, "SELECT id FROM `wishlist` WHERE user_id = '$user_id'");
           if($check_items && mysqli_num_rows($check_items) > 0): 
        ?>
            <a href="wishlist.php?delete_all" class="btn btn-outline-danger rounded-pill fw-bold btn-sm px-4" onclick="return confirm('Clear all items from wishlist?');">
                <i class="fa-solid fa-trash-can me-1"></i> Clear All
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php
            $wishlist_query = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE user_id = '$user_id'");
            
            if($wishlist_query && mysqli_num_rows($wishlist_query) > 0){
                while($fetch_wishlist = mysqli_fetch_assoc($wishlist_query)){
                    // Path eka product page ekatama match kala (uploaded_img)
                    $img_path = "uploaded_img/" . $fetch_wishlist['image'];
                    $display_img = (!empty($fetch_wishlist['image']) && file_exists($img_path)) ? $img_path : 'https://via.placeholder.com/300x200?text=Instrument';
        ?>
        <div class="col-md-4 col-lg-3" data-aos="fade-up">
            <div class="wishlist-card shadow-sm h-100">
                <div class="wishlist-img-wrapper">
                    <img src="<?php echo $display_img; ?>" alt="Product">
                </div>
                
                <div class="card-body text-center pt-0 px-4 pb-4">
                    <h6 class="fw-bold text-dark text-truncate mb-2"><?php echo $fetch_wishlist['name']; ?></h6>
                    <div class="price-tag mb-3">Rs.<?php echo number_format($fetch_wishlist['price'], 2); ?></div>
                    
                    <div class="d-grid gap-2">
                        <form action="products.php" method="post">
                            <input type="hidden" name="product_name" value="<?php echo $fetch_wishlist['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $fetch_wishlist['price']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $fetch_wishlist['image']; ?>">
                            <input type="hidden" name="product_quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn btn-move-cart w-100 py-2">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                            </button>
                        </form>
                        <a href="wishlist.php?remove=<?php echo $fetch_wishlist['id']; ?>" class="btn btn-remove py-2 fw-bold" onclick="return confirm('Remove this item?');">
                            <i class="fa-solid fa-trash-can me-1"></i> Remove
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5 empty-wishlist" data-aos="zoom-in">
                        <i class="fa-solid fa-heart-circle-xmark mb-4"></i>
                        <h3 class="fw-bold text-dark">Your wishlist is lonely!</h3>
                        <p class="text-muted mb-4">You haven\'t saved any instruments yet. Let\'s find some magic.</p>
                        <a href="products.php" class="btn btn-warning px-5 py-3 rounded-pill fw-bold shadow">
                            <i class="fas fa-search me-2"></i>Explore Products
                        </a>
                      </div>';
            }
        ?>
    </div>
</div>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>

<?php include 'footer.php'; ?>
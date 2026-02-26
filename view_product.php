<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
$product_id = $_GET['id'] ?? 0;

// Product details ganeema
$product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$product_id'");
$product = mysqli_fetch_assoc($product_query);

// VERIFIED REVIEW LOGIC
$check_order = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id' AND status = 'Completed'");
$can_review = (mysqli_num_rows($check_order) > 0);

if(isset($_POST['submit_review'])){
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    mysqli_query($conn, "INSERT INTO `reviews` (product_id, user_id, rating, comment) VALUES ('$product_id', '$user_id', '$rating', '$comment')");
}
?>

<?php include 'header.php'; ?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="uploaded_img/<?php echo $product['image_url']; ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h1><?php echo $product['name']; ?></h1>
            <h3 class="text-success">£<?php echo number_format($product['price'], 2); ?></h3>
            <p>Stock Available: <?php echo $product['stock_quantity']; ?></p>
            
            <hr>
            <h4>Customer Reviews</h4>
            <?php
            $reviews = mysqli_query($conn, "SELECT reviews.*, users.name FROM `reviews` JOIN `users` ON reviews.user_id = users.id WHERE product_id = '$product_id'");
            while($rev = mysqli_fetch_assoc($reviews)){
                echo "<div class='mb-2 border-bottom'><strong>{$rev['name']}</strong> ({$rev['rating']}/5)<p>{$rev['comment']}</p></div>";
            }
            ?>

            <?php if($can_review): ?>
            <div class="card p-3 mt-4 bg-light">
                <h6>Leave a Verified Review</h6>
                <form action="" method="POST">
                    <select name="rating" class="form-select mb-2">
                        <option value="5">5 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <textarea name="comment" class="form-control mb-2" required></textarea>
                    <button name="submit_review" class="btn btn-dark">Post Review</button>
                </form>
            </div>
            <?php else: ?>
                <p class="text-muted mt-3 small italic">Only customers who purchased this can leave a review.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
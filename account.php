<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$user_id = $_SESSION['user_id'] ?? 0;
if($user_id == 0){ header('location:login.php'); exit(); }

// Review එක සේව් කිරීම
if(isset($_POST['submit_review'])){
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_name = $_SESSION['user_name'];

    mysqli_query($conn, "INSERT INTO `message`(user_id, name, message) VALUES('$user_id', '$user_name', 'Order #$order_id Rating: $rating Star - $comment')");
    
    echo "<script>alert('Thank you for your feedback!'); window.location.href='account.php';</script>";
}

// Order එක අයින් කිරීම
if(isset($_GET['remove_order'])){
    $remove_id = mysqli_real_escape_string($conn, $_GET['remove_order']);
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$remove_id' AND user_id = '$user_id'");
    header('location:account.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | Melody Masters Gold</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --gold-primary: #D4AF37; 
            --gold-dark: #B8860B; 
            --dark-card: #1e1e1e;
        }
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        
        /* Sidebar Styling */
        .dashboard-sidebar { 
            background: white; 
            border-radius: 20px; 
            padding: 35px 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.05); 
            border-top: 5px solid var(--gold-primary);
        }
        .user-avatar { 
            width: 90px; height: 90px; 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark)); 
            color: white; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 35px; margin: 0 auto 15px;
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
        }
        
        /* Table Styling */
        .order-card { 
            background: white; border-radius: 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.05); 
            overflow: hidden; border: none; 
        }
        .table thead { background: #fffcf5; }
        .table thead th { 
            color: var(--gold-dark); padding: 20px; 
            font-size: 13px; border: none; text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Buttons & Icons */
        .btn-gold { 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark)); 
            color: white !important; border: none; 
            width: 40px; height: 40px; border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4); }

        .btn-review {
            background: #212529; color: #ffc107 !important;
            border: none; padding: 8px 16px; border-radius: 10px;
            font-weight: 600; font-size: 12px; transition: 0.3s;
            text-decoration: none;
        }
        .btn-review:hover { background: #000; transform: translateY(-2px); }

        .btn-delete {
            color: #ccc; font-size: 18px; transition: 0.3s;
        }
        .btn-delete:hover { color: #dc3545; }

        .status-badge { 
            padding: 6px 14px; border-radius: 30px; 
            font-size: 10px; font-weight: 800; text-transform: uppercase;
        }
        .delivered { background: #e8f5e9; color: #2e7d32; }
        .pending { background: #fff3e0; color: #ef6c00; }

        /* Rating Stars */
        .rating-stars { display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; }
        .rating-stars input { display: none; }
        .rating-stars label { cursor: pointer; font-size: 35px; color: #ddd; transition: 0.2s; }
        .rating-stars input:checked ~ label, .rating-stars label:hover, .rating-stars label:hover ~ label { color: var(--gold-primary); }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="dashboard-sidebar text-center">
                <div class="user-avatar"><i class="fas fa-crown"></i></div>
                <h5 class="fw-bold mb-1"><?php echo $_SESSION['user_name'] ?? 'Guest'; ?></h5>
                <p class="text-muted small mb-4">Gold Member</p>
                <hr class="mb-4">
                <div class="d-grid gap-2">
                    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill py-2">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout Account
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 text-dark">Purchase History</h3>
            </div>
            
            <div class="order-card">
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 text-start">Order Info</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Sheet</th>
                                <th>Feedback</th>
                                <th class="pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $orders_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id' ORDER BY id DESC");
                                if(mysqli_num_rows($orders_query) > 0){
                                    while($fetch_orders = mysqli_fetch_assoc($orders_query)){
                                        $order_id = $fetch_orders['id'];
                                        $status = strtolower($fetch_orders['status'] ?? 'pending');
                                        $prod_str = $fetch_orders['total_products'] ?? '';
                            ?>
                            <tr>
                                <td class="ps-4 text-start py-4">
                                    <span class="fw-bold d-block text-dark">#<?php echo $order_id; ?></span>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($fetch_orders['order_date'])); ?></small>
                                </td>
                                <td><span class="status-badge <?php echo $status; ?>"><?php echo $status; ?></span></td>
                                <td><span class="fw-bold">Rs. <?php echo number_format($fetch_orders['total_amount'], 2); ?></span></td>
                                
                                <td>
                                    <?php 
                                        if(stripos($prod_str, 'Digital') !== false){
                                            $first_item = explode(', ', $prod_str)[0];
                                            $get_pdf = mysqli_query($conn, "SELECT pdf_url FROM `products` WHERE name = '$first_item' LIMIT 1");
                                            if($p = mysqli_fetch_assoc($get_pdf)){
                                                echo '<a href="uploaded_pdf/'.$p['pdf_url'].'" class="btn-gold" download title="Download PDF"><i class="fas fa-download"></i></a>';
                                            }
                                        } else { echo '<i class="fas fa-minus text-muted small"></i>'; }
                                    ?>
                                </td>

                                <td>
                                    <button class="btn-review" onclick="openReviewModal(<?php echo $order_id; ?>)">
                                        <i class="fas fa-star me-1"></i> Review
                                    </button>
                                </td>

                                <td class="pe-4">
                                    <a href="account.php?remove_order=<?php echo $order_id; ?>" class="btn-delete" onclick="return confirm('Delete this record?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="py-5 text-muted">No purchases yet.</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">Share Your Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="order_id" id="modal_order_id">
                    <p class="text-center text-muted mb-4 small">How would you rate Order #<span id="order_text" class="fw-bold text-dark"></span>?</p>
                    
                    <div class="rating-stars mb-4">
                        <input type="radio" name="rating" value="5" id="5"><label for="5">★</label>
                        <input type="radio" name="rating" value="4" id="4"><label for="4">★</label>
                        <input type="radio" name="rating" value="3" id="3" checked><label for="3">★</label>
                        <input type="radio" name="rating" value="2" id="2"><label for="2">★</label>
                        <input type="radio" name="rating" value="1" id="1"><label for="1">★</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Your Feedback</label>
                        <textarea name="comment" class="form-control border-0 bg-light" rows="4" placeholder="How was the music sheet? Was it helpful?" required style="border-radius: 15px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" name="submit_review" class="btn btn-dark w-100 py-3 fw-bold" style="border-radius: 15px; background: #000;">
                        SUBMIT REVIEW
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openReviewModal(orderId) {
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('order_text').innerText = orderId;
        var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        myModal.show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
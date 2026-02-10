<?php
// profile.php - CORRECTED VERSION
session_start();
require_once "includes/db.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user details
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = query($query);
$user = fetch_one($result);

// Update profile
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = escape($_POST['name']);
    $phone = escape($_POST['phone']);
    $address = escape($_POST['address']);
    
    $update_query = "UPDATE users SET name = '$name', phone = '$phone', address = '$address' WHERE user_id = '$user_id'";
    query($update_query);
    
    $_SESSION['username'] = $name;
    $success = "Profile updated successfully";
    
    // Refresh user data
    $result = query($query);
    $user = fetch_one($result);
}

// Change password
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = escape($_POST['current_password']);
    $new_password = escape($_POST['new_password']);
    $confirm_password = escape($_POST['confirm_password']);
    
    // Verify current password
    if($user && isset($user['password'])) {
        if(!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect";
        } elseif($new_password != $confirm_password) {
            $error = "New passwords do not match";
        } elseif(strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = '$user_id'";
            query($update_query);
            $success = "Password changed successfully";
        }
    } else {
        $error = "User not found";
    }
}

// Get user orders
$orders_query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 10";
$orders_result = query($orders_query);
$orders = fetch_all($orders_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h4 class="fw-bold"><?= htmlspecialchars($user['name'] ?? 'User') ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        <p class="mb-0">
                            <span class="badge bg-<?= ($user['role'] ?? 'customer') == 'admin' ? 'danger' : 'primary' ?>">
                                <?= ucfirst($user['role'] ?? 'customer') ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Account Information</h6>
                        <?php if(isset($user['created_at'])): ?>
                        <p class="mb-2"><i class="fas fa-calendar me-2"></i> Member since: 
                            <?= date('M Y', strtotime($user['created_at'])) ?>
                        </p>
                        <?php endif; ?>
                        <?php if(isset($user['phone']) && !empty($user['phone'])): ?>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($user['phone']) ?></p>
                        <?php endif; ?>
                        <?php if(isset($user['address']) && !empty($user['address'])): ?>
                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($user['address']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile">
                            Profile
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#orders">
                            Orders
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#password">
                            Password
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-4">Edit Profile</h5>
                                <form method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="name" class="form-control" 
                                                   value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" 
                                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control" 
                                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary mt-3">
                                        Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="orders">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-4">My Orders</h5>
                                <?php if(empty($orders)): ?>
                                <p class="text-muted">No orders found</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($orders as $order): ?>
                                            <tr>
                                                <td>#<?= $order['order_id'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                                <td>Rs. <?= number_format($order['total_amount'] ?? 0, 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        ($order['status'] ?? 'pending') == 'delivered' ? 'success' : 
                                                        (($order['status'] ?? 'pending') == 'pending' ? 'warning' : 'info')
                                                    ?>">
                                                        <?= ucfirst($order['status'] ?? 'pending') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="order_details.php?id=<?= $order['order_id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="password">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-4">Change Password</h5>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Tab functionality
    const triggerTabList = document.querySelectorAll('button[data-bs-toggle="tab"]')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
    </script>
</body>
</html>
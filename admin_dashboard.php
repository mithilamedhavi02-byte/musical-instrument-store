<?php
include 'config.php';
session_start();
// Dashboard ekata admin witharak yanna denna
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){ header('location:login.php'); exit(); }

// 1. Total Sales
$res = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM `orders` WHERE status = 'Completed'");
$rev = mysqli_fetch_assoc($res);

// 2. Low Stock (Assignment point: Inventory Control)
$low_stock = mysqli_query($conn, "SELECT * FROM `products` WHERE stock_quantity < 5");
?>

<?php include 'admin_header.php'; ?>
<div class="container py-5">
    <h2 class="fw-bold mb-4">Admin Operational Dashboard</h2>
    <div class="row g-4 text-center">
        <div class="col-md-6">
            <div class="card p-4 bg-dark text-white">
                <h6>Total Revenue</h6>
                <h2 class="text-warning">£<?php echo number_format($rev['total'] ?? 0, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 bg-danger text-white">
                <h6>Low Stock Alerts</h6>
                <h2><?php echo mysqli_num_rows($low_stock); ?> Items</h2>
            </div>
        </div>
    </div>
    
    <h4 class="mt-5 fw-bold text-danger">Inventory Alerts (Restock Soon)</h4>
    <table class="table mt-3 bg-white shadow-sm">
        <thead class="table-dark">
            <tr><th>Product</th><th>Stock Left</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($low_stock)){ ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td class="text-danger fw-bold"><?php echo $row['stock_quantity']; ?></td>
                <td><a href="admin_products.php" class="btn btn-sm btn-outline-dark">Update Stock</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
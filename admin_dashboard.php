<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || strtolower(trim($_SESSION['user_role'] ?? '')) !== 'admin'){
    header('location:login.php');
    exit();
}

// 1. Total Sales Calculation
$res = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM `orders`") 
       or die('Query Failed: ' . mysqli_error($conn));
$rev = mysqli_fetch_assoc($res);
$total_revenue = $rev['total'] ?? 0;

// 2. Low Stock Alerts
$low_stock = mysqli_query($conn, "SELECT * FROM `products` WHERE stock_quantity < 5 ORDER BY stock_quantity ASC") 
             or die('Query Failed: ' . mysqli_error($conn));
$low_stock_count = mysqli_num_rows($low_stock);

// 3. Overall Stats
$total_products = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM `products`"));
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM `users` WHERE user_type = 'user'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Elite Dashboard | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
   
   <style>
      :root { 
         --gold: #D4AF37; 
         --dark-gold: #996515;
         --black: #0b0b0b;
         --sidebar-width: 260px; 
      }

      body { 
         background-color: #f8f8f8; 
         font-family: 'Poppins', sans-serif; 
         color: #333; 
         margin: 0;
         overflow-x: hidden;
      }

      /* Sidebar Integration */
      .main-content { 
         margin-left: var(--sidebar-width); 
         width: calc(100% - var(--sidebar-width));
         padding: 0 20px 80px 20px; 
         transition: all 0.3s ease;
         margin-top: -80px; 
      }

      /* Luxury Hero Section */
      .admin-hero {
         background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), 
                     url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1600&q=80');
         background-size: cover; background-position: center;
         padding: 100px 0 140px 0; color: white; text-align: center;
         clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
         width: 100%;
      }
      .admin-hero h1 { 
         font-family: 'Playfair Display', serif; 
         color: var(--gold); 
         font-size: 2.8rem; 
         letter-spacing: 2px;
      }

      /* Stat Cards Styling */
      .luxury-stat-card {
         background: white;
         border: none;
         border-radius: 20px;
         padding: 25px;
         box-shadow: 0 10px 30px rgba(0,0,0,0.05);
         border-bottom: 4px solid var(--gold);
         transition: 0.3s;
         text-align: center;
         height: 100%;
      }
      .luxury-stat-card:hover { transform: translateY(-5px); }
      .luxury-stat-card i { color: var(--gold); margin-bottom: 15px; }
      .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--black); }
      .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: #777; }

      /* Table Styling */
      .inventory-card {
         background: white;
         border-radius: 20px;
         box-shadow: 0 15px 35px rgba(0,0,0,0.05);
         overflow: hidden;
         border: none;
         width: 100%;
      }
      .table thead { background: var(--black); color: var(--gold); }
      .table thead th { padding: 18px; border: none; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
      .table tbody td { padding: 15px; border-bottom: 1px solid #f1f1f1; vertical-align: middle; font-size: 0.9rem; }

      /* Components */
      .btn-gold-action {
         background: linear-gradient(45deg, var(--dark-gold), var(--gold));
         color: white; border: none; padding: 6px 18px; border-radius: 50px;
         font-weight: 600; font-size: 0.8rem; transition: 0.3s; text-decoration: none; display: inline-block;
      }
      .btn-gold-action:hover { box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3); color: #fff; transform: scale(1.05); }

      .stock-badge {
         background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.2);
         padding: 4px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem;
      }

      .section-title {
         font-family: 'Playfair Display', serif; font-size: 1.8rem;
         color: var(--black); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;
      }
      .section-title::after { content: ""; height: 1px; flex-grow: 1; background: #ddd; }

      @media (max-width: 992px) {
         .main-content { margin-left: 0; width: 100%; padding: 0 15px 50px 15px; }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="admin-hero">
    <h1>Operational Dashboard</h1>
    <p class="lead opacity-75">Elite Management Portal for Melody Masters</p>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="luxury-stat-card">
                    <i class="fas fa-crown fa-2x"></i>
                    <div class="stat-value">Rs.<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="luxury-stat-card">
                    <i class="fas fa-exclamation-circle fa-2x" style="color: #e74c3c;"></i>
                    <div class="stat-value text-danger"><?php echo $low_stock_count; ?></div>
                    <div class="stat-label">Low Stock Alerts</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="luxury-stat-card">
                    <i class="fas fa-guitar fa-2x"></i>
                    <div class="stat-value"><?php echo $total_products; ?></div>
                    <div class="stat-label">Total Instruments</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="luxury-stat-card">
                    <i class="fas fa-users fa-2x"></i>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Active Customers</div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Inventory Requiring Attention</h2>
        
        <div class="inventory-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Product Instrument</th>
                            <th>Stock Level</th>
                            <th class="text-center">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($low_stock_count > 0){
                            while($row = mysqli_fetch_assoc($low_stock)){ 
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo $row['name']; ?></div>
                                <small class="text-muted">Masterpiece SKU: #MEL-<?php echo $row['id']; ?></small>
                            </td>
                            <td>
                                <span class="stock-badge">
                                    <i class="fas fa-level-down-alt me-1"></i> Critically Low: <?php echo $row['stock_quantity']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="admin_products.php?update=<?php echo $row['id']; ?>" class="btn-gold-action">
                                    RESTOCK <i class="fas fa-plus-circle ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                            echo "<tr><td colspan='3' class='text-center py-5'>
                                    <i class='fas fa-check-double fa-3x mb-3' style='color: var(--gold); opacity: 0.5;'></i>
                                    <h5 class='text-muted'>The inventory is fully stocked.</h5>
                                  </td></tr>"; 
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// about.php
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         /* White & Gold Theme */
        :root {
            --gold: #D4AF37;
            --dark-gold: #B8860B;
            --black: #0A0A0A;
            --white: #FFFFFF;
            --light-gray: #F9F9F9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--black);
        }
        
        /* Hero Section */
         .navbar {
            background: var(--black) !important;
            border-bottom: 2px solid var(--gold);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: var(--gold) !important;
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
        }
        
        .nav-link {
            color: var(--white) !important;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--gold) !important;
        }
        .about-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1516924962500-2b4b3b99ea02?auto=format&fit=crop&w=1600');
             background-size: cover;
            background-position: center;
            padding: 100px 0 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }
        .mission-card { border-left: 4px solid #d4af37; }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <section class="about-hero d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Our Musical Journey</h1>
            <p class="lead">Serving musicians with passion since 2010</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800" 
                         class="img-fluid rounded shadow" alt="About Us">
                </div>
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">The Music Shop Story</h2>
                    <p class="lead">අපි ලංකාවේ සංගීත ලෝලීන්ට ලෝකයේ ප්‍රමුඛතම සන්නාමයන් සාධාරණ මිලකට ලබා දෙනවා.</p>
                    <p>Founded in 2010, The Music Shop started as a small store in Colombo with a simple mission: to make quality musical instruments accessible to every Sri Lankan musician. Today, we're the largest music retailer in the country.</p>
                    <div class="row mt-4">
                        <div class="col-6">
                            <h3 class="fw-bold text-primary">5000+</h3>
                            <p>Happy Customers</p>
                        </div>
                        <div class="col-6">
                            <h3 class="fw-bold text-primary">100+</h3>
                            <p>Brands Available</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 mission-card p-4">
                        <div class="mb-3">
                            <i class="fas fa-gem fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold">Quality First</h4>
                        <p>Every instrument we sell undergoes strict quality checks. We only stock genuine products from reputable manufacturers.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 mission-card p-4">
                        <div class="mb-3">
                            <i class="fas fa-headphones fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold">Expert Support</h4>
                        <p>Our team includes experienced musicians who provide expert advice and after-sales support.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 mission-card p-4">
                        <div class="mb-3">
                            <i class="fas fa-shipping-fast fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold">Nationwide Delivery</h4>
                        <p>We deliver to every corner of Sri Lanka with safe and reliable shipping partners.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Why Choose Us?</h2>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="p-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <h5>100% Authentic</h5>
                        <p class="small text-muted">All products come with manufacturer warranty</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-3">
                        <i class="fas fa-tools fa-2x text-success mb-3"></i>
                        <h5>Free Setup</h5>
                        <p class="small text-muted">Free instrument setup with every purchase</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-3">
                        <i class="fas fa-undo fa-2x text-success mb-3"></i>
                        <h5>Easy Returns</h5>
                        <p class="small text-muted">7-day return policy for quality issues</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-3">
                        <i class="fas fa-graduation-cap fa-2x text-success mb-3"></i>
                        <h5>Music Lessons</h5>
                        <p class="small text-muted">Free basic lessons with instrument purchase</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
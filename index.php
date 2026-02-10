<?php
// index.php - COMPLETE WHITE & GOLD THEME
session_start();
require_once "includes/db.php";

// Initialize sessions
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Get featured products
$featured_result = query("SELECT p.*, c.category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.category_id 
                         WHERE p.stock > 0 
                         ORDER BY p.created_at DESC LIMIT 6");
$featured_products = $featured_result ? fetch_all($featured_result) : [];

// Get categories
$cat_result = query("SELECT * FROM categories LIMIT 6");
$categories = $cat_result ? fetch_all($cat_result) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Music Shop - Premium Musical Instruments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1600');
            background-size: cover;
            background-position: center;
            color: var(--white);
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            padding: 40px 0;
        }
        
/* ටයිප් වන අකුරු වල Style එක */
.hero-title {
    color: #d4af37; /* සම්පූර්ණ වැකියම Gold color වේ */
    font-weight: 800;
    text-shadow: 0 0 15px rgba(212, 175, 55, 0.3); /* සියුම් දීප්තියක් */
    display: inline-block;
}

/* Gold Cursor එක සඳහා CSS */
.hero-title::after {
    content: '|';
    animation: blink 0.7s infinite;
    margin-left: 5px;
    color: #f4e4b1; /* Cursor එකට ලා රන්වන් පැහැයක් */
    text-shadow: 0 0 10px #d4af37, 0 0 20px #d4af37; /* Cursor එක දිදුලන ලෙස (Glow) */
    font-weight: normal;
}

/* Cursor Blink Animation */
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}  
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            color: var(--white);
            max-width: 600px;
        }
        
        /* Section Titles */
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            color: var(--text-dark);
        }
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, var(--gold), var(--gold-dark));
        }
        .section-title.center {
            text-align: center;
        }
        .section-title.center::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Product Cards */
        .product-card {
            background: var(--white);
            border: 1px solid var(--gray);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            box-shadow: 0 15px 30px rgba(212,175,55,0.15);
        }
        
        .product-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            background: var(--white);
            border: 1px solid var(--gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: var(--text-dark);
            z-index: 10;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .wishlist-btn:hover {
            transform: scale(1.1);
            background: var(--gold-light);
            border-color: var(--gold);
        }
        .wishlist-btn.active {
            background: var(--gold);
            color: var(--white);
            border-color: var(--gold);
        }
        
        /* Category Cards */
        .category-card {
            background: var(--white);
            border: 1px solid var(--gray);
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            text-decoration: none;
            display: block;
            color: var(--text-dark);
        }
        .category-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            box-shadow: 0 15px 30px rgba(212,175,55,0.1);
            text-decoration: none;
            color: var(--text-dark);
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            background: rgba(212,175,55,0.1);
            border: 2px solid rgba(212,175,55,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--gold);
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        .category-card:hover .category-icon {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--white);
        }
        
        /* Buttons */
        .btn-gold {
            background: linear-gradient(to right, var(--gold), var(--gold-dark));
            border: none;
            color: var(--white);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,175,55,0.3);
            color: var(--white);
        }
        
        .btn-outline-gold {
            border: 2px solid var(--gold);
            color: var(--gold);
            background: transparent;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,175,55,0.2);
        }
        
        .btn-outline-light {
            border: 2px solid var(--white);
            color: var(--white);
            background: transparent;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-outline-light:hover {
            background: var(--white);
            color: var(--gold-dark);
            transform: translateY(-2px);
        }
        
        .price-tag {
            background: linear-gradient(to right, var(--gold), var(--gold-dark));
            color: var(--white);
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        /* Feature Boxes */
        .feature-box {
            text-align: center;
            padding: 30px 20px;
            border-radius: 12px;
            background: var(--white);
            border: 1px solid var(--gray);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        .feature-box:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            box-shadow: 0 15px 30px rgba(212,175,55,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--white);
            font-size: 2rem;
            box-shadow: 0 5px 15px rgba(212,175,55,0.3);
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), 
                        url('https://images.unsplash.com/photo-1518609878373-06d740f60d8b?auto=format&fit=crop&w=1600');
            background-size: cover;
            background-position: center;
            color: var(--white);
            padding: 80px 0;
            border-radius: 15px;
            border: 1px solid var(--gold);
        }
        
        /* Footer */
        .footer {
            background: var(--off-white);
            color: var(--text-dark);
            padding: 60px 0 25px;
            margin-top: 50px;
            border-top: 3px solid var(--gold);
        }
        .footer a {
            color: var(--gold-dark);
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }
        .footer a:hover {
            color: var(--gold);
        }
        .footer-heading {
            color: var(--gold-dark);
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        /* Category Badge */
        .category-badge {
            background: rgba(212,175,55,0.1);
            border: 1px solid rgba(212,175,55,0.3);
            color: var(--gold-dark);
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        /* Stock Warning */
        .stock-warning {
            background: rgba(255,193,7,0.1);
            border: 1px solid rgba(255,193,7,0.3);
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.8rem;
            color: #b8941f;
            margin-top: 10px;
            display: inline-block;
        }
        
        /* Button Container */
        .btn-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Text Colors */
        .text-light-emphasis {
            color: var(--text-light) !important;
        }
        
        .text-muted {
            color: var(--text-light) !important;
        }
        
        /* Background Colors */
        .bg-light {
            background: var(--off-white) !important;
            border-top: 1px solid var(--gray);
            border-bottom: 1px solid var(--gray);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                min-height: 70vh;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .btn-gold,
            .btn-outline-gold,
            .btn-outline-light {
                padding: 10px 20px;
                font-size: 1rem;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .product-card,
            .category-card,
            .feature-box {
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php 
    $navbar_style = 'light';
    $navbar_bg = 'light';
    include "includes/navbar.php"; 
    ?>
    
    <!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-content">
                    <h1 class="hero-title" id="typewriter"></h1>
                    <p class="hero-subtitle">Premium instruments, expert services, and endless inspiration for every musician</p>
                    <div class="btn-container">
                        <a href="products.php" class="btn btn-gold">
                            <i class="fas fa-guitar me-2"></i>Shop Now
                        </a>
                        <a href="services.php" class="btn btn-outline-light">
                            <i class="fas fa-concierge-bell me-2"></i>Our Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    
    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 section-title center">Featured Products</h2>
            <div class="row g-4">
                <?php if(!empty($featured_products)): ?>
                    <?php foreach($featured_products as $product): 
                        $is_in_wishlist = in_array($product['product_id'], $_SESSION['wishlist']);
                    ?>
                    <div class="col-md-4 col-lg-2">
                        <div class="product-card">
                            <button class="wishlist-btn <?= $is_in_wishlist ? 'active' : '' ?>" 
                                    onclick="toggleWishlist(<?= $product['product_id'] ?>, this)">
                                <i class="<?= $is_in_wishlist ? 'fas' : 'far' ?> fa-heart"></i>
                            </button>
                            
                            <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://images.unsplash.com/photo-1525201548942-d8732f6617a0?auto=format&fit=crop&w=300') ?>" 
                                 class="product-img" 
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                                </div>
                                <h6 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h6>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <h5 class="fw-bold mb-0" style="color: var(--gold);">
                                        Rs. <?= number_format($product['price'], 2) ?>
                                    </h5>
                                    <button class="btn btn-sm btn-gold" 
                                            onclick="addToCart(<?= $product['product_id'] ?>)">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                                <?php if($product['stock'] < 5): ?>
                                <div class="mt-2">
                                    <small class="stock-warning d-inline-block">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Only <?= $product['stock'] ?> left!
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm py-5">
                        <i class="fas fa-music fa-3x mb-3" style="color: var(--gold);"></i>
                        <p class="text-muted">No featured products available.</p>
                        <a href="products.php" class="btn btn-outline-gold mt-3">Browse Products</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 section-title center">Shop By Category</h2>
            <div class="row g-4">
                <?php if(!empty($categories)): ?>
                    <?php foreach($categories as $category): ?>
                    <div class="col-md-4 col-lg-2">
                        <a href="products.php?category=<?= $category['category_id'] ?>" class="category-card">
                            <div class="category-icon">
                                <?php 
                                $icons = [
                                    'Guitars' => 'fa-guitar',
                                    'Pianos' => 'fa-piano',
                                    'Drums' => 'fa-drum',
                                    'Violins' => 'fa-violin',
                                    'Flutes' => 'fa-flute',
                                    'Amps' => 'fa-volume-up',
                                    'Keyboards' => 'fa-keyboard',
                                    'Synthesizers' => 'fa-sliders-h',
                                    'Wind Instruments' => 'fa-music',
                                    'String Instruments' => 'fa-guitar'
                                ];
                                $icon = $icons[$category['category_name']] ?? 'fa-music';
                                ?>
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <h5 class="fw-bold mb-0"><?= htmlspecialchars($category['category_name']) ?></h5>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No categories available</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Why Choose Us -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 section-title center">Why Choose The Music Shop?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">100% Genuine</h4>
                        <p class="text-muted">All products are authentic with manufacturer warranty and certification</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Islandwide Delivery</h4>
                        <p class="text-muted">Fast, reliable, and insured delivery across Sri Lanka with real-time tracking</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Expert Support</h4>
                        <p class="text-muted">24/7 customer support, expert advice, and after-sales service</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Call to Action -->
    <section class="py-5">
        <div class="container">
            <div class="cta-section text-center rounded">
                <h2 class="fw-bold mb-4">Ready to Start Your Musical Journey?</h2>
                <p class="lead mb-5 text-light-emphasis">Join thousands of satisfied musicians who trust The Music Shop</p>
                <div class="btn-container justify-content-center">
                    <a href="products.php" class="btn btn-gold">
                        <i class="fas fa-shopping-cart me-2"></i>Shop Now
                    </a>
                    <a href="services.php" class="btn btn-outline-light">
                        <i class="fas fa-calendar-check me-2"></i>Book Service
                    </a>
                    <a href="contact.php" class="btn btn-outline-light">
                        <i class="fas fa-phone-alt me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-heading">The Music Shop</h5>
                    <p class="text-light-emphasis">Your premier destination for musical instruments, services, and education in Sri Lanka.</p>
                    <div class="mt-3">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-youtube fa-lg"></i></a>
                        <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="products.php">Products</a></li>
                        <li class="mb-2"><a href="services.php">Services</a></li>
                        <li class="mb-2"><a href="about.php">About Us</a></li>
                        <li class="mb-2"><a href="contact.php">Contact</a></li>
                        <li><a href="login.php">My Account</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-heading">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Music Street, Colombo</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+94 11 234 5678</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@themusicshop.lk</li>
                        <li><i class="fas fa-clock me-2"></i>Mon-Sat: 9AM - 7PM</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: var(--gray);">
            <div class="text-center">
                <p class="mb-0 text-light-emphasis">&copy; <?= date('Y') ?> The Music Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    function addToCart(productId) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', 'add');
        
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNotification(data.message, 'success');
                const cartBadge = document.querySelector('a[href="cart.php"] .badge');
                if(cartBadge) {
                    cartBadge.textContent = data.cart_count || 0;
                }
            } else {
                if(data.requires_login) {
                    if(confirm('You need to login first. Do you want to login now?')) {
                        window.location.href = data.redirect;
                    }
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred.', 'error');
        });
    }
    
    function toggleWishlist(productId, button) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', 'toggle');
        
        fetch('add_to_wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                if(data.added) {
                    button.classList.add('active');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                    showNotification('Added to wishlist!', 'success');
                } else {
                    button.classList.remove('active');
                    button.innerHTML = '<i class="far fa-heart"></i>';
                    showNotification('Removed from wishlist', 'info');
                }
                const wishlistBadge = document.querySelector('a[href="wishlist.php"] .badge');
                if(wishlistBadge) {
                    wishlistBadge.textContent = data.wishlist_count || 0;
                }
            } else {
                if(data.requires_login) {
                    if(confirm('You need to login first. Do you want to login now?')) {
                        window.location.href = data.redirect;
                    }
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred.', 'error');
        });
    }
    
    function showNotification(message, type) {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
        notification.style.zIndex = '1050';
        notification.innerHTML = `
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            ${message}
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Add animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);
        
        // Observe all product cards and feature boxes
        document.querySelectorAll('.product-card, .feature-box, .category-card').forEach(element => {
            observer.observe(element);
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
    const text = "Unleash Your Musical Potential";
    const speed = 100; // අකුරක් ලිවීමට ගතවන කාලය (milliseconds)
    let i = 0;
    const element = document.getElementById("typewriter");

    function typeWriter() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }

    // Animation එක ආරම්භ කිරීම
    typeWriter();
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
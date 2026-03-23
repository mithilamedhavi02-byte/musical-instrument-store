<?php
// services.php
session_start();
require_once "includes/db.php";

// Initialize sessions if not set
if (!isset($_SESSION['cart'])) { 
    $_SESSION['cart'] = []; 
}
if (!isset($_SESSION['wishlist'])) { 
    $_SESSION['wishlist'] = []; 
}

// Calculate cart total items
$cart_total_items = array_sum($_SESSION['cart']);
$wishlist_count = count($_SESSION['wishlist']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
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

        /* --- Header: Black & Gold --- */


        
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
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }

        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            letter-spacing: 2px;
        }






        /* --- Service Cards --- */
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 40px;
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            text-align: center;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 100px;
            height: 3px;
            background: var(--gold);
        }

        .service-card {
            border: 1px solid rgba(212, 175, 55, 0.2);
            background: var(--white);
            border-radius: 0;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .service-card:hover {
            border-color: var(--gold);
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: var(--black);
            color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 20px;
            border: 1px solid var(--gold);
            transition: all 0.3s;
        }

        .service-card:hover .service-icon {
            background: var(--gold);
            color: var(--black);
        }

        /* --- Buttons --- */
        .btn-gold {
            background: var(--black);
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 0;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
            padding: 12px 24px;
        }

        .btn-gold:hover {
            background: var(--gold);
            color: var(--black);
        }

        .btn-outline-gold {
            background: transparent;
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 0;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
            padding: 12px 24px;
        }
        
        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--black);
        }

        /* --- Feature Box --- */
        .feature-box {
            border: 1px solid rgba(212, 175, 55, 0.2);
            background: var(--white);
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }

        .feature-box:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            background: var(--black);
            color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            border: 1px solid var(--gold);
            transition: all 0.3s;
        }

        .feature-box:hover .benefit-icon {
            background: var(--gold);
            color: var(--black);
        }

        /* --- List Items --- */
        .list-item {
            padding-left: 25px;
            position: relative;
            margin-bottom: 10px;
            color: var(--black);
        }
        
        .list-item::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--gold);
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* --- Footer --- */
        .footer {
            background: var(--black);
            color: var(--white);
            padding: 50px 0 20px;
            border-top: 2px solid var(--gold);
        }
        
        .footer a {
            color: var(--gold);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer a:hover {
            color: var(--white);
        }
        
        .footer-heading {
            color: var(--gold);
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }
        
        /* --- Badges --- */
        .cart-badge, .wishlist-badge {
            background-color: var(--gold) !important;
            color: var(--black) !important;
            font-size: 0.7rem;
            padding: 3px 6px;
            margin-left: 3px;
        }
        
        /* --- Pricing Card --- */
        .price-tag {
            color: var(--dark-gold);
            font-weight: 700;
            font-size: 1.25rem;
            display: inline-block;
            padding: 5px 15px;
            border: 1px solid var(--gold);
            background: var(--black);
            color: var(--gold);
            text-align: center;
        }
        
        .pricing-card {
            text-align: center;
            padding: 15px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.3s;
            height: 100%;
        }
        
        .pricing-card:hover {
            border-color: var(--gold);
        }
        
        /* --- Responsive --- */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
        
        /* --- Toast notifications --- */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .custom-toast {
            background: var(--black);
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 0;
            min-width: 300px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .custom-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* --- Loading overlay --- */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--gold);
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* --- Form styling --- */
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        /* --- Check list items --- */
        .check-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .check-icon {
            width: 40px;
            height: 40px;
            background: var(--black);
            color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 15px;
            border: 1px solid var(--gold);
        }
        
        .check-content h5 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="mb-3">Premium Music Services</h1>
            <p class="text-uppercase tracking-widest" style="letter-spacing: 3px;">Beyond Instruments - Your Complete Musical Partner</p>
        </div>
    </header>

    <!-- Main Services -->
    <main class="container py-5">
        <h2 class="section-title">Our Core Services</h2>
        
        <div class="row g-4 mb-5">
            <!-- Repair Service -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="card-body p-4">
                        <div class="service-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-center">Instrument Repair</h4>
                        <p class="text-muted mb-4 text-center">Professional repair services for all types of musical instruments.</p>
                        <ul class="list-unstyled">
                            <li class="list-item">Guitar setup & restringing</li>
                            <li class="list-item">Piano tuning & repair</li>
                            <li class="list-item">Amplifier servicing</li>
                            <li class="list-item">Wind instrument overhaul</li>
                        </ul>
                        <a href="contact.php?service=repair" class="btn btn-gold w-100 mt-3">
                            <i class="fas fa-calendar-check me-2"></i>Book Service
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Music Academy -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="card-body p-4">
                        <div class="service-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-center">Music Academy</h4>
                        <p class="text-muted mb-4 text-center">Learn to play with certified instructors.</p>
                        <ul class="list-unstyled">
                            <li class="list-item">Guitar lessons (all levels)</li>
                            <li class="list-item">Piano/keyboard classes</li>
                            <li class="list-item">Drumming workshops</li>
                            <li class="list-item">Vocal training & theory</li>
                        </ul>
                        <a href="contact.php?service=academy" class="btn btn-gold w-100 mt-3">
                            <i class="fas fa-user-graduate me-2"></i>Enroll Now
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Service -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="card-body p-4">
                        <div class="service-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-center">Islandwide Delivery</h4>
                        <p class="text-muted mb-4 text-center">Safe delivery across Sri Lanka.</p>
                        <ul class="list-unstyled">
                            <li class="list-item">Free delivery over Rs. 5000</li>
                            <li class="list-item">Same-day delivery in Colombo</li>
                            <li class="list-item">Secure premium packaging</li>
                            <li class="list-item">Installation service available</li>
                        </ul>
                        <a href="products.php" class="btn btn-gold w-100 mt-3">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rental Services -->
        <section class="py-5">
            <h2 class="section-title">Rental Services</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="service-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="benefit-icon me-3">
                                    <i class="fas fa-guitar"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Instrument Rental</h4>
                            </div>
                            <p class="text-muted">Need an instrument for a short period? We offer affordable rental options.</p>
                            <div class="row mt-4">
                                <div class="col-6 mb-3">
                                    <div class="pricing-card">
                                        <h5 class="fw-bold mb-2 price-tag">Rs. 1500</h5>
                                        <small class="text-muted">/week</small>
                                        <p class="small mb-0 mt-2">Acoustic Guitar</p>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="pricing-card">
                                        <h5 class="fw-bold mb-2 price-tag">Rs. 2500</h5>
                                        <small class="text-muted">/week</small>
                                        <p class="small mb-0 mt-2">Digital Keyboard</p>
                                    </div>
                                </div>
                            </div>
                            <a href="contact.php?service=rental" class="btn btn-gold w-100 mt-2">
                                <i class="fas fa-calendar-alt me-2"></i>Check Availability
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="service-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="benefit-icon me-3">
                                    <i class="fas fa-volume-up"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Sound Equipment Rental</h4>
                            </div>
                            <p class="text-muted">For events, concerts, or recording sessions.</p>
                            <div class="row mt-4">
                                <div class="col-6 mb-3">
                                    <div class="pricing-card">
                                        <h5 class="fw-bold mb-2 price-tag">Rs. 5000</h5>
                                        <small class="text-muted">/day</small>
                                        <p class="small mb-0 mt-2">PA System</p>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="pricing-card">
                                        <h5 class="fw-bold mb-2 price-tag">Rs. 3000</h5>
                                        <small class="text-muted">/day</small>
                                        <p class="small mb-0 mt-2">Drum Kit</p>
                                    </div>
                                </div>
                            </div>
                            <a href="contact.php?service=sound-rental" class="btn btn-gold w-100 mt-2">
                                <i class="fas fa-headphones me-2"></i>View Equipment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Custom Setup -->
        <section class="py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title">Custom Instrument Setup</h2>
                    <p class="mb-4">We offer custom instrument setup services to match your playing style:</p>
                    <div class="mb-4">
                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="check-content">
                                <h5 class="fw-bold mb-1">Action Adjustment</h5>
                                <p class="text-muted mb-0">Perfect string height and tension</p>
                            </div>
                        </div>
                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="check-content">
                                <h5 class="fw-bold mb-1">Intonation Setup</h5>
                                <p class="text-muted mb-0">Precision tuning across all frets</p>
                            </div>
                        </div>
                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="check-content">
                                <h5 class="fw-bold mb-1">Custom Electronics</h5>
                                <p class="text-muted mb-0">Pickup installation and wiring</p>
                            </div>
                        </div>
                    </div>
                    <a href="contact.php?service=custom" class="btn btn-gold px-4 py-3">
                        <i class="fas fa-quote-right me-2"></i>Get a Quote
                    </a>
                </div>
                <div class="col-lg-6">
                    <div style="border: 1px solid var(--gold); padding: 5px;">
                        <img src="https://images.unsplash.com/photo-1564182842519-8a3b2d945c11?auto=format&fit=crop&w=800" 
                             class="img-fluid w-100" 
                             alt="Custom Instrument Setup">
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Benefits -->
        <section class="py-5">
            <h2 class="section-title">Why Choose Our Services?</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="feature-box">
                        <div class="benefit-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Certified Experts</h5>
                        <p class="text-muted small">Certified by instrument manufacturers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Warranty Protected</h5>
                        <p class="text-muted small">90-day warranty on all services</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Fast Turnaround</h5>
                        <p class="text-muted small">Most repairs in 2-3 business days</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <div class="benefit-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Price Guarantee</h5>
                        <p class="text-muted small">Competitive pricing, no hidden costs</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-heading">The Music Shop</h5>
                    <p class="text-light">Your premier destination for musical instruments, services, and education.</p>
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
            <hr class="my-4" style="border-color: #333;">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 The Music Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Add animation to cards on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.service-card, .feature-box, .pricing-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    });
    </script>
</body>
</html>
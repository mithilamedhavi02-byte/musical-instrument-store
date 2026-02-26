<?php 
include 'config.php';
include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    /* Products page ekata match wena widihata hadapu Hero Section */
    .about-hero {
        background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                    url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 120px 0;
        position: relative;
        overflow: hidden;
    }
    
    /* Music notes floating effect from products page */
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 50 L30 40 L40 50 L50 30 L60 50 L70 35 L80 50" stroke="%23ffc107" fill="none" stroke-width="2"/><circle cx="25" cy="60" r="3" fill="%23ffc107"/><circle cx="45" cy="60" r="3" fill="%23ffc107"/><circle cx="65" cy="60" r="3" fill="%23ffc107"/></svg>');
        background-size: 200px 200px;
        animation: floatNotes 20s linear infinite;
    }
    
    @keyframes floatNotes {
        from { transform: translateY(0) rotate(0deg); }
        to { transform: translateY(-100%) rotate(10deg); }
    }
    
    .about-hero h1 {
        font-size: 4rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #ffc107;
        animation: fadeInScale 1s ease-out;
    }

    /* Glassmorphism Cards */
    .info-card {
        background: white;
        border-radius: 20px;
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid #ffc107;
    }
    
    .info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 30px rgba(0,0,0,0.15) !important;
    }

    .brand-badge {
        background: #1a1a1a;
        color: #ffc107;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 15px;
    }

    .title-underline {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #ffc107, #ff9800);
        margin-bottom: 20px;
        border-radius: 2px;
    }

    /* Stats bar matching category filter style */
    .stats-bar {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-top: 2px solid #ffc107;
        border-bottom: 2px solid #ffc107;
        padding: 40px 0;
        color: white;
    }

    .stat-item h2 {
        color: #ffc107;
        font-weight: 800;
        font-size: 2.5rem;
        margin-bottom: 0;
    }

    .stat-item p {
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .about-img {
        border-radius: 20px;
        box-shadow: 20px 20px 0px rgba(255, 193, 7, 0.2);
        transition: 0.5s ease;
    }
    
    .about-img:hover {
        box-shadow: 10px 10px 0px #ffc107;
        transform: scale(1.02);
    }
</style>

<div class="about-hero text-center text-white">
    <div class="container position-relative">
        <h1 class="display-3 fw-bold">Who We Are</h1>
        <p class="lead">Everything you need to know about Melody Masters</p>
    </div>
</div>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6" data-aos="fade-right">
            <span class="brand-badge text-uppercase">Since 2026</span>
            <h2 class="fw-bold text-dark">Our Musical Mission</h2>
            <div class="title-underline"></div>
            <p class="text-muted fs-5">Melody Masters is more than just an instrument store. We are a community of music lovers dedicated to providing the world's finest musical tools.</p>
            <p class="text-muted">Just like our vast collection of <a href="products.php" class="text-warning fw-bold text-decoration-none">Instruments</a>, our story is built on quality and passion. We ensure that every note you play sounds perfect by providing top-tier brands like Yamaha, Fender, and Casio.</p>
            
            <a href="products.php" class="btn btn-warning px-4 py-2 rounded-pill fw-bold shadow-sm mt-3">
                <i class="fas fa-shopping-bag me-2"></i>Browse Collection
            </a>
        </div>
        <div class="col-md-6 mt-5 mt-md-0" data-aos="zoom-in">
            <img src="https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?auto=format&fit=crop&w=800&q=80" alt="About Us" class="img-fluid about-img">
        </div>
    </div>
</div>

<div class="stats-bar shadow-lg">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="100">
                <h2>100+</h2>
                <p>Instruments</p>
            </div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="200">
                <h2>15k+</h2>
                <p>Happy Clients</p>
            </div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="300">
                <h2>10+</h2>
                <p>Years Experience</p>
            </div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="400">
                <h2>24/7</h2>
                <p>Expert Support</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mt-5">
    <div class="row g-4 text-center">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="info-card p-5 shadow-sm h-100">
                <i class="fas fa-eye fa-3x text-warning mb-4"></i>
                <h4 class="fw-bold">Our Vision</h4>
                <p class="text-muted">To be the global heartbeat of musical innovation and accessibility for every artist.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="info-card p-5 shadow-sm h-100">
                <i class="fas fa-bullseye fa-3x text-warning mb-4"></i>
                <h4 class="fw-bold">Our Mission</h4>
                <p class="text-muted">Empowering musicians by providing premium instruments at unbeatable value through a seamless experience.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="info-card p-5 shadow-sm h-100">
                <i class="fas fa-heart fa-3x text-warning mb-4"></i>
                <h4 class="fw-bold">Our Passion</h4>
                <p class="text-muted">We believe music changes lives, and we're here to provide the perfect tools for that change.</p>
            </div>
        </div>
    </div>
</div>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>

<?php include 'footer.php'; ?>
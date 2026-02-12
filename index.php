<?php include 'header.php'; ?>

<style>
    /* Hero Section lassanata hadanna */
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                    url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }
    .hero-content h1 {
        font-size: 4rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .feature-card {
        border: none;
        transition: 0.3s;
        padding: 30px;
        border-radius: 15px;
    }
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .feature-icon {
        font-size: 3rem;
        color: #ffc107;
        margin-bottom: 20px;
    }
</style>

<div class="hero-section shadow-lg">
    <div class="hero-content container">
        <h1 class="text-warning">Melody Masters</h1>
        <p class="fs-4 mb-4">Unleash your inner musician with premium instruments & digital sheet music.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="products.php" class="btn btn-warning btn-lg px-5 fw-bold me-sm-3">Start Shopping</a>
            <a href="about.php" class="btn btn-outline-light btn-lg px-5">Learn More</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Why Music Professionals Choose Us</h2>
        <div class="bg-warning mx-auto" style="width: 80px; height: 4px; border-radius: 2px;"></div>
    </div>
    
    <div class="row g-4 text-center mt-2">
        <div class="col-md-4">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-guitar feature-icon"></i>
                <h4 class="fw-bold">Premium Brands</h4>
                <p class="text-muted">Directly imported from the world's most trusted musical manufacturers.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-truck-fast feature-icon"></i>
                <h4 class="fw-bold">Secure Shipping</h4>
                <p class="text-muted">Each instrument is carefully inspected and double-packaged before delivery.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-file-audio feature-icon"></i>
                <h4 class="fw-bold">Digital Library</h4>
                <p class="text-muted">Access thousands of digital sheet music titles instantly after purchase.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-dark text-white py-5 mt-5">
    <div class="container text-center">
        <h2 class="mb-4">Ready to start your musical journey?</h2>
        <a href="register.php" class="btn btn-warning px-5 fw-bold">Join Our Community</a>
    </div>
</div>

<?php include 'footer.php'; ?>
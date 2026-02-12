<?php 
include 'config.php';
include 'header.php'; 
?>

<style>
    .about-hero {
        background: linear-gradient(45deg, #1a1a1a, #333);
        padding: 80px 0;
        margin-bottom: 50px;
    }
    .stats-card {
        border-radius: 15px;
        transition: 0.3s;
        border: none;
        background: #f8f9fa;
    }
    .stats-card:hover {
        background: #ffc107;
        color: #000;
        transform: translateY(-5px);
    }
    .about-img {
        border-radius: 20px;
        box-shadow: 20px 20px 0px #ffc107;
        max-width: 100%;
        height: auto;
    }
</style>

<div class="about-hero text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Musical Journey</h1>
        <p class="lead">Crafting excellence in every note since 2026.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <h2 class="fw-bold text-dark mb-4">Who We Are</h2>
            <div class="bg-warning mb-4" style="width: 60px; height: 5px;"></div>
            <p class="text-muted fs-5">Melody Masters is more than just an instrument store. We are a community of music lovers dedicated to providing the world's finest musical tools to aspiring artists.</p>
            <p class="text-muted">Our store features top brands like **Yamaha, Fender, and Casio**, ensuring quality in every note you play. Whether you're looking for your first guitar or a professional digital piano, we have you covered.</p>
            
            <div class="row mt-4 g-3">
                <div class="col-6 col-md-4 text-center">
                    <div class="p-3 stats-card shadow-sm">
                        <h3 class="fw-bold mb-0">500+</h3>
                        <small>Products</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 text-center">
                    <div class="p-3 stats-card shadow-sm">
                        <h3 class="fw-bold mb-0">1k+</h3>
                        <small>Students</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="https://images.unsplash.com/photo-1520529611124-84ff1ff5010e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="About Melody Masters" class="about-img mt-4 mt-md-0">
        </div>
    </div>

    <div class="row mt-5 text-center g-4">
        <div class="col-md-4">
            <div class="p-4 border-0 card shadow-sm h-100">
                <i class="fa-solid fa-eye fa-3x text-warning mb-3"></i>
                <h4 class="fw-bold">Our Vision</h4>
                <p class="text-muted">To be the global heartbeat of musical innovation and accessibility.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border-0 card shadow-sm h-100">
                <i class="fa-solid fa-bullseye fa-3x text-warning mb-3"></i>
                <h4 class="fw-bold">Our Mission</h4>
                <p class="text-muted">Empowering musicians by providing premium instruments at unbeatable value.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border-0 card shadow-sm h-100">
                <i class="fa-solid fa-heart fa-3x text-warning mb-3"></i>
                <h4 class="fw-bold">Our Passion</h4>
                <p class="text-muted">We believe music changes lives, and we're here to provide the tools for that change.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
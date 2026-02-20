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
        padding: 0 15px; /* Mobile devices wala margin ekak thiyaganna */
    }
    
    /* Melody Masters akurin akura display wena animation */
    .animated-text {
        font-size: 4rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: inline-block;
    }
    
    .animated-text span {
        display: inline-block;
        opacity: 0;
        transform: translateY(50px) rotate(10deg);
        animation: letterReveal 0.6s forwards;
        color: #ffc107;
        text-shadow: 0 0 20px rgba(255, 193, 7, 0.5);
    }
    
    @keyframes letterReveal {
        0% { opacity: 0; transform: translateY(50px) rotate(10deg); }
        50% { opacity: 0.8; transform: translateY(-10px) rotate(-2deg); }
        100% { opacity: 1; transform: translateY(0) rotate(0); }
    }
    
    .animated-text .space { width: 10px; display: inline-block; }
    
    .hero-content p { opacity: 0; animation: fadeInText 1s ease-out 2s forwards; }
    .hero-content div { opacity: 0; animation: fadeInText 1s ease-out 2.5s forwards; }
    
    @keyframes fadeInText {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Feature card enhancements */
    .feature-card {
        border: none;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        padding: 30px;
        border-radius: 15px;
    }
    .feature-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .feature-icon {
        font-size: 3rem;
        color: #ffc107;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    
    /* Responsive Media Queries (Mobile Optimization) */
    @media (max-width: 768px) {
        .animated-text {
            font-size: 2.5rem; /* Mobile wala akuru podi kala */
        }
        .hero-section {
            height: 70vh; /* Mobile wala hero section height eka adu kala */
        }
        .hero-content p {
            font-size: 1.1rem !important; /* Mobile wala text eka resize kala */
        }
        .feature-card {
            margin-bottom: 20px; /* Cards athara gap eka damma */
        }
        .display-4 {
            font-size: 2.2rem;
        }
    }

    @media (max-width: 576px) {
        .animated-text {
            font-size: 1.8rem; /* Godak podi phone walata galapenna */
        }
        .btn-lg {
            width: 100%; /* Buttons full width kala */
            margin-bottom: 10px;
        }
    }
</style>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<div class="hero-section shadow-lg">
    <div class="hero-content container">
        <h1 class="animated-text" id="melodyMastersText"></h1>
        <p class="fs-4 mb-4">Unleash your inner musician with premium instruments & digital sheet music.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="products.php" class="btn btn-warning btn-lg px-4 fw-bold">Start Shopping</a>
            <a href="about.php" class="btn btn-outline-light btn-lg px-4">Learn More</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold" data-aos="fade-right">Why Music Professionals Choose Us</h2>
        <div class="bg-warning mx-auto" style="width: 80px; height: 4px; border-radius: 2px;" data-aos="zoom-in" data-aos-delay="100"></div>
    </div>
    
    <div class="row g-4 text-center mt-2">
        <div class="col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="50">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-guitar feature-icon"></i>
                <h4 class="fw-bold">Premium Brands</h4>
                <p class="text-muted">Directly imported from the world's most trusted musical manufacturers.</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6" data-aos="flip-up" data-aos-delay="150">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-truck-fast feature-icon"></i>
                <h4 class="fw-bold">Secure Shipping</h4>
                <p class="text-muted">Each instrument is carefully inspected and double-packaged before delivery.</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-12" data-aos="flip-right" data-aos-delay="250">
            <div class="card feature-card bg-light">
                <i class="fa-solid fa-file-audio feature-icon"></i>
                <h4 class="fw-bold">Digital Library</h4>
                <p class="text-muted">Access thousands of digital sheet music titles instantly after purchase.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-dark text-white py-5 mt-5 cta-section">
    <div class="container text-center px-4" data-aos="zoom-in-up">
        <h2 class="mb-4">Ready to start your musical journey?</h2>
        <a href="register.php" class="btn btn-warning btn-lg px-5 fw-bold w-xs-100">Join Our Community</a>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: true, offset: 100 });
  
  document.addEventListener('DOMContentLoaded', function() {
      const text = "Melody Masters";
      const container = document.getElementById('melodyMastersText');
      for(let i = 0; i < text.length; i++) {
          const span = document.createElement('span');
          if(text[i] === ' ') {
              span.innerHTML = '&nbsp;';
              span.classList.add('space');
          } else {
              span.textContent = text[i];
          }
          span.style.animationDelay = (i * 0.1) + 's';
          container.appendChild(span);
      }
  });
</script>

<?php include 'footer.php'; ?>
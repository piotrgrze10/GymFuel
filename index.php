<?php
require_once 'includes/config.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="preload" as="image" href="./img/diet-695723_640.jpg">
    <link rel="preload" as="image" href="./img/diet-695723_1920.jpg" media="(min-width: 1200px)">
    <link rel="preload" as="image" href="./img/card1.jpg">
    <link rel="preload" as="image" href="./img/card2.jpeg">
    <link rel="preload" as="image" href="./img/card3.jpg">
    <link rel="preload" as="image" href="./img/card4.jpg">
    <link rel="preload" as="image" href="./img/card5.jpg">
    <link rel="preload" as="image" href="./img/person1.jpg">
    <link rel="preload" as="image" href="./img/person2.jpg">
    <link rel="preload" as="image" href="./img/person3.jpg">
    <link rel="stylesheet" href="./css/navbar.css?v=ULTRAFIX">
    <link rel="stylesheet" href="./css/main.css?v=MOBILE_VISIBLE_TEXT">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#aboutus">About us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Offer</a></li>
                    <li class="nav-item"><a class="nav-link" href="#achievements">Achievements</a></li>
                    <li class="nav-item"><a class="nav-link" href="./search_products.php">Search products</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </nav>
    <header class="hero-img" id="home">
        <div class="hero-text p-2">
            <h1 class="hero-title">Welcome to <br>Gym <span class="blue-text">Fuel</span> Junction!</h1>
            <p>In a space where fitness dreams begin</p>
            <a href="#aboutus" class="btn btn-outline-light mt-2 text-uppercase">explore us</a>
        </div>
        <div class="hero-shadow"></div>

        <a href="#aboutus"><i class="fa-solid fa-chevron-down"></i></a>
    </header>
    <main>
        <section id="aboutus" class="aboutus-new py-5 bg-white">
            <div class="container">
                <h2 class="section-title">About GymFuel</h2>
                <div class="underline"></div>
                
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h3 class="mb-3">Transform Your Nutrition Journey</h3>
                        <p class="lead mb-4">Your ultimate companion for achieving fitness goals through intelligent nutrition tracking.</p>
                        <p class="mb-4">Whether you're looking to lose weight, gain muscle, or maintain a healthy lifestyle, we provide the tools and insights you need to succeed.</p>
                        
                        <div class="mb-3">
                            <i class="fa-solid fa-check-circle text-primary me-2"></i>
                            <span>Track calories with 14+ million food products</span>
                        </div>
                        <div class="mb-3">
                            <i class="fa-solid fa-check-circle text-primary me-2"></i>
                            <span>Monitor macros and nutrients effortlessly</span>
                        </div>
                        <div class="mb-3">
                            <i class="fa-solid fa-check-circle text-primary me-2"></i>
                            <span>Visualize your progress with real-time analytics</span>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="row text-center g-4">
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fa-solid fa-users fa-3x mb-2 text-primary"></i>
                                    <h4 class="mb-0">50K+</h4>
                                    <p class="text-muted small mb-0">Users</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fa-solid fa-fire-flame-curved fa-3x mb-2 text-danger"></i>
                                    <h4 class="mb-0">2M+</h4>
                                    <p class="text-muted small mb-0">Calories</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fa-solid fa-weight-scale fa-3x mb-2 text-success"></i>
                                    <h4 class="mb-0">500K</h4>
                                    <p class="text-muted small mb-0">KG Lost</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fa-solid fa-star fa-3x mb-2 text-warning"></i>
                                    <h4 class="mb-0">4.9/5</h4>
                                    <p class="text-muted small mb-0">Rating</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="overlapping-cards py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="cards-wrapper">
                            <div class="overlap-card">
                                <div class="card-frame">
                                    <img src="./img/card1.jpg" alt="Reach & Maintain Your Goal Weight" class="card-img" loading="eager" fetchpriority="high" decoding="sync">
                                    <h4 class="card-caption">Reach & Maintain Your Goal Weight</h4>
                                </div>
                            </div>

                            <div class="overlap-card">
                                <div class="card-frame">
                                    <img src="./img/card2.jpeg" alt="Track Your Progress" class="card-img" loading="eager" fetchpriority="high" decoding="sync">
                                    <h4 class="card-caption">Track Your Progress</h4>
                                </div>
                            </div>

                            <div class="overlap-card">
                                <div class="card-frame">
                                    <img src="./img/card3.jpg" alt="Build Healthy Habits" class="card-img" loading="eager" fetchpriority="high" decoding="sync">
                                    <h4 class="card-caption">Build Healthy Habits</h4>
                                </div>
                            </div>

                            <div class="overlap-card">
                                <div class="card-frame">
                                    <img src="./img/card4.jpg" alt="Monitor Nutrients" class="card-img" loading="eager" fetchpriority="high" decoding="sync">
                                    <h4 class="card-caption">Monitor Nutrients</h4>
                                </div>
                            </div>

                            <div class="overlap-card">
                                <div class="card-frame">
                                    <img src="./img/card5.jpg" alt="Achieve Your Goals" class="card-img" loading="eager" fetchpriority="high" decoding="sync">
                                    <h4 class="card-caption">Achieve Your Goals</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features py-5 bg-light">
            <div class="container">
                <h2 class="section-title">What We Offer</h2>
                <div class="underline"></div>
                <p class="text-center mb-5 text-muted">Everything you need for successful nutrition tracking</p>
                
                <div class="row g-4">
                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card p-4 bg-white rounded shadow-sm h-100">
                            <div class="feature-offer-icon mb-3">
                                <i class="fa-solid fa-bullseye fa-3x text-primary"></i>
                            </div>
                            <h4 class="mb-3">Suggested Calorie Goals</h4>
                            <p class="text-muted mb-0">Let our intelligent system calculate your optimal daily calorie intake based on your age, weight, height, activity level, and fitness objectives.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card p-4 bg-white rounded shadow-sm h-100">
                            <div class="feature-offer-icon mb-3">
                                <i class="fa-solid fa-sliders fa-3x text-primary"></i>
                            </div>
                            <h4 class="mb-3">Custom Calorie Goals</h4>
                            <p class="text-muted mb-0">Set your own personalized calorie targets. Have complete control over your daily intake limits to match your unique preferences and goals.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card p-4 bg-white rounded shadow-sm h-100">
                            <div class="feature-offer-icon mb-3">
                                <i class="fa-solid fa-utensils fa-3x text-primary"></i>
                            </div>
                            <h4 class="mb-3">Flexible Meal Structure</h4>
                            <p class="text-muted mb-0">Log your meals however you want! Track breakfast, lunch, dinner, and snacks with complete freedom to organize your nutrition your way.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card p-4 bg-white rounded shadow-sm h-100">
                            <div class="feature-offer-icon mb-3">
                                <i class="fa-solid fa-droplet fa-3x text-primary"></i>
                            </div>
                            <h4 class="mb-3">Water Tracking</h4>
                            <p class="text-muted mb-0">Monitor your daily hydration with our water tracking feature. Stay on top of your fluid intake to maintain optimal health and performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="achievements" class="success-stories-new py-5 bg-white">
            <div class="container">
                <h2 class="section-title">Success Stories</h2>
                <div class="underline"></div>
                <p class="text-center mb-5 text-muted">Real people, real results</p>
                
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="testimonial-card text-center p-4 bg-light rounded">
                            <div class="testimonial-image mb-3">
                                <img src="./img/person1.jpg" alt="Sarah Mitchell" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;" loading="eager" fetchpriority="high" decoding="sync">
                            </div>
                            <h5>Sarah Mitchell</h5>
                            <div class="text-warning mb-2">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="mb-2 text-muted small"><strong>-25kg</strong> in 6 months</p>
                            <p class="mb-0">"GymFuel changed my life! The calorie tracking made it so easy to stay on track."</p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial-card text-center p-4 bg-light rounded border border-warning border-2">
                            <div class="testimonial-image mb-3">
                                <img src="./img/person2.jpg" alt="Alex & Jordan Miller" class="rounded-circle testimonial-img-pair" style="width: 100px; height: 100px; object-fit: cover; object-position: center;" loading="eager" fetchpriority="high" decoding="sync">
                            </div>
                            <h5>Alex & Jordan Miller</h5>
                            <div class="text-warning mb-2">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="mb-2 text-muted small"><strong>-35kg combined</strong> in 10 months</p>
                            <p class="mb-0">"We started our fitness journey together. Lost 35kg and gained a healthier relationship with food!"</p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial-card text-center p-4 bg-light rounded">
                            <div class="testimonial-image mb-3">
                                <img src="./img/person3.jpg" alt="David Thompson" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;" loading="eager" fetchpriority="high" decoding="sync">
                            </div>
                            <h5>David Thompson</h5>
                            <div class="text-warning mb-2">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="mb-2 text-muted small"><strong>+12kg muscle</strong> in 7 months</p>
                            <p class="mb-0">"GymFuel helped me track my macros perfectly for bulking. Gained 12kg of lean muscle!"</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h4 class="mb-3"><i class="fa-solid fa-fire-flame-curved text-danger"></i> Gym<span class="text-primary">Fuel</span></h4>
                        <p class="text-white-50">Your ultimate nutrition tracking companion for achieving your fitness goals.</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-white me-3 fs-4"><i class="fa-brands fa-facebook"></i></a>
                            <a href="#" class="text-white me-3 fs-4"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="text-white me-3 fs-4"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" class="text-white me-3 fs-4"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-3">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="#aboutus" class="text-white-50 text-decoration-none">About Us</a></li>
                            <li class="mb-2"><a href="#achievements" class="text-white-50 text-decoration-none">Success Stories</a></li>
                            <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">Features</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-3">Support</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Help Center</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Contact Us</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Terms of Service</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3">
                        <h5 class="mb-3">Get In Touch</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-envelope me-2"></i>contact@gymfuel.com</li>
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-phone me-2"></i>+1 (234) 567-890</li>
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-location-dot me-2"></i>123 Fitness Street</li>
                        </ul>
                    </div>
                </div>
                
                <hr class="my-4 bg-white-50">
                
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="mb-0 text-white-50">&copy; 2024 GymFuel. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    <script src="./js/script.js"></script>
</body>

</html>

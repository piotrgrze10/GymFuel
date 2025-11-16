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
    <link rel="stylesheet" href="./css/navbar.css?v=NOWRAP_FIX">
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
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="./search_products.php">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="bmi_calculator.php">BMI Calculator</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#aboutus">About us</a></li>
                        <li class="nav-item"><a class="nav-link" href="#features">Offer</a></li>
                        <li class="nav-item"><a class="nav-link" href="#achievements">Achievements</a></li>
                        <li class="nav-item"><a class="nav-link" href="./search_products.php">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="bmi_calculator.php">BMI Calculator</a></li>
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
        <section id="aboutus" class="aboutus-new py-5">
            <div class="container">
                <div class="aboutus-header text-center mb-5">
                    <h2 class="section-title mb-3">About GymFuel</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="aboutus-subtitle text-muted">Your trusted partner in achieving fitness excellence</p>
                </div>
                
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="aboutus-content">
                            <h3 class="aboutus-title mb-4">Transform Your Nutrition Journey</h3>
                            <p class="aboutus-lead mb-4">Your ultimate companion for achieving fitness goals through intelligent nutrition tracking.</p>
                            <p class="aboutus-text mb-4">Whether you're looking to lose weight, gain muscle, or maintain a healthy lifestyle, we provide the tools and insights you need to succeed.</p>
                            
                            <div class="aboutus-features">
                                <div class="feature-item mb-3">
                                    <div class="feature-icon-wrapper">
                                        <i class="fa-solid fa-check-circle"></i>
                                    </div>
                                    <span class="feature-text">Track calories with 14+ million food products</span>
                                </div>
                                <div class="feature-item mb-3">
                                    <div class="feature-icon-wrapper">
                                        <i class="fa-solid fa-check-circle"></i>
                                    </div>
                                    <span class="feature-text">Monitor macros and nutrients effortlessly</span>
                                </div>
                                <div class="feature-item mb-3">
                                    <div class="feature-icon-wrapper">
                                        <i class="fa-solid fa-check-circle"></i>
                                    </div>
                                    <span class="feature-text">Visualize your progress with real-time analytics</span>
                        </div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="row text-center g-4">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon-wrapper stat-icon-primary">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <h4 class="stat-number mb-2">50K+</h4>
                                    <p class="stat-label mb-0">Users</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon-wrapper stat-icon-danger">
                                        <i class="fa-solid fa-fire-flame-curved"></i>
                                    </div>
                                    <h4 class="stat-number mb-2">2M+</h4>
                                    <p class="stat-label mb-0">Calories</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon-wrapper stat-icon-success">
                                        <i class="fa-solid fa-weight-scale"></i>
                                    </div>
                                    <h4 class="stat-number mb-2">500K</h4>
                                    <p class="stat-label mb-0">KG Lost</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon-wrapper stat-icon-warning">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <h4 class="stat-number mb-2">4.9/5</h4>
                                    <p class="stat-label mb-0">Rating</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="simple-cards-section py-5 bg-light">
            <div class="container">
                <div class="row g-4 justify-content-center simple-cards-row">
                    <div class="col-lg col-md-6 col-sm-6">
                        <div class="simple-card simple-card-1">
                            <div class="simple-card-image">
                                <img src="./img/card1.jpg" alt="Reach & Maintain Your Goal Weight" class="simple-card-img" loading="lazy">
                            </div>
                            <div class="simple-card-caption simple-card-caption-1">
                                <h4>Reach & Maintain Your Goal Weight</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-sm-6">
                        <div class="simple-card simple-card-2">
                            <div class="simple-card-image">
                                <img src="./img/card2.jpeg" alt="Track Your Progress" class="simple-card-img" loading="lazy">
                            </div>
                            <div class="simple-card-caption simple-card-caption-2">
                                <h4>Track Your Progress</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-sm-6">
                        <div class="simple-card simple-card-3">
                            <div class="simple-card-image">
                                <img src="./img/card3.jpg" alt="Build Healthy Habits" class="simple-card-img" loading="lazy">
                            </div>
                            <div class="simple-card-caption simple-card-caption-3">
                                <h4>Build Healthy Habits</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-sm-6">
                        <div class="simple-card simple-card-4">
                            <div class="simple-card-image">
                                <img src="./img/card4.jpg" alt="Monitor Nutrients" class="simple-card-img" loading="lazy">
                            </div>
                            <div class="simple-card-caption simple-card-caption-4">
                                <h4>Monitor Nutrients</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-sm-6">
                        <div class="simple-card simple-card-5">
                            <div class="simple-card-image">
                                <img src="./img/card5.jpg" alt="Achieve Your Goals" class="simple-card-img" loading="lazy">
                            </div>
                            <div class="simple-card-caption simple-card-caption-5">
                                <h4>Achieve Your Goals</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="help" class="help-section py-5">
            <div class="container">
                <div class="help-header text-center mb-5">
                    <h2 class="section-title mb-3">How can we help?</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="help-subtitle text-muted">I want to use GymFuel to…</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="help-card h-100">
                            <div class="help-card-content">
                                <h4 class="help-card-title">Keep track of my food intake</h4>
                                <div class="help-card-arrow">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="help-card-image">
                                <div class="help-image-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Image placeholder</span>
                                </div>
                            </div>
                                </div>
                            </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="help-card h-100">
                            <div class="help-card-content">
                                <h4 class="help-card-title">Monitor my health metrics</h4>
                                <div class="help-card-arrow">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="help-card-image">
                                <div class="help-image-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Image placeholder</span>
                                </div>
                            </div>
                                </div>
                            </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="help-card h-100">
                            <div class="help-card-content">
                                <h4 class="help-card-title">Optimize and refine my diet</h4>
                                <div class="help-card-arrow">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="help-card-image">
                                <div class="help-image-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Image placeholder</span>
                                </div>
                            </div>
                                </div>
                            </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="help-card h-100">
                            <div class="help-card-content">
                                <h4 class="help-card-title">Analyze my diet progress</h4>
                                <div class="help-card-arrow">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="help-card-image">
                                <div class="help-image-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Image placeholder</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features py-5">
            <div class="container">
                <div class="features-header text-center mb-5">
                    <h2 class="section-title mb-3">What We Offer</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="features-subtitle text-muted">Everything you need for successful nutrition tracking</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-1">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Suggested Calorie Goals</h4>
                            <p class="feature-offer-text mb-0">Let our intelligent system calculate your optimal daily calorie intake based on your age, weight, height, activity level, and fitness objectives.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-2">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Custom Calorie Goals</h4>
                            <p class="feature-offer-text mb-0">Set your own personalized calorie targets. Have complete control over your daily intake limits to match your unique preferences and goals.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-3">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Flexible Meal Structure</h4>
                            <p class="feature-offer-text mb-0">Log your meals however you want! Track breakfast, lunch, dinner, and snacks with complete freedom to organize your nutrition your way.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-4">
                                <i class="fa-solid fa-droplet"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Water Tracking</h4>
                            <p class="feature-offer-text mb-0">Monitor your daily hydration with our water tracking feature. Stay on top of your fluid intake to maintain optimal health and performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="why-gymfuel" class="why-gymfuel-section py-5">
            <div class="container">
                <div class="why-gymfuel-header text-center mb-5">
                    <h2 class="section-title mb-3">Why use GymFuel?</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="why-gymfuel-subtitle text-muted">Our comprehensive nutrition tracking tools will help you</p>
                </div>

                <div class="why-gymfuel-panels">
                    <div class="why-panel why-panel-1">
                        <div class="why-panel-content">
                            <div class="why-panel-text">
                                <h3 class="why-panel-heading">Track Your Macros & Nutrients</h3>
                                <p class="why-panel-description">Monitor your protein, carbs, fats, and calories with detailed food logging. See exactly what you're eating and make informed decisions to optimize your nutrition.</p>
                            </div>
                            <div class="why-panel-visual">
                                <div class="why-panel-icon-wrapper">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="why-panel why-panel-2">
                        <div class="why-panel-content">
                            <div class="why-panel-text">
                                <h3 class="why-panel-heading">Reach & Maintain Your Goal Weight</h3>
                                <p class="why-panel-description">Monitor your food intake with detailed food journaling, verified nutrition information, and personalized calorie targets to keep yourself accountable and achieve your fitness goals.</p>
                            </div>
                            <div class="why-panel-visual">
                                <div class="why-panel-icon-wrapper">
                                    <i class="fa-solid fa-weight-scale"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="why-panel why-panel-3">
                        <div class="why-panel-content">
                            <div class="why-panel-text">
                                <h3 class="why-panel-heading">Get a Complete View of Your Health</h3>
                                <p class="why-panel-description">Track your daily nutrition, monitor your BMI, calculate your BMR and TDEE, and visualize your progress with comprehensive analytics to understand your health journey.</p>
                            </div>
                            <div class="why-panel-visual">
                                <div class="why-panel-icon-wrapper">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="why-panel why-panel-4">
                        <div class="why-panel-content">
                            <div class="why-panel-text">
                                <h3 class="why-panel-heading">Gain a Trustworthy Companion</h3>
                                <p class="why-panel-description">We're proud to offer accurate nutrition information within a secure framework to keep your data safe. We encrypt all data, uphold industry best practices, and enforce strict access controls.</p>
                            </div>
                            <div class="why-panel-visual">
                                <div class="why-panel-icon-wrapper">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="achievements" class="success-stories-new py-5 bg-white">
            <div class="container">
                <div class="success-stories-header text-center mb-5">
                    <h2 class="section-title mb-3">Success Stories</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="success-stories-subtitle text-muted">Real people, real results</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="testimonial-card text-center p-4 bg-light rounded">
                            <div class="testimonial-image mb-3">
                                <img src="./img/person1.jpg" alt="Sarah Mitchell" class="rounded-circle testimonial-img" loading="eager" fetchpriority="high" decoding="sync">
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
                                <img src="./img/person2.jpg" alt="Alex & Jordan Miller" class="rounded-circle testimonial-img testimonial-img-pair" loading="eager" fetchpriority="high" decoding="sync">
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
                                <img src="./img/person3.jpg" alt="David Thompson" class="rounded-circle testimonial-img" loading="eager" fetchpriority="high" decoding="sync">
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

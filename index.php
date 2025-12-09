<?php
require_once 'includes/config.php';
redirectIfLoggedIn();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="GymFuel - Your ultimate nutrition tracking companion for achieving your fitness goals. Track calories, macros, and monitor your health metrics.">
    <meta name="keywords" content="nutrition tracking, calorie counter, fitness, health, diet, macros, BMI calculator">
    <meta name="author" content="GymFuel">
    <title>GymFuel - Nutrition Tracking & Fitness Companion</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🔥%3C/text%3E%3C/svg%3E">
    
    <!-- DNS Prefetch & Preconnect for external resources -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://kit.fontawesome.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Google Fonts with font-display swap -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Preload critical hero image only with fetchpriority -->
    <link rel="preload" as="image" href="./img/diet-695723_640.jpg" media="(max-width: 1199px)" fetchpriority="high">
    <link rel="preload" as="image" href="./img/diet-695723_1920.jpg" media="(min-width: 1200px)" fetchpriority="high">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset('./css/navbar.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('./css/main.css'); ?>">
    
    <!-- Font Awesome with defer -->
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="/" aria-label="GymFuel Home">
                <i class="fa-solid fa-fire-flame-curved logo-icon" aria-hidden="true"></i> 
                Gym<span class="blue-text">Fuel</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation menu">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/charts">Charts</a></li>
                        <li class="nav-item"><a class="nav-link" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#why-gymfuel">Why GymFuel?</a></li>
                        <li class="nav-item"><a class="nav-link" href="#calorie-tracking">Calorie Tracking</a></li>
                        <li class="nav-item"><a class="nav-link" href="#features">Offer</a></li>
                        <li class="nav-item"><a class="nav-link" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </nav>
    <header class="hero-img" id="home">
        <div class="hero-text p-2">
            <h1 class="hero-title">Welcome to <br>Gym <span class="blue-text">Fuel</span> Junction!</h1>
            <p>In a space where fitness dreams begin</p>
            <a href="#calorie-tracking" class="btn btn-outline-light mt-2 text-uppercase">explore us</a>
        </div>
        <div class="hero-shadow"></div>

        <a href="#calorie-tracking" aria-label="Scroll to calorie tracking section">
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </a>
    </header>
    <main>
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
                                <div class="why-panel-icon-wrapper why-panel-icon-wrapper--image">
                                    <img src="./img/ss1.png" 
                                         alt="Nutrition tracking highlight" 
                                         class="why-panel-image"
                                         loading="lazy"
                                         width="360"
                                         height="360"
                                         decoding="async">
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
                                <div class="why-panel-icon-wrapper why-panel-icon-wrapper--image">
                                    <img src="./img/ss2.png" 
                                         alt="Goal weight tracking" 
                                         class="why-panel-image"
                                         loading="lazy"
                                         width="360"
                                         height="360"
                                         decoding="async">
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
                                <div class="why-panel-icon-wrapper why-panel-icon-wrapper--image">
                                    <img src="./img/ss3.png" 
                                         alt="Complete health view" 
                                         class="why-panel-image"
                                         loading="lazy"
                                         width="360"
                                         height="360"
                                         decoding="async">
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
                                <div class="why-panel-icon-wrapper why-panel-icon-wrapper--image">
                                    <img src="./img/ss4.png" 
                                         alt="Trustworthy companion" 
                                         class="why-panel-image"
                                         loading="lazy"
                                         width="360"
                                         height="360"
                                         decoding="async">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="calorie-tracking" class="calorie-tracking-section py-5 bg-white">
            <div class="container">
                <div class="calorie-tracking-header text-center mb-5">
                    <h2 class="section-title mb-3">Log meals with ease</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="calorie-tracking-subtitle text-muted">Over 14 million products in the database</p>
                </div>
                <div class="row g-4 gx-4 align-items-center">
                    <div class="col-lg-6 col-12 order-lg-1 order-2">
                        <div class="calorie-tracking-image-wrapper rounded shadow-sm overflow-hidden">
                            <img src="./img/strawberryindex.png" 
                                 alt="Nutrition tracking interface showing meal logging" 
                                 class="calorie-tracking-image" 
                                 loading="lazy"
                                 width="400"
                                 height="400"
                                 decoding="async">
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 order-lg-2 order-1">
                        <div class="calorie-tracking-content">
                            <ul class="calorie-tracking-features list-unstyled">
                                <li class="calorie-tracking-feature-item d-flex gap-3 mb-3">
                                    <div class="calorie-tracking-icon-wrapper">
                                        <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                                    </div>
                                    <div class="calorie-tracking-feature-text">
                                        <strong>View calories and nutrients</strong>
                                    </div>
                                </li>
                                <li class="calorie-tracking-feature-item d-flex gap-3 mb-3">
                                    <div class="calorie-tracking-icon-wrapper">
                                        <i class="fa-solid fa-balance-scale" aria-hidden="true"></i>
                                    </div>
                                    <div class="calorie-tracking-feature-text">
                                        <strong>Compare portion sizes</strong>
                                    </div>
                                </li>
                                <li class="calorie-tracking-feature-item d-flex gap-3 mb-3">
                                    <div class="calorie-tracking-icon-wrapper">
                                        <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                                    </div>
                                    <div class="calorie-tracking-feature-text">
                                        <strong>Track progress and goals</strong>
                                    </div>
                                </li>
                            </ul>
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
                
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <div class="col">
                        <article class="help-card h-100" role="article" aria-labelledby="help-card-1">
                            <div class="help-card-content">
                                <h4 class="help-card-title" id="help-card-1">Keep track of my food intake</h4>
                            </div>
                            <div class="help-card-image">
                                <img src="./img/products/person1.jpg" 
                                     alt="Person tracking food intake with GymFuel app" 
                                     class="help-card-img" 
                                     loading="lazy"
                                     width="300"
                                     height="250"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 1.2;">
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="help-card h-100" role="article" aria-labelledby="help-card-2">
                            <div class="help-card-content">
                                <h4 class="help-card-title" id="help-card-2">Monitor my health metrics</h4>
                            </div>
                            <div class="help-card-image">
                                <img src="./img/products/person2.jpg" 
                                     alt="Person monitoring health metrics and progress" 
                                     class="help-card-img" 
                                     loading="lazy"
                                     width="300"
                                     height="250"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 1.2;">
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="help-card h-100" role="article" aria-labelledby="help-card-3">
                            <div class="help-card-content">
                                <h4 class="help-card-title" id="help-card-3">Optimize and refine my diet</h4>
                            </div>
                            <div class="help-card-image">
                                <img src="./img/products/person3.jpg" 
                                     alt="Person optimizing and refining their diet plan" 
                                     class="help-card-img" 
                                     loading="lazy"
                                     width="300"
                                     height="250"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 1.2;">
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="help-card h-100" role="article" aria-labelledby="help-card-4">
                            <div class="help-card-content">
                                <h4 class="help-card-title" id="help-card-4">Analyze my diet progress</h4>
                            </div>
                            <div class="help-card-image">
                                <img src="./img/products/person4.jpg" 
                                     alt="Person analyzing diet progress and statistics" 
                                     class="help-card-img" 
                                     loading="lazy"
                                     width="300"
                                     height="250"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 1.2;">
                            </div>
                        </article>
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
                                <i class="fa-solid fa-bullseye" aria-hidden="true"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Suggested Calorie Goals</h4>
                            <p class="feature-offer-text mb-0">Let our intelligent system calculate your optimal daily calorie intake based on your age, weight, height, activity level, and fitness objectives.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-2">
                                <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Custom Calorie Goals</h4>
                            <p class="feature-offer-text mb-0">Set your own personalized calorie targets. Have complete control over your daily intake limits to match your unique preferences and goals.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-3">
                                <i class="fa-solid fa-utensils" aria-hidden="true"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Flexible Meal Structure</h4>
                            <p class="feature-offer-text mb-0">Log your meals however you want! Track breakfast, lunch, dinner, and snacks with complete freedom to organize your nutrition your way.</p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="feature-offer-card h-100">
                            <div class="feature-offer-icon-wrapper feature-icon-4">
                                <i class="fa-solid fa-droplet" aria-hidden="true"></i>
                            </div>
                            <h4 class="feature-offer-title mb-3">Water Tracking</h4>
                            <p class="feature-offer-text mb-0">Monitor your daily hydration with our water tracking feature. Stay on top of your fluid intake to maintain optimal health and performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="simple-cards-section py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title mb-3">Register now</h2>
                    <div class="underline mx-auto mb-4"></div>
                    <p class="features-subtitle text-muted">Whether you're counting calories, macros, or micronutrients, you can count on us</p>
                    <a href="/register" class="btn btn-primary btn-lg mt-3 text-uppercase">Sign up</a>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center simple-cards-row">
                    <div class="col">
                        <article class="simple-card simple-card-1" role="article" aria-labelledby="card-1">
                            <div class="simple-card-image">
                                <img src="./img/card1.jpg" 
                                     alt="Reach & Maintain Your Goal Weight" 
                                     class="simple-card-img" 
                                     loading="lazy"
                                     width="280"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 0.8;">
                            </div>
                            <div class="simple-card-caption simple-card-caption-1">
                                <h4 id="card-1">Reach & Maintain Your Goal Weight</h4>
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="simple-card simple-card-2" role="article" aria-labelledby="card-2">
                            <div class="simple-card-image">
                                <img src="./img/card2.jpeg" 
                                     alt="Track Your Progress" 
                                     class="simple-card-img" 
                                     loading="lazy"
                                     width="280"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 0.8;">
                            </div>
                            <div class="simple-card-caption simple-card-caption-2">
                                <h4 id="card-2">Track Your Progress</h4>
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="simple-card simple-card-3" role="article" aria-labelledby="card-3">
                            <div class="simple-card-image">
                                <img src="./img/card3.jpg" 
                                     alt="Build Healthy Habits" 
                                     class="simple-card-img" 
                                     loading="lazy"
                                     width="280"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 0.8;">
                            </div>
                            <div class="simple-card-caption simple-card-caption-3">
                                <h4 id="card-3">Build Healthy Habits</h4>
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="simple-card simple-card-4" role="article" aria-labelledby="card-4">
                            <div class="simple-card-image">
                                <img src="./img/card4.jpg" 
                                     alt="Monitor Nutrients" 
                                     class="simple-card-img" 
                                     loading="lazy"
                                     width="280"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 0.8;">
                            </div>
                            <div class="simple-card-caption simple-card-caption-4">
                                <h4 id="card-4">Monitor Nutrients</h4>
                            </div>
                        </article>
                    </div>

                    <div class="col">
                        <article class="simple-card simple-card-5" role="article" aria-labelledby="card-5">
                            <div class="simple-card-image">
                                <img src="./img/card5.jpg" 
                                     alt="Achieve Your Goals" 
                                     class="simple-card-img" 
                                     loading="lazy"
                                     width="280"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover; aspect-ratio: 0.8;">
                            </div>
                            <div class="simple-card-caption simple-card-caption-5">
                                <h4 id="card-5">Achieve Your Goals</h4>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h4 class="mb-3"><i class="fa-solid fa-fire-flame-curved text-danger" aria-hidden="true"></i> Gym<span class="text-primary">Fuel</span></h4>
                        <p class="text-white-50">Your ultimate nutrition tracking companion for achieving your fitness goals.</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-white me-3 fs-4 text-decoration-none" aria-label="Facebook"><i class="fa-brands fa-facebook" aria-hidden="true"></i></a>
                            <a href="#" class="text-white me-3 fs-4 text-decoration-none" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                            <a href="#" class="text-white me-3 fs-4 text-decoration-none" aria-label="Twitter"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a>
                            <a href="#" class="text-white me-3 fs-4 text-decoration-none" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-3">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="#calorie-tracking" class="text-white-50 text-decoration-none">Calorie Tracking</a></li>
                            <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">Features</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-3">Support</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" aria-label="Help Center">Help Center</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" aria-label="Contact Us">Contact Us</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" aria-label="Privacy Policy">Privacy Policy</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" aria-label="Terms of Service">Terms of Service</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3">
                        <h5 class="mb-3">Get In Touch</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-envelope me-2" aria-hidden="true"></i>contact@gymfuel.com</li>
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-phone me-2" aria-hidden="true"></i>+1 (234) 567-890</li>
                            <li class="mb-2 text-white-50"><i class="fa-solid fa-location-dot me-2" aria-hidden="true"></i>123 Fitness Street</li>
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

    <!-- Bootstrap JS Bundle with defer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
        crossorigin="anonymous"
        defer></script>
    
    <!-- Custom JS with defer -->
    <script src="<?php echo asset('js/script.js'); ?>" defer></script>
</body>

</html>

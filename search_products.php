<?php
require_once 'includes/config.php';
$logged_in = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Search Products - GymFuel</title>
    <meta name="description" content="Search and track nutritional information for popular foods. Find calories, protein, carbs, and fat content for your favorite foods.">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🔥%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo asset('css/navbar.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/search.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/footer.css'); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <?php if ($logged_in): ?>
                <a class="navbar-brand" href="/dashboard"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/charts">Charts</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="navbar-brand" href="/"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="search-container">
        <div class="search-header">
            <h1>Fuel Your Fitness Journey</h1>
            <p style="font-size: 16px; color: #666; margin-bottom: 20px;">
                Search <strong style="color: #039dff;">popular foods</strong> with complete nutritional data
            </p>
            <div class="search-box-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="search" 
                       id="searchInput" 
                       class="search-box" 
                       placeholder="Search for foods..."
                       autocomplete="off"
                       inputmode="search">
                <button class="search-clear-btn" id="searchClearBtn">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="search-filters" id="searchFilters" style="display: none;">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fa-solid fa-arrow-down-short-wide"></i> Sort by:
                    </label>
                    <select class="filter-select" id="sortSelect">
                        <option value="relevance">Relevance</option>
                        <option value="calories-low">Calories (Low to High)</option>
                        <option value="calories-high">Calories (High to Low)</option>
                        <option value="protein-high">Protein (High to Low)</option>
                        <option value="name-az">Name (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <div id="recentSearchesContainer" class="recent-searches-container" style="margin-top: 20px; display: none;">
                <div class="recent-searches-title">
                    <i class="fa-solid fa-clock-rotate-left"></i> Recent:
                </div>
                <div id="recentSearchesList" class="recent-searches-list"></div>
            </div>
        </div>

        <div id="errorMessage" class="error-message"></div>

        <div id="loadingSpinner" class="loading-spinner">
            <div class="shimmer-wrapper">
                <div class="shimmer-card"></div>
                <div class="shimmer-card"></div>
                <div class="shimmer-card"></div>
                <div class="shimmer-card"></div>
                <div class="shimmer-card"></div>
                <div class="shimmer-card"></div>
            </div>
        </div>
        
        <div id="clearCategoryWrapper" style="display: none;">
            <div>
                <div>
                    <i class="fa-solid fa-tags"></i>
                    <span id="activeCategoryName">Showing category products</span>
                </div>
                <button id="clearCategoryBtnTop" class="btn-clear-category" onclick="clearCategorySelection(); return false;">
                    <i class="fa-solid fa-times"></i> Clear Selection
                </button>
            </div>
        </div>
        
        <div id="categoriesSection" class="categories-section">
            <div class="categories-title">
                <i class="fa-solid fa-tags" aria-hidden="true"></i> 
                <span>Browse by Category</span>
            </div>
            <div class="categories-grid" role="grid" aria-label="Food categories">
                <button class="category-card" 
                        data-category="fruits" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Fruits category">
                    <div class="category-icon-wrapper category-fruits">
                        <i class="fa-solid fa-apple-whole" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Fruits</span>
                </button>
                <button class="category-card" 
                        data-category="vegetables" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Vegetables category">
                    <div class="category-icon-wrapper category-vegetables">
                        <i class="fa-solid fa-carrot" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Vegetables</span>
                </button>
                <button class="category-card" 
                        data-category="meat" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Meat and Fish category">
                    <div class="category-icon-wrapper category-meat">
                        <i class="fa-solid fa-drumstick-bite" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Meat & Fish</span>
                </button>
                <button class="category-card" 
                        data-category="dairy" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Dairy category">
                    <div class="category-icon-wrapper category-dairy">
                        <i class="fa-solid fa-cheese" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Dairy</span>
                </button>
                <button class="category-card" 
                        data-category="grains" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Grains and Cereals category">
                    <div class="category-icon-wrapper category-grains">
                        <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Grains & Cereals</span>
                </button>
                <button class="category-card" 
                        data-category="desserts" 
                        type="button"
                        role="gridcell"
                        aria-label="Browse Desserts category">
                    <div class="category-icon-wrapper category-desserts">
                        <i class="fa-solid fa-cake-candles" aria-hidden="true"></i>
                    </div>
                    <span class="category-name">Desserts</span>
                </button>
            </div>
        </div>

        <div id="resultsScrollTarget" class="results-scroll-target"></div>
        <div id="results"></div>
        
        <div id="paginationContainer" class="pagination-container" style="display: none;">
            <button id="prevBtn" class="pagination-btn" onclick="changePage(-1)">
                <i class="fa-solid fa-chevron-left"></i> Previous
            </button>
            <div id="paginationInfo" class="pagination-info"></div>
            <button id="nextBtn" class="pagination-btn" onclick="changePage(1)">
                Next <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>


    <div id="calculatorModal" class="calculator-modal">
        <div class="calculator-content" style="position: relative;">
            <button class="btn-close-custom" onclick="closeCalculator()" type="button">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
            <div class="calculator-header">
                <div id="calcProductImage" style="width: 60px; height: 60px; margin: 0 auto 10px; border-radius: 10px; overflow: hidden; background: linear-gradient(135deg, #039dff 0%, #0066cc 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-bowl-food" style="font-size: 24px; color: white;"></i>
                </div>
                    <h2 id="calcProductName"></h2>
                <p>Select portion size or enter custom weight (grams)</p>
            </div>
            <div class="serving-options" id="servingOptions">
            </div>
            <input type="number" 
                   id="customWeight" 
                   class="custom-weight-input" 
                   placeholder="Enter custom weight in grams..."
                   min="0"
                   step="0.1"
                   style="direction: ltr !important; text-align: left !important;">
            <div class="nutrition-display" id="nutritionDisplay">
            </div>
        </div>
    </div>
    
    <?php 
    $logged_in = isset($_SESSION['user_id']);
    include 'includes/footer.php'; 
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo asset('js/search_products.js'); ?>"></script>
</body>
</html>

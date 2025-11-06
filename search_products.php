<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products - GymFuel</title>
    <meta name="description" content="Search and track nutritional information for popular foods. Find calories, protein, carbs, and fat content for your favorite foods.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/search.css?v=1.0.3">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <?php $logged_in = isset($_SESSION['user_id']); ?>
            <a class="navbar-brand" href="<?php echo $logged_in ? 'dashboard.php' : 'index.php'; ?>"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $logged_in ? 'dashboard.php' : 'index.php'; ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="search_products.php">Search Products</a></li>
                    <?php if ($logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
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
                <input type="text" 
                       id="searchInput" 
                       class="search-box" 
                       placeholder="Search for foods..."
                       autocomplete="off">
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
        
        <div id="clearCategoryWrapper" style="display: none; margin: 0 30px 20px; padding: 15px 20px; background: white; border: 2px solid #039dff; border-radius: 12px; box-shadow: 0 2px 8px rgba(3, 157, 255, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 600; color: #333;">
                    <i class="fa-solid fa-tags" style="color: #039dff; margin-right: 8px;"></i>
                    <span id="activeCategoryName">Showing category products</span>
                </div>
                <button id="clearCategoryBtnTop" class="btn-clear-category" onclick="clearCategorySelection(); return false;">
                    <i class="fa-solid fa-times"></i> Clear Selection
                </button>
            </div>
        </div>
        
        <div id="categoriesSection" class="categories-section">
            <div class="categories-title">
                <i class="fa-solid fa-tags"></i> Browse by Category
            </div>
            <div class="categories-grid">
                <div class="category-btn" data-category="fruits">
                    <i class="fa-solid fa-apple-whole"></i> Fruits
                </div>
                <div class="category-btn" data-category="vegetables">
                    <i class="fa-solid fa-carrot"></i> Vegetables
                </div>
                <div class="category-btn" data-category="meat">
                    <i class="fa-solid fa-drumstick-bite"></i> Meat & Fish
                </div>
                <div class="category-btn" data-category="dairy">
                    <i class="fa-solid fa-cheese"></i> Dairy
                </div>
                <div class="category-btn" data-category="grains">
                    <i class="fa-solid fa-wheat-awn"></i> Grains & Cereals
                </div>
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

    <div class="footer">
        <div class="footer-content">
            <h3><i class="fa-solid fa-fire-flame-curved"></i> Gym<span style="color: #039dff;">Fuel</span></h3>
            <p>Track your nutrition and fuel your fitness journey</p>
            <p style="font-size: 13px; color: #999;">Complete nutritional database for your fitness goals</p>
            <div class="footer-links">
                <a href="<?php echo $logged_in ? 'dashboard.php' : 'index.php'; ?>">
                    <i class="fa-solid fa-home"></i> Home
                </a>
                <a href="search_products.php">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </a>
                <?php if ($logged_in): ?>
                    <a href="dashboard.php">
                        <i class="fa-solid fa-dashboard"></i> Dashboard
                    </a>
                <?php endif; ?>
            </div>
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> GymFuel. Complete nutritional data for fitness.</p>
            </div>
        </div>
    </div>

    <div id="calculatorModal" class="calculator-modal">
        <div class="calculator-content" style="position: relative;">
            <button class="close-modal" onclick="closeCalculator()">&times;</button>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/search_products.js?v=1.0.0"></script>
</body>
</html>

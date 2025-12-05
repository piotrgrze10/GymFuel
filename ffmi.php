<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$logged_in = isLoggedIn();
$user = null;

if ($logged_in) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>FFMI Calculator - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🔥%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo asset('css/navbar.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/bmi.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/footer.css'); ?>">
</head>
<body style="padding-top: 76px;" class="bmi-page">
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $logged_in ? '/dashboard' : '/'; ?>"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/charts">Charts</a></li>
                        <li class="nav-item"><a class="nav-link" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="/search_products">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/bmi_calculator">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bmi-container">
        <div class="bmi-card">
            <a href="/bmi_calculator" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Calculators</span>
            </a>
            
            <div class="bmi-header">
                <i class="fas fa-dumbbell"></i>
                <h1>FFMI Calculator</h1>
                <p>Calculate your Fat-Free Mass Index to track your muscle mass</p>
            </div>

            <?php if (!$logged_in): ?>
            <div class="info-notification">
                <i class="fas fa-info-circle"></i>
                <span>Register to save your calculation results and track your progress over time.</span>
                <a href="/register" class="info-link">Sign up</a>
            </div>
            <?php endif; ?>

            <div class="calculator-panel" id="ffmiPanel">
                <div class="calculator-form-wrapper">
                    <div class="ffmi-form" id="ffmiForm">
                        <div class="input-group">
                            <label for="ffmiHeight">
                                <i class="fas fa-ruler-vertical"></i>
                                <span class="label-text">Height</span>
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="number" 
                                    id="ffmiHeight" 
                                    placeholder="Enter height in cm" 
                                    min="0" 
                                    step="0.1"
                                    class="bmi-input"
                                >
                                <span class="input-unit">cm</span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="ffmiWeight">
                                <i class="fas fa-weight"></i>
                                <span class="label-text">Weight</span>
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="number" 
                                    id="ffmiWeight" 
                                    placeholder="Enter weight in kg" 
                                    min="0" 
                                    step="0.1"
                                    class="bmi-input"
                                >
                                <span class="input-unit">kg</span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="bodyFat">
                                <i class="fas fa-percent"></i>
                                <span class="label-text">Body Fat Percentage</span>
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="number" 
                                    id="bodyFat" 
                                    placeholder="Enter body fat %" 
                                    min="0" 
                                    max="100"
                                    step="0.1"
                                    class="bmi-input"
                                >
                                <span class="input-unit">%</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="calculate-btn" id="calculateFfmiBtn">
                                <i class="fas fa-calculator"></i>
                                <span>Calculate FFMI</span>
                            </button>
                            <button class="reset-btn" id="resetFfmiBtn" style="display: none;">
                                <i class="fas fa-redo"></i>
                                <span>Reset</span>
                            </button>
                        </div>

                        <div class="error-message" id="ffmiErrorMessage"></div>
                    </div>

                    <div class="ffmi-result" id="ffmiResult">
                        <div class="result-content">
                            <div class="result-icon-wrapper">
                                <i class="result-icon" id="ffmiResultIcon"></i>
                            </div>
                            <div class="result-value">
                                <span class="bmi-number" id="ffmiValue">0.0</span>
                                <span class="bmi-label">Your FFMI</span>
                            </div>
                            <div class="result-category" id="ffmiCategory">
                                <span class="category-badge" id="ffmiCategoryBadge"></span>
                                <p class="category-description" id="ffmiCategoryDescription"></p>
                            </div>
                            <div class="result-legend" id="ffmiLegend">
                                <p class="legend-title"><i class="fas fa-info-circle"></i> FFMI Reference</p>
                                <div class="legend-items">
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #bee3f8;"></span>
                                        <span class="legend-text">&lt; 16 - Below Average</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #c6f6d5;"></span>
                                        <span class="legend-text">16 - 18 - Average</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #feebc8;"></span>
                                        <span class="legend-text">18 - 20 - Above Average</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #fed7d7;"></span>
                                        <span class="legend-text">20 - 22 - Superior</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #c6f6d5; border: 2px solid #48bb78;"></span>
                                        <span class="legend-text">≥ 22 - Excellent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ffmi-scale" id="ffmiScale">
                    <h3><i class="fas fa-chart-line"></i> FFMI Categories</h3>
                    <div class="scale-items">
                        <div class="scale-item underweight">
                            <span class="scale-range">&lt; 16</span>
                            <span class="scale-label">Below Average</span>
                        </div>
                        <div class="scale-item normal">
                            <span class="scale-range">16 - 18</span>
                            <span class="scale-label">Average</span>
                        </div>
                        <div class="scale-item overweight">
                            <span class="scale-range">18 - 20</span>
                            <span class="scale-label">Above Average</span>
                        </div>
                        <div class="scale-item obese">
                            <span class="scale-range">20 - 22</span>
                            <span class="scale-label">Superior</span>
                        </div>
                        <div class="scale-item excellent">
                            <span class="scale-range">≥ 22</span>
                            <span class="scale-label">Excellent</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($logged_in): ?>
            <div class="bmi-history" id="bmiHistory">
                <h3><i class="fas fa-history"></i> Recent Calculations</h3>
                <div class="history-list" id="historyList">
                    <p class="no-history">No calculations yet</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.isUserLoggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
    </script>
    <script src="<?php echo asset('js/bmi_calculator.js'); ?>"></script>
</body>
</html>


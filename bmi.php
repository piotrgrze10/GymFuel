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
    <title>BMI Calculator - GymFuel</title>
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
                <i class="fas fa-weight-scale"></i>
                <h1>BMI Calculator</h1>
                <p>Calculate your Body Mass Index to track your fitness progress</p>
            </div>

            <?php if (!$logged_in): ?>
            <div class="info-notification">
                <i class="fas fa-info-circle"></i>
                <span>Register to save your calculation results and track your progress over time.</span>
                <a href="/register" class="info-link">Sign up</a>
            </div>
            <?php endif; ?>

            <div class="calculator-panel" id="bmiPanel">
                <div class="unit-switcher" id="unitSwitcher">
                    <button class="unit-btn active" data-unit="metric">
                        <i class="fas fa-ruler"></i> Metric (cm/kg)
                    </button>
                    <button class="unit-btn" data-unit="imperial">
                        <i class="fas fa-ruler-combined"></i> Imperial (ft/lbs)
                    </button>
                </div>

                <div class="calculator-form-wrapper">
                    <div class="bmi-form" id="bmiForm">
                        <div class="input-group">
                            <label for="height">
                                <i class="fas fa-ruler-vertical"></i>
                                <span class="label-text">Height</span>
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="number" 
                                    id="height" 
                                    placeholder="Enter height in cm" 
                                    min="0" 
                                    step="0.1"
                                    class="bmi-input"
                                >
                                <span class="input-unit">cm</span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="weight">
                                <i class="fas fa-weight"></i>
                                <span class="label-text">Weight</span>
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="number" 
                                    id="weight" 
                                    placeholder="Enter weight in kg" 
                                    min="0" 
                                    step="0.1"
                                    class="bmi-input"
                                >
                                <span class="input-unit">kg</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="calculate-btn" id="calculateBtn">
                                <i class="fas fa-calculator"></i>
                                <span>Calculate BMI</span>
                            </button>
                            <button class="reset-btn" id="resetBtn" style="display: none;">
                                <i class="fas fa-redo"></i>
                                <span>Reset</span>
                            </button>
                        </div>

                        <div class="error-message" id="errorMessage"></div>
                    </div>

                    <div class="bmi-result" id="bmiResult">
                        <div class="result-content">
                            <div class="result-icon-wrapper">
                                <i class="result-icon" id="resultIcon"></i>
                            </div>
                            <div class="result-value">
                                <span class="bmi-number" id="bmiValue">0.0</span>
                                <span class="bmi-label">Your BMI</span>
                            </div>
                            <div class="result-category" id="bmiCategory">
                                <span class="category-badge" id="categoryBadge"></span>
                                <p class="category-description" id="categoryDescription"></p>
                            </div>
                            <div class="result-legend" id="bmiLegend">
                                <p class="legend-title"><i class="fas fa-info-circle"></i> BMI Reference</p>
                                <div class="legend-items">
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #bee3f8;"></span>
                                        <span class="legend-text">&lt; 18.5 - Underweight</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #c6f6d5;"></span>
                                        <span class="legend-text">18.5 - 24.9 - Normal Weight</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #feebc8;"></span>
                                        <span class="legend-text">25.0 - 29.9 - Overweight</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: #fed7d7;"></span>
                                        <span class="legend-text">≥ 30.0 - Obese</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bmi-scale" id="bmiScale">
                    <h3><i class="fas fa-chart-line"></i> BMI Categories</h3>
                    <div class="scale-items">
                        <div class="scale-item underweight">
                            <span class="scale-range">&lt; 18.5</span>
                            <span class="scale-label">Underweight</span>
                        </div>
                        <div class="scale-item normal">
                            <span class="scale-range">18.5 - 24.9</span>
                            <span class="scale-label">Normal Weight</span>
                        </div>
                        <div class="scale-item overweight">
                            <span class="scale-range">25.0 - 29.9</span>
                            <span class="scale-label">Overweight</span>
                        </div>
                        <div class="scale-item obese">
                            <span class="scale-range">≥ 30.0</span>
                            <span class="scale-label">Obese</span>
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


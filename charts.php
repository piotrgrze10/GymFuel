<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Charts - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🔥%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo asset('css/navbar.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/charts.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/footer.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
</head>
<body style="padding-top: 76px;">
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="/dashboard"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/charts">Charts</a></li>
                    <li class="nav-item"><a class="nav-link" href="/search_products">Search Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/bmi_calculator">Calculators</a></li>
                    <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="charts-container">
        <div class="container py-4">
            <div class="charts-header mb-4">
                <h1 class="charts-title">
                    <i class="fa-solid fa-chart-line"></i>
                    Your Progress Charts
                </h1>
                <p class="charts-subtitle">Visualize your nutrition and fitness journey</p>
            </div>

            <div class="charts-controls mb-4">
                <div class="control-group">
                    <label for="timeRange" class="control-label">
                        <i class="fa-solid fa-calendar-days"></i>
                        Time Range
                    </label>
                    <select id="timeRange" class="form-select control-select">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="all">All Data</option>
                    </select>
                </div>
            </div>

            <div class="chart-buttons mb-4">
                <button class="chart-btn active" data-chart="energy">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                    <span>Energy Consumed</span>
                </button>
                <button class="chart-btn" data-chart="weight">
                    <i class="fa-solid fa-weight-scale"></i>
                    <span>Weight</span>
                </button>
                <button class="chart-btn" data-chart="macros">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Macronutrients</span>
                </button>
                <button class="chart-btn" data-chart="water">
                    <i class="fa-solid fa-droplet"></i>
                    <span>Water Intake</span>
                </button>
            </div>

            <div id="noDataMessage" class="no-data-message" style="display: none;">
                <div class="no-data-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3>No Data Available</h3>
                <p>Start tracking your meals to see your progress charts here!</p>
                <a href="/dashboard" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Go to Dashboard
                </a>
            </div>

            <div id="chartDisplay" class="chart-display">
                <div class="chart-card active" data-chart-type="energy">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                            Energy Consumed
                        </h3>
                        <div class="chart-subtitle">Daily calories (kcal)</div>
                    </div>
                    <div class="chart-container">
                        <div id="energyChart"></div>
                    </div>
                </div>

                <div class="chart-card" data-chart-type="weight">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fa-solid fa-weight-scale"></i>
                            Weight
                        </h3>
                        <div class="chart-subtitle">Body weight (kg)</div>
                    </div>
                    <div class="chart-container">
                        <div id="weightChart"></div>
                    </div>
                </div>

                <div class="chart-card" data-chart-type="macros">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fa-solid fa-chart-pie"></i>
                            Macronutrients
                        </h3>
                        <div class="chart-subtitle">Protein, Carbs, Fat (g)</div>
                    </div>
                    <div class="chart-container">
                        <div id="macrosChart"></div>
                    </div>
                </div>

                <div class="chart-card" data-chart-type="water">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fa-solid fa-droplet"></i>
                            Water Intake
                        </h3>
                        <div class="chart-subtitle">Daily hydration (ml)</div>
                    </div>
                    <div class="chart-container">
                        <div id="waterChart"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php 
    $logged_in = true;
    include 'includes/footer.php'; 
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>


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
    <title>Calculators - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/navbar.css?v=NOWRAP_FIX">
    <link rel="stylesheet" href="css/bmi.css?v=MODERN_REDESIGN">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body style="padding-top: 76px;" class="bmi-page">
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $logged_in ? 'dashboard.php' : 'index.php'; ?>"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="charts.php">Charts</a></li>
                        <li class="nav-item"><a class="nav-link" href="search_products.php">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="bmi_calculator.php">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="search_products.php">Search Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="bmi_calculator.php">Calculators</a></li>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bmi-container">
        <div class="bmi-card">
            <div class="bmi-header">
                <i class="fas fa-calculator"></i>
                <h1>Calculators</h1>
                <p>Choose a calculator to track your fitness progress</p>
            </div>

            <?php if (!$logged_in): ?>
            <div class="cta-banner">
                <div class="cta-content">
                    <i class="fas fa-star"></i>
                    <div class="cta-text">
                        <strong>Want to save your results?</strong>
                        <p>Create a free account to track your calculations history and access full nutrition tracking!</p>
                    </div>
                </div>
                <div class="cta-buttons">
                    <a href="auth/register.php" class="cta-btn primary">Register Free</a>
                    <a href="auth/login.php" class="cta-btn secondary">Login</a>
                </div>
            </div>
            <?php endif; ?>

            <section class="calculator-selector">
                <h2 class="sr-only">Choose a Calculator</h2>
                <div class="calculator-buttons">
                    <a href="bmi.php" class="calculator-button bmi-button">
                        <div class="button-ripple"></div>
                        <div class="button-content">
                            <i class="fas fa-weight-scale button-icon"></i>
                            <span class="button-text">BMI Calculator</span>
                            <i class="fas fa-arrow-right button-arrow"></i>
                        </div>
                    </a>
                    
                    <a href="ffmi.php" class="calculator-button ffmi-button">
                        <div class="button-ripple"></div>
                        <div class="button-content">
                            <i class="fas fa-dumbbell button-icon"></i>
                            <span class="button-text">FFMI Calculator</span>
                            <i class="fas fa-arrow-right button-arrow"></i>
                        </div>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


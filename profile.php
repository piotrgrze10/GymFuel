<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$current_weight = $user['weight'];

$goal_weight = $current_weight;
if ($user['goal'] === 'lose_weight') {
    $goal_weight = $current_weight - 10;
} elseif ($user['goal'] === 'gain_weight') {
    $goal_weight = $current_weight + 5;
}

$weight_change = $current_weight - $goal_weight;

$stmt = $pdo->prepare("
    SELECT log_date, total_calories, total_protein, total_carbs, total_fat, water_intake
    FROM daily_logs 
    WHERE user_id = ? 
    AND log_date <= CURDATE()
    ORDER BY log_date DESC 
    LIMIT 7
");
$stmt->execute([$_SESSION['user_id']]);
$recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$avg_calories = 0;
$avg_protein = 0;
$avg_carbs = 0;
$avg_fat = 0;
$days_logged = count($recent_logs);

if ($days_logged > 0) {
    foreach ($recent_logs as $log) {
        $avg_calories += $log['total_calories'];
        $avg_protein += $log['total_protein'];
        $avg_carbs += $log['total_carbs'];
        $avg_fat += $log['total_fat'];
    }
    $avg_calories = round($avg_calories / $days_logged);
    $avg_protein = round($avg_protein / $days_logged);
    $avg_carbs = round($avg_carbs / $days_logged);
    $avg_fat = round($avg_fat / $days_logged);
}

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?");
$stmt->execute([$_SESSION['user_id'], $today]);
$today_log = $stmt->fetch();

if (!$today_log) {
    $today_log = [
        'total_calories' => 0,
        'total_protein' => 0,
        'total_carbs' => 0,
        'total_fat' => 0,
        'water_intake' => 0
    ];
}

$streak = 0;
$check_date = new DateTime();
while (true) {
    $date_str = $check_date->format('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM daily_logs WHERE user_id = ? AND log_date = ? AND total_calories > 0");
    $stmt->execute([$_SESSION['user_id'], $date_str]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        $streak++;
        $check_date->modify('-1 day');
    } else {
        break;
    }
    
    if ($streak > 365) break;
}

$activity_levels = [
    'sedentary' => 'Sedentary',
    'lightly_active' => 'Lightly Active',
    'moderately_active' => 'Moderately Active',
    'very_active' => 'Very Active',
    'extra_active' => 'Extra Active'
];

$protein_target = round(($user['tdee'] * 0.30) / 4);
$carbs_target = round(($user['tdee'] * 0.40) / 4);
$fat_target = round(($user['tdee'] * 0.30) / 9);

$names = explode(' ', $_SESSION['user_name']);
$initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#667eea">
    <title>My Profile - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_products.php">Search Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="bmi_calculator.php">BMI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-header-bg"></div>
            <div class="container">
                <div class="profile-header-content">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar">
                            <span class="profile-initials"><?php echo $initials; ?></span>
                        </div>
                        <div class="profile-info">
                            <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                            <p class="profile-email">
                                <i class="fa-solid fa-envelope"></i>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                            <div class="profile-badges">
                                <span class="profile-badge">
                                    <i class="fa-solid fa-fire"></i>
                                    <?php echo $streak; ?> Day Streak
                                </span>
                                <span class="profile-badge">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $user['goal'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="btn-profile-action primary" id="editProfileBtn">
                            <i class="fa-solid fa-pen"></i>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container profile-content">
            <div class="section-title">
                <i class="fa-solid fa-gauge-high"></i>
                <h2>Today's Overview</h2>
                <p>Your nutrition and activity at a glance</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card calories-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-fire-flame-curved"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Calories</h3>
                        <div class="stat-value">
                            <?php echo number_format($today_log['total_calories']); ?>
                            <span class="stat-unit">/ <?php echo number_format($user['tdee']); ?> kcal</span>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo min(($today_log['total_calories'] / $user['tdee']) * 100, 100); ?>%"></div>
                            </div>
                            <span class="progress-percentage"><?php echo round(($today_log['total_calories'] / $user['tdee']) * 100); ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card water-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Hydration</h3>
                        <div class="stat-value">
                            <?php echo number_format($today_log['water_intake']); ?>
                            <span class="stat-unit">/ 2000 ml</span>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar water">
                                <div class="progress-fill" style="width: <?php echo min(($today_log['water_intake'] / 2000) * 100, 100); ?>%"></div>
                            </div>
                            <span class="progress-percentage"><?php echo round(($today_log['water_intake'] / 2000) * 100); ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card weight-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-weight-scale"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Current Weight</h3>
                        <div class="stat-value">
                            <?php echo number_format($current_weight, 1); ?>
                            <span class="stat-unit">kg</span>
                        </div>
                        <div class="stat-meta">
                            Goal: <?php echo number_format($goal_weight, 1); ?> kg
                        </div>
                    </div>
                </div>

                <div class="stat-card activity-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-person-running"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-label">Activity Level</h3>
                        <div class="stat-value" style="font-size: 1.3rem;">
                            <?php echo $activity_levels[$user['activity_level']]; ?>
                        </div>
                        <div class="stat-meta">
                            BMR: <?php echo number_format($user['bmr']); ?> kcal
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-title">
                <i class="fa-solid fa-chart-pie"></i>
                <h2>Today's Macronutrients</h2>
                <p>Track your protein, carbs, and fats</p>
            </div>

            <div class="macros-grid">
                <div class="macro-card">
                    <div class="macro-header">
                        <div class="macro-icon protein">
                            <i class="fa-solid fa-dumbbell"></i>
                        </div>
                        <div class="macro-info">
                            <h4>Protein</h4>
                            <p class="macro-target">Target: <?php echo $protein_target; ?>g</p>
                        </div>
                    </div>
                    <div class="macro-value">
                        <?php echo number_format($today_log['total_protein'], 1); ?><span class="macro-unit">g</span>
                    </div>
                    <div class="macro-progress">
                        <div class="progress-bar">
                            <div class="progress-fill protein" style="width: <?php echo min(($today_log['total_protein'] / $protein_target) * 100, 100); ?>%"></div>
                        </div>
                    </div>
                    <p class="macro-percentage"><?php echo round(($today_log['total_protein'] / $protein_target) * 100); ?>% of daily goal</p>
                </div>

                <div class="macro-card">
                    <div class="macro-header">
                        <div class="macro-icon carbs">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="macro-info">
                            <h4>Carbs</h4>
                            <p class="macro-target">Target: <?php echo $carbs_target; ?>g</p>
                        </div>
                    </div>
                    <div class="macro-value">
                        <?php echo number_format($today_log['total_carbs'], 1); ?><span class="macro-unit">g</span>
                    </div>
                    <div class="macro-progress">
                        <div class="progress-bar">
                            <div class="progress-fill carbs" style="width: <?php echo min(($today_log['total_carbs'] / $carbs_target) * 100, 100); ?>%"></div>
                        </div>
                    </div>
                    <p class="macro-percentage"><?php echo round(($today_log['total_carbs'] / $carbs_target) * 100); ?>% of daily goal</p>
                </div>

                <div class="macro-card">
                    <div class="macro-header">
                        <div class="macro-icon fat">
                            <i class="fa-solid fa-droplet"></i>
                        </div>
                        <div class="macro-info">
                            <h4>Fat</h4>
                            <p class="macro-target">Target: <?php echo $fat_target; ?>g</p>
                        </div>
                    </div>
                    <div class="macro-value">
                        <?php echo number_format($today_log['total_fat'], 1); ?><span class="macro-unit">g</span>
                    </div>
                    <div class="macro-progress">
                        <div class="progress-bar">
                            <div class="progress-fill fat" style="width: <?php echo min(($today_log['total_fat'] / $fat_target) * 100, 100); ?>%"></div>
                        </div>
                    </div>
                    <p class="macro-percentage"><?php echo round(($today_log['total_fat'] / $fat_target) * 100); ?>% of daily goal</p>
                </div>
            </div>

            <div class="section-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <h2>Recent Activity</h2>
                <p>Your last 7 days of tracking</p>
            </div>

            <div class="activity-timeline">
                <?php if (count($recent_logs) > 0): ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <?php
                        $log_date = new DateTime($log['log_date']);
                        $is_today = $log['log_date'] === date('Y-m-d');
                        $calories_percentage = ($log['total_calories'] / $user['tdee']) * 100;
                        ?>
                        <div class="activity-item <?php echo $is_today ? 'today' : ''; ?>">
                            <div class="activity-date">
                                <div class="activity-day"><?php echo $log_date->format('D'); ?></div>
                                <div class="activity-date-num"><?php echo $log_date->format('j'); ?></div>
                                <div class="activity-month"><?php echo $log_date->format('M'); ?></div>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    <h4><?php echo $is_today ? 'Today' : $log_date->format('l'); ?></h4>
                                    <span class="activity-calories"><?php echo number_format($log['total_calories']); ?> kcal</span>
                                </div>
                                <div class="activity-macros">
                                    <span><i class="fa-solid fa-dumbbell"></i> P: <?php echo number_format($log['total_protein'], 1); ?>g</span>
                                    <span><i class="fa-solid fa-bolt"></i> C: <?php echo number_format($log['total_carbs'], 1); ?>g</span>
                                    <span><i class="fa-solid fa-droplet"></i> F: <?php echo number_format($log['total_fat'], 1); ?>g</span>
                                    <?php if ($log['water_intake'] > 0): ?>
                                        <span><i class="fa-solid fa-glass-water"></i> <?php echo number_format($log['water_intake']); ?>ml</span>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo min($calories_percentage, 100); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($calories_percentage >= 95 && $calories_percentage <= 105): ?>
                                <div class="activity-badge success">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <h3>No Recent Activity</h3>
                        <p>Start tracking your meals to see your activity here!</p>
                        <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($days_logged > 0): ?>
            <div class="section-title">
                <i class="fa-solid fa-chart-column"></i>
                <h2>7-Day Average</h2>
                <p>Your average intake over the past week</p>
            </div>

            <div class="weekly-stats">
                <div class="weekly-stat">
                    <div class="weekly-icon calories">
                        <i class="fa-solid fa-fire-flame-curved"></i>
                    </div>
                    <div class="weekly-content">
                        <h4>Average Calories</h4>
                        <p class="weekly-value"><?php echo number_format($avg_calories); ?> <span>kcal/day</span></p>
                    </div>
                </div>
                <div class="weekly-stat">
                    <div class="weekly-icon protein">
                        <i class="fa-solid fa-dumbbell"></i>
                    </div>
                    <div class="weekly-content">
                        <h4>Average Protein</h4>
                        <p class="weekly-value"><?php echo number_format($avg_protein); ?> <span>g/day</span></p>
                    </div>
                </div>
                <div class="weekly-stat">
                    <div class="weekly-icon carbs">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="weekly-content">
                        <h4>Average Carbs</h4>
                        <p class="weekly-value"><?php echo number_format($avg_carbs); ?> <span>g/day</span></p>
                    </div>
                </div>
                <div class="weekly-stat">
                    <div class="weekly-icon fat">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div class="weekly-content">
                        <h4>Average Fat</h4>
                        <p class="weekly-value"><?php echo number_format($avg_fat); ?> <span>g/day</span></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="motivation-card">
                <div class="motivation-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="motivation-content">
                    <h3>You're doing great!</h3>
                    <?php if ($streak > 0): ?>
                        <p>Keep up your <?php echo $streak; ?>-day streak! Consistency is key to reaching your fitness goals.</p>
                    <?php else: ?>
                        <p>Start tracking your meals today and build a healthy streak!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-user-pen"></i>
                        Edit Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" value="<?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" value="<?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[1] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" value="<?php echo $user['weight']; ?>" step="0.1" min="20" max="300" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" class="form-control" id="height" value="<?php echo $user['height']; ?>" step="0.1" min="50" max="250" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" value="<?php echo $user['age']; ?>" min="13" max="120" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Activity Level</label>
                            <select class="form-control" id="activityLevel">
                                <option value="sedentary" <?php echo $user['activity_level'] === 'sedentary' ? 'selected' : ''; ?>>Sedentary</option>
                                <option value="lightly_active" <?php echo $user['activity_level'] === 'lightly_active' ? 'selected' : ''; ?>>Lightly Active</option>
                                <option value="moderately_active" <?php echo $user['activity_level'] === 'moderately_active' ? 'selected' : ''; ?>>Moderately Active</option>
                                <option value="very_active" <?php echo $user['activity_level'] === 'very_active' ? 'selected' : ''; ?>>Very Active</option>
                                <option value="extra_active" <?php echo $user['activity_level'] === 'extra_active' ? 'selected' : ''; ?>>Extra Active</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Goal</label>
                            <select class="form-control" id="goal">
                                <option value="lose_weight" <?php echo $user['goal'] === 'lose_weight' ? 'selected' : ''; ?>>Lose Weight</option>
                                <option value="maintain" <?php echo $user['goal'] === 'maintain' ? 'selected' : ''; ?>>Maintain</option>
                                <option value="gain_weight" <?php echo $user['goal'] === 'gain_weight' ? 'selected' : ''; ?>>Gain Weight</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProfileBtn">
                        <i class="fa-solid fa-check"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $logged_in = isset($_SESSION['user_id']);
    include 'includes/footer.php'; 
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>


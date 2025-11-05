<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?");
$stmt->execute([$_SESSION['user_id'], $today]);
$today_log = $stmt->fetch();

if (!$today_log) {
    $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, log_date) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $today]);
    $today_log = [
        'id' => $pdo->lastInsertId(),
        'total_calories' => 0,
        'total_protein' => 0,
        'total_carbs' => 0,
        'total_fat' => 0,
        'total_fiber' => 0,
        'water_intake' => 0
    ];
}

$stmt = $pdo->prepare("
    SELECT fe.*, fd.image 
    FROM food_entries fe 
    JOIN food_database fd ON fe.food_id = fd.id 
    WHERE fe.log_id = ? 
    ORDER BY fe.created_at ASC
");
$stmt->execute([$today_log['id']]);
$food_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$entries_by_meal = [
    'breakfast' => [],
    'lunch' => [],
    'dinner' => [],
    'snack' => []
];

foreach ($food_entries as $entry) {
    $entries_by_meal[$entry['meal_type']][] = $entry;
}

$meal_calories = [];
foreach ($entries_by_meal as $meal_type => $entries) {
    $meal_calories[$meal_type] = 0;
    foreach ($entries as $entry) {
        $meal_calories[$meal_type] += floatval($entry['calories']);
    }
}

$remaining_calories = $user['tdee'] - floatval($today_log['total_calories']);
$calorie_percentage = $user['tdee'] > 0 ? ($today_log['total_calories'] / $user['tdee']) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body style="padding-top: 76px;">
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-fire-flame-curved logo-icon"></i> Gym<span class="blue-text">Fuel</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bmi-calculator">BMI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ffmi-calculator">FFMI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link" href="#progress">Progress</a></li>
                    <li class="nav-item">
                        <span class="nav-link text-primary">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4" style="margin-top: 80px;">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card calorie-overview mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="fw-bold mb-1"><?php echo date('l, F j'); ?></h3>
                                <p class="text-muted mb-0">Today's Nutrition</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                                <i class="fa-solid fa-plus"></i> Add Food
                            </button>
                        </div>

                        <div class="calorie-ring-container">
                            <svg class="calorie-ring" width="240" height="240">
                                <!-- Background circle -->
                                <circle cx="120" cy="120" r="95" stroke="url(#bgGradient)" stroke-width="16" fill="none" opacity="0.15"></circle>
                                <!-- Progress circle -->
                                <circle cx="120" cy="120" r="95" stroke="url(#<?php echo $calorie_percentage >= 100 ? 'overGradient' : 'progressGradient'; ?>)" 
                                        stroke-width="16" fill="none" stroke-dasharray="<?php echo 2 * M_PI * 95; ?>"
                                        stroke-dashoffset="<?php echo 2 * M_PI * 95 * (1 - min($calorie_percentage, 100) / 100); ?>"
                                        stroke-linecap="round" transform="rotate(-90 120 120)" class="progress-circle">
                                </circle>
                                <!-- Gradient definitions -->
                                <defs>
                                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#0d6efd;stop-opacity:1" />
                                    </linearGradient>
                                    <linearGradient id="overGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#dc3545;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#fd7e14;stop-opacity:1" />
                                    </linearGradient>
                                    <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#0d6efd;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#667eea;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="calorie-ring-text">
                                <div class="calorie-percentage"><?php echo number_format($calorie_percentage); ?>%</div>
                                <h2 class="mb-0"><?php echo number_format($today_log['total_calories']); ?></h2>
                                <p class="text-muted mb-2" style="font-size: 0.85rem;">of <?php echo number_format($user['tdee']); ?> kcal</p>
                                <div class="remaining-calories">
                                    <?php if ($remaining_calories > 0): ?>
                                        <span class="badge-remaining success"><?php echo number_format($remaining_calories); ?> left</span>
                                    <?php elseif ($remaining_calories < 0): ?>
                                        <span class="badge-remaining danger"><?php echo number_format(abs($remaining_calories)); ?> over</span>
                                    <?php else: ?>
                                        <span class="badge-remaining perfect">Perfect!</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon protein">
                                        <i class="fa-solid fa-dumbbell"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_protein'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Protein</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon carbs">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_carbs'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Carbs</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon fat">
                                        <i class="fa-solid fa-droplet"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_fat'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Fat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="meals-section">
                    <div class="meal-card">
                        <div class="meal-header">
                            <h4><i class="fa-solid fa-sun"></i> Breakfast</h4>
                            <span class="meal-calories"><?php echo number_format($meal_calories['breakfast']); ?> kcal</span>
                        </div>
                        <div class="meal-entries" id="breakfast-entries">
                            <?php 
                            if (!empty($entries_by_meal['breakfast'])):
                                foreach ($entries_by_meal['breakfast'] as $entry):
                            ?>
                                <div class="entry-item" data-entry-id="<?php echo $entry['id']; ?>">
                                    <div class="entry-name">
                                        <strong><?php echo htmlspecialchars($entry['food_name']); ?></strong>
                                        <small class="text-muted d-block">
                                            <?php 
                                            $quantity = number_format($entry['quantity'], 0);
                                            $unit = isset($entry['quantity_unit']) ? $entry['quantity_unit'] : 'g';
                                            
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                $plural_map = [
                                                    'egg' => 'eggs',
                                                    'slice' => 'slices',
                                                    'tbsp' => 'tbsp',
                                                    'cup' => 'cups',
                                                    'serving' => 'servings',
                                                    'avocado' => 'avocados',
                                                    'orange' => 'oranges'
                                                ];
                                                $unit = isset($plural_map[$unit]) ? $plural_map[$unit] : $unit . 's';
                                            }
                                            echo $quantity . ' ' . $unit;
                                            ?>
                                        </small>
                                        <div class="entry-macros mt-2">
                                            <small style="color: #6c757d;">
                                                <?php echo number_format($entry['calories']); ?> kcal | 
                                                P: <?php echo number_format($entry['protein'], 1); ?>g | 
                                                C: <?php echo number_format($entry['carbs'], 1); ?>g | 
                                                F: <?php echo number_format($entry['fat'], 1); ?>g
                                            </small>
                                        </div>
                                    </div>
                                    <div class="entry-calories">
                                        <button class="btn-remove-entry" data-entry-id="<?php echo $entry['id']; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </div>
                    </div>

                    
                    <div class="meal-card">
                        <div class="meal-header">
                            <h4><i class="fa-solid fa-bowl-rice"></i> Lunch</h4>
                            <span class="meal-calories"><?php echo number_format($meal_calories['lunch']); ?> kcal</span>
                        </div>
                        <div class="meal-entries" id="lunch-entries">
                            <?php 
                            if (!empty($entries_by_meal['lunch'])):
                                foreach ($entries_by_meal['lunch'] as $entry):
                            ?>
                                <div class="entry-item" data-entry-id="<?php echo $entry['id']; ?>">
                                    <div class="entry-name">
                                        <strong><?php echo htmlspecialchars($entry['food_name']); ?></strong>
                                        <small class="text-muted d-block">
                                            <?php 
                                            $quantity = number_format($entry['quantity'], 0);
                                            $unit = isset($entry['quantity_unit']) ? $entry['quantity_unit'] : 'g';
                                            
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                $plural_map = [
                                                    'egg' => 'eggs',
                                                    'slice' => 'slices',
                                                    'tbsp' => 'tbsp',
                                                    'cup' => 'cups',
                                                    'serving' => 'servings',
                                                    'avocado' => 'avocados',
                                                    'orange' => 'oranges'
                                                ];
                                                $unit = isset($plural_map[$unit]) ? $plural_map[$unit] : $unit . 's';
                                            }
                                            echo $quantity . ' ' . $unit;
                                            ?>
                                        </small>
                                        <div class="entry-macros mt-2">
                                            <small style="color: #6c757d;">
                                                <?php echo number_format($entry['calories']); ?> kcal | 
                                                P: <?php echo number_format($entry['protein'], 1); ?>g | 
                                                C: <?php echo number_format($entry['carbs'], 1); ?>g | 
                                                F: <?php echo number_format($entry['fat'], 1); ?>g
                                            </small>
                                        </div>
                                    </div>
                                    <div class="entry-calories">
                                        <button class="btn-remove-entry" data-entry-id="<?php echo $entry['id']; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </div>
                    </div>

                    
                    <div class="meal-card">
                        <div class="meal-header">
                            <h4><i class="fa-solid fa-utensils"></i> Dinner</h4>
                            <span class="meal-calories"><?php echo number_format($meal_calories['dinner']); ?> kcal</span>
                        </div>
                        <div class="meal-entries" id="dinner-entries">
                            <?php 
                            if (!empty($entries_by_meal['dinner'])):
                                foreach ($entries_by_meal['dinner'] as $entry):
                            ?>
                                <div class="entry-item" data-entry-id="<?php echo $entry['id']; ?>">
                                    <div class="entry-name">
                                        <strong><?php echo htmlspecialchars($entry['food_name']); ?></strong>
                                        <small class="text-muted d-block">
                                            <?php 
                                            $quantity = number_format($entry['quantity'], 0);
                                            $unit = isset($entry['quantity_unit']) ? $entry['quantity_unit'] : 'g';
                                            
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                $plural_map = [
                                                    'egg' => 'eggs',
                                                    'slice' => 'slices',
                                                    'tbsp' => 'tbsp',
                                                    'cup' => 'cups',
                                                    'serving' => 'servings',
                                                    'avocado' => 'avocados',
                                                    'orange' => 'oranges'
                                                ];
                                                $unit = isset($plural_map[$unit]) ? $plural_map[$unit] : $unit . 's';
                                            }
                                            echo $quantity . ' ' . $unit;
                                            ?>
                                        </small>
                                        <div class="entry-macros mt-2">
                                            <small style="color: #6c757d;">
                                                <?php echo number_format($entry['calories']); ?> kcal | 
                                                P: <?php echo number_format($entry['protein'], 1); ?>g | 
                                                C: <?php echo number_format($entry['carbs'], 1); ?>g | 
                                                F: <?php echo number_format($entry['fat'], 1); ?>g
                                            </small>
                                        </div>
                                    </div>
                                    <div class="entry-calories">
                                        <button class="btn-remove-entry" data-entry-id="<?php echo $entry['id']; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </div>
                    </div>

                    
                    <div class="meal-card">
                        <div class="meal-header">
                            <h4><i class="fa-solid fa-cookie"></i> Snacks</h4>
                            <span class="meal-calories"><?php echo number_format($meal_calories['snack']); ?> kcal</span>
                        </div>
                        <div class="meal-entries" id="snack-entries">
                            <?php 
                            if (!empty($entries_by_meal['snack'])):
                                foreach ($entries_by_meal['snack'] as $entry):
                            ?>
                                <div class="entry-item" data-entry-id="<?php echo $entry['id']; ?>">
                                    <div class="entry-name">
                                        <strong><?php echo htmlspecialchars($entry['food_name']); ?></strong>
                                        <small class="text-muted d-block">
                                            <?php 
                                            $quantity = number_format($entry['quantity'], 0);
                                            $unit = isset($entry['quantity_unit']) ? $entry['quantity_unit'] : 'g';
                                            
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                $plural_map = [
                                                    'egg' => 'eggs',
                                                    'slice' => 'slices',
                                                    'tbsp' => 'tbsp',
                                                    'cup' => 'cups',
                                                    'serving' => 'servings',
                                                    'avocado' => 'avocados',
                                                    'orange' => 'oranges'
                                                ];
                                                $unit = isset($plural_map[$unit]) ? $plural_map[$unit] : $unit . 's';
                                            }
                                            echo $quantity . ' ' . $unit;
                                            ?>
                                        </small>
                                        <div class="entry-macros mt-2">
                                            <small style="color: #6c757d;">
                                                <?php echo number_format($entry['calories']); ?> kcal | 
                                                P: <?php echo number_format($entry['protein'], 1); ?>g | 
                                                C: <?php echo number_format($entry['carbs'], 1); ?>g | 
                                                F: <?php echo number_format($entry['fat'], 1); ?>g
                                            </small>
                                        </div>
                                    </div>
                                    <div class="entry-calories">
                                        <button class="btn-remove-entry" data-entry-id="<?php echo $entry['id']; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                
                <div class="card goals-card mb-4">
                    <div class="card-body">
                        <div class="goals-header">
                            <h5 class="fw-bold mb-0">
                                <i class="fa-solid fa-trophy goals-trophy-icon"></i>
                                Your Goals
                            </h5>
                        </div>
                        
                        <div class="goals-item goals-clickable" data-info-type="goal">
                            <div class="goals-icon-wrapper goal-icon">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <div class="goals-content">
                                <div class="goals-label">Goal</div>
                                <div class="goals-value"><?php echo ucfirst(str_replace('_', ' ', $user['goal'])); ?></div>
                            </div>
                            <div class="goals-info-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                        </div>

                        <div class="goals-item goals-clickable" data-info-type="tdee">
                            <div class="goals-icon-wrapper target-icon">
                                <i class="fa-solid fa-fire"></i>
                            </div>
                            <div class="goals-content">
                                <div class="goals-label">Daily Target</div>
                                <div class="goals-value"><?php echo number_format($user['tdee']); ?> <span class="goals-unit">kcal</span></div>
                            </div>
                            <div class="goals-info-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                        </div>

                        <div class="goals-item goals-clickable" data-info-type="bmr">
                            <div class="goals-icon-wrapper bmr-icon">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </div>
                            <div class="goals-content">
                                <div class="goals-label">BMR</div>
                                <div class="goals-value"><?php echo number_format($user['bmr']); ?> <span class="goals-unit">kcal</span></div>
                            </div>
                            <div class="goals-info-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card progress-tracker-card">
                    <div class="card-body">
                        <div class="progress-tracker-header">
                            <h5 class="fw-bold mb-0">
                                <i class="fa-solid fa-chart-line progress-chart-icon"></i>
                                Daily Progress
                            </h5>
                        </div>
                        
                        <div class="progress-tracker-item">
                            <div class="progress-tracker-top">
                                <div class="progress-tracker-icon-wrapper">
                                    <i class="fa-solid fa-fire-flame-curved"></i>
                                </div>
                                <div class="progress-tracker-info">
                                    <div class="progress-tracker-label">Calories</div>
                                    <div class="progress-tracker-value">
                                        <?php echo number_format($today_log['total_calories']); ?> 
                                        <span class="progress-tracker-total">/ <?php echo number_format($user['tdee']); ?></span>
                                        <span class="progress-tracker-unit">kcal</span>
                                    </div>
                                </div>
                                <div class="progress-tracker-percentage">
                                    <?php echo number_format($calorie_percentage, 1); ?>%
                                </div>
                            </div>
                            
                            <div class="progress-tracker-bar-container">
                                <div class="progress-tracker-bar">
                                    <div class="progress-tracker-fill" style="width: <?php echo min($calorie_percentage, 100); ?>%">
                                        <div class="progress-tracker-shine"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress-tracker-status">
                                <?php if ($remaining_calories > 0): ?>
                                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                                    <span style="color: #10b981;"><?php echo number_format($remaining_calories); ?> kcal remaining</span>
                                <?php elseif ($remaining_calories < 0): ?>
                                    <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i>
                                    <span style="color: #f59e0b;"><?php echo number_format(abs($remaining_calories)); ?> kcal over goal</span>
                                <?php else: ?>
                                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                                    <span style="color: #10b981;">Perfect! Goal reached!</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card water-tracker-card mt-4" id="waterTrackerCard">
                    <div class="card-body">
                        <div class="water-header">
                            <h5 class="fw-bold mb-0">
                                <i class="fa-solid fa-droplet water-icon"></i>
                                Water Intake
                            </h5>
                            <button class="btn-water-reset" id="waterResetBtn" title="Reset water intake">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                        
                        <div class="water-display">
                            <div class="water-amount">
                                <span class="water-current" id="waterCurrent">0</span>
                                <span class="water-separator">/</span>
                                <span class="water-goal" id="waterGoal">2000</span>
                                <span class="water-unit">ml</span>
                            </div>
                            <div class="water-percentage" id="waterPercentage">0%</div>
                        </div>

                        <div class="water-progress-container">
                            <div class="water-progress-bar" id="waterProgressBar">
                                <div class="water-progress-fill" id="waterProgressFill" style="width: 0%">
                                    <div class="water-wave"></div>
                                </div>
                            </div>
                        </div>

                        <div class="water-buttons">
                            <button class="btn-water-add" id="addWater250" data-amount="250">
                                <i class="fa-solid fa-plus"></i>
                                <span class="btn-water-amount">250 ml</span>
                            </button>
                            <button class="btn-water-add" id="addWater500" data-amount="500">
                                <i class="fa-solid fa-plus"></i>
                                <span class="btn-water-amount">500 ml</span>
                            </button>
                        </div>

                        <div class="water-tips" id="waterTips">
                            <i class="fa-solid fa-lightbulb"></i>
                            <span>Stay hydrated throughout the day!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addFoodModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Food</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Meal Selection -->
                    <div class="meal-selector">
                        <label class="modal-label">Select Meal</label>
                        <select class="form-select modern-select" id="mealType">
                            <option value="breakfast">🌅 Breakfast</option>
                            <option value="lunch">🍽️ Lunch</option>
                            <option value="dinner">🌙 Dinner</option>
                            <option value="snack">🍪 Snack</option>
                        </select>
                    </div>

                    <!-- Food Search -->
                    <div class="food-search-section">
                        <label class="modal-label">Search Food</label>
                        <div class="search-box">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" class="form-control modern-input" id="foodSearch" placeholder="Type to search...">
                        </div>
                        <div id="foodResults" class="food-results-list"></div>
                    </div>

                    <!-- Selected Food Display -->
                    <div id="selectedFood" style="display: none;" class="selected-food-card">
                        <div class="food-header">
                            <h6 id="selectedFoodName"></h6>
                        </div>
                        <div class="food-macros-grid">
                            <div class="macro-item">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                                <span><strong id="selectedFoodCalories"></strong> kcal</span>
                            </div>
                            <div class="macro-item">
                                <i class="fa-solid fa-dumbbell"></i>
                                <span><strong id="selectedFoodProtein"></strong>g protein</span>
                            </div>
                            <div class="macro-item">
                                <i class="fa-solid fa-bolt"></i>
                                <span><strong id="selectedFoodCarbs"></strong>g carbs</span>
                            </div>
                            <div class="macro-item">
                                <i class="fa-solid fa-droplet"></i>
                                <span><strong id="selectedFoodFat"></strong>g fat</span>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Selector -->
                    <div id="unitSelectorContainer" style="display: none;" class="unit-selector-section">
                        <label class="modal-label">Unit</label>
                        <select class="form-select modern-select" id="unitSelector">
                            <option value="gram">gram</option>
                        </select>
                    </div>

                    <!-- Quantity Selection -->
                    <div id="quantityInput" style="display: none;" class="quantity-section">
                        <label class="modal-label" id="quantityLabel">Amount</label>
                        
                        <!-- Quick Amount Buttons -->
                        <div id="quickAmountButtons" class="quick-amounts"></div>
                        
                        <!-- Quantity Input -->
                        <div class="quantity-input-group">
                            <button class="qty-btn-modern" type="button" id="decreaseQty">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" class="form-control qty-input-modern" id="quantity" value="100" min="0.1" step="0.1">
                            <span class="qty-unit-modern" id="quantityUnit">g</span>
                            <button class="qty-btn-modern" type="button" id="increaseQty">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        
                        <!-- Calculated Info -->
                        <div id="calculatedInfo" class="calculated-info"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addFoodBtn" disabled>Add Food</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-body text-center p-5">
                    <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: #fee; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-trash" style="color: #dc3545; font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-3 fw-bold">Remove Food Item?</h5>
                    <p class="text-muted mb-4">Are you sure you want to remove this item from your log?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="waterResetModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(6, 182, 212, 0.3); overflow: hidden;">
                <div class="modal-body text-center p-5" style="background: linear-gradient(135deg, #e0f7ff 0%, #f0fbff 100%);">
                    <div class="water-modal-icon">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <h5 class="mb-3 fw-bold" style="color: #0891b2;">Reset Water Intake?</h5>
                    <p class="text-muted mb-4" style="font-size: 0.95rem;">Are you sure you want to reset your water intake for today?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                        <button type="button" class="btn btn-water-confirm px-4" id="confirmWaterResetBtn">
                            <i class="fa-solid fa-rotate-right me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="goalInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content info-modal-content">
                <div class="modal-body p-5">
                    <div class="info-modal-icon goal-modal-icon">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h5 class="mb-3 fw-bold text-center info-modal-title">Your Fitness Goal</h5>
                    <div class="info-modal-text">
                        <p><strong>What is it?</strong></p>
                        <p>Your fitness goal determines how your daily calorie target is calculated to help you achieve your desired body composition.</p>
                        
                        <p class="mt-3"><strong>Goal Types:</strong></p>
                        <ul>
                            <li><strong>Maintain:</strong> Keep your current weight. Your daily target equals your TDEE.</li>
                            <li><strong>Lose Weight:</strong> Create a calorie deficit (typically 300-500 kcal below TDEE) for gradual fat loss.</li>
                            <li><strong>Gain Muscle:</strong> Create a calorie surplus (typically 200-400 kcal above TDEE) for muscle growth.</li>
                        </ul>

                        <p class="mt-3"><strong>Impact on Calories:</strong></p>
                        <p>Your goal directly adjusts your daily calorie target from your baseline TDEE to support your fitness objectives safely and effectively.</p>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">Got it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tdeeInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content info-modal-content">
                <div class="modal-body p-5">
                    <div class="info-modal-icon tdee-modal-icon">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <h5 class="mb-3 fw-bold text-center info-modal-title">Daily Target (TDEE)</h5>
                    <div class="info-modal-text">
                        <p><strong>What is TDEE?</strong></p>
                        <p>TDEE (Total Daily Energy Expenditure) is the total number of calories you burn in a day, including all activities.</p>
                        
                        <p class="mt-3"><strong>How is it calculated?</strong></p>
                        <p>TDEE = BMR × Activity Level Multiplier</p>
                        <ul>
                            <li><strong>Sedentary:</strong> BMR × 1.2 (little to no exercise)</li>
                            <li><strong>Light:</strong> BMR × 1.375 (1-3 days/week)</li>
                            <li><strong>Moderate:</strong> BMR × 1.55 (3-5 days/week)</li>
                            <li><strong>Active:</strong> BMR × 1.725 (6-7 days/week)</li>
                            <li><strong>Very Active:</strong> BMR × 1.9 (intense daily training)</li>
                        </ul>

                        <p class="mt-3"><strong>Why it matters:</strong></p>
                        <p>This is your maintenance calories. Eating at this level maintains your current weight. Your goal then adjusts this number up or down.</p>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">Got it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bmrInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content info-modal-content">
                <div class="modal-body p-5">
                    <div class="info-modal-icon bmr-modal-icon">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <h5 class="mb-3 fw-bold text-center info-modal-title">BMR (Basal Metabolic Rate)</h5>
                    <div class="info-modal-text">
                        <p><strong>What is BMR?</strong></p>
                        <p>BMR is the number of calories your body needs to perform basic life-sustaining functions while at complete rest.</p>
                        
                        <p class="mt-3"><strong>How is it calculated?</strong></p>
                        <p>We use the Mifflin-St Jeor Equation:</p>
                        <div class="formula-box">
                            <p><strong>Men:</strong> (10 × weight in kg) + (6.25 × height in cm) - (5 × age) + 5</p>
                            <p><strong>Women:</strong> (10 × weight in kg) + (6.25 × height in cm) - (5 × age) - 161</p>
                        </div>

                        <p class="mt-3"><strong>What does it include?</strong></p>
                        <ul>
                            <li>Breathing and circulation</li>
                            <li>Cell production and repair</li>
                            <li>Nutrient processing</li>
                            <li>Brain and nerve function</li>
                        </ul>

                        <p class="mt-3"><strong>Important:</strong></p>
                        <p>Your BMR is the minimum calories needed. Never eat significantly below this number as it can harm your metabolism and health.</p>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">Got it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>


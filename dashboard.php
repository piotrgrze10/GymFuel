<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get today's date
$today = date('Y-m-d');

// Get or create today's log
$stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?");
$stmt->execute([$_SESSION['user_id'], $today]);
$today_log = $stmt->fetch();

if (!$today_log) {
    // Create a new log for today
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

// Get all food entries for today
$stmt = $pdo->prepare("
    SELECT fe.*, fd.image 
    FROM food_entries fe 
    JOIN food_database fd ON fe.food_id = fd.id 
    WHERE fe.log_id = ? 
    ORDER BY fe.created_at ASC
");
$stmt->execute([$today_log['id']]);
$food_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize entries by meal type
$entries_by_meal = [
    'breakfast' => [],
    'lunch' => [],
    'dinner' => [],
    'snack' => []
];

foreach ($food_entries as $entry) {
    $entries_by_meal[$entry['meal_type']][] = $entry;
}

// Calculate calories per meal
$meal_calories = [];
foreach ($entries_by_meal as $meal_type => $entries) {
    $meal_calories[$meal_type] = 0;
    foreach ($entries as $entry) {
        $meal_calories[$meal_type] += floatval($entry['calories']);
    }
}

// Calculate remaining calories
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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body style="padding-top: 76px;">
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 py-3" style="z-index: 1000;">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fa-solid fa-fire-flame-curved logo-icon"></i> 
                Gym<span class="blue-text">Fuel</span>
            </a>
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
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Calories Overview Card -->
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
                            <svg class="calorie-ring" width="220" height="220">
                                <circle cx="110" cy="110" r="90" stroke="#e9ecef" stroke-width="14" fill="none"></circle>
                                <circle cx="110" cy="110" r="90" stroke="<?php echo $calorie_percentage >= 100 ? '#dc3545' : '#0d6efd'; ?>" 
                                        stroke-width="14" fill="none" stroke-dasharray="<?php echo 2 * M_PI * 90; ?>"
                                        stroke-dashoffset="<?php echo 2 * M_PI * 90 * (1 - min($calorie_percentage, 100) / 100); ?>"
                                        stroke-linecap="round" transform="rotate(-90 110 110)">
                                </circle>
                            </svg>
                            <div class="calorie-ring-text">
                                <h2 class="mb-0" style="font-size: 2rem;"><?php echo number_format($today_log['total_calories']); ?></h2>
                                <p class="text-muted mb-1" style="font-size: 0.9rem;"><?php echo number_format($user['tdee']); ?> kcal goal</p>
                                <div class="remaining-calories">
                                    <?php if ($remaining_calories > 0): ?>
                                        <span class="text-success fw-bold"><?php echo number_format($remaining_calories); ?> left</span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold"><?php echo abs($remaining_calories); ?> over</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Macro Breakdown -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon protein">
                                        <i class="fa-solid fa-drumstick-bite"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_protein'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Protein</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon carbs">
                                        <i class="fa-solid fa-bread-slice"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_carbs'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Carbs</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="macro-card">
                                    <div class="macro-icon fat">
                                        <i class="fa-solid fa-oil-can"></i>
                                    </div>
                                    <h5 class="mb-1"><?php echo number_format($today_log['total_fat'], 1); ?>g</h5>
                                    <p class="text-muted small mb-0">Fat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meals Section -->
                <div class="meals-section">
                    <!-- Breakfast -->
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
                                            
                                            // Add 's' for plural if unit is not 'g' and quantity > 1
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                // Handle special cases
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

                    <!-- Lunch -->
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
                                            
                                            // Add 's' for plural if unit is not 'g' and quantity > 1
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                // Handle special cases
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

                    <!-- Dinner -->
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
                                            
                                            // Add 's' for plural if unit is not 'g' and quantity > 1
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                // Handle special cases
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

                    <!-- Snacks -->
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
                                            
                                            // Add 's' for plural if unit is not 'g' and quantity > 1
                                            if ($unit !== 'g' && floatval($entry['quantity']) > 1) {
                                                // Handle special cases
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

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Your Goals</h5>
                        <div class="info-item">
                            <i class="fa-solid fa-bullseye text-primary"></i>
                            <div>
                                <strong>Goal:</strong> <?php echo ucfirst(str_replace('_', ' ', $user['goal'])); ?>
                            </div>
                        </div>
                        <div class="info-item mt-3">
                            <i class="fa-solid fa-fire text-danger"></i>
                            <div>
                                <strong>Daily Target:</strong> <?php echo number_format($user['tdee']); ?> kcal
                            </div>
                        </div>
                        <div class="info-item mt-3">
                            <i class="fa-solid fa-heart-pulse text-success"></i>
                            <div>
                                <strong>BMR:</strong> <?php echo number_format($user['bmr']); ?> kcal
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Daily Progress</h5>
                        <div class="progress-item">
                            <small class="text-muted">Calories</small>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min($calorie_percentage, 100); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo number_format($calorie_percentage, 1); ?>% of goal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Food Modal -->
    <div class="modal fade" id="addFoodModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Food</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Meal</label>
                        <select class="form-select" id="mealType">
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Search Food</label>
                        <input type="text" class="form-control" id="foodSearch" placeholder="Search for food...">
                        <div id="foodResults" class="mt-3"></div>
                    </div>
                    <div id="selectedFood" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <h6 id="selectedFoodName"></h6>
                                <p class="small text-muted mb-0">
                                    Calories: <span id="selectedFoodCalories"></span>kcal | 
                                    Protein: <span id="selectedFoodProtein"></span>g | 
                                    Carbs: <span id="selectedFoodCarbs"></span>g | 
                                    Fat: <span id="selectedFoodFat"></span>g
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3" id="quantityInput" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" id="quantityLabel">Amount</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleUnit" style="display: none;">
                                <i class="fa-solid fa-arrows-rotate"></i> Switch to grams
                            </button>
                        </div>
                        <div id="quickAmountButtons" class="mb-3"></div>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="decreaseQty">-</button>
                            <input type="number" class="form-control text-center" id="quantity" value="100" min="1" step="1">
                            <span class="input-group-text" id="quantityUnit">g</span>
                            <button class="btn btn-outline-secondary" type="button" id="increaseQty">+</button>
                        </div>
                        <div id="calculatedInfo" class="mt-2 text-center" style="font-weight: 600; color: #0d6efd;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addFoodBtn" disabled>Add Food</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>


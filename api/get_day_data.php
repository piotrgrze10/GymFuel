<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    echo json_encode(['success' => false, 'error' => 'Invalid date format']);
    exit;
}

$date_limit_future = date('Y-m-d', strtotime('+30 days'));
$date_limit_past = date('Y-m-d', strtotime('-365 days'));

if ($selected_date > $date_limit_future || $selected_date < $date_limit_past) {
    echo json_encode(['success' => false, 'error' => 'Date out of range']);
    exit;
}

$today = date('Y-m-d');
$is_today = ($selected_date === $today);

$stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?");
$stmt->execute([$_SESSION['user_id'], $selected_date]);
$daily_log = $stmt->fetch();

if (!$daily_log) {
    $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, log_date) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $selected_date]);
    $daily_log = [
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
    SELECT fe.*, fd.image, fd.name as food_name
    FROM food_entries fe 
    JOIN food_database fd ON fe.food_id = fd.id 
    WHERE fe.log_id = ? 
    ORDER BY fe.created_at ASC
");
$stmt->execute([$daily_log['id']]);
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

$stmt = $pdo->prepare("SELECT tdee FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch();
$tdee = $user_data['tdee'];

$remaining_calories = $tdee - floatval($daily_log['total_calories']);
$calorie_percentage = $tdee > 0 ? ($daily_log['total_calories'] / $tdee) * 100 : 0;

$days_diff = (strtotime($selected_date) - strtotime($today)) / 86400;
$date_label = '';

if ($is_today) {
    $date_label = '<span class="today-badge">Today</span>';
} elseif ($days_diff == -1) {
    $date_label = 'Yesterday';
} elseif ($days_diff == 1) {
    $date_label = 'Tomorrow';
} elseif ($days_diff < 0) {
    $date_label = abs($days_diff) . ' days ago';
} else {
    $date_label = 'In ' . $days_diff . ' days';
}

$nutrition_label = $is_today ? "Today's Nutrition" : ($days_diff < 0 ? "Past Day Nutrition" : "Planned Nutrition");

echo json_encode([
    'success' => true,
    'date' => $selected_date,
    'formatted_date' => date('l, F j, Y', strtotime($selected_date)),
    'date_label' => $date_label,
    'nutrition_label' => $nutrition_label,
    'is_today' => $is_today,
    'daily_log' => $daily_log,
    'entries_by_meal' => $entries_by_meal,
    'meal_calories' => $meal_calories,
    'remaining_calories' => $remaining_calories,
    'calorie_percentage' => $calorie_percentage,
    'tdee' => $tdee
]);


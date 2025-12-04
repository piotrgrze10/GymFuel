<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/weight_history.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$range = isset($_GET['range']) ? $_GET['range'] : '7';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

$end_date = date('Y-m-d');
switch ($range) {
    case '7':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case '30':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'all':
        $start_date = date('Y-m-d', strtotime('-365 days'));
        break;
    default:
        $start_date = date('Y-m-d', strtotime('-7 days'));
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            log_date,
            total_calories,
            total_protein,
            total_carbs,
            total_fat,
            water_intake
        FROM daily_logs 
        WHERE user_id = ? 
        AND log_date >= ? 
        AND log_date <= ?
        ORDER BY log_date ASC
    ");
    $stmt->execute([$user_id, $start_date, $end_date]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $energy_data = [];
    $weight_data = [];
    $macros_data = [];
    $water_data = [];

    foreach ($logs as $log) {
        $date = $log['log_date'];
        $date_formatted = date('M j', strtotime($date));
        
        $energy_data[] = [
            'date' => $date,
            'date_formatted' => $date_formatted,
            'calories' => floatval($log['total_calories'])
        ];

        $macros_data[] = [
            'date' => $date,
            'date_formatted' => $date_formatted,
            'protein' => floatval($log['total_protein']),
            'carbs' => floatval($log['total_carbs']),
            'fat' => floatval($log['total_fat'])
        ];

        $water_data[] = [
            'date' => $date,
            'date_formatted' => $date_formatted,
            'water' => intval($log['water_intake'])
        ];
    }
    
    $weight_entries = fetchWeightHistory($pdo, $user_id, $start_date, $end_date);
    if (empty($weight_entries)) {
        $weight_entries[] = [
            'weight' => floatval($user['weight']),
            'recorded_at' => $end_date . ' 23:59:59'
        ];
    }

    $weight_data = [];
    foreach ($weight_entries as $entry) {
        $entryDate = substr($entry['recorded_at'], 0, 10);
        $weight_data[] = [
            'date' => $entryDate,
            'date_formatted' => date('M j', strtotime($entryDate)),
            'weight' => floatval($entry['weight'])
        ];
    }

    $height_m = $user['height'] / 100;
    $current_bmi = $user['weight'] / ($height_m * $height_m);
    
    if (empty($logs)) {
        echo json_encode([
            'success' => true,
            'range' => $range,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'energy_data' => [],
            'weight_data' => [],
            'macros_data' => [],
            'water_data' => [],
            'user_info' => [
                'current_weight' => floatval($user['weight']),
                'current_bmi' => round($current_bmi, 1),
                'tdee' => floatval($user['tdee'])
            ]
        ]);
        exit();
    }

    echo json_encode([
        'success' => true,
        'range' => $range,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'energy_data' => $energy_data,
        'weight_data' => $weight_data,
        'macros_data' => $macros_data,
            'water_data' => $water_data,
        'user_info' => [
                'current_weight' => floatval($user['weight']),
                'current_bmi' => round($current_bmi, 1),
                'tdee' => floatval($user['tdee'])
        ]
    ]);

} catch(PDOException $e) {
    error_log("Database error in get_charts_data: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => getSafeErrorMessage($e)]);
}


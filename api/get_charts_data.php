<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

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
    $bmi_data = [];

    $height_m = $user['height'] / 100;
    $current_bmi = $user['weight'] / ($height_m * $height_m);
    
    $body_fat_percentage = 15;
    $lean_body_mass = $user['weight'] * (1 - $body_fat_percentage / 100);
    $current_ffmi = $lean_body_mass / ($height_m * $height_m);

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

        $weight_data[] = [
            'date' => $date,
            'date_formatted' => $date_formatted,
            'weight' => floatval($user['weight'])
        ];

        $bmi_data[] = [
            'date' => $date,
            'date_formatted' => $date_formatted,
            'bmi' => round($current_bmi, 1),
            'ffmi' => round($current_ffmi, 1)
        ];
    }

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
            'bmi_data' => [],
            'user_info' => [
                'current_weight' => floatval($user['weight']),
                'current_bmi' => round($current_bmi, 1),
                'current_ffmi' => round($current_ffmi, 1),
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
        'bmi_data' => $bmi_data,
        'user_info' => [
            'current_weight' => floatval($user['weight']),
            'current_bmi' => round($current_bmi, 1),
            'current_ffmi' => round($current_ffmi, 1),
            'tdee' => floatval($user['tdee'])
        ]
    ]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


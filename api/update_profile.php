<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    require_once '../includes/config.php';
    require_once '../includes/auth.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server configuration error: ' . $e->getMessage()]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid input - unable to parse JSON'
    ]);
    exit;
}

$firstName = trim($input['firstName'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$weight = floatval($input['weight'] ?? 0);
$height = floatval($input['height'] ?? 0);
$age = intval($input['age'] ?? 0);
$activityLevel = $input['activityLevel'] ?? '';
$goal = $input['goal'] ?? '';

if (empty($firstName) || empty($lastName)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
    exit;
}

if ($weight < 20 || $weight > 300) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter weight between 20-300 kg']);
    exit;
}

if ($height < 50 || $height > 250) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter height between 50-250 cm']);
    exit;
}

if ($age < 13 || $age > 120) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid age (13-120)']);
    exit;
}

$validActivityLevels = ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extra_active'];
if (!in_array($activityLevel, $validActivityLevels)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid activity level']);
    exit;
}

$validGoals = ['lose_weight', 'maintain', 'gain_weight'];
if (!in_array($goal, $validGoals)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid goal']);
    exit;
}

$stmt = $pdo->prepare("SELECT gender FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
    ob_end_clean();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$bmr = calculateBMR($weight, $height, $age, $currentUser['gender']);
$tdee = calculateTDEE($bmr, $activityLevel);
$finalCalories = adjustForGoal($tdee, $goal);

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET first_name = ?, 
            last_name = ?, 
            weight = ?, 
            height = ?, 
            age = ?, 
            activity_level = ?, 
            goal = ?, 
            bmr = ?, 
            tdee = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $firstName,
        $lastName,
        $weight,
        $height,
        $age,
        $activityLevel,
        $goal,
        $bmr,
        $finalCalories,
        $_SESSION['user_id']
    ]);
    
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
    $_SESSION['user_calories'] = $finalCalories;
    $_SESSION['user_bmr'] = $bmr;
    
    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' => [
            'bmr' => round($bmr),
            'tdee' => round($finalCalories),
            'name' => $firstName . ' ' . $lastName
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Profile update database error: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    error_log('Profile update error: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating profile. Please try again.'
    ]);
}
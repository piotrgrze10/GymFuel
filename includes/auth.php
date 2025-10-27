<?php
require_once 'config.php';

function calculateBMR($weight, $height, $age, $gender) {
    if ($gender === 'male') {
        return 10 * $weight + 6.25 * $height - 5 * $age + 5;
    } else {
        return 10 * $weight + 6.25 * $height - 5 * $age - 161;
    }
}

$activity_multipliers = [
    'sedentary' => 1.2,
    'lightly_active' => 1.375,
    'moderately_active' => 1.55,
    'very_active' => 1.725,
    'extra_active' => 1.9
];

function calculateTDEE($bmr, $activity_level) {
    global $activity_multipliers;
    return $bmr * $activity_multipliers[$activity_level];
}

function adjustForGoal($tdee, $goal) {
    $adjustments = [
        'lose_weight' => -500,
        'maintain' => 0,
        'gain_weight' => 500
    ];
    
    return $tdee + $adjustments[$goal];
}

function registerUser($data) {
    global $pdo;
    
    try {

        $bmr = calculateBMR($data['weight'], $data['height'], $data['age'], $data['gender']);

        $tdee = calculateTDEE($bmr, $data['activity_level']);

        $final_calories = adjustForGoal($tdee, $data['goal']);

        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, first_name, last_name, gender, age, height, weight, activity_level, goal, bmr, tdee) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['email'],
            $hashed_password,
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['age'],
            $data['height'],
            $data['weight'],
            $data['activity_level'],
            $data['goal'],
            $bmr,
            $final_calories
        ]);
        
        return ['success' => true, 'user_id' => $pdo->lastInsertId(), 'calories' => $final_calories, 'bmr' => $bmr];
    } catch(PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function loginUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_calories'] = $user['tdee'];
            $_SESSION['user_bmr'] = $user['bmr'];
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
    } catch(PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function logoutUser() {
    session_destroy();
    header('Location: login.php');
    exit();
}

function getUserByEmail($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return false;
    }
}
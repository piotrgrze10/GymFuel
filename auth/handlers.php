<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
        echo json_encode(['success' => false, 'error' => 'Invalid request token']);
        exit();
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'reset_flow') {
        foreach (array_keys($_SESSION) as $k) {
            if (strpos($k, 'reg_') === 0) {
                unset($_SESSION[$k]);
            }
        }
        echo json_encode(['success' => true]);
        exit();
    }
    if ($action === 'step1') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($email) || empty($password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid email address']);
            exit();
        }
        
        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'error' => 'Passwords do not match']);
            exit();
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
            exit();
        }

        if (getUserByEmail($email)) {
            echo json_encode(['success' => false, 'error' => 'Email already registered']);
            exit();
        }

        $_SESSION['reg_email'] = $email;
        $_SESSION['reg_password'] = $password;
        $_SESSION['reg_step'] = 1;
        session_write_close();
        session_start();
        echo json_encode(['success' => true]);
    } elseif ($action === 'step2') {
        if (!isset($_SESSION['reg_email']) || !isset($_SESSION['reg_password'])) {
            echo json_encode(['success' => false, 'error' => 'Please complete step 1 first']);
            exit();
        }
        
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $gender = $_POST['gender'] ?? '';
        
        if (empty($first_name) || empty($last_name) || empty($gender)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit();
        }
        
        if (!in_array($gender, ['male', 'female', 'other'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid gender']);
            exit();
        }
        
        $_SESSION['reg_first_name'] = $first_name;
        $_SESSION['reg_last_name'] = $last_name;
        $_SESSION['reg_gender'] = $gender;
        $_SESSION['reg_step'] = 2;
        session_write_close();
        session_start();
        echo json_encode(['success' => true]);
    } elseif ($action === 'step3') {
        if (!isset($_SESSION['reg_first_name']) || !isset($_SESSION['reg_last_name']) || !isset($_SESSION['reg_gender'])) {
            echo json_encode(['success' => false, 'error' => 'Please complete previous steps first']);
            exit();
        }
        
        $age = intval($_POST['age'] ?? 0);
        $height = floatval($_POST['height'] ?? 0);
        $weight = floatval($_POST['weight'] ?? 0);
        
        if ($age < 13 || $age > 120) {
            echo json_encode(['success' => false, 'error' => 'Please enter a valid age (13-120)']);
            exit();
        }
        
        if ($height < 50 || $height > 250) {
            echo json_encode(['success' => false, 'error' => 'Please enter height between 50-250 cm']);
            exit();
        }
        
        if ($weight < 20 || $weight > 300) {
            echo json_encode(['success' => false, 'error' => 'Please enter weight between 20-300 kg']);
            exit();
        }
        
        $_SESSION['reg_age'] = $age;
        $_SESSION['reg_height'] = $height;
        $_SESSION['reg_weight'] = $weight;
        $_SESSION['reg_step'] = 3;
        session_write_close();
        session_start();
        echo json_encode(['success' => true]);
    } elseif ($action === 'step4') {
        if (!isset($_SESSION['reg_age']) || !isset($_SESSION['reg_height']) || !isset($_SESSION['reg_weight'])) {
            echo json_encode(['success' => false, 'error' => 'Please complete previous steps first']);
            exit();
        }
        
        $activity_level = $_POST['activity_level'] ?? '';
        
        $valid_levels = ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extra_active'];
        if (!in_array($activity_level, $valid_levels)) {
            echo json_encode(['success' => false, 'error' => 'Invalid activity level']);
            exit();
        }
        
        $_SESSION['reg_activity_level'] = $activity_level;
        $_SESSION['reg_step'] = 4;
        session_write_close();
        session_start();
        echo json_encode(['success' => true]);
    } elseif ($action === 'step5') {
        if (!isset($_SESSION['reg_activity_level'])) {
            echo json_encode(['success' => false, 'error' => 'Please complete previous steps first']);
            exit();
        }
        
        $goal = $_POST['goal'] ?? '';
        
        $valid_goals = ['lose_weight', 'maintain', 'gain_weight'];
        if (!in_array($goal, $valid_goals)) {
            echo json_encode(['success' => false, 'error' => 'Invalid goal selection']);
            exit();
        }

        $reg_data = [
            'email' => $_SESSION['reg_email'],
            'password' => $_SESSION['reg_password'],
            'first_name' => $_SESSION['reg_first_name'],
            'last_name' => $_SESSION['reg_last_name'],
            'gender' => $_SESSION['reg_gender'],
            'age' => $_SESSION['reg_age'],
            'height' => $_SESSION['reg_height'],
            'weight' => $_SESSION['reg_weight'],
            'activity_level' => $_SESSION['reg_activity_level'],
            'goal' => $goal
        ];

        $result = registerUser($reg_data);
        if ($result['success']) {
            foreach (array_keys($_SESSION) as $k) {
                if (strpos($k, 'reg_') === 0) {
                    unset($_SESSION[$k]);
                }
            }
            echo json_encode([
                'success' => true,
                'calories' => $result['calories'],
                'bmr' => $result['bmr']
            ]);
        } else {
            echo json_encode($result);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
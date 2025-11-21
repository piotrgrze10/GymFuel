<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'search_food') {
        $search_term = $_POST['search_term'] ?? '';
        
        if (empty($search_term)) {
            echo json_encode(['success' => false, 'error' => 'Search term is required']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM food_database 
                WHERE name LIKE ? 
                ORDER BY name 
                LIMIT 20
            ");
            $stmt->execute(["%$search_term%"]);
            $foods = $stmt->fetchAll();
            
            $results = [];
            foreach ($foods as $food) {
                $stmt_units = $pdo->prepare("
                    SELECT * FROM product_units 
                    WHERE food_id = ? 
                    ORDER BY display_order ASC
                ");
                $stmt_units->execute([$food['id']]);
                $units = $stmt_units->fetchAll(PDO::FETCH_ASSOC);
                
                $food['available_units'] = $units;
                $results[] = $food;
            }
            
            echo json_encode(['success' => true, 'results' => $results]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } elseif ($action === 'add_food') {
        $food_id = intval($_POST['food_id'] ?? 0);
        $meal_type = $_POST['meal_type'] ?? '';
        $quantity = floatval($_POST['quantity'] ?? 1);
        $selected_unit = $_POST['selected_unit'] ?? 'gram';
        $log_date = trim($_POST['log_date'] ?? date('Y-m-d'));
        
        if ($food_id <= 0 || empty($meal_type)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit();
        }
        
        $valid_meals = ['breakfast', 'lunch', 'dinner', 'snack'];
        if (!in_array($meal_type, $valid_meals)) {
            echo json_encode(['success' => false, 'error' => 'Invalid meal type']);
            exit();
        }

        $log_date_obj = DateTime::createFromFormat('Y-m-d', $log_date);
        if (!$log_date_obj || $log_date_obj->format('Y-m-d') !== $log_date) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            exit();
        }

        $date_limit_future = (new DateTime('+30 days'))->format('Y-m-d');
        $date_limit_past = (new DateTime('-365 days'))->format('Y-m-d');
        if ($log_date > $date_limit_future || $log_date < $date_limit_past) {
            echo json_encode(['success' => false, 'error' => 'Date out of range']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM food_database WHERE id = ?");
            $stmt->execute([$food_id]);
            $food = $stmt->fetch();
            
            if (!$food) {
                echo json_encode(['success' => false, 'error' => 'Food not found']);
                exit();
            }
            
            $stmt_unit = $pdo->prepare("
                SELECT * FROM product_units 
                WHERE food_id = ? AND unit_name = ?
            ");
            $stmt_unit->execute([$food_id, $selected_unit]);
            $unit_info = $stmt_unit->fetch();
            
            if (!$unit_info) {
                if ($food['unit_type'] === 'pieces') {
                    $weight_in_grams = $quantity * $food['weight_per_unit'];
                } else {
                    $weight_in_grams = $quantity;
                }
                $display_unit = $selected_unit;
            } else {
                $weight_in_grams = $quantity * $unit_info['weight_in_grams'];
                $display_unit = $unit_info['unit_display'];
            }
            
            $stmt = $pdo->prepare("SELECT id FROM daily_logs WHERE user_id = ? AND log_date = ?");
            $stmt->execute([$_SESSION['user_id'], $log_date]);
            $log = $stmt->fetch();
            
            if (!$log) {
                $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, log_date) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $log_date]);
                $log_id = $pdo->lastInsertId();
            } else {
                $log_id = $log['id'];
            }
            
            $multiplier = $weight_in_grams / 100;
            
            $calories = $food['calories'] * $multiplier;
            $protein = $food['protein'] * $multiplier;
            $carbs = $food['carbs'] * $multiplier;
            $fat = $food['fat'] * $multiplier;
            $fiber = $food['fiber'] * $multiplier;
            $sugar = $food['sugar'] * $multiplier;
            
            $stmt = $pdo->prepare("
                INSERT INTO food_entries 
                (log_id, food_id, food_name, meal_type, quantity, quantity_unit, calories, protein, carbs, fat, fiber, sugar) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $log_id,
                $food_id,
                $food['name'],
                $meal_type,
                $quantity,
                $display_unit,
                $calories,
                $protein,
                $carbs,
                $fat,
                $fiber,
                $sugar
            ]);
            
            $entry_id = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("
                UPDATE daily_logs 
                SET total_calories = total_calories + ?, 
                    total_protein = total_protein + ?, 
                    total_carbs = total_carbs + ?, 
                    total_fat = total_fat + ?, 
                    total_fiber = total_fiber + ? 
                WHERE id = ?
            ");
            $stmt->execute([$calories, $protein, $carbs, $fat, $fiber, $log_id]);
            
            $stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE id = ?");
            $stmt->execute([$log_id]);
            $updated_log = $stmt->fetch();
            
            echo json_encode([
                'success' => true, 
                'entry_id' => $entry_id,
                'calories' => $calories,
                'totals' => [
                    'calories' => floatval($updated_log['total_calories']),
                    'protein' => floatval($updated_log['total_protein']),
                    'carbs' => floatval($updated_log['total_carbs']),
                    'fat' => floatval($updated_log['total_fat'])
                ]
            ]);
            
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } elseif ($action === 'remove_food') {
        $entry_id = intval($_POST['entry_id'] ?? 0);
        
        if ($entry_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid entry ID']);
            exit();
        }
        
        try {
            
            $stmt = $pdo->prepare("
                SELECT fe.*, dl.user_id 
                FROM food_entries fe 
                JOIN daily_logs dl ON fe.log_id = dl.id 
                WHERE fe.id = ?
            ");
            $stmt->execute([$entry_id]);
            $entry = $stmt->fetch();
            
            if (!$entry) {
                echo json_encode(['success' => false, 'error' => 'Entry not found']);
                exit();
            }
            
            if ($entry['user_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit();
            }
            
            $stmt = $pdo->prepare("
                UPDATE daily_logs 
                SET total_calories = total_calories - ?, 
                    total_protein = total_protein - ?, 
                    total_carbs = total_carbs - ?, 
                    total_fat = total_fat - ?, 
                    total_fiber = total_fiber - ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $entry['calories'], 
                $entry['protein'], 
                $entry['carbs'], 
                $entry['fat'], 
                $entry['fiber'], 
                $entry['log_id']
            ]);
            
            $stmt = $pdo->prepare("DELETE FROM food_entries WHERE id = ?");
            $stmt->execute([$entry_id]);
            
            $stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE id = ?");
            $stmt->execute([$entry['log_id']]);
            $updated_log = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'totals' => [
                    'calories' => floatval($updated_log['total_calories']),
                    'protein' => floatval($updated_log['total_protein']),
                    'carbs' => floatval($updated_log['total_carbs']),
                    'fat' => floatval($updated_log['total_fat'])
                ]
            ]);
            
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } elseif ($action === 'update_water') {
        $date = $_POST['date'] ?? date('Y-m-d');
        $water_intake = intval($_POST['water_intake'] ?? 0);
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            exit();
        }
        
        if ($water_intake < 0 || $water_intake > 10000) {
            echo json_encode(['success' => false, 'error' => 'Invalid water intake value']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("SELECT id FROM daily_logs WHERE user_id = ? AND log_date = ?");
            $stmt->execute([$_SESSION['user_id'], $date]);
            $log = $stmt->fetch();
            
            if (!$log) {
                $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, log_date, water_intake) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $date, $water_intake]);
            } else {
                $stmt = $pdo->prepare("UPDATE daily_logs SET water_intake = ? WHERE id = ?");
                $stmt->execute([$water_intake, $log['id']]);
            }
            
            echo json_encode(['success' => true, 'water_intake' => $water_intake]);
            
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
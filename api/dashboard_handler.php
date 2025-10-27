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
            $results = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'results' => $results]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        
    } elseif ($action === 'add_food') {
        $food_id = intval($_POST['food_id'] ?? 0);
        $meal_type = $_POST['meal_type'] ?? '';
        $quantity = floatval($_POST['quantity'] ?? 1);
        
        if ($food_id <= 0 || empty($meal_type)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit();
        }
        
        $valid_meals = ['breakfast', 'lunch', 'dinner', 'snack'];
        if (!in_array($meal_type, $valid_meals)) {
            echo json_encode(['success' => false, 'error' => 'Invalid meal type']);
            exit();
        }
        
        try {
            // Get food details
            $stmt = $pdo->prepare("SELECT * FROM food_database WHERE id = ?");
            $stmt->execute([$food_id]);
            $food = $stmt->fetch();
            
            if (!$food) {
                echo json_encode(['success' => false, 'error' => 'Food not found']);
                exit();
            }
            
            // Get or create today's log
            $today = date('Y-m-d');
            $stmt = $pdo->prepare("SELECT id FROM daily_logs WHERE user_id = ? AND log_date = ?");
            $stmt->execute([$_SESSION['user_id'], $today]);
            $log = $stmt->fetch();
            
            if (!$log) {
                $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, log_date) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $today]);
                $log_id = $pdo->lastInsertId();
            } else {
                $log_id = $log['id'];
            }
            
            // Calculate values based on quantity
            // If unit_type is 'pieces', quantity is the number of pieces
            // If unit_type is 'grams', quantity is grams and we need to convert to 100g base
            if ($food['unit_type'] === 'pieces') {
                // For pieces: quantity is the actual count
                $multiplier = $quantity;
            } else {
                // For grams: convert to 100g base
                $multiplier = $quantity / 100;
            }
            
            $calories = $food['calories'] * $multiplier;
            $protein = $food['protein'] * $multiplier;
            $carbs = $food['carbs'] * $multiplier;
            $fat = $food['fat'] * $multiplier;
            $fiber = $food['fiber'] * $multiplier;
            $sugar = $food['sugar'] * $multiplier;
            
            // Determine unit for display
            $display_unit = $food['unit_name'] ?? 'g';
            if ($food['unit_type'] !== 'pieces') {
                $display_unit = 'g';
            }
            
            // Insert food entry
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
            
            // Update daily log totals
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
            
            // Get updated totals
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
            // Get entry details and verify ownership
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
            
            // Update daily log totals
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
            
            // Delete entry
            $stmt = $pdo->prepare("DELETE FROM food_entries WHERE id = ?");
            $stmt->execute([$entry_id]);
            
            // Get updated totals
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
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}


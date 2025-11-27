<?php

function ensureWeightHistoryTable(PDO $pdo): void
{
    static $initialized = false;

    if ($initialized) {
        return;
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS weight_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            weight DECIMAL(6,2) NOT NULL,
            recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_weight_history_user_date (user_id, recorded_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $initialized = true;
}

function logWeightSnapshot(PDO $pdo, int $userId, float $weight): void
{
    ensureWeightHistoryTable($pdo);

    $stmt = $pdo->prepare("
        INSERT INTO weight_history (user_id, weight) 
        VALUES (?, ?)
    ");
    $stmt->execute([$userId, round($weight, 2)]);
}

function fetchWeightHistory(PDO $pdo, int $userId, string $startDate, string $endDate): array
{
    ensureWeightHistoryTable($pdo);

    $stmt = $pdo->prepare("
        SELECT weight, recorded_at
        FROM weight_history
        WHERE user_id = ?
        AND recorded_at >= ?
        AND recorded_at <= ?
        ORDER BY recorded_at ASC
    ");

    $stmt->execute([
        $userId,
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59'
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


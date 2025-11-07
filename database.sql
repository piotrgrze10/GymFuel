CREATE DATABASE IF NOT EXISTS gymfuel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gymfuel;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT NOT NULL,
    height DECIMAL(5,2) NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    activity_level VARCHAR(50) NOT NULL,
    goal VARCHAR(50) NOT NULL,
    bmr DECIMAL(10,2) NOT NULL,
    tdee DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS registration_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255),
    registration_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS food_database (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    calories DECIMAL(8,2) NOT NULL,
    protein DECIMAL(8,2) NOT NULL DEFAULT 0,
    carbs DECIMAL(8,2) NOT NULL DEFAULT 0,
    fat DECIMAL(8,2) NOT NULL DEFAULT 0,
    fiber DECIMAL(8,2) DEFAULT 0,
    sugar DECIMAL(8,2) DEFAULT 0,
    unit_type ENUM('grams', 'pieces') DEFAULT 'grams',
    unit_name VARCHAR(50) DEFAULT 'g',
    weight_per_unit DECIMAL(8,2) DEFAULT 100.00,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS daily_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    log_date DATE NOT NULL,
    total_calories DECIMAL(10,2) DEFAULT 0,
    total_protein DECIMAL(10,2) DEFAULT 0,
    total_carbs DECIMAL(10,2) DEFAULT 0,
    total_fat DECIMAL(10,2) DEFAULT 0,
    total_fiber DECIMAL(10,2) DEFAULT 0,
    water_intake INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS food_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id INT NOT NULL,
    food_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    quantity DECIMAL(8,2) NOT NULL,
    quantity_unit VARCHAR(50) DEFAULT 'g',
    calories DECIMAL(10,2) NOT NULL,
    protein DECIMAL(10,2) NOT NULL,
    carbs DECIMAL(10,2) NOT NULL,
    fat DECIMAL(10,2) NOT NULL,
    fiber DECIMAL(10,2) DEFAULT 0,
    sugar DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (log_id) REFERENCES daily_logs(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food_database(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_session_id ON registration_sessions(session_id);
CREATE INDEX idx_log_date ON daily_logs(log_date);
CREATE INDEX idx_log_user ON daily_logs(user_id);
CREATE INDEX idx_entry_log ON food_entries(log_id);

INSERT INTO food_database (name, calories, protein, carbs, fat, fiber, sugar, unit_type, unit_name, weight_per_unit) VALUES
('Apple', 52, 0.3, 14, 0.2, 2.4, 10.4, 'grams', 'g', 100.00),
('Banana', 89, 1.1, 23, 0.3, 2.6, 12.2, 'grams', 'g', 100.00),
('Chicken Breast', 165, 31, 0, 3.6, 0, 0, 'grams', 'g', 100.00),
('Brown Rice', 111, 2.6, 23, 0.9, 1.8, 0.4, 'grams', 'g', 100.00),
('Large Egg', 70, 6, 0.6, 5, 0, 0.6, 'pieces', 'egg', 50.00),
('Oats', 389, 17, 66, 7, 11, 0.8, 'grams', 'g', 100.00),
('Spinach', 23, 2.9, 3.6, 0.4, 2.2, 0.4, 'grams', 'g', 100.00),
('Salmon', 206, 25, 0, 12, 0, 0, 'grams', 'g', 100.00),
('Greek Yogurt', 59, 10, 3.6, 0.4, 0, 3.6, 'grams', 'g', 100.00),
('Broccoli', 34, 2.8, 7, 0.4, 2.6, 1.5, 'grams', 'g', 100.00),
('Sweet Potato', 86, 1.6, 20, 0.1, 3, 4.2, 'grams', 'g', 100.00),
('Whole Wheat Bread', 69, 3.6, 12, 1, 1.9, 2.4, 'pieces', 'slice', 28.00),
('Peanut Butter', 94, 4, 3, 8, 1, 1.4, 'pieces', 'tbsp', 16.00),
('Almonds', 161, 6, 6.1, 14, 3.5, 1.2, 'pieces', 'serving', 28.00),
('Avocado', 160, 2, 8.5, 14.7, 6.7, 0.7, 'pieces', 'avocado', 200.00),
('Milk', 149, 8, 12, 8, 0, 12, 'pieces', 'cup', 240.00),
('Orange', 47, 0.9, 12, 0.1, 2.4, 9, 'pieces', 'orange', 130.00),
('Grapes', 69, 0.6, 18, 0.2, 0.9, 16, 'grams', 'g', 100.00);
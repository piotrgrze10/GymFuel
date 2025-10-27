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
    expires_at TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_session_id ON registration_sessions(session_id);
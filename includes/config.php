<?php

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $env_file = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_file as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Database configuration - uses environment variables or defaults to local development
define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');
define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'gymfuel');

// Environment setting (production, development, staging)
define('APP_ENV', $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));

// Error reporting based on environment
if (APP_ENV === 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Set session save path - create directory if it doesn't exist
$session_path = __DIR__ . '/../sessions';
if (!file_exists($session_path)) {
    mkdir($session_path, 0755, true);
}
ini_set('session.save_path', $session_path);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set secure session cookie parameters in production
if (APP_ENV === 'production') {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    if (APP_ENV === 'production') {
        die("Database connection failed. Please contact the administrator.");
    } else {
        die("Connection failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: /dashboard');
        exit();
    }
}

/**
 * Safe error message handler - prevents exposing sensitive information in production
 */
function getSafeErrorMessage($exception, $defaultMessage = 'An error occurred. Please try again.') {
    if (APP_ENV === 'production') {
        error_log("Error: " . $exception->getMessage());
        return $defaultMessage;
    } else {
        return $exception->getMessage();
    }
}

/**
 * Generates cache-busting version parameter based on file modification time
 * This ensures browsers always load the latest version of CSS/JS files
 * 
 * @param string $file_path Relative path to the file (e.g., 'css/main.css')
 * @return string URL with version parameter (e.g., 'css/main.css?v=1234567890')
 */
function asset($file_path) {
    $full_path = __DIR__ . '/../' . ltrim($file_path, '/');
    
    // Check if file exists
    if (file_exists($full_path)) {
        // Use file modification time as version - changes automatically when file is updated
        $version = filemtime($full_path);
        $separator = strpos($file_path, '?') !== false ? '&' : '?';
        return $file_path . $separator . 'v=' . $version;
    }
    
    // If file doesn't exist, return original path
    return $file_path;
}
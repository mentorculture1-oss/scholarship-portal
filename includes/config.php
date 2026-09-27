<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'scholarship_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// APPLICATION CONFIGURATION
// ============================================
define('APP_NAME', 'Online Scholarship Portal');
define('APP_URL', 'http://localhost/scholarship-portal/');
define('APP_VERSION', '1.0.0');

// ============================================
// FILE UPLOAD CONFIGURATION
// ============================================
define('UPLOAD_DIR', 'C:/xampp/htdocs/scholarship-portal/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// ============================================
// SESSION CONFIGURATION
// ============================================
define('SESSION_LIFETIME', 3600);

// ============================================
// TIMEZONE
// ============================================
date_default_timezone_set('Africa/Nairobi');

// ============================================
// ERROR REPORTING
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// START SESSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// DATABASE CONNECTION (PDO)
// ============================================
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// ============================================
// LOAD HELPERS
// ============================================
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/notification_functions.php';
?>
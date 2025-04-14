<?php
// Include the main configuration file
require_once __DIR__ . '/../config.php';

// Database configuration - preventing redefinition errors
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'golden_cloud'); // Changed from golden_resort

// Display errors only during development
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Create connection only if it doesn't already exist
if (!isset($GLOBALS['db_connection']) || !$GLOBALS['db_connection']) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    // Check connection
    if ($conn->connect_error) {
        if (DEBUG_MODE) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            die("Database connection error. Please contact the administrator.");
        }
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        // Database created successfully or already exists
    } else {
        if (DEBUG_MODE) {
            echo "Error creating database: " . $conn->error;
        } else {
            echo "Database configuration error. Please contact the administrator.";
        }
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create users table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        if (DEBUG_MODE) {
            echo "Error creating users table: " . $conn->error;
        } else {
            echo "Database configuration error. Please contact the administrator.";
        }
    }
    
    // Create gallery table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS gallery (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        if (DEBUG_MODE) {
            echo "Error creating gallery table: " . $conn->error;
        } else {
            echo "Database configuration error. Please contact the administrator.";
        }
    }
    
    // Create settings table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        setting_name VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        if (DEBUG_MODE) {
            echo "Error creating settings table: " . $conn->error;
        } else {
            echo "Database configuration error. Please contact the administrator.";
        }
    }
    
    // Insert default settings if not exists
    $default_settings = [
        ['primary_color', '#D4AF37'],
        ['background_color', '#FDF5E6'],
        ['site_title', 'Golden Cloud'],
        ['contact_phone', '+1234567890'],
        ['contact_email', 'info@goldencloud.com'],
        ['contact_whatsapp', '+1234567890'],
        ['address', '123 Golden Avenue, Beach City'],
        ['site_language', 'ar'], // Setting Arabic as default language
        ['about_image', 'assets/images/about.jpg'], // Default about image
        ['site_logo', 'assets/images/logo.png'], // Default logo image
        ['hero_image', 'assets/images/hero-bg.jpg'] // Default hero/home background image
    ];
    
    foreach ($default_settings as $setting) {
        $check = $conn->query("SELECT * FROM settings WHERE setting_name = '{$setting[0]}'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO settings (setting_name, setting_value) VALUES ('{$setting[0]}', '{$setting[1]}')");
        }
    }
    
    // Create default admin user if not exists
    $check = $conn->query("SELECT * FROM users WHERE username = 'admin'");
    if ($check->num_rows == 0) {
        // Password is 'admin123' - do not use in production!
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password, email, role) VALUES ('admin', '$hashed_password', 'admin@goldencloud.com', 'admin')");
    }
    
    // Store connection in global variable to prevent multiple connections
    $GLOBALS['db_connection'] = $conn;
} else {
    $conn = $GLOBALS['db_connection'];
}

return $conn;
?>

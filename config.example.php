<?php
/**
 * Sample configuration file for Golden Cloud Resort
 * Copy this file to config.php and update with your actual database credentials
 */

// Database configuration
define('DB_HOST', 'localhost');       // Database host
define('DB_USER', 'your_db_username'); // Database username
define('DB_PASS', 'your_db_password'); // Database password
define('DB_NAME', 'your_db_name');    // Database name

// Site configuration
define('SITE_URL', 'https://your-domain.com'); // Your website URL (without trailing slash)
define('TIMEZONE', 'Asia/Riyadh');    // Set the timezone for your location

// Bilingual configuration
define('DEFAULT_LANGUAGE', 'en');     // Default language (en or ar)
define('ENABLE_LANGUAGE_SWITCHER', true); // Enable language switching

// Email configuration (for contact form)
define('CONTACT_EMAIL', 'contact@your-domain.com'); // Email where contact form submissions are sent
define('SMTP_HOST', '');     // SMTP server (leave empty to use PHP mail())
define('SMTP_USER', '');     // SMTP username
define('SMTP_PASS', '');     // SMTP password
define('SMTP_PORT', 587);    // SMTP port (usually 587 for TLS)

// Debug mode (set to false in production)
define('DEBUG_MODE', false);

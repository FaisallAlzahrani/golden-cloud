<?php
/**
 * Database Export Script for Golden Cloud Resort
 * Use this to create a SQL backup before deploying to GitHub
 */

// Include configuration
require_once 'config.php';

// Set headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="golden_cloud_backup_' . date('Y-m-d') . '.sql"');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all tables
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Export SQL
echo "-- Golden Cloud Database Backup\n";
echo "-- Created: " . date("Y-m-d H:i:s") . "\n";
echo "-- ---------------------------------------\n\n";

// Add database recreation commands (commented out for safety)
echo "-- DROP DATABASE IF EXISTS `" . DB_NAME . "`;\n";
echo "-- CREATE DATABASE `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
echo "-- USE `" . DB_NAME . "`;\n\n";

// Export each table
foreach ($tables as $table) {
    // Get table creation SQL
    $result = $conn->query("SHOW CREATE TABLE `$table`");
    $row = $result->fetch_row();
    echo $row[1] . ";\n\n";
    
    // Get table data
    $result = $conn->query("SELECT * FROM `$table`");
    $num_fields = $result->field_count;
    
    // Format and export data rows
    while ($row = $result->fetch_row()) {
        echo "INSERT INTO `$table` VALUES (";
        
        for ($i = 0; $i < $num_fields; $i++) {
            if (isset($row[$i])) {
                if (is_numeric($row[$i])) {
                    echo $row[$i];
                } else {
                    echo "'" . $conn->real_escape_string($row[$i]) . "'";
                }
            } else {
                echo "NULL";
            }
            
            if ($i < ($num_fields - 1)) {
                echo ", ";
            }
        }
        
        echo ");\n";
    }
    
    echo "\n";
}

// Special comments for amenities and bilingual support
echo "-- ---------------------------------------\n";
echo "-- Golden Cloud Resort Custom Settings\n";
echo "-- Bilingual support: English and Arabic\n";
echo "-- Custom Amenities: Outdoor Garden, Children's Playground, Coffee Shop, Barbecue Area\n";

// Close connection
$conn->close();
?>

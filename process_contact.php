<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        // Redirect back with error
        header('Location: index.php#contact');
        exit;
    }
    
    // In a real application, you would send the email here
    // For now, we'll just simulate success
    
    // Redirect with success message
    header('Location: index.php?success=1#contact');
    exit;
} else {
    // If not a POST request, redirect back to home
    header('Location: index.php');
    exit;
}
?>

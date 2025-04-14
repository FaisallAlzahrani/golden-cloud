<?php
require_once 'includes/functions.php';

// Destroy the session
session_destroy();

// Redirect to the home page
redirect('index.php');
?>

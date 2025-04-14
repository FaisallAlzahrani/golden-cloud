<?php
// Check if functions file is already included, if not include it
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/functions.php';
}

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);
?>
<!DOCTYPE html>
<html lang="<?php echo getSetting('site_language') ?: 'en'; ?>" dir="<?php echo getSetting('site_language') == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getSetting('site_title'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <link rel="stylesheet" href="<?php echo (dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF'])) ?>/assets/css/style.css">
    <style>
        <?php echo getThemeColorCSS(); ?>
        
        <?php if (getSetting('site_language') == 'ar'): ?>
        /* RTL Support for Arabic */
        body {
            font-family: 'Tajawal', sans-serif;
            text-align: right;
        }
        
        .main-nav ul {
            padding-right: 0;
        }
        
        .main-nav ul li {
            margin-left: 0;
            margin-right: 25px;
        }
        
        .section-title::after {
            margin: 15px auto;
        }
        
        .contact-info-item i {
            margin-right: 0;
            margin-left: 15px;
        }
        
        .whatsapp-icon {
            left: auto;
            right: 20px;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <?php if (getSetting('site_logo')): ?>
                        <img src="<?php echo (dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF'])) ?>/<?php echo getSetting('site_logo'); ?>" alt="<?php echo getSetting('site_title'); ?>" style="max-height: 60px;">
                    <?php else: ?>
                        <h1><?php echo getSetting('site_title'); ?></h1>
                    <?php endif; ?>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#home" class="nav-link"><?php echo __('Home'); ?></a></li>
                    <li><a href="#about" class="nav-link"><?php echo __('About'); ?></a></li>
                    <li><a href="#gallery" class="nav-link"><?php echo __('Gallery'); ?></a></li>
                    <li><a href="#contact" class="nav-link"><?php echo __('Contact'); ?></a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/index.php"><?php echo __('Admin'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><?php echo __('Logout'); ?></a></li>
                    <?php else: ?>
                        <li><a href="login.php"><?php echo __('Login'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="language-switcher">
                <a href="?lang=en" class="lang-btn <?php echo getSetting('site_language') == 'en' || !getSetting('site_language') ? 'active' : ''; ?>">EN</a>
                <a href="?lang=ar" class="lang-btn <?php echo getSetting('site_language') == 'ar' ? 'active' : ''; ?>">AR</a>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    
    <div class="visitor-counter-fixed">
        <div class="visitor-counter">
            <i class="fas fa-users"></i>
            <div class="visitor-count">
                <span id="visitorCount"><?php echo number_format(getVisitorCount()); ?></span>
                <div class="visitor-label"><?php echo __('Visitors'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="whatsapp-icon">
        <a href="https://wa.me/<?php echo str_replace('+', '', getSetting('contact_whatsapp')); ?>" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    
    <main>

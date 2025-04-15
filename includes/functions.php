<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Start with error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Handle language switching and set the appropriate language
 */
function handleLanguageSwitching() {
    // Include config if not already included
    if (!defined('DEFAULT_LANGUAGE')) {
        if (file_exists(__DIR__ . '/../config.php')) {
            require_once __DIR__ . '/../config.php';
        } else {
            define('DEFAULT_LANGUAGE', 'en');
        }
    }
    
    // Check if language is set in GET parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
        $_SESSION['site_language'] = $_GET['lang'];
        // Update the database setting for this language
        if (function_exists('updateSetting')) {
            updateSetting('site_language', $_GET['lang']);
        }
    } 
    // Check if language is set in session
    elseif (isset($_SESSION['site_language'])) {
        // Keep using the session language
    } 
    // Try to get from database
    elseif (function_exists('getSetting') && getSetting('site_language')) {
        $_SESSION['site_language'] = getSetting('site_language');
    }
    // Otherwise use default from config
    else {
        $_SESSION['site_language'] = DEFAULT_LANGUAGE;
    }
    
    // Set appropriate locale and text direction
    if ($_SESSION['site_language'] == 'ar') {
        setlocale(LC_ALL, 'ar_SA.utf8', 'ar_SA', 'ar');
    } else {
        setlocale(LC_ALL, 'en_US.utf8', 'en_US', 'en');
    }
}

// Call the language handling function at the start
handleLanguageSwitching();

// Get database connection
function getConnection() {
    return require(__DIR__ . '/../config/database.php');
}

// Get a setting value from the database
function getSetting($setting_name) {
    $conn = getConnection();
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_name = '$setting_name'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }
    
    return null;
}

// Update a setting value in the database
function updateSetting($setting_name, $setting_value) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
    $stmt->bind_param("ss", $setting_value, $setting_name);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Secure input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate a random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Upload an image
function uploadImage($file, $directory = '../assets/uploads/') {
    $target_dir = $directory;
    $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = generateRandomString() . '.' . $fileExtension;
    $target_file = $target_dir . $newFileName;
    
    // Check if file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large."];
    }
    
    // Allow certain file formats
    if($fileExtension != "jpg" && $fileExtension != "png" && $fileExtension != "jpeg" && $fileExtension != "gif" ) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => substr($target_file, 3)];
    } else {
        return ["success" => false, "message" => "Error uploading file."];
    }
}

// Display alert message
function showAlert($message, $type = 'info') {
    return "<div class='alert alert-$type'>$message</div>";
}

// Get all gallery images
function getGalleryImages() {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
    
    $images = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
    
    return $images;
}

// Get a specific number of recent gallery images
function getRecentGalleryImages($limit = 6) {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT $limit");
    
    $images = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
    
    return $images;
}

// Get all users
function getAllUsers() {
    $conn = getConnection();
    $result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
    
    $users = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Get CSS variables for theme colors
function getThemeColorCSS() {
    $primaryColor = getSetting('primary_color');
    $backgroundColor = getSetting('background_color');
    
    return <<<CSS
    :root {
        --primary-color: $primaryColor;
        --primary-color-light: #F5E7C1;
        --primary-color-dark: #B8860B;
        --background-color: $backgroundColor;
        --text-color: #333;
        --text-color-light: #666;
        --white: #fff;
        --black: #000;
        --gray-light: #f5f5f5;
        --gray: #e0e0e0;
        --gray-dark: #aaa;
    }
CSS;
}

// Translations function
function __($text) {
    $language = getSetting('site_language') ?: 'en';
    
    if ($language == 'en') {
        return $text;
    }
    
    $translations = [
        'ar' => [
            // Header translations
            'Home' => 'الرئيسية',
            'About' => 'من نحن',
            'Gallery' => 'معرض الصور',
            'Contact' => 'اتصل بنا',
            'Login' => 'تسجيل الدخول',
            'Logout' => 'تسجيل الخروج',
            'Admin' => 'لوحة التحكم',
            
            // Hero section
            'Welcome to' => 'مرحبا بكم في',
            'Welcome to Ghaima Resort' => 'مرحبا بكم في منتجع غيمة',
            'Experience luxury and comfort in our exclusive resort destination' => 'استمتع بالفخامة والراحة في وجهتنا الحصرية',
            'Book Now' => 'احجز الآن',
            
            // About section
            'About Us' => 'من نحن',
            'Welcome to Golden Cloud, where luxury meets comfort in a perfect blend of elegant accommodations and exceptional service. Nestled in a prime location, our resort offers a sanctuary of relaxation and indulgence for travelers seeking an unforgettable escape.' => 
            'مرحبًا بكم في جولدن كلاود، حيث تلتقي الفخامة بالراحة في مزيج مثالي من الإقامة الأنيقة والخدمة الاستثنائية. يقع منتجعنا في موقع متميز، ويوفر ملاذًا للاسترخاء والتدليل للمسافرين الباحثين عن هروب لا يُنسى.',
            
            'Welcome to Ghaima Resort – your ideal destination for relaxation and unforgettable moments. We offer a variety of accommodation options, including single rooms, one-bedroom suites, two-bedroom suites, and a traditional gathering lounge (Dewaniya) to ensure our guests enjoy a comfortable stay.' => 
            'مرحبًا بكم في منتجع غيمة - وجهتكم المثالية للاسترخاء واللحظات التي لا تُنسى. نقدم مجموعة متنوعة من خيارات الإقامة، بما في ذلك الغرف المفردة وأجنحة بغرفة نوم واحدة وأجنحة بغرفتي نوم وصالة استقبال تقليدية (ديوانية) لضمان تمتع ضيوفنا بإقامة مريحة.',
            
            'Our commitment to excellence is reflected in every detail of our resort, from the meticulously designed interiors to the personalized services that cater to your every need. Whether you are here for a romantic getaway, a family vacation, or a corporate retreat, Golden Cloud promises an experience that exceeds your expectations.' => 
            'ينعكس التزامنا بالتميز في كل تفصيل من تفاصيل منتجعنا، بدءًا من التصميمات الداخلية المدروسة بعناية وحتى الخدمات الشخصية التي تلبي جميع احتياجاتك. سواء كنت هنا لقضاء عطلة رومانسية، أو إجازة عائلية، أو رحلة عمل، فإن جولدن كلاود يعدك بتجربة تفوق توقعاتك.',
            
            'Our commitment to excellence is reflected in every detail of our resort – from thoughtfully designed interiors to personalized services tailored to meet all your needs. Whether you are here for a romantic getaway, a family vacation, or a business trip, Ghaima Resort promises an experience that exceeds your expectations.' => 
            'ينعكس التزامنا بالتميز في كل تفصيل من تفاصيل منتجعنا، بدءًا من التصميمات الداخلية المدروسة بعناية وحتى الخدمات الشخصية التي تلبي جميع احتياجاتك. سواء كنت هنا لقضاء عطلة رومانسية، أو إجازة عائلية، أو رحلة عمل، فإن منتجع غيمة يعدك بتجربة تفوق توقعاتك.',
            
            'With state-of-the-art facilities, gourmet dining options, and a range of recreational activities, we ensure that your stay with us is nothing short of extraordinary. Our dedicated staff is always ready to assist you, making your comfort and satisfaction our top priority.' => 
            'مع المرافق الحديثة، وخيارات الطعام الفاخرة، ومجموعة من الأنشطة الترفيهية، فإننا نضمن أن إقامتك معنا لن تكون أقل من رائعة. فريقنا المتفاني جاهز دائمًا لمساعدتك، مما يجعل راحتك ورضاك أولويتنا القصوى.',
            
            'Enjoy the highest levels of comfort and luxury. Our goal is to provide a unique and memorable experience, where guests can relax in a serene atmosphere surrounded by nature\'s beauty. We look forward to welcoming you to our resort!' => 
            'استمتع بأعلى مستويات الراحة والرفاهية. هدفنا هو توفير تجربة فريدة ولا تُنسى، حيث يمكن للضيوف الاسترخاء في أجواء هادئة محاطة بجمال الطبيعة. نتطلع إلى الترحيب بكم في منتجعنا!',
            
            'View Our Gallery' => 'عرض الصور',
            
            // Gallery section
            'Our Gallery' => 'معرض الصور',
            'Beautiful View' => 'منظر جميل',
            'Experience the luxury of our resort' => 'استمتع بفخامة منتجعنا',
            'View More' => 'عرض المزيد',
            'Image %1 of %2' => 'صورة %1 من %2',
            'Close' => 'إغلاق',
            'Next' => 'التالي',
            'Previous' => 'السابق',
            'You can close this modal content with the ESC key' => 'يمكنك إغلاق هذا المحتوى بمفتاح ESC',
            'Something Went Wrong, Please Try Again Later' => 'حدث خطأ ما، يرجى المحاولة مرة أخرى لاحقًا',
            'Image Not Found' => 'الصورة غير موجودة',
            'Element Not Found' => 'العنصر غير موجود',
            'Error Loading AJAX : Not Found' => 'خطأ في تحميل AJAX: غير موجود',
            'Error Loading AJAX : Forbidden' => 'خطأ في تحميل AJAX: ممنوع',
            'Error Loading Page' => 'خطأ في تحميل الصفحة',
            
            // Amenities section
            'Our Amenities' => 'مرافقنا',
            'Outdoor Garden' => 'حديقة خارجية',
            'Relax in our beautiful garden surrounded by nature' => 'استرخِ في حديقتنا الجميلة المحاطة بالطبيعة',
            'Children\'s Playground' => 'منطقة ألعاب للأطفال',
            'Fun and safe play area for your little ones' => 'منطقة لعب آمنة وممتعة لأطفالك',
            'Coffee Shop' => 'مقهى',
            'Enjoy freshly brewed coffee and delicious snacks' => 'استمتع بالقهوة الطازجة والوجبات الخفيفة اللذيذة',
            'Barbecue Area' => 'منطقة شواء',
            'Perfect spot for grilling and outdoor dining' => 'مكان مثالي للشواء وتناول الطعام في الهواء الطلق',
            'Swimming Pool' => 'حمام سباحة',
            'Enjoy our luxurious infinity pool with panoramic views' => 'استمتع بحمام السباحة الفاخر مع إطلالات بانورامية',
            'Spa & Wellness' => 'سبا وعافية',
            'Rejuvenate your body and mind in our premium spa' => 'جدد نشاط جسمك وعقلك في السبا الفاخر',
            'Fine Dining' => 'مطعم فاخر',
            'Savor exquisite cuisine prepared by our master chefs' => 'استمتع بالمأكولات الرائعة التي يعدها كبار الطهاة لدينا',
            'Fitness Center' => 'مركز لياقة بدنية',
            'Stay fit in our state-of-the-art fitness facility' => 'حافظ على لياقتك في مركز اللياقة البدنية المتطور',
            
            // Contact section
            'Contact Us' => 'اتصل بنا',
            'Get In Touch' => 'تواصل معنا',
            'Location' => 'الموقع',
            'Phone' => 'الهاتف',
            'Email' => 'البريد الإلكتروني',
            'WhatsApp' => 'واتساب',
            'Your Name' => 'الاسم',
            'Your Email' => 'البريد الإلكتروني',
            'Subject' => 'الموضوع',
            'Your Message' => 'رسالتك',
            'Send Message' => 'إرسال الرسالة',
            'Your message has been sent successfully. We\'ll get back to you soon!' => 'تم إرسال رسالتك بنجاح. سنرد عليك قريبًا!',
            
            // Footer
            'Quick Links' => 'روابط سريعة',
            'Follow Us' => 'تابعنا',
            'All Rights Reserved.' => 'جميع الحقوق محفوظة.',
            'Visitors' => 'الزوار',
            
            // Login Page
            'Login to Your Account' => 'تسجيل الدخول إلى حسابك',
            'Username' => 'اسم المستخدم',
            'Password' => 'كلمة المرور',
            'Return to' => 'العودة إلى',
        ]
    ];
    
    return $translations[$language][$text] ?? $text;
}

/**
 * Get visitor count in a consistent way
 * 
 * @return int Current visitor count
 */
function getVisitorCount() {
    static $cachedCount = null;
    
    // If we already calculated the count in this request, return the cached value
    if ($cachedCount !== null) {
        return $cachedCount;
    }
    
    $conn = getConnection();
    $checkResult = $conn->query("SELECT setting_value FROM settings WHERE setting_name = 'visitor_count'");
    
    if ($checkResult && $checkResult->num_rows > 0) {
        $visitorCount = (int)getSetting('visitor_count');
    } else {
        // Create initial visitor count if it doesn't exist
        $initialCount = rand(235, 298); // More realistic starting number
        $conn->query("INSERT INTO settings (setting_name, setting_value) VALUES ('visitor_count', '$initialCount')");
        $visitorCount = $initialCount;
    }
    
    // Track real unique visitors using cookies
    $incrementCount = false;
    if (!isset($_COOKIE['visitor_counted'])) {
        // Set a cookie that expires in 24 hours
        setcookie('visitor_counted', '1', time() + 86400, '/');
        $incrementCount = true;
    }
    
    // Only increment by 1 for genuine new visitors
    if ($incrementCount) {
        $newCount = $visitorCount + 1;
        updateSetting('visitor_count', (string)$newCount);
        $visitorCount = $newCount;
    }
    
    // Cache the result for this request
    $cachedCount = $visitorCount;
    
    return $visitorCount;
}
?>

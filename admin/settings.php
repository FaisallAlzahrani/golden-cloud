<?php
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Initialize message variable
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle image deletion
    if (isset($_POST['action']) && $_POST['action'] === 'delete_image' && isset($_POST['image_type'])) {
        $imageType = sanitize($_POST['image_type']);
        
        // Delete the image file if it exists
        $currentImage = getSetting($imageType);
        if ($currentImage && file_exists('../' . $currentImage)) {
            unlink('../' . $currentImage);
        }
        
        // Clear the setting in database
        $conn = getConnection();
        $imageType = $conn->real_escape_string($imageType);
        $conn->query("UPDATE settings SET setting_value = '' WHERE setting_name = '$imageType'");
        
        $message = showAlert('Image deleted successfully.', 'success');
    }
    
    // Handle general settings update
    elseif (isset($_POST['save_general_settings'])) {
        $conn = getConnection();
        
        // Update basic text settings
        $settingsToUpdate = [
            'site_title' => sanitize($_POST['site_title'] ?? 'Golden Cloud'),
            'primary_color' => sanitize($_POST['primary_color'] ?? '#D4AF37'),
            'background_color' => sanitize($_POST['background_color'] ?? '#FDF5E6'),
        ];
        
        // Update each text setting
        foreach ($settingsToUpdate as $name => $value) {
            $name = $conn->real_escape_string($name);
            $value = $conn->real_escape_string($value);
            $conn->query("UPDATE settings SET setting_value = '$value' WHERE setting_name = '$name'");
        }
        
        $message = showAlert('General settings saved successfully.', 'success');
    }
    
    // Handle contact information update
    elseif (isset($_POST['save_contact_info'])) {
        $conn = getConnection();
        
        // Update contact information settings
        $settingsToUpdate = [
            'contact_phone' => sanitize($_POST['contact_phone'] ?? ''),
            'contact_email' => sanitize($_POST['contact_email'] ?? ''),
            'contact_whatsapp' => sanitize($_POST['contact_whatsapp'] ?? ''),
            'address' => sanitize($_POST['address'] ?? '')
        ];
        
        // Update each contact setting
        foreach ($settingsToUpdate as $name => $value) {
            $name = $conn->real_escape_string($name);
            $value = $conn->real_escape_string($value);
            $conn->query("UPDATE settings SET setting_value = '$value' WHERE setting_name = '$name'");
        }
        
        $message = showAlert('Contact information saved successfully.', 'success');
    }
    
    // Handle images update
    elseif (isset($_POST['save_images'])) {
        $conn = getConnection();
        $uploadedImages = [];
        
        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['name']) {
            $logoUpload = uploadImage($_FILES['site_logo'], '../assets/images/');
            if ($logoUpload['success']) {
                $logoPath = $logoUpload['file_path'];
                $conn->query("UPDATE settings SET setting_value = '$logoPath' WHERE setting_name = 'site_logo'");
                $uploadedImages[] = 'Logo';
            }
        }
        
        // Handle about image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['name']) {
            $aboutImageUpload = uploadImage($_FILES['about_image'], '../assets/images/');
            if ($aboutImageUpload['success']) {
                $aboutPath = $aboutImageUpload['file_path'];
                $conn->query("UPDATE settings SET setting_value = '$aboutPath' WHERE setting_name = 'about_image'");
                $uploadedImages[] = 'About image';
            }
        }
        
        // Handle hero image upload
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['name']) {
            $heroImageUpload = uploadImage($_FILES['hero_image'], '../assets/images/');
            if ($heroImageUpload['success']) {
                $heroPath = $heroImageUpload['file_path'];
                $conn->query("UPDATE settings SET setting_value = '$heroPath' WHERE setting_name = 'hero_image'");
                $uploadedImages[] = 'Hero image';
            }
        }
        
        // Prepare success message
        $message = 'Images saved successfully.';
        if (!empty($uploadedImages)) {
            $message .= ' Uploaded: ' . implode(', ', $uploadedImages);
        }
        
        $message = showAlert($message, 'success');
    }
    
    // Handle visitor counter update
    elseif (isset($_POST['save_visitor_counter'])) {
        $conn = getConnection();
        
        // Update visitor counter setting
        $visitorCount = sanitize($_POST['visitor_count'] ?? '1232');
        $conn->query("UPDATE settings SET setting_value = '$visitorCount' WHERE setting_name = 'visitor_count'");
        
        $message = showAlert('Visitor counter saved successfully.', 'success');
    }
}

// Get current settings after any updates
$conn = getConnection();
$result = $conn->query("SELECT * FROM settings");
$settings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}

// Include header
include 'header.php';
?>

<div class="admin-content">
    <h2>Website Settings</h2>
    
    <?php echo $message; ?>
    
    <div class="form-card">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <h3>General Settings</h3>
            </div>
            
            <div class="form-group">
                <label for="site_title">Website Title</label>
                <input type="text" id="site_title" name="site_title" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['site_title'] ?? 'Golden Cloud'); ?>" required>
            </div>
            
            <div class="form-group">
                <h3>Color Settings</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color">Primary Color (Gold)</label>
                    <div class="color-picker">
                        <input type="color" id="primary_color" name="primary_color" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#D4AF37'); ?>">
                        <span id="primary_color_hex"><?php echo htmlspecialchars($settings['primary_color'] ?? '#D4AF37'); ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="background_color">Background Color</label>
                    <div class="color-picker">
                        <input type="color" id="background_color" name="background_color" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['background_color'] ?? '#FDF5E6'); ?>">
                        <span id="background_color_hex"><?php echo htmlspecialchars($settings['background_color'] ?? '#FDF5E6'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save_general_settings" class="btn">Save General Settings</button>
            </div>
        </form>
    </div>
    
    <div class="form-card">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <h3>Contact Information</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_phone">Phone Number</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="contact_email">Email Address</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_whatsapp">WhatsApp Number (with country code)</label>
                    <input type="text" id="contact_whatsapp" name="contact_whatsapp" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['contact_whatsapp'] ?? ''); ?>">
                    <small>Example: +1234567890</small>
                </div>
                
                <div class="form-group">
                    <label for="address">Resort Address</label>
                    <input type="text" id="address" name="address" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save_contact_info" class="btn">Save Contact Information</button>
            </div>
        </form>
    </div>
    
    <div class="form-card">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <h3>Logo and Images</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="site_logo">Logo</label>
                    <input type="file" id="site_logo" name="site_logo" class="form-control">
                    <?php if (!empty($settings['site_logo'])) : ?>
                        <div class="image-preview-container" style="margin-top: 15px;">
                            <img src="../<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="Logo" 
                                 style="max-width: 200px; max-height: 100px; margin-bottom: 10px; display: block; border: 1px solid #ddd; padding: 5px;">
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_type" value="site_logo">
                                <button type="submit" class="btn btn-danger btn-small">Delete Logo</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="about_image">About Image</label>
                    <input type="file" id="about_image" name="about_image" class="form-control">
                    <?php if (!empty($settings['about_image'])) : ?>
                        <div class="image-preview-container" style="margin-top: 15px;">
                            <img src="../<?php echo htmlspecialchars($settings['about_image']); ?>" alt="About Image" 
                                 style="max-width: 200px; max-height: 150px; margin-bottom: 10px; display: block; border: 1px solid #ddd; padding: 5px;">
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_type" value="about_image">
                                <button type="submit" class="btn btn-danger btn-small">Delete About Image</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="hero_image">Hero Image</label>
                    <input type="file" id="hero_image" name="hero_image" class="form-control">
                    <?php if (!empty($settings['hero_image'])) : ?>
                        <div class="image-preview-container" style="margin-top: 15px;">
                            <img src="../<?php echo htmlspecialchars($settings['hero_image']); ?>" alt="Hero Image" 
                                 style="max-width: 200px; max-height: 150px; margin-bottom: 10px; display: block; border: 1px solid #ddd; padding: 5px;">
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_type" value="hero_image">
                                <button type="submit" class="btn btn-danger btn-small">Delete Hero Image</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save_images" class="btn">Save Images</button>
            </div>
        </form>
    </div>
    
    <div class="form-card">
        <form method="post" action="">
            <div class="form-group">
                <h3>Visitor Counter Settings</h3>
            </div>
            
            <div class="form-group">
                <label for="visitor_count">Current Visitor Count</label>
                <input type="number" id="visitor_count" name="visitor_count" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['visitor_count'] ?? '1232'); ?>" required>
                <small>This will be displayed on the website as the starting point for visitor counts.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save_visitor_counter" class="btn">Save Visitor Counter</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update hex code display when color picker changes
        const primaryColorPicker = document.getElementById('primary_color');
        const primaryColorHex = document.getElementById('primary_color_hex');
        const backgroundColorPicker = document.getElementById('background_color');
        const backgroundColorHex = document.getElementById('background_color_hex');
        
        if (primaryColorPicker && primaryColorHex) {
            primaryColorPicker.addEventListener('input', function() {
                primaryColorHex.textContent = this.value;
            });
        }
        
        if (backgroundColorPicker && backgroundColorHex) {
            backgroundColorPicker.addEventListener('input', function() {
                backgroundColorHex.textContent = this.value;
            });
        }
    });
</script>

<?php include 'footer.php'; ?>

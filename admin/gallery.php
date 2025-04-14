<?php
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$conn = getConnection();
$message = '';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && isset($_FILES['image'])) {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        
        // Validate inputs
        if (empty($title) || empty($_FILES['image']['name'])) {
            $message = showAlert('Title and image are required.', 'danger');
        } else {
            // Upload the image
            $uploadResult = uploadImage($_FILES['image'], '../assets/uploads/');
            
            if ($uploadResult['success']) {
                // Insert into database
                $imagePath = $uploadResult['file_path'];
                $stmt = $conn->prepare("INSERT INTO gallery (title, description, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $description, $imagePath);
                
                if ($stmt->execute()) {
                    $message = showAlert('Image uploaded successfully.', 'success');
                } else {
                    $message = showAlert('Error saving image information: ' . $conn->error, 'danger');
                }
                $stmt->close();
            } else {
                $message = showAlert('Error uploading image: ' . $uploadResult['message'], 'danger');
            }
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['image_id'])) {
        $imageId = (int)$_POST['image_id'];
        
        // Get image path to delete file
        $result = $conn->query("SELECT image_path FROM gallery WHERE id = $imageId");
        if ($result && $result->num_rows > 0) {
            $imagePath = $result->fetch_assoc()['image_path'];
            $fullPath = '../' . $imagePath;
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $imageId);
            
            if ($stmt->execute()) {
                // Try to delete the file
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                $message = showAlert('Image deleted successfully.', 'success');
            } else {
                $message = showAlert('Error deleting image: ' . $conn->error, 'danger');
            }
            $stmt->close();
        } else {
            $message = showAlert('Image not found.', 'danger');
        }
    }
}

// Get all gallery images
$galleryImages = getGalleryImages();

// Include header
include 'header.php';
?>

<div class="admin-content">
    <div class="admin-header-actions">
        <h2>Gallery Management</h2>
        <button class="btn" id="showAddImageForm">Add New Image</button>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Add Image Form (hidden by default) -->
    <div class="form-card" id="addImageForm" style="display: none;">
        <h3>Add New Image</h3>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none;">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Upload Image</button>
                <button type="button" class="btn" style="background-color: #6c757d;" id="cancelAddImage">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Gallery Grid -->
    <div class="gallery-grid">
        <?php if (empty($galleryImages)): ?>
            <p style="grid-column: 1 / -1; text-align: center; padding: 30px;">No images in gallery. Add some images to display here.</p>
        <?php else: ?>
            <?php foreach ($galleryImages as $image): ?>
                <div class="gallery-item">
                    <img src="../<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>">
                    <div class="gallery-details">
                        <h3><?php echo $image['title']; ?></h3>
                        <p><?php echo $image['description']; ?></p>
                        <div class="gallery-actions">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                <button type="submit" class="btn btn-small btn-danger delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showAddImageForm = document.getElementById('showAddImageForm');
        const addImageForm = document.getElementById('addImageForm');
        const cancelAddImage = document.getElementById('cancelAddImage');
        
        if (showAddImageForm && addImageForm) {
            showAddImageForm.addEventListener('click', function() {
                addImageForm.style.display = 'block';
                this.style.display = 'none';
            });
        }
        
        if (cancelAddImage && addImageForm && showAddImageForm) {
            cancelAddImage.addEventListener('click', function() {
                addImageForm.style.display = 'none';
                showAddImageForm.style.display = 'inline-block';
                document.getElementById('imagePreview').style.display = 'none';
            });
        }
        
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>

<?php include 'footer.php'; ?>

<?php
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Include header
include 'header.php';
?>

<div class="admin-content">
    <div class="admin-overview">
        <h2>Dashboard Overview</h2>
        
        <div class="admin-stats">
            <?php
            $conn = getConnection();
            
            // Count users
            $userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
            
            // Count gallery images
            $imageCount = $conn->query("SELECT COUNT(*) as count FROM gallery")->fetch_assoc()['count'];
            ?>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Users</h3>
                    <p><?php echo $userCount; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-info">
                    <h3>Gallery Images</h3>
                    <p><?php echo $imageCount; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="stat-info">
                    <h3>Settings</h3>
                    <p>7</p>
                </div>
            </div>
        </div>
        
        <div class="admin-recent">
            <h3>Recent Activity</h3>
            <div class="recent-items">
                <div class="recent-item">
                    <div class="recent-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="recent-info">
                        <p>Welcome to the admin dashboard!</p>
                        <small>Use the menu on the left to manage your resort website.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

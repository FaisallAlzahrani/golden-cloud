<?php
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$conn = getConnection();
$message = '';

// Handle user creation or update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = sanitize($_POST['role'] ?? 'user');
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        
        // Validate inputs
        if (empty($username) || empty($email) || ($_POST['action'] === 'add' && empty($password))) {
            $message = showAlert('All fields are required.', 'danger');
        } else {
            if ($_POST['action'] === 'add') {
                // Check if username or email already exists
                $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                $check->bind_param("ss", $username, $email);
                $check->execute();
                $result = $check->get_result();
                
                if ($result->num_rows > 0) {
                    $message = showAlert('Username or email already exists.', 'danger');
                } else {
                    // Hash the password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert the new user
                    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
                    
                    if ($stmt->execute()) {
                        $message = showAlert('User created successfully.', 'success');
                    } else {
                        $message = showAlert('Error creating user: ' . $conn->error, 'danger');
                    }
                    $stmt->close();
                }
                $check->close();
            } elseif ($_POST['action'] === 'edit') {
                // Update existing user
                if (!empty($password)) {
                    // Update with new password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $username, $hashedPassword, $email, $role, $userId);
                } else {
                    // Update without changing password
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $username, $email, $role, $userId);
                }
                
                if ($stmt->execute()) {
                    $message = showAlert('User updated successfully.', 'success');
                } else {
                    $message = showAlert('Error updating user: ' . $conn->error, 'danger');
                }
                $stmt->close();
            }
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        
        // Check if this is the last admin
        $adminCheck = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $adminCount = $adminCheck->fetch_assoc()['count'];
        
        $isAdmin = $conn->query("SELECT role FROM users WHERE id = $userId")->fetch_assoc()['role'] === 'admin';
        
        if ($isAdmin && $adminCount <= 1) {
            $message = showAlert('Cannot delete the last admin user.', 'danger');
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            
            if ($stmt->execute()) {
                $message = showAlert('User deleted successfully.', 'success');
            } else {
                $message = showAlert('Error deleting user: ' . $conn->error, 'danger');
            }
            $stmt->close();
        }
    }
}

// Get user to edit if ID is provided
$editUser = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $userId = (int)$_GET['edit'];
    $result = $conn->query("SELECT id, username, email, role FROM users WHERE id = $userId");
    if ($result && $result->num_rows > 0) {
        $editUser = $result->fetch_assoc();
    }
}

// Get all users
$users = getAllUsers();

// Include header
include 'header.php';
?>

<div class="admin-content">
    <div class="admin-header-actions">
        <h2><?php echo $editUser ? 'Edit User' : 'Users Management'; ?></h2>
        <?php if (!$editUser): ?>
            <button class="btn" id="showAddUserForm">Add New User</button>
        <?php endif; ?>
    </div>
    
    <?php echo $message; ?>
    
    <?php if ($editUser): ?>
    <!-- Edit User Form -->
    <div class="form-card">
        <form method="post" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $editUser['username']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $editUser['email']; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password (leave empty to keep current)</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="user" <?php echo $editUser['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Update User</button>
                <a href="users.php" class="btn" style="background-color: #6c757d;">Cancel</a>
            </div>
        </form>
    </div>
    <?php else: ?>
    <!-- Add User Form (hidden by default) -->
    <div class="form-card" id="addUserForm" style="display: none;">
        <h3>Add New User</h3>
        <form method="post" action="">
            <input type="hidden" name="action" value="add">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Create User</button>
                <button type="button" class="btn" style="background-color: #6c757d;" id="cancelAddUser">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Users List -->
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No users found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td>
                                <span class="badge" style="background-color: <?php echo $user['role'] === 'admin' ? 'var(--primary-color)' : '#6c757d'; ?>; color: white; padding: 3px 8px; border-radius: 4px;">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="data-actions">
                                <a href="users.php?edit=<?php echo $user['id']; ?>" class="btn btn-small">Edit</a>
                                
                                <form method="post" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showAddUserForm = document.getElementById('showAddUserForm');
        const addUserForm = document.getElementById('addUserForm');
        const cancelAddUser = document.getElementById('cancelAddUser');
        
        if (showAddUserForm && addUserForm) {
            showAddUserForm.addEventListener('click', function() {
                addUserForm.style.display = 'block';
                this.style.display = 'none';
            });
        }
        
        if (cancelAddUser && addUserForm && showAddUserForm) {
            cancelAddUser.addEventListener('click', function() {
                addUserForm.style.display = 'none';
                showAddUserForm.style.display = 'inline-block';
            });
        }
    });
</script>

<?php include 'footer.php'; ?>

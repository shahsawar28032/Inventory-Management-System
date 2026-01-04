<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Only admin can access users
if (!isAdmin()) {
    header("Location: ../../index.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$page_title = "Edit User";
$error = '';
$success = '';

// Get user ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_GET['id'];

// Get user data
$query = "SELECT id, username, email, role FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validate input
    if (empty($username) || empty($email)) {
        $error = "Username and email are required.";
    } else {
        // Check if username or email already exists (excluding current user)
        $check_query = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->bindParam(':id', $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = "Username or email already exists.";
        } else {
            if (!empty($password)) {
                // Update with password
                if (strlen($password) < 6) {
                    $error = "Password must be at least 6 characters long.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $query = "UPDATE users SET username = :username, email = :email, password = :password, role = :role WHERE id = :id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':password', $hashed_password);
                }
            } else {
                // Update without password
                $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
                $stmt = $conn->prepare($query);
            }
            
            if (empty($error)) {
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':id', $user_id);
                
                if ($stmt->execute()) {
                    $success = "User updated successfully!";
                    // Refresh user data
                    $user['username'] = $username;
                    $user['email'] = $email;
                    $user['role'] = $role;
                } else {
                    $error = "Error updating user. Please try again.";
                }
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit User</h1>
        <a href="index.php" class="btn btn-secondary">Back to Users</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                                <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
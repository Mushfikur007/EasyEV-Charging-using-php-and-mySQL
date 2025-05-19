<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/User.php';

// Check if user is logged in as admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    $_SESSION['message'] = "You must be logged in as an administrator to view users.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create user object
$user = new User($conn);

// Get all users
$all_users = $user->getAllUsers();
?>

<h2 class="mb-4">All Users</h2>

<?php if($all_users->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $all_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <?php if($row['user_type'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No users found in the system.
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 
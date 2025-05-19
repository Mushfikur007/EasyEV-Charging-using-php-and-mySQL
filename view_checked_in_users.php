<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/User.php';

// Check if user is logged in as admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    $_SESSION['message'] = "You must be logged in as an administrator to view checked-in users.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create user object
$user = new User($conn);

// Get all checked-in users
$checked_in_users = $user->getUsersCheckedIn();
?>

<h2 class="mb-4">Currently Checked-in Users</h2>

<?php if($checked_in_users->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Check-in Time</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $checked_in_users->fetch_assoc()): ?>
                    <?php
                    // Calculate current duration
                    $check_in_time = strtotime($row['check_in_time']);
                    $current_time = time();
                    $duration_hours = ($current_time - $check_in_time) / 3600; // Convert seconds to hours
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?> (ID: <?php echo $row['location_id']; ?>)</td>
                        <td><?php echo $row['check_in_time']; ?></td>
                        <td><?php echo number_format($duration_hours, 2); ?> hours</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No users are currently checked in at any charging station.
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 
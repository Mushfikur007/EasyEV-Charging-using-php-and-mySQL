<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingSession.php';

// Check if user is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'user') {
    $_SESSION['message'] = "You must be logged in as a user to check out.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Check if session ID is provided
if(!isset($_GET['session_id']) || empty($_GET['session_id'])) {
    $_SESSION['message'] = "No charging session selected.";
    $_SESSION['message_type'] = "danger";
    header('Location: my_sessions.php');
    exit();
}

// Get session ID
$session_id = intval($_GET['session_id']);

// Create session object
$session = new ChargingSession($conn);

// Get session details
$session_data = $session->getSessionById($session_id);

// Check if session exists and belongs to the user
if(!$session_data || $session_data['user_id'] != $_SESSION['user_id'] || $session_data['check_out_time'] !== null) {
    $_SESSION['message'] = "Invalid charging session or you've already checked out.";
    $_SESSION['message_type'] = "danger";
    header('Location: my_sessions.php');
    exit();
}

// Process check-out
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check out user
    if($session->checkOut($session_id)) {
        $_SESSION['message'] = "Check-out successful! Your charging session has ended.";
        $_SESSION['message_type'] = "success";
        header('Location: my_sessions.php');
        exit();
    } else {
        $_SESSION['message'] = "Failed to check out. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    // Calculate current duration and cost (for display only)
    $check_in_time = strtotime($session_data['check_in_time']);
    $current_time = time();
    $duration_hours = ($current_time - $check_in_time) / 3600; // Convert seconds to hours
    $estimated_cost = $duration_hours * $session_data['cost_per_hour'];
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Check Out from Charging Station</h4>
            </div>
            <div class="card-body">
                <div class="checkout-info mb-4">
                    <h5><?php echo htmlspecialchars($session_data['description']); ?></h5>
                    <p><strong>Check-in Time:</strong> <?php echo $session_data['check_in_time']; ?></p>
                    <p><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    <p><strong>Duration:</strong> <?php echo number_format($duration_hours, 2); ?> hours</p>
                    <p class="mb-0"><strong>Estimated Cost:</strong> <span class="cost-display">$<?php echo number_format($estimated_cost, 2); ?></span></p>
                </div>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?session_id=' . $session_id); ?>">
                    <div class="alert alert-info">
                        <p class="mb-0">By checking out, you agree to pay the total cost for your charging session.</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Confirm Check Out</button>
                        <a href="my_sessions.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
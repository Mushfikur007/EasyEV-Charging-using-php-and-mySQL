<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';
require_once 'classes/ChargingSession.php';

// Check if user is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'user') {
    $_SESSION['message'] = "You must be logged in as a user to check in.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Check if location ID is provided
if(!isset($_GET['location_id']) || empty($_GET['location_id'])) {
    $_SESSION['message'] = "No charging location selected.";
    $_SESSION['message_type'] = "danger";
    header('Location: available_locations.php');
    exit();
}

// Get location ID
$location_id = intval($_GET['location_id']);

// Create objects
$location = new ChargingLocation($conn);
$session = new ChargingSession($conn);

// Check if user is already checked in somewhere
if($session->isUserCheckedIn($_SESSION['user_id'])) {
    $_SESSION['message'] = "You are already checked in at a charging station. Please check out first.";
    $_SESSION['message_type'] = "warning";
    header('Location: my_sessions.php');
    exit();
}

// Check if location has available stations
if(!$location->hasAvailableStations($location_id)) {
    $_SESSION['message'] = "This charging location is currently full. Please try another location.";
    $_SESSION['message_type'] = "warning";
    header('Location: available_locations.php');
    exit();
}

// Get location details
$location_data = $location->getLocationById($location_id);

if(!$location_data) {
    $_SESSION['message'] = "Charging location not found.";
    $_SESSION['message_type'] = "danger";
    header('Location: available_locations.php');
    exit();
}

// Process check-in
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set session properties
    $session->user_id = $_SESSION['user_id'];
    $session->location_id = $location_id;
    
    // Check in user
    if($session->checkIn()) {
        $_SESSION['message'] = "Check-in successful! Your charging session has started.";
        $_SESSION['message_type'] = "success";
        header('Location: my_sessions.php');
        exit();
    } else {
        $_SESSION['message'] = "Failed to check in. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Check In to Charging Station</h4>
            </div>
            <div class="card-body">
                <div class="session-info mb-4">
                    <h5><?php echo htmlspecialchars($location_data['description']); ?></h5>
                    <p class="mb-2"><strong>Cost:</strong> $<?php echo number_format($location_data['cost_per_hour'], 2); ?> per hour</p>
                    <p class="mb-0"><strong>Check-in Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?location_id=' . $location_id); ?>">
                    <div class="alert alert-info">
                        <p class="mb-0">By checking in, you agree to pay the charging fees based on the duration of your session.</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Confirm Check In</button>
                        <a href="available_locations.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
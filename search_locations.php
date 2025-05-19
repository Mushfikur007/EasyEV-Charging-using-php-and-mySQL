<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';
require_once 'classes/ChargingSession.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must be logged in to search charging stations.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create objects
$location = new ChargingLocation($conn);
$session = new ChargingSession($conn);

// Check if user is already checked in
$user_checked_in = false;
if($_SESSION['user_type'] == 'user') {
    $user_checked_in = $session->isUserCheckedIn($_SESSION['user_id']);
}

// Search for locations
$search_results = null;
$search_term = '';

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_results = $location->searchLocations($search_term);
}
?>

<h2 class="mb-4">Search Charging Stations</h2>

<div class="search-box mb-4">
    <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="row g-3">
        <div class="col-md-10">
            <input type="text" class="form-control" id="search" name="search" placeholder="Search by location ID or description..." value="<?php echo htmlspecialchars($search_term); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
</div>

<?php if($_SESSION['user_type'] == 'user' && $user_checked_in): ?>
    <div class="alert alert-warning mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You are currently checked in at a charging station. You need to check out before checking in at another station.
        <a href="my_sessions.php" class="alert-link">View your active session</a>
    </div>
<?php endif; ?>

<?php if($search_results !== null): ?>
    <?php if($search_results->num_rows > 0): ?>
        <h3 class="mb-3">Search Results</h3>
        <div class="row">
            <?php while($row = $search_results->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card location-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><?php echo htmlspecialchars($row['description']); ?></span>
                            <?php if($row['available_stations'] > 0): ?>
                                <span class="badge bg-success"><?php echo $row['available_stations']; ?> Available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Full</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <strong>Location ID:</strong> <?php echo $row['location_id']; ?><br>
                                <strong>Cost:</strong> $<?php echo number_format($row['cost_per_hour'], 2); ?> per hour<br>
                                <strong>Total Stations:</strong> <?php echo $row['num_stations']; ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <?php if($_SESSION['user_type'] == 'user'): ?>
                                <?php if($row['available_stations'] > 0 && !$user_checked_in): ?>
                                    <a href="check_in.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-primary">Check In</a>
                                <?php else: ?>
                                    <button class="btn btn-primary" disabled>Check In</button>
                                <?php endif; ?>
                            <?php elseif($_SESSION['user_type'] == 'admin'): ?>
                                <a href="edit_location.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-primary">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No charging stations found matching your search criteria.
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 
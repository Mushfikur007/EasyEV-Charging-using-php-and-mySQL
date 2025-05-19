<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';
require_once 'classes/ChargingSession.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must be logged in to view available charging stations.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create objects
$location = new ChargingLocation($conn);
$session = new ChargingSession($conn);

// Check if user is already checked in
$user_checked_in = $session->isUserCheckedIn($_SESSION['user_id']);

// Get available locations
$available_locations = $location->getAvailableLocations();
?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Available Charging Stations</h2>
            <p class="text-muted">Find and check in to available EV charging stations</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button id="refreshButton" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <small class="text-muted ms-2">Last updated: <span id="lastUpdated"><?php echo date('Y-m-d H:i:s'); ?></span></small>
        </div>
    </div>

    <?php if($user_checked_in): ?>
        <div class="alert alert-warning shadow-sm border-0">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading">You're Already Checked In</h5>
                    <p class="mb-0">You are currently checked in at a charging station. You need to check out before checking in at another station.</p>
                    <a href="my_sessions.php" class="alert-link">View your active session <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stations Display -->
    <div class="row g-4" id="locationsContainer">
        <?php if($available_locations->num_rows > 0): ?>
            <?php while($row = $available_locations->fetch_assoc()): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card location-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($row['description']); ?></h5>
                            <span class="badge bg-success rounded-pill">
                                <i class="fas fa-bolt me-1"></i> <?php echo $row['available_stations']; ?> Available
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Cost per Hour:</span>
                                    <span class="fw-bold text-primary">$<?php echo number_format($row['cost_per_hour'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Stations:</span>
                                    <span class="fw-bold"><?php echo $row['num_stations']; ?></span>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: <?php echo ($row['available_stations'] / $row['num_stations']) * 100; ?>%;" 
                                    aria-valuenow="<?php echo $row['available_stations']; ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="<?php echo $row['num_stations']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <?php if(!$user_checked_in): ?>
                                <a href="check_in.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-plug me-2"></i>Check In
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-plug me-2"></i>Check In
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info shadow-sm border-0">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">No Available Stations</h5>
                            <p class="mb-0">There are no available charging stations at the moment. Please check back later.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshButton = document.getElementById('refreshButton');
    const locationsContainer = document.getElementById('locationsContainer');
    const lastUpdated = document.getElementById('lastUpdated');
    
    refreshButton.addEventListener('click', function() {
        refreshButton.disabled = true;
        refreshButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
        
        // Fetch updated locations
        fetch('get_available_locations_for_users.php')
            .then(response => response.text())
            .then(data => {
                locationsContainer.innerHTML = data;
                lastUpdated.textContent = new Date().toLocaleString();
                refreshButton.disabled = false;
                refreshButton.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Refresh';
            })
            .catch(error => {
                console.error('Error:', error);
                refreshButton.disabled = false;
                refreshButton.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Refresh';
            });
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        refreshButton.click();
    }, 30000);
});
</script>

<?php require_once 'includes/footer.php'; ?>
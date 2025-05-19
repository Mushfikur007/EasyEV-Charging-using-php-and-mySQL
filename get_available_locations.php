<?php
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';

// Check if user is logged in as admin
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Create location object
$location = new ChargingLocation($conn);

// Get available locations
$available_locations = $location->getAvailableLocations();

// Generate HTML output
ob_start();
?>
<div class="row">
    <?php if($available_locations->num_rows > 0): ?>
        <?php while($row = $available_locations->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card location-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($row['description']); ?></span>
                        <span class="badge bg-success"><?php echo $row['available_stations']; ?> Available</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>ID:</strong> <?php echo $row['location_id']; ?><br>
                            <strong>Cost:</strong> $<?php echo number_format($row['cost_per_hour'], 2); ?> per hour<br>
                            <strong>Total Stations:</strong> <?php echo $row['num_stations']; ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="edit_location.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No available charging stations at the moment.
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
echo ob_get_clean();
?> 
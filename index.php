<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';

// Create charging location object
$location = new ChargingLocation($conn);

// Get available locations
$available_locations = $location->getAvailableLocations();
?>

<div class="hero text-center">
    <div class="container">
        <h1 class="display-4">Welcome to EasyEV Charging</h1>
        <p class="lead">Find the nearest charging station for your electric vehicle</p>
        <div class="mt-4">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="search_locations.php" class="btn btn-primary btn-lg me-2">Find Stations</a>
                <?php if($_SESSION['user_type'] == 'user'): ?>
                    <a href="my_sessions.php" class="btn btn-outline-light btn-lg">My Sessions</a>
                <?php else: ?>
                    <a href="manage_locations.php" class="btn btn-outline-light btn-lg">Manage Stations</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-lg me-2">Login</a>
                <a href="register.php" class="btn btn-outline-light btn-lg">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt text-primary me-2"></i>Find Stations</h5>
                <p class="card-text">Easily locate charging stations near you with real-time availability information.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-bolt text-primary me-2"></i>Quick Charging</h5>
                <p class="card-text">Check-in to start charging your vehicle and check-out when you're done.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-history text-primary me-2"></i>Track History</h5>
                <p class="card-text">View your charging history and keep track of your EV charging sessions.</p>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-4">Available Charging Stations</h2>

<?php if($available_locations->num_rows > 0): ?>
    <div class="row">
        <?php while($row = $available_locations->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card location-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($row['description']); ?></span>
                        <span class="badge bg-success"><?php echo $row['available_stations']; ?> Available</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Cost:</strong> $<?php echo number_format($row['cost_per_hour'], 2); ?> per hour<br>
                            <strong>Total Stations:</strong> <?php echo $row['num_stations']; ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'user'): ?>
                            <a href="check_in.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-primary">Check In</a>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-primary">Login to Check In</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No available charging stations at the moment. Please check back later.</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 
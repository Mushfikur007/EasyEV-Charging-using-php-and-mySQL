<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';

// Check if user is logged in as admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    $_SESSION['message'] = "You must be logged in as an administrator to edit locations.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Check if location ID is provided
if(!isset($_GET['location_id']) || empty($_GET['location_id'])) {
    $_SESSION['message'] = "No charging location selected.";
    $_SESSION['message_type'] = "danger";
    header('Location: manage_locations.php');
    exit();
}

// Get location ID
$location_id = intval($_GET['location_id']);

// Create location object
$location = new ChargingLocation($conn);

// Get location details
$location_data = $location->getLocationById($location_id);

if(!$location_data) {
    $_SESSION['message'] = "Charging location not found.";
    $_SESSION['message_type'] = "danger";
    header('Location: manage_locations.php');
    exit();
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $num_stations = isset($_POST['num_stations']) ? intval($_POST['num_stations']) : 0;
    $cost_per_hour = isset($_POST['cost_per_hour']) ? floatval($_POST['cost_per_hour']) : 0;
    
    // Validate form data
    $errors = [];
    
    if(empty($description)) {
        $errors[] = "Description is required";
    }
    
    if($num_stations <= 0) {
        $errors[] = "Number of stations must be greater than 0";
    }
    
    if($cost_per_hour <= 0) {
        $errors[] = "Cost per hour must be greater than 0";
    }
    
    // If no errors, update location
    if(empty($errors)) {
        // Set location properties
        $location->location_id = $location_id;
        $location->description = $description;
        $location->num_stations = $num_stations;
        $location->cost_per_hour = $cost_per_hour;
        
        // Update location
        if($location->update()) {
            $_SESSION['message'] = "Charging location updated successfully.";
            $_SESSION['message_type'] = "success";
            header('Location: manage_locations.php');
            exit();
        } else {
            $_SESSION['message'] = "Failed to update charging location.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
        $_SESSION['message_type'] = "danger";
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Charging Location</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?location_id=' . $location_id); ?>" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location ID</label>
                        <input type="text" class="form-control" id="location_id" value="<?php echo $location_data['location_id']; ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($location_data['description']); ?>" required>
                        <div class="invalid-feedback">Please enter a description.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="num_stations" class="form-label">Number of Stations</label>
                        <input type="number" class="form-control" id="num_stations" name="num_stations" value="<?php echo $location_data['num_stations']; ?>" min="1" required>
                        <div class="invalid-feedback">Please enter a valid number of stations (minimum 1).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cost_per_hour" class="form-label">Cost per Hour ($)</label>
                        <input type="number" class="form-control" id="cost_per_hour" name="cost_per_hour" value="<?php echo $location_data['cost_per_hour']; ?>" min="0.01" step="0.01" required>
                        <div class="invalid-feedback">Please enter a valid cost per hour (minimum $0.01).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Created At</label>
                        <input type="text" class="form-control" id="created_at" value="<?php echo $location_data['created_at']; ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="updated_at" class="form-label">Last Updated</label>
                        <input type="text" class="form-control" id="updated_at" value="<?php echo $location_data['updated_at']; ?>" readonly>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Location</button>
                        <a href="manage_locations.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingLocation.php';

// Check if user is logged in as admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    $_SESSION['message'] = "You must be logged in as an administrator to manage locations.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create location object
$location = new ChargingLocation($conn);

// Process form submission for adding a new location
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
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
    
    // If no errors, add location
    if(empty($errors)) {
        // Set location properties
        $location->description = $description;
        $location->num_stations = $num_stations;
        $location->cost_per_hour = $cost_per_hour;
        
        // Create location
        if($location->create()) {
            $_SESSION['message'] = "Charging location added successfully.";
            $_SESSION['message_type'] = "success";
            header('Location: manage_locations.php');
            exit();
        } else {
            $_SESSION['message'] = "Failed to add charging location.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
        $_SESSION['message_type'] = "danger";
    }
}

// Get all locations
$all_locations = $location->getAllLocations();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Charging Locations</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
        <i class="fas fa-plus me-2"></i>Add New Location
    </button>
</div>

<ul class="nav nav-tabs mb-4" id="locationTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Locations</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab" aria-controls="available" aria-selected="false">Available Locations</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="full-tab" data-bs-toggle="tab" data-bs-target="#full" type="button" role="tab" aria-controls="full" aria-selected="false">Full Locations</button>
    </li>
</ul>

<div class="tab-content" id="locationTabsContent">
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <?php if($all_locations->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Stations</th>
                            <th>Cost/Hour</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $all_locations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['location_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo $row['num_stations']; ?></td>
                                <td>$<?php echo number_format($row['cost_per_hour'], 2); ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td><?php echo $row['updated_at']; ?></td>
                                <td>
                                    <a href="edit_location.php?location_id=<?php echo $row['location_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No charging locations found. Add a new location to get started.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="tab-pane fade" id="available" role="tabpanel" aria-labelledby="available-tab">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Click on the "Available Locations" tab to view locations with available charging stations.
        </div>
    </div>
    
    <div class="tab-pane fade" id="full" role="tabpanel" aria-labelledby="full-tab">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Click on the "Full Locations" tab to view locations with no available charging stations.
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLocationModalLabel">Add New Charging Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                        <div class="invalid-feedback">Please enter a description.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="num_stations" class="form-label">Number of Stations</label>
                        <input type="number" class="form-control" id="num_stations" name="num_stations" min="1" required>
                        <div class="invalid-feedback">Please enter a valid number of stations (minimum 1).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cost_per_hour" class="form-label">Cost per Hour ($)</label>
                        <input type="number" class="form-control" id="cost_per_hour" name="cost_per_hour" min="0.01" step="0.01" required>
                        <div class="invalid-feedback">Please enter a valid cost per hour (minimum $0.01).</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Add Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Load available and full locations via AJAX when tabs are clicked
    document.addEventListener('DOMContentLoaded', function() {
        const availableTab = document.getElementById('available-tab');
        const fullTab = document.getElementById('full-tab');
        const availableContent = document.getElementById('available');
        const fullContent = document.getElementById('full');
        
        availableTab.addEventListener('click', function() {
            if (availableContent.innerHTML.includes('Click on the')) {
                availableContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                
                // Make an AJAX call to get available locations
                fetch('get_available_locations.php')
                    .then(response => response.text())
                    .then(data => {
                        availableContent.innerHTML = data;
                    })
                    .catch(error => {
                        availableContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading available locations: ${error}
                            </div>
                        `;
                    });
            }
        });
        
        fullTab.addEventListener('click', function() {
            if (fullContent.innerHTML.includes('Click on the')) {
                fullContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                
                // Make an AJAX call to get full locations
                fetch('get_full_locations.php')
                    .then(response => response.text())
                    .then(data => {
                        fullContent.innerHTML = data;
                    })
                    .catch(error => {
                        fullContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading full locations: ${error}
                            </div>
                        `;
                    });
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?> 
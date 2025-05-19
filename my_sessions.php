<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/ChargingSession.php';

// Check if user is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'user') {
    $_SESSION['message'] = "You must be logged in as a user to view your sessions.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit();
}

// Create session object
$session = new ChargingSession($conn);

// Get active and past sessions
$active_sessions = $session->getActiveSessionForUser($_SESSION['user_id']);
$past_sessions = $session->getPastSessionsForUser($_SESSION['user_id']);
?>

<h2 class="mb-4">My Charging Sessions</h2>

<ul class="nav nav-tabs mb-4" id="sessionTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">Active Sessions</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">Past Sessions</button>
    </li>
</ul>

<div class="tab-content" id="sessionTabsContent">
    <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
        <?php if($active_sessions->num_rows > 0): ?>
            <?php while($row = $active_sessions->fetch_assoc()): ?>
                <?php
                // Calculate current duration and cost
                $check_in_time = strtotime($row['check_in_time']);
                $current_time = time();
                $duration_hours = ($current_time - $check_in_time) / 3600; // Convert seconds to hours
                $current_cost = $duration_hours * $row['cost_per_hour'];
                ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($row['description']); ?></h5>
                        <span class="badge bg-primary">Active</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Check-in Time:</strong> <?php echo $row['check_in_time']; ?></p>
                                <p><strong>Duration:</strong> <?php echo number_format($duration_hours, 2); ?> hours</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cost per Hour:</strong> $<?php echo number_format($row['cost_per_hour'], 2); ?></p>
                                <p><strong>Current Cost:</strong> $<?php echo number_format($current_cost, 2); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="check_out.php?session_id=<?php echo $row['session_id']; ?>" class="btn btn-primary">Check Out</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                You have no active charging sessions.
                <a href="available_locations.php" class="alert-link">Find available charging stations</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
        <?php if($past_sessions->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Check-in Time</th>
                            <th>Check-out Time</th>
                            <th>Duration</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $past_sessions->fetch_assoc()): ?>
                            <?php
                            $check_in_time = strtotime($row['check_in_time']);
                            $check_out_time = strtotime($row['check_out_time']);
                            $duration_hours = ($check_out_time - $check_in_time) / 3600; // Convert seconds to hours
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo $row['check_in_time']; ?></td>
                                <td><?php echo $row['check_out_time']; ?></td>
                                <td><?php echo number_format($duration_hours, 2); ?> hours</td>
                                <td>$<?php echo number_format($row['total_cost'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                You have no past charging sessions.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
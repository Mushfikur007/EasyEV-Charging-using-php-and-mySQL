<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyEV Charging</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap and FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-charging-station me-2"></i>EasyEV Charging
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Home</a>
                        </li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php if($_SESSION['user_type'] == 'admin'): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="adminDropdown">
                                        <li><a class="dropdown-item" href="manage_locations.php"><i class="fas fa-map-marker-alt me-2"></i>Manage Locations</a></li>
                                        <li><a class="dropdown-item" href="view_users.php"><i class="fas fa-users me-2"></i>View Users</a></li>
                                        <li><a class="dropdown-item" href="view_checked_in_users.php"><i class="fas fa-clipboard-check me-2"></i>Checked-in Users</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="available_locations.php"><i class="fas fa-map-marked-alt me-1"></i> Available Stations</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="my_sessions.php"><i class="fas fa-history me-1"></i> My Sessions</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="search_locations.php"><i class="fas fa-search me-1"></i> Search</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i> Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container py-4">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check-circle' : ($_SESSION['message_type'] == 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); 
                unset($_SESSION['message_type']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
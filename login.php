<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/User.php';

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Process login form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate form data
    $errors = [];
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, attempt to login
    if(empty($errors)) {
        // Create user object
        $user = new User($conn);
        $user->email = $email;
        $user->password = $password;
        
        // Login user
        if($user->login()) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['name'] = $user->name;
            $_SESSION['email'] = $user->email;
            $_SESSION['user_type'] = $user->user_type;
            
            // Set success message
            $_SESSION['message'] = "Welcome back, " . $user->name . "!";
            $_SESSION['message_type'] = "success";
            
            // Redirect to dashboard
            header('Location: index.php');
            exit();
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card auth-form">
            <div class="card-header">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
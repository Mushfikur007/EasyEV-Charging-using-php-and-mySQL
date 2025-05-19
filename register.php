<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'classes/User.php';

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Process registration form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'user';
    
    // Validate form data
    $errors = [];
    
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, register user
    if(empty($errors)) {
        // Create user object
        $user = new User($conn);
        $user->name = $name;
        $user->email = $email;
        $user->phone = $phone;
        $user->password = $password;
        $user->user_type = $user_type;
        
        // Register user
        if($user->register()) {
            // Set success message
            $_SESSION['message'] = "Registration successful! Please login.";
            $_SESSION['message_type'] = "success";
            
            // Redirect to login page
            header('Location: login.php');
            exit();
        } else {
            $errors[] = "Email already exists or registration failed";
        }
    }
}
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card auth-form">
            <div class="card-header">
                <h4 class="mb-0">Register</h4>
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
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                        <div class="invalid-feedback">Please enter your phone number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter a password (minimum 6 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="user_type_user" value="user" <?php echo (!isset($user_type) || $user_type == 'user') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="user_type_user">
                                Regular User
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="user_type_admin" value="admin" <?php echo (isset($user_type) && $user_type == 'admin') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="user_type_admin">
                                Administrator
                            </label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php
session_start();
include("includes/config.php");

// Redirect if already logged in
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Since password was stored using MySQL's PASSWORD() function,
            // we need to compare using the same function
            $checkQuery = $conn->query("SELECT PASSWORD('$password') AS hashed_password");
            $hashedResult = $checkQuery->fetch_assoc();
            $inputHashedPassword = $hashedResult['hashed_password'];
            
            if ($inputHashedPassword === $row['password']) {
                $_SESSION['user_type'] = 'admin';
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['username'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Incorrect username or password, please try again";
            }
        } else {
            $error = "Invalid credentials";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="assets/css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <div class="school-logo">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h3>Admin Login</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert" id="errorAlert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
                <?php endif; ?>
                
                <form id="loginForm" action="" method="post">
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to hide error message after 3 seconds
        function hideErrorMessage() {
            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.transition = 'opacity 0.5s ease';
                    errorAlert.style.opacity = '0';
                    
                    // Remove element from DOM after fade out
                    setTimeout(() => {
                        errorAlert.remove();
                    }, 500);
                }, 3000);
            }
        }

        // Call the function when page loads
        document.addEventListener('DOMContentLoaded', hideErrorMessage);
        
        // Simple form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('The fields cannot be empty');
            }
        });
    </script>
</body>
</html>
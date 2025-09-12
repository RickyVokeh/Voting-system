<?php
session_start();
include("../includes/config.php");

// Redirect if already logged in
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'voter') {
    header("Location: voting.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voter_id = trim($_POST['voter_id']);
    $name = trim($_POST['name']);
    
    // Validate inputs
    if (empty($voter_id) || empty($name)) {
        $error = "Please enter both Voter ID and Name";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM voters WHERE voter_id = ? AND name = ?");
        $stmt->bind_param("ss", $voter_id, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $voter = $result->fetch_assoc();
            
            // Check if voter has already voted
            if ($voter['has_voted']) {
                $error = "You have already voted. You can only vote once, thank you for voting.";
            } else {
                $_SESSION['user_type'] = 'voter';
                $_SESSION['voter_id'] = $voter['voter_id'];
                $_SESSION['voter_name'] = $voter['name'];
                header("Location: voting.php");
                exit();
            }
        } else {
            $error = "Invalid Voter ID or Name. Please check your credentials.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/voter_login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <div class="school-logo">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <h2>Voter Login</h2>
                <p>Enter your details to cast your vote</p>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" id="errorAlert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
                <?php endif; ?>
                
                <form id="loginForm" action="" method="post">
                    <div class="form-group">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="text" class="form-control" id="voter_id" name="voter_id" placeholder="Voter ID" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login & Vote
                    </button>
                </form>
                
                <div class="login-help">
                    <p>Having trouble voting? Contact the election committee for assistance.</p>
                </div>
            </div>
        </div>
    </div>
    

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
            const voterId = document.getElementById('voter_id').value.trim();
            const name = document.getElementById('name').value.trim();
            
            if (!voterId || !name) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>
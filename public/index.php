<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS Technical College</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="../assets/js/index.js" defer></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>ICS Technical College</h1>
            <h1>School Voting System 2025</h1>
        </div>
    </header>
    
    <div class="container">
        <section class="hero">
            <h2>Secure Student Elections</h2>
            <p><i>Cast your vote for student council positions in a secure and transparent manner. Your vote matters!</i></p>
        </section>
        
        <div class="login-options">
            <div class="login-card">
                <div class="icon-container admin-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h3>Admin Login</h3>
                <p>Access the admin dashboard to manage elections, candidates, and view results</p>
                <button class="btn btn-admin" onclick="location.href='admin_login.php'">Admin Login</button>
            </div>
            
            <div class="login-card">
                <div class="icon-container voter-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3>Voter Login</h3>
                <p>Login with your student credentials to cast your vote in active elections</p>
                <button class="btn" onclick="location.href='voter_login.php'">Voter Login</button>
            </div>
        </div>
        
        <section class="features">
            <h2>System Features</h2>
            <div class="feature-grid">
                <div class="feature">
                    <h3>Secure Voting</h3>
                    <p>Each voter can only vote once per position with secure authentication</p>
                    <p>Each voter can only vote once and vote for only one candidate per position</p>
                </div>
                <div class="feature">
                    <h3>Real-time Results</h3>
                    <p>Elections results will be displayed once the votes are casted</p>
                </div>
                <div class="feature">
                    <h3>Easy Management</h3>
                    <p>UThe voting process is secure and the results cannot be manipulated</p>
                </div>
            </div>
        </section>
    </div>
    <?php
    include("includes/footer.php");
    ?>
    
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="../assets/js/index.js" defer></script>
</head>
<body>
    <!-- Banner with Logo -->
    <div class="banner">
        <div class="banner-content">
            <div class="logo">
                <i class="fas fa-vote-yea"></i>
            </div>
            <div class="banner-text">
                <h1>School Voting System</h1>
                <p>Secure & Transparent Student Elections</p>
            </div>
        </div>
    </div>
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h2>Welcome to Our Digital Voting Platform</h2>
            <p>Empowering students to participate in fair, secure, and transparent elections. Cast your vote with confidence and help shape your school's future.</p>
        </div>
    </div>
    
    <!-- Main Content with Login Cards -->
    <div class="main-content">
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
    </div>
    
    <!-- System Description Section -->
    <div class="system-description">
        <div class="description-content">
            <h2>Why Choose Our Voting System?</h2>
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Maximum Security</h3>
                    <p>Advanced encryption and authentication ensure that every vote is secure and tamper-proof.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <h3>Fast & Efficient</h3>
                    <p>Streamlined voting process with instant results calculation and real-time updates.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Mobile Friendly</h3>
                    <p>Accessible on any device, allowing voters to participate from anywhere on campus.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Real-time Analytics</h3>
                    <p>Comprehensive reporting and analytics for transparent election monitoring.</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("includes/footer.php")
    ?>
</body>
</html>
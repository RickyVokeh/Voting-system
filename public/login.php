<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
        }
        
        /* Banner Styles */
        .banner {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .banner-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 0 20px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4a6da7 0%, #2c3e50 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .banner-text {
            text-align: center;
        }
        
        .banner-text h1 {
            color: #2c3e50;
            font-size: 2.2rem;
            margin-bottom: 5px;
        }
        
        .banner-text p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        /* Hero Section */
        .hero-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 60px 20px;
            text-align: center;
            color: white;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        
        .login-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 900px;
            width: 100%;
        }
        
        /* Modified Login Cards - Reduced Size */
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            width: 100%;
            max-width: 320px; /* Reduced from 380px */
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }
        
        .login-card:nth-child(1)::before {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
        }
        
        .login-card:nth-child(2)::before {
            background: linear-gradient(to right, #2193b0, #6dd5ed);
        }
        
        .icon-container {
            width: 70px; /* Reduced from 90px */
            height: 70px; /* Reduced from 90px */
            margin: 0 auto 15px; /* Reduced margin */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px; /* Reduced from 40px */
            color: white;
        }
        
        .admin-icon {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
            box-shadow: 0 5px 15px rgba(255, 75, 43, 0.4);
        }
        
        .voter-icon {
            background: linear-gradient(to right, #2193b0, #6dd5ed);
            box-shadow: 0 5px 15px rgba(33, 147, 176, 0.4);
        }
        
        .login-card h3 {
            font-size: 20px; /* Reduced from 24px */
            margin-bottom: 12px; /* Reduced margin */
            color: #333;
        }
        
        .login-card p {
            color: #666;
            margin-bottom: 20px; /* Reduced margin */
            line-height: 1.5;
            font-size: 0.95rem; /* Slightly smaller text */
        }
        
        .btn {
            display: inline-block;
            padding: 10px 25px; /* Slightly reduced padding */
            background: #4a6da7;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px; /* Slightly smaller font */
        }
        
        .btn-admin {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        /* System Description Section */
        .system-description {
            background: rgba(255, 255, 255, 0.9);
            padding: 50px 20px;
            text-align: center;
        }
        
        .description-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .system-description h2 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 30px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .feature {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-5px);
        }
        
        .feature i {
            font-size: 2.5rem;
            color: #4a6da7;
            margin-bottom: 15px;
        }
        
        .feature h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .feature p {
            color: #7f8c8d;
            line-height: 1.5;
        }
        
        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .banner-content {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .login-options {
                flex-direction: column;
                align-items: center;
            }
            
            .login-card {
                max-width: 100%;
            }
            
            .hero-section h2 {
                font-size: 2rem;
            }
            
            .hero-section p {
                font-size: 1.1rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
    
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 School Voting System. All rights reserved. | Designed for Educational Institutions</p>
    </div>
</body>
</html>
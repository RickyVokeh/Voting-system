
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Options with Icons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: grey;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 900px;
            width: 100%;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 100%;
            max-width: 380px;
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
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
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
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .login-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #4a6da7;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
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
        
        @media (max-width: 768px) {
            .login-options {
                flex-direction: column;
                align-items: center;
            }
            
            .login-card {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
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
</body>
</html>
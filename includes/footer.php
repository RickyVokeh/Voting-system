<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <style>
        /* Footer Styles */
.footer {
    background: #f7aa05;
    color: white;
    padding: 25px 0;
    margin-top: 40px;
    position: relative;
    width: 100%;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}


.footer-info {
    margin-bottom: 15px;
}

.footer-info p {
    margin: 5px 0;
    color: white;
}

.footer-copyright {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 15px;
    width: 100%;
}

.footer-copyright p {
    margin: 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

/* Sticky footer behavior */
.admin-container {
    display: flex;
    min-height: 100vh;
    flex-direction: column;
}

.main-content {
    flex: 1;
}

/* Responsive footer */
@media (max-width: 768px) {
    .footer {
        padding: 20px 0;
    }

    .footer-links {
        flex-direction: column;
        gap: 15px;
    }

    .footer-content {
        padding: 0 15px;
    }
}
    </style>
</head>
<body>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-info">
            <p><i class="fas fa-building"></i>Ics Technical College School Voting System Administration</p>
            <p><i class="fas fa-clock"></i> Always Available for Election Management</p>
        </div>
        
        <div class="footer-copyright">
            <p>&copy; 2025 School Voting System. All rights reserved. | Made by Ricky Tech</p>
        </div>
    </div>
</footer>
</body>
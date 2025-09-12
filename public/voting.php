<?php
session_start();
include("../includes/config.php");

// Redirect if not logged in as voter
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'voter') {
    header("Location: voter_login.php");
    exit();
}

// Check if voter has already voted
$voter_id = $_SESSION['voter_id'];
$check_voted = $conn->query("SELECT has_voted FROM voters WHERE voter_id = '$voter_id'");
$voter_status = $check_voted->fetch_assoc();

if ($voter_status['has_voted']) {
    // Voter has already voted, show thank you message
    $already_voted = true;
} else {
    $already_voted = false;
    
    // Get all positions
    $positions_result = $conn->query("SELECT * FROM positions ORDER BY position_id");
    
    // Process vote if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vote'])) {
        $success = true;
        
        // Check if all positions have been voted for
        $positions_count = $positions_result->num_rows;
        $votes_count = count($_POST) - 1; // Subtract 1 for the submit button
        
        if ($votes_count < $positions_count) {
            $error = "Please vote for all positions.";
            $success = false;
        } else {
            // Process each vote
            foreach ($_POST as $key => $value) {
                if ($key !== 'submit_vote') {
                    $candidate_id = intval($value);
                    $position_id = intval(str_replace('position_', '', $key));
                    
                    // Insert vote into database
                    $insert_query = "INSERT INTO votes (voter_id, candidate_id, position_id) 
                                    VALUES ('$voter_id', $candidate_id, $position_id)";
                    
                    if (!$conn->query($insert_query)) {
                        $error = "Error recording your vote. Please try again.";
                        $success = false;
                        break;
                    }
                }
            }
            
            if ($success) {
                // Mark voter as having voted
                $conn->query("UPDATE voters SET has_voted = 1 WHERE voter_id = '$voter_id'");
                $message = "Thank you for voting! Your vote has been recorded.";
                $already_voted = true;
            }
        }
    }
    
    // Reset positions result pointer
    $positions_result = $conn->query("SELECT * FROM positions ORDER BY position_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Interface</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/voting.cs">
    <style>
        /* Voting Interface Styles */
:root {
    --primary: #4a6da7;
    --secondary: #7b9acc;
    --accent: #f47a60;
    --light: #f8f9fa;
    --dark: #343a40;
    --success: #28a745;
    --danger: #dc3545;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
}

.voting-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.voting-header {
    background: rgb(252, 165, 3);
    color: white;
    padding: 35px 30px 35px 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.voting-header h1 {
    font-size: 1.8rem;
}

.voter-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn-logout {
    background-color: #17a2b8;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-logout:hover {
    background-color: #3b994f;
}

.voting-content {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.thank-you-message {
    text-align: center;
    padding: 40px 20px;
}

.thank-you-message .icon {
    font-size: 4rem;
    color: var(--success);
    margin-bottom: 20px;
}

.thank-you-message h2 {
    color: var(--success);
    margin-bottom: 15px;
}

.thank-you-message p {
    margin-bottom: 10px;
    color: #6c757d;
}

.success-message {
    color: var(--success) !important;
    font-weight: bold;
    margin-top: 20px !important;
}

.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: opacity 0.5s ease;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.voting-instructions {
    background-color: var(--light);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid var(--primary);
}

.voting-instructions h2 {
    color: #e48213;
    margin-bottom: 10px;
}

.position-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.position-title {
    color: #4e925d;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid var(--light);
}

.candidates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.candidate-card {
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
}

.candidate-card:hover {
    border-color: var(--primary);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.candidate-card label {
    display: block;
    cursor: pointer;
    height: 100%;
}

.candidate-card input[type="radio"] {
    display: none;
}

.candidate-card input[type="radio"]:checked+.candidate-content {
    background-color: var(--danger);
    border-color: var(--primary);
}

.candidate-content {
    padding: 15px;
    text-align: center;
    height: 100%;
    transition: background-color 0.3s;
}

.candidate-photo {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 3px solid #eee;
}

.candidate-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 2.5rem;
    color: #999;
}

.candidate-card h4 {
    color: var(--dark);
    margin-bottom: 5px;
}

.submit-section {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid var(--light);
}

.btn-submit {
    background-color: var(--success);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-submit:hover {
    background-color: #e48213;
}

.vote-warning {
    margin-top: 10px;
    color: var(--danger);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .voting-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .candidates-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }

    .candidate-photo,
    .candidate-placeholder {
        width: 80px;
        height: 80px;
    }
}
    </style>
</head>
<body>
    <div class="voting-container">
        <header class="voting-header">
            <h1>Ics Technical College School Voting System</h1>
            <div class="voter-info">
                <span>Welcome, <?php echo $_SESSION['voter_name']; ?> </span>
                <a href="voter_logout.php" class="btn-logout">Logout</a>
            </div>
        </header>

        <div class="voting-content">
            <?php if ($already_voted): ?>
                <div class="thank-you-message">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Thank You for Voting!</h2>
                    <p>You have successfully cast your vote for your prefered candidate.</p>
                    <p>Election results will be available after voting closes.</p>
                    <?php if (isset($message)) echo "<p class='success-message'>$message</p>"; ?>
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="voting-instructions">
                    <h2>Cast Your Vote</h2>
                    <p>Please select one candidate for each position. You can only vote once for each position.</p>
                </div>
                
                <form id="votingForm" action="" method="post">
                    <?php while ($position = $positions_result->fetch_assoc()): ?>
                        <div class="position-section">
                            <h3 class="position-title"><?php echo $position['position_name']; ?></h3>
                            <div class="candidates-grid">
                                <?php
                                $position_id = $position['position_id'];
                                $candidates_result = $conn->query("
                                    SELECT * FROM candidates 
                                    WHERE position_id = $position_id 
                                    ORDER BY name
                                ");
                                
                                while ($candidate = $candidates_result->fetch_assoc()): 
                                ?>
                                    <div class="candidate-card">
                                        <label>
                                            <input type="radio" name="position_<?php echo $position_id; ?>" 
                                                   value="<?php echo $candidate['candidate_id']; ?>" required>
                                            <div class="candidate-content">
                                                <?php if (!empty($candidate['photo_url'])): ?>
                                                    <img src="<?php echo $candidate['photo_url']; ?>" 
                                                         alt="<?php echo $candidate['name']; ?>" 
                                                         class="candidate-photo">
                                                <?php else: ?>
                                                    <div class="candidate-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <h4><?php echo $candidate['name']; ?></h4>
                                            </div>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div class="submit-section">
                        <button type="submit" name="submit_vote" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Submit Your Vote
                        </button>
                        <p class="vote-warning">Once submitted, your vote cannot be changed.</p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
     <?php
    include("../includes/footer.php");
    ?>

    <script>
        // Confirm before submitting vote
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to submit your vote? Click cancel to confirm your choice first. You cannot change your vote after submission.')) {
                e.preventDefault();
            }
        });
        
        // Auto-hide alert after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>
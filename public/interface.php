<?php
session_start();
include("includes/config.php");

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
    <title>Voting Interface - ICS Technical College</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/interface.css">
</head>
<body>
    <div class="voting-container">
        <header class="voting-header">
            <h1><i class="fas fa-vote-yea"></i> ICS Technical College Voting System</h1>
            <div class="voter-info">
                <span class="voter-badge"><i class="fas fa-user"></i> <?php echo $_SESSION['voter_name']; ?></span>
                <a href="voter_logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </header>

        <div class="voting-content">
            <?php if ($already_voted): ?>
                <div class="thank-you-message">
                    <div class="success-icon">
                        <div class="circle">
                            <i class="fas fa-check checkmark"></i>
                        </div>
                    </div>
                    <h2>Thank You for Voting!</h2>
                    <p>Your vote has been successfully recorded and will help shape our college's future.</p>
                    <p>Election results will be available after the voting period ends.</p>
                    <?php if (isset($message)) echo "<p class='success-message'>$message</p>"; ?>
                    
                    <div style="margin-top: 30px;">
                        <a href="voter_logout.php" class="btn-logout" style="display: inline-flex;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="voting-instructions">
                    <h2><i class="fas fa-info-circle"></i> Voting Instructions</h2>
                    <p><strong>Please carefully review these instructions before voting:</strong></p>
                    <p>• Select <strong>one candidate</strong> for each position by clicking on their card</p>
                    <p>• You can only vote <strong>once per position</strong></p>
                    <p>• Review your selections before submitting - <strong>votes cannot be changed</strong> after submission</p>
                    <p>• Your vote is <strong>confidential and secure</strong></p>
                </div>
                
                <form id="votingForm" action="" method="post">
                    <?php while ($position = $positions_result->fetch_assoc()): ?>
                        <div class="position-section">
                            <h3 class="position-title">
                                <i class="fas fa-briefcase"></i> <?php echo $position['position_name']; ?>
                            </h3>
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
                                                        <i class="fas fa-user-tie"></i>
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
                        <p class="vote-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Once submitted, your vote cannot be changed.
                        </p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    include("includes/footer.php");
    ?>

    <script>
        // Confirm before submitting vote
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            // Check if all positions have a selection
            const radioGroups = document.querySelectorAll('input[type="radio"]');
            const selected = document.querySelectorAll('input[type="radio"]:checked');
            const positions = document.querySelectorAll('.position-section');
            
            if (selected.length < positions.length) {
                alert('Please vote for all positions before submitting.');
                e.preventDefault();
                return;
            }
            
            if (!confirm('Are you sure you want to submit your vote? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
        
        // Enhanced radio selection feedback
        document.querySelectorAll('.candidate-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected class from all cards in this position
                const positionName = this.name;
                document.querySelectorAll(`input[name="${positionName}"]`).forEach(r => {
                    r.closest('.candidate-card').classList.remove('selected');
                });
                
                // Add selected class to current card
                this.closest('.candidate-card').classList.add('selected');
            });
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
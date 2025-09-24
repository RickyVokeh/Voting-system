<?php
include("includes/config.php");
include("includes/auth_check.php");

// Initialize variables
$message = "";
$error = "";

// Handle CSV upload for users
if (isset($_POST['upload_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip the header row
        fgetcsv($handle);
        
        $successCount = 0;
        $errorCount = 0;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) == 2) {
                $voter_id = $conn->real_escape_string(trim($data[0]));
                $name = $conn->real_escape_string(trim($data[1]));
                
                // Check if voter already exists
                $checkQuery = "SELECT * FROM voters WHERE voter_id = '$voter_id'";
                $checkResult = $conn->query($checkQuery);
                
                if ($checkResult->num_rows == 0) {
                    // Insert new voter
                    $insertQuery = "INSERT INTO voters (voter_id, name) VALUES ('$voter_id', '$name')";
                    if ($conn->query($insertQuery)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    $errorCount++;
                }
            }
        }
        
        fclose($handle);
        
        if ($successCount > 0) {
            $message = "You have Successfully imported $successCount voters.";
        }
        if ($errorCount > 0) {
            $error = "Failed to import $errorCount voters (possibility of  duplicates, please check and try again).";
        }
    } else {
        $error = "Please select a valid CSV file.";
    }
}

// Handle individual voter addition
if (isset($_POST['add_voter'])) {
    $voter_id = $conn->real_escape_string(trim($_POST['voter_id']));
    $name = $conn->real_escape_string(trim($_POST['name']));
    
    if (!empty($voter_id) && !empty($name)) {
        // Check if voter already exists
        $checkQuery = "SELECT * FROM voters WHERE voter_id = '$voter_id'";
        $checkResult = $conn->query($checkQuery);
        
        if ($checkResult->num_rows == 0) {
            $insertQuery = "INSERT INTO voters (voter_id, name) VALUES ('$voter_id', '$name')";
            if ($conn->query($insertQuery)) {
                $message = "Voter added successfully.";
            } else {
                $error = "Error adding voter: " . $conn->error;
            }
        } else {
            $error = "Voter with ID '$voter_id' already exists.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

// Handle voter editing
if (isset($_POST['edit_voter'])) {
    $voter_id = $conn->real_escape_string(trim($_POST['voter_id']));
    $name = $conn->real_escape_string(trim($_POST['name']));
    $original_voter_id = $conn->real_escape_string(trim($_POST['original_voter_id']));
    
    if (!empty($voter_id) && !empty($name)) {
        // Check if new voter ID already exists (if changed)
        if ($voter_id !== $original_voter_id) {
            $checkQuery = "SELECT * FROM voters WHERE voter_id = '$voter_id'";
            $checkResult = $conn->query($checkQuery);
            
            if ($checkResult->num_rows > 0) {
                $error = "Voter with ID '$voter_id' already exists.";
            } else {
                $updateQuery = "UPDATE voters SET voter_id = '$voter_id', name = '$name' WHERE voter_id = '$original_voter_id'";
                if ($conn->query($updateQuery)) {
                    $message = "Voter updated successfully.";
                } else {
                    $error = "Error updating voter: " . $conn->error;
                }
            }
        } else {
            $updateQuery = "UPDATE voters SET name = '$name' WHERE voter_id = '$voter_id'";
            if ($conn->query($updateQuery)) {
                $message = "Voter updated successfully.";
            } else {
                $error = "Error updating voter: " . $conn->error;
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

// Handle candidate editing
if (isset($_POST['edit_candidate'])) {
    $candidate_id = intval($_POST['candidate_id']);
    $name = $conn->real_escape_string(trim($_POST['candidate_name']));
    $position_id = intval($_POST['position_id']);
    
    // Handle photo upload
    $photo_path = "";
    if (isset($_FILES['candidate_photo']) && $_FILES['candidate_photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/candidates/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['candidate_photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $photo_path = $upload_dir . $file_name;
        
        move_uploaded_file($_FILES['candidate_photo']['tmp_name'], $photo_path);
        
        // Delete old photo if exists
        $selectQuery = "SELECT photo_url FROM candidates WHERE candidate_id = $candidate_id";
        $result = $conn->query($selectQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['photo_url']) && file_exists($row['photo_url'])) {
                unlink($row['photo_url']);
            }
        }
        
        $updateQuery = "UPDATE candidates SET name = '$name', position_id = $position_id, photo_url = '$photo_path' WHERE candidate_id = $candidate_id";
    } else {
        $updateQuery = "UPDATE candidates SET name = '$name', position_id = $position_id WHERE candidate_id = $candidate_id";
    }
    
    if ($conn->query($updateQuery)) {
        $message = "Candidate updated successfully.";
    } else {
        $error = "Error updating candidate: " . $conn->error;
    }
}

// Handle user deletion
if (isset($_GET['delete_voter'])) {
    $voter_id = $conn->real_escape_string($_GET['delete_voter']);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $deleteQuery = "DELETE FROM voters WHERE voter_id = '$voter_id'";
    
    if ($conn->query($deleteQuery)) {
        $message = "Voter deleted successfully.";
        // Redirect to same page to refresh the list
        header("Location: admin_dashboard.php?active_section=voter-management&page=$page");
        exit();
    } else {
        $error = "Error deleting voter: " . $conn->error;
    }
}

// Handle candidate addition
if (isset($_POST['add_candidate'])) {
    $name = $conn->real_escape_string(trim($_POST['candidate_name']));
    $position_id = intval($_POST['position_id']);
    
    // Handle photo upload
    $photo_path = "";
    if (isset($_FILES['candidate_photo']) && $_FILES['candidate_photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/candidates/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['candidate_photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $photo_path = $upload_dir . $file_name;
        
        move_uploaded_file($_FILES['candidate_photo']['tmp_name'], $photo_path);
    }
    
    $insertQuery = "INSERT INTO candidates (name, position_id, photo_url) VALUES ('$name', $position_id, '$photo_path')";
    
    if ($conn->query($insertQuery)) {
        $message = "You have added Candidate successfully.";
    } else {
        $error = "Error adding candidate: " . $conn->error;
    }
}

// Handle candidate deletion
if (isset($_GET['delete_candidate'])) {
    $candidate_id = intval($_GET['delete_candidate']);
    
    // Get photo path to delete the file
    $selectQuery = "SELECT photo_url FROM candidates WHERE candidate_id = $candidate_id";
    $result = $conn->query($selectQuery);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['photo_url']) && file_exists($row['photo_url'])) {
            unlink($row['photo_url']);
        }
    }
    
    $deleteQuery = "DELETE FROM candidates WHERE candidate_id = $candidate_id";
    
    if ($conn->query($deleteQuery)) {
        $message = "Candidate deleted successfully.";
    } else {
        $error = "Error deleting candidate: " . $conn->error;
    }
}

// Get statistics for dashboard
$voters_count = $conn->query("SELECT COUNT(*) as count FROM voters")->fetch_assoc()['count'];
$voted_count = $conn->query("SELECT COUNT(*) as count FROM voters WHERE has_voted = 1")->fetch_assoc()['count'];
$candidates_count = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
$positions_count = $conn->query("SELECT COUNT(*) as count FROM positions")->fetch_assoc()['count'];

// Pagination setup for voters
$voters_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $voters_per_page;

// Get total number of voters
$total_voters = $conn->query("SELECT COUNT(*) as count FROM voters")->fetch_assoc()['count'];
$total_pages = ceil($total_voters / $voters_per_page);

// Get voters for current page
$voters_query = "SELECT * FROM voters ORDER BY name LIMIT $offset, $voters_per_page";
$voters_result = $conn->query($voters_query);

// Get all candidates with position names
$candidates_result = $conn->query("
    SELECT c.*, p.position_name 
    FROM candidates c 
    LEFT JOIN positions p ON c.position_id = p.position_id 
    ORDER BY p.position_name, c.name
");

// Reset positions result pointer
$positions_result = $conn->query("SELECT * FROM positions ORDER BY position_name");

// Get election results
$results_result = $conn->query("
    SELECT p.position_name, c.name as candidate_name, COUNT(v.vote_id) as vote_count
    FROM votes v
    JOIN candidates c ON v.candidate_id = c.candidate_id
    JOIN positions p ON v.position_id = p.position_id
    GROUP BY p.position_id, c.candidate_id
    ORDER BY p.position_name, vote_count DESC
");

// Handle active section
$active_section = isset($_GET['active_section']) ? $_GET['active_section'] : 'dashboard';

// Get voter data for editing
$edit_voter_data = null;
if (isset($_GET['edit_voter'])) {
    $voter_id = $conn->real_escape_string($_GET['edit_voter']);
    $edit_voter_result = $conn->query("SELECT * FROM voters WHERE voter_id = '$voter_id'");
    if ($edit_voter_result->num_rows > 0) {
        $edit_voter_data = $edit_voter_result->fetch_assoc();
    }
}

// Get candidate data for editing
$edit_candidate_data = null;
if (isset($_GET['edit_candidate'])) {
    $candidate_id = intval($_GET['edit_candidate']);
    $edit_candidate_result = $conn->query("SELECT * FROM candidates WHERE candidate_id = $candidate_id");
    if ($edit_candidate_result->num_rows > 0) {
        $edit_candidate_data = $edit_candidate_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .pagination-info {
            color: #6c757d;
            font-weight: 500;
        }

        .pagination-btn {
            padding: 8px 16px;
            background-color: #4a6da7;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pagination-btn:hover {
            background-color: #3a5a8f;
        }

        .pagination-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .pagination-next {
            margin-left: auto;
        }

        /* Submenu Item Styles */
        .submenu-item {
            padding: 12px 20px 12px 50px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .submenu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #f47a60;
            color: white;
        }

        .submenu-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: white;
            color: white;
        }

        .submenu-item i {
            margin-right: 15px;
            font-size: 1rem;
            width: 16px;
            text-align: center;
        }

        /* Edit button styles */
        .btn-edit {
            background-color: #ffc107;
            color: #212529;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <h4>ICS College Voting System</h4>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-item <?php echo $active_section === 'dashboard' ? 'active' : ''; ?>" data-target="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </div>
                <div class="menu-item <?php echo $active_section === 'add-voters' ? 'active' : ''; ?>" data-target="add-voters">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Voters</span>
                </div>
                <div class="menu-item <?php echo $active_section === 'voter-management' ? 'active' : ''; ?>" data-target="voter-management">
                    <i class="fas fa-users"></i>
                    <span>Voters Management</span>
                </div>
                <div class="submenu-item <?php echo $active_section === 'voter-search' ? 'active' : ''; ?>" data-target="voter-search">
                    <i class="fas fa-search"></i>
                    <span>Voters Search</span>
                </div>
                <div class="menu-item <?php echo $active_section === 'add-candidates' ? 'active' : ''; ?>" data-target="add-candidates">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Candidates</span>
                </div>
                <div class="menu-item <?php echo $active_section === 'candidate-management' ? 'active' : ''; ?>" data-target="candidate-management">
                    <i class="fas fa-user-tie"></i>
                    <span>Candidates Management</span>
                </div>
                <div class="menu-item <?php echo $active_section === 'election-results' ? 'active' : ''; ?>" data-target="election-results">
                    <i class="fas fa-chart-bar"></i>
                    <span>Election Results</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="admin-header">
                <div class="header-container">
                    <h1>Voting System - Admin Dashboard</h1>
                    <div class="user-info">
                        <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                        <a href="logout.php" class="btn-logout">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Display Messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Dashboard Section -->
            <div class="content-section <?php echo $active_section === 'dashboard' ? 'active' : ''; ?>" id="dashboard">
                <h2 class="section-title">Dashboard Overview</h2>
                
                <!-- Dashboard Statistics -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <i class="fas fa-users"></i>
                        <h3>Registered Voters</h3>
                        <p><?php echo $voters_count; ?></p>
                    </div>
                    <div class="dashboard-card">
                        <i class="fas fa-vote-yea"></i>
                        <h3>Votes Cast</h3>
                        <p><?php echo $voted_count; ?></p>
                    </div>
                    <div class="dashboard-card">
                        <i class="fas fa-user-tie"></i>
                        <h3>Candidates</h3>
                        <p><?php echo $candidates_count; ?></p>
                    </div>
                    <div class="dashboard-card">
                        <i class="fas fa-briefcase"></i>
                        <h3>Positions</h3>
                        <p><?php echo $positions_count; ?></p>
                    </div>
                </div>
            </div>

            <!-- Add Voters Section -->
            <div class="content-section <?php echo $active_section === 'add-voters' ? 'active' : ''; ?>" id="add-voters">
                <h2 class="section-title">Add Voters</h2>
                
                <!-- Individual Voter Addition Form -->
                <div class="form-section">
                    <h3>Add Individual Voter</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="voter_id">Voter ID:</label>
                            <input type="text" id="voter_id" name="voter_id" class="form-control" required 
                                   value="<?php echo isset($_POST['voter_id']) ? htmlspecialchars($_POST['voter_id']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" class="form-control" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <button type="submit" name="add_voter" class="btn btn-primary">Add Voter</button>
                    </form>
                </div>

                <!-- CSV Upload Form -->
                <div class="file-upload">
                    <h3>Upload Voters via CSV file</h3>
                    <p>CSV format: voter_id, name (one voter per line)</p>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="csv_file" accept=".csv" required>
                        </div>
                        <button type="submit" name="upload_csv" class="btn btn-primary">Upload CSV file</button>
                    </form>
                </div>
            </div>

            <!-- Voter Management Section -->
            <div class="content-section <?php echo $active_section === 'voter-management' ? 'active' : ''; ?>" id="voter-management">
                <h2 class="section-title">Voter Management</h2>
                
                <!-- Edit Voter Form (shown when editing) -->
                <?php if ($edit_voter_data): ?>
                <div class="form-section">
                    <h3>Edit Voter</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="original_voter_id" value="<?php echo $edit_voter_data['voter_id']; ?>">
                        <div class="form-group">
                            <label for="edit_voter_id">Voter ID:</label>
                            <input type="text" id="edit_voter_id" name="voter_id" class="form-control" required 
                                   value="<?php echo $edit_voter_data['voter_id']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="edit_name">Full Name:</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required 
                                   value="<?php echo $edit_voter_data['name']; ?>">
                        </div>
                        <button type="submit" name="edit_voter" class="btn btn-primary">Update Voter</button>
                        <a href="?active_section=voter-management" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Voters Table with Pagination -->
                <h3>Registered Voters</h3>
                <?php if ($voters_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Voter ID</th>
                                <th>Name</th>
                                <th>Voting Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php while ($voter = $voters_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $voter['voter_id']; ?></td>
                                <td><?php echo $voter['name']; ?></td>
                                <td>
                                    <span class="<?php echo $voter['has_voted'] ? 'status-voted' : 'status-not-voted'; ?>">
                                        <?php echo $voter['has_voted'] ? 'Voted' : 'Not Voted'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?active_section=voter-management&edit_voter=<?php echo $voter['voter_id']; ?>" 
                                       class="btn btn-edit">Edit</a>
                                    <a href="?delete_voter=<?php echo $voter['voter_id']; ?>&page=<?php echo $page; ?>&active_section=voter-management" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this voter?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination Controls -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?active_section=voter-management&page=<?php echo $page - 1; ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn" style="visibility: hidden;"></span>
                        <?php endif; ?>
                        
                        <span class="pagination-info">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?active_section=voter-management&page=<?php echo $page + 1; ?>" class="pagination-btn pagination-next">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn" style="visibility: hidden;"></span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p>No voters registered yet.</p>
                <?php endif; ?>
            </div>

            <!-- Voter Search Section -->
            <div class="content-section <?php echo $active_section === 'voter-search' ? 'active' : ''; ?>" id="voter-search">
                <h2 class="section-title">Voter Search</h2>
                
                <div class="search-section">
                    <h3>Search for Voter by ID</h3>
                    <form method="GET" action="">
                        <input type="hidden" name="active_section" value="voter-search">
                        <div class="form-group">
                            <label for="search_voter_id">Enter Voter ID:</label>
                            <input type="text" id="search_voter_id" name="voter_id" class="form-control" 
                                   placeholder="Enter voter ID to search" value="<?php echo isset($_GET['voter_id']) ? htmlspecialchars($_GET['voter_id']) : ''; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Search Voter</button>
                    </form>
                    
                    <?php
                    // Handle voter search
                    if (isset($_GET['voter_id']) && !empty($_GET['voter_id'])) {
                        $search_voter_id = $conn->real_escape_string(trim($_GET['voter_id']));
                        $search_query = "SELECT * FROM voters WHERE voter_id = '$search_voter_id'";
                        $search_result = $conn->query($search_query);
                        
                        if ($search_result && $search_result->num_rows > 0) {
                            $voter_data = $search_result->fetch_assoc();
                            ?>
                            <div class="search-results">
                                <h4>Voter Details</h4>
                                <div class="voter-details">
                                    <p><strong>Voter ID:</strong> <?php echo $voter_data['voter_id']; ?></p>
                                    <p><strong>Name:</strong> <?php echo $voter_data['name']; ?></p>
                                    <p><strong>Voting Status:</strong> 
                                        <span class="<?php echo $voter_data['has_voted'] ? 'status-voted' : 'status-not-voted'; ?>">
                                            <?php echo $voter_data['has_voted'] ? 'Voted' : 'Not Voted'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="search-results">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Voter with ID "<?php echo htmlspecialchars($_GET['voter_id']); ?>" not found.
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Add Candidates Section -->
            <div class="content-section <?php echo $active_section === 'add-candidates' ? 'active' : ''; ?>" id="add-candidates">
                <h2 class="section-title">Add Candidates</h2>
                
                <!-- Add Candidate Form -->
                <form method="POST" enctype="multipart/form-data">
                    <h3>Add New Candidate</h3>
                    <div class="form-group">
                        <label for="candidate_name">Candidate Name:</label>
                        <input type="text" id="candidate_name" name="candidate_name" class="form-control" required 
                               value="<?php echo isset($_POST['candidate_name']) ? htmlspecialchars($_POST['candidate_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="position_id">Position:</label>
                        <select id="position_id" name="position_id" class="form-control" required>
                            <option value="">Select Position</option>
                            <?php 
                            // Reset and fetch positions again for the form
                            $positions_form = $conn->query("SELECT * FROM positions ORDER BY position_name");
                            while ($position = $positions_form->fetch_assoc()): ?>
                                <option value="<?php echo $position['position_id']; ?>" 
                                    <?php echo (isset($_POST['position_id']) && $_POST['position_id'] == $position['position_id']) ? 'selected' : ''; ?>>
                                    <?php echo $position['position_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="candidate_photo">Candidate Photo:</label>
                        <input type="file" id="candidate_photo" name="candidate_photo" accept="image/*">
                    </div>
                    
                    <button type="submit" name="add_candidate" class="btn btn-primary">Add Candidate</button>
                </form>
            </div>

            <!-- Candidate Management Section -->
            <div class="content-section <?php echo $active_section === 'candidate-management' ? 'active' : ''; ?>" id="candidate-management">
                <h2 class="section-title">Candidate Management</h2>
                
                <!-- Edit Candidate Form (shown when editing) -->
                <?php if ($edit_candidate_data): ?>
                <div class="form-section">
                    <h3>Edit Candidate</h3>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="candidate_id" value="<?php echo $edit_candidate_data['candidate_id']; ?>">
                        <div class="form-group">
                            <label for="edit_candidate_name">Candidate Name:</label>
                            <input type="text" id="edit_candidate_name" name="candidate_name" class="form-control" required 
                                   value="<?php echo $edit_candidate_data['name']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_position_id">Position:</label>
                            <select id="edit_position_id" name="position_id" class="form-control" required>
                                <option value="">Select Position</option>
                                <?php 
                                $positions_form = $conn->query("SELECT * FROM positions ORDER BY position_name");
                                while ($position = $positions_form->fetch_assoc()): ?>
                                    <option value="<?php echo $position['position_id']; ?>" 
                                        <?php echo ($edit_candidate_data['position_id'] == $position['position_id']) ? 'selected' : ''; ?>>
                                        <?php echo $position['position_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_candidate_photo">Candidate Photo (leave empty to keep current):</label>
                            <input type="file" id="edit_candidate_photo" name="candidate_photo" accept="image/*">
                            <?php if (!empty($edit_candidate_data['photo_url'])): ?>
                                <p>Current photo: <img src="<?php echo $edit_candidate_data['photo_url']; ?>" alt="Current Photo" style="max-width: 100px; max-height: 100px;"></p>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" name="edit_candidate" class="btn btn-primary">Update Candidate</button>
                        <a href="?active_section=candidate-management" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Candidates Table -->
                <h3>Current Candidates</h3>
                <?php if ($candidates_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($candidate = $candidates_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($candidate['photo_url'])): ?>
                                        <img src="<?php echo $candidate['photo_url']; ?>" alt="Candidate Photo" class="candidate-photo">
                                    <?php else: ?>
                                        <i class="fas fa-user-tie" style="font-size: 2rem;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $candidate['name']; ?></td>
                                <td><?php echo $candidate['position_name']; ?></td>
                                <td>
                                    <a href="?active_section=candidate-management&edit_candidate=<?php echo $candidate['candidate_id']; ?>" 
                                       class="btn btn-edit">Edit</a>
                                    <a href="?delete_candidate=<?php echo $candidate['candidate_id']; ?>&active_section=candidate-management" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this candidate?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No candidates registered yet.</p>
                <?php endif; ?>
            </div>

            <!-- Election Results Section -->
            <div class="content-section <?php echo $active_section === 'election-results' ? 'active' : ''; ?>" id="election-results">
                <h2 class="section-title">Election Results</h2>
                
                <?php if ($results_result->num_rows > 0): ?>
                    <?php
                    $current_position = "";
                    while ($result = $results_result->fetch_assoc()):
                        if ($current_position != $result['position_name']):
                            if ($current_position != ""): ?>
                                </table>
                            <?php endif; ?>
                            <h3><?php echo $result['position_name']; ?></h3>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Votes</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php $current_position = $result['position_name']; ?>
                        <?php endif; ?>
                        
                        <tr>
                            <td><?php echo $result['candidate_name']; ?></td>
                            <td><?php echo $result['vote_count']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody></table>
                <?php else: ?>
                    <p>No votes have been cast yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    include("includes/footer.php")
    ?>

     <script>
        // Sidebar navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            const submenuItems = document.querySelectorAll('.submenu-item');
            const contentSections = document.querySelectorAll('.content-section');
            
            // Function to activate a section
            function activateSection(target) {
                // Update URL without reloading the page
                const url = new URL(window.location);
                url.searchParams.set('active_section', target);
                window.history.replaceState({}, '', url);
                
                // Update active menu items
                menuItems.forEach(mi => mi.classList.remove('active'));
                submenuItems.forEach(mi => mi.classList.remove('active'));
                
                // Activate the clicked item
                const activeItem = document.querySelector(`[data-target="${target}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
                
                // Show corresponding content section
                contentSections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === target) {
                        section.classList.add('active');
                    }
                });
            }
            
            // Add click event listeners to menu items
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    activateSection(target);
                });
            });
            
            // Add click event listeners to submenu items
            submenuItems.forEach(item => {
                item.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    activateSection(target);
                });
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
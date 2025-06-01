<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];
$success_message = '';
$error_message = '';


$query = "SELECT * FROM voters WHERE id = $voter_id";
$result = mysqli_query($conn, $query);
$voter = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    
    
    $email_check_query = "SELECT * FROM voters WHERE email = '$email' AND id != $voter_id";
    $email_check_result = mysqli_query($conn, $email_check_query);
    
    if (mysqli_num_rows($email_check_result) > 0) {
        $error_message = "Email is already taken by another user. Please use a different email.";
    } else {
        
        $update_query = "UPDATE voters SET name = '$name', email = '$email', dob = '$dob' WHERE id = $voter_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Profile updated successfully!";
            
    
            $result = mysqli_query($conn, $query);
            $voter = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Error updating profile: " . mysqli_error($conn);
        }
    }

    if (!empty($_POST['new_password']) && !empty($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
    
        if (password_verify($current_password, $voter['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_update_query = "UPDATE voters SET password = '$hashed_password' WHERE id = $voter_id";
                
                if (mysqli_query($conn, $password_update_query)) {
                    $success_message = "Profile and password updated successfully!";
                } else {
                    $error_message = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $error_message = "New password and confirm password do not match!";
            }
        } else {
            $error_message = "Current password is incorrect!";
        }
    }
}

$voting_history_query = "SELECT v.vote_date, e.title as election_title, c.name as candidate_name, c.role 
                         FROM votes v 
                         JOIN elections e ON v.election_id = e.id 
                         JOIN candidates c ON v.candidate_id = c.id 
                         WHERE v.voter_id = $voter_id 
                         ORDER BY v.vote_date DESC";
$voting_history_result = mysqli_query($conn, $voting_history_query);

$total_votes_query = "SELECT COUNT(*) as total_votes FROM votes WHERE voter_id = $voter_id";
$total_votes_result = mysqli_query($conn, $total_votes_query);
$total_votes_row = mysqli_fetch_assoc($total_votes_result);
$total_votes = $total_votes_row['total_votes'];

$active_elections_query = "SELECT COUNT(*) as active_count FROM elections 
                          WHERE CONCAT(start_date, ' ', start_time) <= NOW() 
                          AND CONCAT(end_date, ' ', end_time) > NOW()";
$active_elections_result = mysqli_query($conn, $active_elections_query);
$active_elections_row = mysqli_fetch_assoc($active_elections_result);
$active_elections = $active_elections_row['active_count'];

$upcoming_elections_query = "SELECT COUNT(*) as upcoming_count FROM elections 
                            WHERE CONCAT(start_date, ' ', start_time) > NOW()";
$upcoming_elections_result = mysqli_query($conn, $upcoming_elections_query);
$upcoming_elections_row = mysqli_fetch_assoc($upcoming_elections_result);
$upcoming_elections = $upcoming_elections_row['upcoming_count'];
?>

<div class="container">
    <h1>My Profile</h1>
    
    <div class="row">
        <div class="col-md-8">
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Personal Information</h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($voter['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($voter['username']); ?>" disabled>
                            <small class="form-text text-muted">Username cannot be changed.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($voter['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($voter['dob']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="created_at">Account Created</label>
                            <input type="text" id="created_at" class="form-control" value="<?php echo date('F j, Y', strtotime($voter['created_at'])); ?>" disabled>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Change Password</h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($voter['name']); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($voter['email']); ?>">
                        <input type="hidden" name="dob" value="<?php echo htmlspecialchars($voter['dob']); ?>">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Voting Statistics</h3>
                </div>
                
                <div class="card-body">
                    <div class="stats-item">
                        <div class="stats-label">Total Votes Cast</div>
                        <div class="stats-value"><?php echo $total_votes; ?></div>
                    </div>
                    
                    <div class="stats-item">
                        <div class="stats-label">Active Elections</div>
                        <div class="stats-value"><?php echo $active_elections; ?></div>
                    </div>
                    
                    <div class="stats-item">
                        <div class="stats-label">Upcoming Elections</div>
                        <div class="stats-value"><?php echo $upcoming_elections; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Voting History</h3>
                </div>
                
                <div class="card-body">
                    <?php if (mysqli_num_rows($voting_history_result) > 0): ?>
                        <div class="voting-history">
                            <?php while ($vote = mysqli_fetch_assoc($voting_history_result)): ?>
                                <div class="history-item">
                                    <div class="history-date">
                                        <?php echo date('M j, Y', strtotime($vote['vote_date'])); ?>
                                    </div>
                                    <div class="history-details">
                                        <div class="history-election"><?php echo htmlspecialchars($vote['election_title']); ?></div>
                                        <div class="history-candidate">Voted for: <?php echo htmlspecialchars($vote['candidate_name']); ?> (<?php echo htmlspecialchars($vote['role']); ?>)</div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't participated in any elections yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
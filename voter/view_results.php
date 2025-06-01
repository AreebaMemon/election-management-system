<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

date_default_timezone_set('Asia/Karachi');

$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;
$voter_id = $_SESSION['voter_id'];

$query = "SELECT * FROM elections WHERE id = $election_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: user_dashboard.php");
    exit();
}

$election = mysqli_fetch_assoc($result);

$current_date = date('Y-m-d H:i:s');
$end_datetime = $election['end_date'] . ' ' . $election['end_time'];

$start_datetime = strtotime($election['start_date'] . ' ' . $election['start_time']);
$end_datetime = strtotime($election['end_date'] . ' ' . $election['end_time']);
$current_time = time();

$status = '';
$status_class = '';

if ($current_time >= $start_datetime && $current_time < $end_datetime) {
    $status = 'Active';
    $status_class = 'status-active';
} elseif ($current_time < $start_datetime) {
    $status = 'Upcoming';
    $status_class = 'status-upcoming';
} else {
    $status = 'Ended';
    $status_class = 'status-ended';
}


$candidates_query = "SELECT c.*, 
                    (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id AND election_id = $election_id) as vote_count
                    FROM candidates c 
                    WHERE c.election_id = $election_id 
                    ORDER BY vote_count DESC, c.role, c.name";
$candidates_result = mysqli_query($conn, $candidates_query);

$total_votes_query = "SELECT COUNT(*) as total FROM votes WHERE election_id = $election_id";
$total_votes_result = mysqli_query($conn, $total_votes_query);
$total_votes_row = mysqli_fetch_assoc($total_votes_result);
$total_votes = $total_votes_row['total'];


$user_vote_query = "SELECT candidate_id FROM votes WHERE voter_id = $voter_id AND election_id = $election_id";
$user_vote_result = mysqli_query($conn, $user_vote_query);
$has_voted = mysqli_num_rows($user_vote_result) > 0;
$user_vote = $has_voted ? mysqli_fetch_assoc($user_vote_result)['candidate_id'] : 0;
?>

<div class="container">
    <h1>Election Results</h1>
    
    <div class="card">
        <div class="election-header">
            <h2><?php echo htmlspecialchars($election['title'] ?? ''); ?></h2>
            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
        </div>
        
        <div class="election-details">
            <p><strong>Start:</strong> <?php echo date('Y-m-d h:i A', $start_datetime); ?></p>
            <p><strong>End:</strong> <?php echo date('Y-m-d h:i A', $end_datetime); ?></p>
            <p><strong>Total Votes Cast:</strong> <?php echo $total_votes; ?></p>
        </div>

        <?php if (!empty($election['description'])): ?>
            <div class="election-description">
                <p><?php echo htmlspecialchars($election['description']); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($status == 'Active' && !$has_voted): ?>
            <div class="alert alert-info">
                This election is still active. Results will be finalized when the election ends. 
                <a href="cast_vote.php?election_id=<?php echo $election_id; ?>" class="btn btn-sm btn-primary">Cast Your Vote</a>
            </div>
        <?php elseif ($status == 'Active' && $has_voted): ?>
            <div class="alert alert-success">
                You have already voted in this election. Results will be finalized when the election ends.
            </div>
        <?php elseif ($status == 'Upcoming'): ?>
            <div class="alert alert-warning">
                This election has not started yet. Results will be available after the election ends.
            </div>
        <?php endif; ?>

        <h3>Results</h3>
        
        <?php if ($status == 'Ended' || $has_voted || $_SESSION['user_type'] == 'admin'): ?>
            <div class="results-container">
                <?php if (mysqli_num_rows($candidates_result) > 0): ?>
                    <div class="results-grid">
                        <?php while ($candidate = mysqli_fetch_assoc($candidates_result)): 
                            $vote_percentage = $total_votes > 0 ? ($candidate['vote_count'] / $total_votes) * 100 : 0;
                            $is_user_choice = $user_vote == $candidate['id'];
                        ?>
                            <div class="result-card <?php echo $is_user_choice ? 'user-voted' : ''; ?>">
                                <div class="candidate-info">
                                    <h4><?php echo htmlspecialchars($candidate['name']); ?></h4>
                                    <p class="candidate-role"><?php echo htmlspecialchars($candidate['role']); ?></p>
                                    <p class="candidate-batch">Batch: <?php echo htmlspecialchars($candidate['batch']); ?></p>
                                    
                                    <?php if ($is_user_choice): ?>
                                        <div class="user-choice-badge">Your Choice</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="vote-stats">
                                    <div class="vote-bar-container">
                                        <div class="vote-bar" style="width: <?php echo $vote_percentage; ?>%"></div>
                                    </div>
                                    <div class="vote-numbers">
                                        <span class="vote-count"><?php echo $candidate['vote_count']; ?> votes</span>
                                        <span class="vote-percentage"><?php echo number_format($vote_percentage, 1); ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No candidates were registered for this election.
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Results will be available once you have voted or when the election ends.
            </div>
        <?php endif; ?>
        
        <div class="form-actions">
            <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

date_default_timezone_set('Asia/Karachi');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;

$query = "SELECT * FROM elections WHERE id = $election_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: user_dashboard.php");
    exit();
}

$election = mysqli_fetch_assoc($result);

$current_date = date('Y-m-d H:i:s');
$end_datetime = $election['end_date'] . ' ' . $election['end_time'];

if (strtotime($end_datetime) < strtotime($current_date)) {
    $_SESSION['error'] = "This election has ended. You cannot cast a vote.";
    header("Location: user_dashboard.php");
    exit();
}

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


$voter_id = $_SESSION['voter_id'];
$check_query = "SELECT * FROM votes WHERE voter_id = $voter_id AND election_id = $election_id";
$vote_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($vote_result) > 0) {
    header("Location: user_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = (int)$_POST['candidate_id'];
    
    $insert_query = "INSERT INTO votes (voter_id, election_id, candidate_id) VALUES ($voter_id, $election_id, $candidate_id)";
    if (mysqli_query($conn, $insert_query)) {
        header("Location: user_dashboard.php?success=1");
        exit();
    } else {
        $error = "Error casting vote. Please try again.";
    }
}

$candidates_query = "SELECT * FROM candidates WHERE election_id = $election_id ORDER BY role, name";
$candidates_result = mysqli_query($conn, $candidates_query);
?>


    <div class="container">
        <h1>Cast Your Vote</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
        <div class="election-header">
            <h2><?php echo htmlspecialchars($election['title'] ?? ''); ?></h2>
            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
        </div>
            
            <div class="election-details">
            <p><strong>Start:</strong> <?php echo date('Y-m-d h:i A', $start_datetime); ?></p>
            <p><strong>End:</strong> <?php echo date('Y-m-d h:i A', $end_datetime); ?></p>
        </div>

        <?php if (!empty($election['description'])): ?>
            <div class="election-description">
                <p><?php echo htmlspecialchars($election['description']); ?></p>
            </div>
        <?php endif; ?>

            <form method="POST" action="cast_vote.php?election_id=<?php echo $election_id; ?>">
            <h3>Select a Candidate</h3>

                <div class="candidates-grid">
                    <?php if (mysqli_num_rows($candidates_result) > 0): ?>
                        <?php while ($candidate = mysqli_fetch_assoc($candidates_result)): ?>
                            <div class="candidate-card">
                                <label class="candidate-label">
                                    <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                                    <div class="candidate-info">
                                        <h3><?php echo htmlspecialchars($candidate['name'] ?? ''); ?></h3>
                                    <p><strong>Role:</strong> <?php echo htmlspecialchars($candidate['role'] ?? ''); ?></p>
                                        <p><strong>Batch:</strong> <?php echo htmlspecialchars($candidate['batch'] ?? ''); ?></p>
                                    </div>
                                </label>
                            <span class="checkmark">&#10003;</span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No candidates available for this election.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cast Vote</button>
                <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.candidate-card');
            
            cards.forEach(card => {
            card.addEventListener('click', function () {
                    cards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                });
            });
        });
    </script>
</body>
</html> 

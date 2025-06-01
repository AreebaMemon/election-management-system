<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

date_default_timezone_set('Asia/Karachi');

$voter_id = $_SESSION['voter_id'];

$voter_query = "SELECT * FROM voters WHERE id = $voter_id";
$voter_result = mysqli_query($conn, $voter_query);
$voter = mysqli_fetch_assoc($voter_result);

$current_date = date('Y-m-d H:i:s');
$elections_query = "SELECT * FROM elections 
                   WHERE CONCAT(start_date, ' ', start_time) <= '$current_date' 
                   AND CONCAT(end_date, ' ', end_time) > '$current_date'
                   ORDER BY start_date DESC, start_time DESC";
$elections_result = mysqli_query($conn, $elections_query);

$upcoming_elections_query = "SELECT * FROM elections 
                              WHERE CONCAT(start_date, ' ', start_time) > '$current_date'
                              ORDER BY start_date ASC, start_time ASC";
$upcoming_elections_result = mysqli_query($conn, $upcoming_elections_query);

$past_votes_query = "SELECT e.*, v.vote_date 
                    FROM elections e 
                    JOIN votes v ON e.id = v.election_id 
                    WHERE v.voter_id = $voter_id 
                    AND CONCAT(e.end_date, ' ', e.end_time) < '$current_date'
                    ORDER BY e.end_date DESC, e.end_time DESC";
$past_votes_result = mysqli_query($conn, $past_votes_query);

$ended_elections_query = "SELECT * FROM elections 
                         WHERE CONCAT(end_date, ' ', end_time) < '$current_date'
                         ORDER BY end_date DESC, end_time DESC";
$ended_elections_result = mysqli_query($conn, $ended_elections_query);
?>



    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($voter['name']); ?>!</h1>
            <p>Here are the elections you can participate in.</p>
        </div>

        <div class="card">
            <h2>Active Elections</h2>
            <?php if (mysqli_num_rows($elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($elections_result)): ?>
                        <?php
                        $vote_check_query = "SELECT * FROM votes WHERE election_id = {$election['id']} AND voter_id = $voter_id";
                        $vote_check_result = mysqli_query($conn, $vote_check_query);
                        $has_voted = mysqli_num_rows($vote_check_result) > 0;
                        ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                            
                            <div class="election-actions">
                                <?php if ($has_voted): ?>
                                    <span class="badge badge-success">Voted</span>
                                    <a href="view_results.php?election_id=<?php echo $election['id']; ?>" class="btn btn-info">View Results</a>
                                <?php else: ?>
                                    <a href="cast_vote.php?election_id=<?php echo $election['id']; ?>" class="btn btn-primary">Vote Now</a>
                                    <a href="view_results.php?election_id=<?php echo $election['id']; ?>" class="btn btn-info">View Results</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No active elections at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Upcoming Elections</h2>
            <?php if (mysqli_num_rows($upcoming_elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($upcoming_elections_result)): ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                            <div class="election-actions">
                                <span class="badge badge-warning">Upcoming</span>
                                <a href="view_results.php?election_id=<?php echo $election['id']; ?>" class="btn btn-info">Preview</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No upcoming elections at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Ended Elections</h2>
            <?php if (mysqli_num_rows($ended_elections_result) > 0): ?>
                <div class="election-grid">
                    <?php while ($election = mysqli_fetch_assoc($ended_elections_result)): 
                        $vote_check_query = "SELECT * FROM votes WHERE election_id = {$election['id']} AND voter_id = $voter_id";
                        $vote_check_result = mysqli_query($conn, $vote_check_query);
                        $has_voted = mysqli_num_rows($vote_check_result) > 0;?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['start_date'] . ' ' . $election['start_time'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('Y-m-d h:i A', strtotime($election['end_date'] . ' ' . $election['end_time'])); ?></p>
                           
                            <div class="election-actions">
                                <?php if ($has_voted): ?>
                                    <span class="badge badge-success">Voted</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Did Not Vote</span>
                                <?php endif; ?>
                                <a href="view_results.php?election_id=<?php echo $election['id']; ?>" class="btn btn-info">View Results</a>
                            </div>
                        </div>
                            <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No ended elections at the moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 

<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$voters_query = "SELECT COUNT(*) as total FROM voters";
$elections_query = "SELECT COUNT(*) as total FROM elections";
$candidates_query = "SELECT COUNT(*) as total FROM candidates";

$voters_result = mysqli_query($conn, $voters_query);
$elections_result = mysqli_query($conn, $elections_query);
$candidates_result = mysqli_query($conn, $candidates_query);

$total_voters = mysqli_fetch_assoc($voters_result)['total'];
$total_elections = mysqli_fetch_assoc($elections_result)['total'];
$total_candidates = mysqli_fetch_assoc($candidates_result)['total'];
?>

<?php include 'header.php'; ?> 

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Voters</h3>
            <p><?php echo $total_voters; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Elections</h3>
            <p><?php echo $total_elections; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Candidates</h3>
            <p><?php echo $total_candidates; ?></p>
        </div>
    </div>
</div>
</body>
</html>

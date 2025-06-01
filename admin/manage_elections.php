<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

date_default_timezone_set('Asia/Karachi');


if (isset($_GET['delete'])) {
    $election_id = $_GET['delete'];
    $delete_query = "DELETE FROM elections WHERE id = $election_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_elections.php");
    exit();
}

$elections_query = "SELECT * FROM elections ORDER BY start_date DESC, start_time DESC";
$elections_result = mysqli_query($conn, $elections_query);

include 'header.php';
?>

<script>
    function confirmDelete(electionId, electionTitle) {
        if (confirm('Are you sure you want to delete the election: "' + electionTitle + '"? This action cannot be undone.')) {
            window.location.href = 'manage_elections.php?delete=' + electionId;
        }
    }
</script>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Elections</h1>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Start Date/Time</th>
                        <th>End Date/Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($election = mysqli_fetch_assoc($elections_result)): 
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
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($election['title']); ?></td>
                        <td><?php echo date('Y-m-d H:i', $start_datetime); ?></td>
                        <td><?php echo date('Y-m-d H:i', $end_datetime); ?></td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                            <td>
                                <a href="edit_election.php?id=<?php echo $election['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $election['id']; ?>, '<?php echo htmlspecialchars($election['title']); ?>')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
                            </div>
           
            <div class="card">
            <a href="create_election.php" class="btn btn-primary">Create New Election</a>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

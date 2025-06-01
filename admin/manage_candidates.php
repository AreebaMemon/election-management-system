<?php
session_start();
include '../config.php';
include 'header.php';


if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $candidate_id = $_GET['delete'];
    $delete_query = "DELETE FROM candidates WHERE id = $candidate_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_candidates.php" . (isset($_GET['election_id']) ? "?election_id=" . $_GET['election_id'] : ""));
    exit();
}

$election_id = isset($_GET['election_id']) ? $_GET['election_id'] : null;

if ($election_id) {
    $candidates_query = "SELECT c.*, e.title as election_title 
                        FROM candidates c 
                        JOIN elections e ON c.election_id = e.id 
                        WHERE c.election_id = $election_id 
                        ORDER BY c.created_at DESC";
} else {
    $candidates_query = "SELECT c.*, e.title as election_title 
                        FROM candidates c 
                        JOIN elections e ON c.election_id = e.id 
                        ORDER BY c.created_at DESC";
}

$candidates_result = mysqli_query($conn, $candidates_query);

// Get elections for dropdown
$elections_query = "SELECT * FROM elections ORDER BY start_date DESC";
$elections_result = mysqli_query($conn, $elections_query);
?>


    <div class="container">
        <h1>Manage Candidates</h1>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Year</th>
                        <th>Role</th>
                        <th>Election</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($candidate = mysqli_fetch_assoc($candidates_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['batch']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['role']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['election_title']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($candidate['created_at'])); ?></td>
                            <td>
                                <div class="candidate-actions">
                                    <a href="edit_candidate.php?id=<?php echo $candidate['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="manage_candidates.php?delete=<?php echo $candidate['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <a href="create_candidate.php" class="btn btn-primary">Add New Candidate</a>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 
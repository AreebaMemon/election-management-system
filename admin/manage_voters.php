<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $voter_id = $_GET['delete'];
    $delete_query = "DELETE FROM voters WHERE id = $voter_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_voters.php");
    exit();
}

$voters_query = "SELECT * FROM voters ORDER BY created_at DESC";
$voters_result = mysqli_query($conn, $voters_query);
?>
    
    <div class="container">
        <h1>Register</h1>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($voter = mysqli_fetch_assoc($voters_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['username']); ?></td>
                            <td><?php echo htmlspecialchars($voter['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($voter['dob'])); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($voter['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $voter['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this voter?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 
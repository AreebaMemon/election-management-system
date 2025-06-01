<?php
session_start();
include '../config.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $election_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM elections WHERE id = $election_id";
    $result = mysqli_query($conn, $query);
    $election = mysqli_fetch_assoc($result);
    
    if (!$election) {
        $_SESSION['error'] = "Election not found";
        header("Location: manage_elections.php");
        exit();
    }
} else {
    header("Location: manage_elections.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    

    $start_datetime = $start_date . ' ' . $start_time;
    $end_datetime = $end_date . ' ' . $end_time;
    
    if (strtotime($start_datetime) > strtotime($end_datetime)) {
        $_SESSION['error'] = "End date/time must be after start date/time";
        header("Location: edit_election.php?id=" . $election_id);
        exit();
    }
    
    $update_query = "UPDATE elections 
                    SET title = '$title', 
                        start_date = '$start_date',
                        start_time = '$start_time',
                        end_date = '$end_date',
                        end_time = '$end_time'
                    WHERE id = $election_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Election updated successfully";
        header("Location: manage_elections.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating election: " . mysqli_error($conn);
        header("Location: edit_election.php?id=" . $election_id);
        exit();
    }
}
?>
    <div class="container">
        <h1>Edit Election</h1>
      
        
        <div class="card">
            <form method="POST" class="form-group">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($election['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $election['start_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" value="<?php echo $election['start_time']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $election['end_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" value="<?php echo $election['end_time']; ?>" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Update Election</button>
                    <a href="manage_elections.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
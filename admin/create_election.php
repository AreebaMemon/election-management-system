<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
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
        header("Location: manage_elections.php");
        exit();
    }

    $query = "INSERT INTO elections (title, start_date, start_time, end_date, end_time) 
              VALUES ('$title', '$start_date', '$start_time', '$end_date', '$end_time')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Election created successfully";
    } else {
        $_SESSION['error'] = "Error creating election: " . mysqli_error($conn);
    }

    header("Location: manage_elections.php");
    exit();
}
?>

<?php 
$page_title = "Create Election"; 
include 'header.php'; 
?>

<div class="container">
    <h1>Create New Election</h1>
    
    <div class="card">
        <form method="POST" class="form-group">
            <div class="form-group">
                <label for="title">Election Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>
            
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Create Election</button>
                <a href="manage_elections.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

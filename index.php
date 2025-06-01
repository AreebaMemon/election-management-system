<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (!isset($_POST['user_type']) || !isset($_POST['username']) || !isset($_POST['password'])) {
        $error = "Please fill in all fields";
    } else {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];

        if ($user_type == 'admin') {
            $query = "SELECT * FROM admins WHERE username='$username'";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) == 1) {
                $admin = mysqli_fetch_assoc($result);
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['username'] = $admin['username'];
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid admin credentials!";
                }
            } else {
                $error = "Invalid admin credentials!";
            }
        } else {
            $query = "SELECT * FROM voters WHERE username='$username'";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) == 1) {
                $voter = mysqli_fetch_assoc($result);
                if (password_verify($password, $voter['password'])) {
                    $_SESSION['voter_id'] = $voter['id'];
                    $_SESSION['user_type'] = 'voter';
                    $_SESSION['username'] = $voter['username'];
                    header("Location: voter/user_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid voter credentials!";
                }
            } else {
                $error = "Invalid voter credentials!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="combined.css">
    <script>
        function toggleRegistrationLink() {
            const userType = document.getElementById('user_type').value;
            const registrationLink = document.getElementById('registration-link');
            
            if (userType === 'voter') {
                registrationLink.style.display = 'block';
            } else {
                registrationLink.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Login</h1>
                <p>Please select your account type and login</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="user_type">Account Type</label>
                    <select id="user_type" name="user_type" class="form-control" required onchange="toggleRegistrationLink()">
                        <option value="">Select Account Type</option>
                        <option value="admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="voter" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'voter') ? 'selected' : ''; ?>>Voter</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="auth-links" id="registration-link" style="display: none;">
                <p>Don't have an account? <a href="register.php">Register as a voter</a></p>
            </div>
        </div>
    </div>

    <script>
            document.addEventListener('DOMContentLoaded', function() {
            toggleRegistrationLink();
        });
    </script>
</body>
</html> 
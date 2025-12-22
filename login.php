<?php
session_start();

// Handle login submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // For now, static login check (no DB)
    if($email === "testuser@example.com" && $password === "123456"){
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'user';
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Recruitment System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>

<header class="navbar">
    <div class="logo">Job_Recruit</div>

    <div class="right-side">
        <a href="index.php" class="nav-link">Home</a>
        <a href="jobseeker.php" class="nav-link">Jobs</a>

        <?php if(isset($_SESSION['user_id'])) { ?>
            <?php if($_SESSION['role'] == 'admin') { ?>
                <a href="admin/dashboard.php" class="btn">Dashboard</a>
            <?php } ?>
            <a href="logout.php" class="btn logout">Logout</a>
        <?php } else { ?>
            <a href="login.php" class="btn signin">Sign In</a>
            <a href="register.php" class="btn signup">Sign Up</a>
        <?php } ?>
    </div>
</header>
<main>
    <div class="login-container">
        <h2>Sign In</h2>
        <?php if($error) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">

            <button type="submit" class="btn signin-btn">Sign In</button>
        </form>
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</main>

<footer>
    <p>&copy; 2025 Job Recruitment System</p>
</footer>

</body>
</html>

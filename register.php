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
     <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
            <a href="login.php" class="btn logout">Logout</a>
        <?php } else { ?>
            <a href="login.php" class="btn signin">Sign In</a>
            <a href="register.php" class="btn signup">Sign Up</a>
        <?php } ?>
    </div>
</header>
<main>
    <div class="back">
    <div class="regis-container">
        <h2>Create Account</h2>
        <?php if($error) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">

           <input type="fname" name="fname" id="fname" required placeholder="Enter your First Name">

           <input type="lname" name="lname" id="lname" required placeholder="Enter your Last Name">

           <input type="position" name="position" id="position" required placeholder="Enter your Position">

           <input type="age" name="age" id="age" required placeholder="Enter your Age">

           <input type="address" name="address" id="address" required placeholder="Enter your Address">
            
            <input type="email" name="email" id="email" required placeholder="Enter your Email">

           <input type="password" name="password" id="password" required placeholder="Enter your Password">

          <input type="repassword" name="repassword" id="repassword" required placeholder="Re-enter your Password">

            <button type="submit" class="btn signup-btn">Sign Up</button>
        </form>
        
        <p>If you have an account? <a href="login.php">Sign In</a></p>

        <!-- SOCIAL ICONS -->
            <div class="social-icons">
                <a href="#" class="social fb" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social insta" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social google" title="Google">
                    <i class="fab fa-google"></i>
                </a>
            </div>
    </div>
    </div>
</main>

<footer>
    <p>&copy; 2025 Job Recruitment System</p>
</footer>

</body>
</html>

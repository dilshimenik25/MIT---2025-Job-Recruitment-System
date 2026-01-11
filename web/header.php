<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page name to hide current link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Recruitment System</title>
    <link rel="stylesheet" href="/job_recruitment/assets/css/style.css">
    <script src="/job_recruitment/assets/js/script.js" defer></script>
</head>
<body>

<header class="navbar">
    <div class="logo">
        <!-- Logo image -->
        <a href="/job_recruitment/web/index.php">
            <img src="/job_recruitment/assets/images/logo.jpeg" alt="Job Recruit Logo" class="logo-img">
            <!-- Optional text next to logo -->
            <span>JOB_RECRUIT</span>
        </a>
    </div>

    <nav class="right-side">

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- Admin links -->
            <?php if ($current_page !== 'dashboard.php'): ?>
                <a href="/job_recruitment/admin/dashboard.php" class="nav-link">Dashboard</a>
            <?php endif; ?>
            <?php if ($current_page !== 'user.php'): ?>
                <a href="/job_recruitment/admin/user.php" class="nav-link">Users</a>
            <?php endif; ?>
            <a href="/job_recruitment/web/logout.php" class="btn logout">Logout</a>

        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
            <!-- Normal logged-in user links -->
            <?php if ($current_page !== 'vacancy.php'): ?>
                <a href="/job_recruitment/web/vacancy.php" class="nav-link">Vacancy</a>
            <?php endif; ?>
            <?php if ($current_page !== 'profile.php'): ?>
                <a href="/job_recruitment/web/profile.php" class="nav-link">Profile</a>
            <?php endif; ?>
            <a href="/job_recruitment/web/logout.php" class="btn logout">Logout</a>

        <?php else: ?>
            <!-- Guest links -->
            <?php if ($current_page !== 'index.php'): ?>
                <a href="/job_recruitment/web/index.php" class="nav-link">Home</a>
            <?php endif; ?>
            <?php if ($current_page !== 'jobseeker.php'): ?>
                <a href="/job_recruitment/web/jobseeker.php" class="nav-link">Jobs</a>
            <?php endif; ?>
            <a href="/job_recruitment/web/login.php" class="btn signin">Sign In</a>
            <a href="/job_recruitment/web/register.php" class="btn signup">Sign Up</a>
        <?php endif; ?>

    </nav>
</header>

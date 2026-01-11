<?php
session_start();

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

include '../web/header.php';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed");
}

// Reports queries
// Total users
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM jobseeker");
$totalRow = $totalResult->fetch_assoc();
$totalUsers = $totalRow['total'];

// Average age
$avgResult = $conn->query("SELECT AVG(age) AS avg_age FROM jobseeker");
$avgRow = $avgResult->fetch_assoc();
$avgAge = $avgRow['avg_age'];

$latestUsers = $conn->query("SELECT firstname, lastname, email FROM jobseeker ORDER BY seeker_id DESC LIMIT 5");
?>


<main class="report-container">

    <h2 class="report-title">Admin Reports Dashboard</h2>

    <!-- Summary Cards -->
    <div class="report-cards">
        <div class="report-card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>

        <div class="report-card">
            <h3>Average Age</h3>
            <p><?php echo round($avgAge); ?></p>
        </div>
    </div>

    <!-- Latest Users -->
    <div class="latest-users">
        <h3>Recently Registered Users</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php while ($row = $latestUsers->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</main>

<?php include '../web/footer.php'; ?>

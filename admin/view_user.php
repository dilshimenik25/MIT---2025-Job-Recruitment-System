<?php
session_start();

// 🔒 Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied");
}

include '../web/header.php';
// Check ID
if (!isset($_GET['id'])) {
    exit("User not found");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$seeker_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM jobseeker WHERE seeker_id = ?");
$stmt->bind_param("i", $seeker_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    exit("User not found");
}

$adminUser = $result->fetch_assoc();
?>
<main>
<div class="vu-wrapper">
    <div class="vu-card">
        <h2 class="vu-title">User Profile</h2>

        <!-- User Info -->
        <div class="vu-info">
            <div class="vu-row">
                <span>Firstname:</span>
                <p><?php echo htmlspecialchars($adminUser['firstname']); ?></p>
            </div>

            <div class="vu-row">
                <span>Lastname:</span>
                <p><?php echo htmlspecialchars($adminUser['lastname']); ?></p>
            </div>

            <div class="vu-row">
                <span>Username:</span>
                <p><?php echo isset($adminUser['username']) ? htmlspecialchars($adminUser['username']) : '-'; ?></p>
            </div>

            <div class="vu-row">
                <span>Position:</span>
                <p><?php echo htmlspecialchars($adminUser['position']); ?></p>
            </div>

            <div class="vu-row">
                <span>Age:</span>
                <p><?php echo htmlspecialchars($adminUser['age']); ?></p>
            </div>

            <div class="vu-row">
                <span>Address:</span>
                <p><?php echo htmlspecialchars($adminUser['address']); ?></p>
            </div>

            <div class="vu-row">
                <span>Email:</span>
                <p><?php echo htmlspecialchars($adminUser['email']); ?></p>
            </div>
        </div>

        <div class="vu-back-btn">
            <a href="user.php">Back to Users</a>
        </div>
    </div>
</div>

</main>

<?php include '../web/footer.php'; ?>



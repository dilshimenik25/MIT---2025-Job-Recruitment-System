<?php
session_start();

// 🔒 Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied");
}

// Check ID
if (!isset($_GET['id'])) {
    exit("User not found");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM jobseeker WHERE seeker_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    exit("User not found");
}

$adminUser = $result->fetch_assoc();
$adminUserPhoto = !empty($adminUser['photo']) ? $adminUser['photo'] : 'default.png';
?>

<div class="user-view-card">
<!-- Profile Photo -->
    <div class="user-view-photo">
        <img src="../uploads/<?php echo htmlspecialchars($adminUserPhoto); ?>" alt="Profile Photo">
    </div>

    <!-- User Info -->
    <!-- User Info -->
    <div class="user-view-row">
        <span>Firstname</span>
        <p><?php echo htmlspecialchars($adminUser['firstname']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Lastname</span>
        <p><?php echo htmlspecialchars($adminUser['lastname']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Username</span>
        <p><?php echo htmlspecialchars($adminUser['username']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Position</span>
        <p><?php echo htmlspecialchars($adminUser['position']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Age</span>
        <p><?php echo htmlspecialchars($adminUser['age']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Address</span>
        <p><?php echo htmlspecialchars($adminUser['address']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Email</span>
        <p><?php echo htmlspecialchars($adminUser['email']); ?></p>
    </div>
</div>
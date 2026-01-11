<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied");
}

if (!isset($_GET['id'])) {
    exit("User not found");
}

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

$user = $result->fetch_assoc();
?>

<div class="user-view-card">

    <div class="user-view-row">
        <span>Firstname</span>
        <p><?php echo htmlspecialchars($user['firstname']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Lastname</span>
        <p><?php echo htmlspecialchars($user['lastname']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Username</span>
        <p><?php echo htmlspecialchars($user['username']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Position</span>
        <p><?php echo htmlspecialchars($user['position']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Age</span>
        <p><?php echo htmlspecialchars($user['age']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Address</span>
        <p><?php echo htmlspecialchars($user['address']); ?></p>
    </div>

    <div class="user-view-row">
        <span>Email</span>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
</div>

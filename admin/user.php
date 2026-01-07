<?php
session_start();

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

include '../web/header.php'; // adjust path based on your structure

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM jobseeker WHERE seeker_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: user.php");
    exit;
}

// Fetch all users
$result = $conn->query("SELECT seeker_id, firstname, lastname, email, position FROM jobseeker ORDER BY seeker_id DESC");
?>

<main>
    <div class="users-container">
        <h2>Registered Users</h2>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['seeker_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['position']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['seeker_id']; ?>">Edit</a> | 
                            <a href="user.php?delete_id=<?php echo $user['seeker_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../web/footer.php'; ?>

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
$result = $conn->query("SELECT seeker_id, firstname, lastname, position, age, address, email, photo FROM jobseeker ORDER BY seeker_id DESC");
?>

<main>
    <div class="admin-users">
        <h2>Registered Users</h2>

        <!-- Admin Actions -->
        <div class="admin-actions">
            <a class="btn-add" href="add_user.php">Add New User</a>
            <a class="btn-reports" href="reports.php">Reports</a>
        </div>

        <div class="table-wrapper">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Position</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $user['seeker_id'] ?></td>
                                <td><?= htmlspecialchars($user['firstname']) ?></td>
                                <td><?= htmlspecialchars($user['lastname']) ?></td>
                                <td><?= htmlspecialchars($user['position']) ?></td>
                                <td><?= htmlspecialchars($user['age']) ?></td>
                                <td><?= htmlspecialchars($user['address']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <!-- View opens a normal page -->
                                    <a class="btn-view" href="view_user.php?id=<?= $user['seeker_id'] ?>">View</a>
                                    <a class="btn-edit" href="edit_user.php?id=<?= $user['seeker_id'] ?>">Edit</a>
                                    <a class="btn-delete" href="user.php?delete_id=<?= $user['seeker_id'] ?>"
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" style="text-align:center;">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>


<?php include '../web/footer.php'; ?>


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
$result = $conn->query("SELECT seeker_id, firstname, lastname, position, age, address, email FROM jobseeker ORDER BY seeker_id DESC");
?>

<main>
    <div class="admin-users">
        <h2>Registered Users</h2>

        <!-- Admin Actions: Add User & Reports -->
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
                                <td><?php echo $user['seeker_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($user['position']); ?></td>
                                 <td><?php echo htmlspecialchars($user['age']); ?></td>
                                  <td><?php echo htmlspecialchars($user['address']); ?></td>
                                   <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="action-buttons">
                                    <a class="btn-edit" href="edit_user.php?id=<?php echo $user['seeker_id']; ?>">Edit</a>
                                     <a class="btn-view" href="#" onclick="openViewPopup(<?php echo $user['seeker_id']; ?>)">View</a>
                                    <a class="btn-delete"
                                       href="user.php?delete_id=<?php echo $user['seeker_id']; ?>"
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- View User Popup -->
<div id="viewUserPopup" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeViewPopup()">&times;</span>
        <h3>User Details</h3>
        <div id="popupBody">
            <!-- User details will load here via AJAX -->
        </div>
    </div>
</div>



<?php include '../web/footer.php'; ?>

<?php
session_start();

// 🔒 Admin only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

// Include header
include '../web/header.php';

// Connect to database
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

// Check if id is provided
if (!isset($_GET['id'])) {
    header("Location: user.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch user data
$stmt = $conn->prepare("SELECT firstname, lastname, email, position FROM jobseeker WHERE seeker_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: user.php");
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $position  = trim($_POST['position']);

    // Optional: add email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        $stmt = $conn->prepare("UPDATE jobseeker SET firstname=?, lastname=?, email=?, position=? WHERE seeker_id=?");
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $position, $id);

        if ($stmt->execute()) {
            $success = "User updated successfully!";
            // Update local $user array to reflect changes
            $user['firstname'] = $firstname;
            $user['lastname']  = $lastname;
            $user['email']     = $email;
            $user['position']  = $position;
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>

<main>
    <div class="edit-user-container">
        <h2>Edit User</h2>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="">
            <label>Firstname:</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

            <label>Lastname:</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" required>

            <button type="submit" class="btn">Update</button>
            <a href="user.php" class="btn cancel">Cancel</a>
        </form>
    </div>
</main>

<?php include '../web/footer.php'; ?>

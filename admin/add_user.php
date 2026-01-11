<?php
session_start();

// 🔒 Admin only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

include '../web/header.php'; // adjust path if needed

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $username  = trim($_POST['username']);
    $position  = trim($_POST['position']);
    $age       = intval($_POST['age']);
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO jobseeker (firstname, lastname, username, position, age, address, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiss", $firstname, $lastname, $username, $position, $age, $address, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $error = "Failed to add user: " . $conn->error;
        }
    }
}
?>

<main>
    <div class="add-user-container">
        <h2>Add New User</h2>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="">
            <label>Firstname:</label>
            <input type="text" name="firstname" required>

            <label>Lastname:</label>
            <input type="text" name="lastname" required>

            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Position:</label>
            <input type="text" name="position" required>

            <label>Age:</label>
            <input type="number" name="age" min="1" required>

            <label>Address:</label>
            <input type="text" name="address" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Add User</button>
            <a href="user.php" class="btn cancel">Cancel</a>
        </form>
    </div>
</main>

<?php include '../web/footer.php'; ?>

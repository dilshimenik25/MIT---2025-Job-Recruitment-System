<?php
session_start();

// 🔒 Only allow logged-in normal users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

// Include header
include 'header.php';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize variables
$seeker_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
$sql = "SELECT firstname, lastname, position, age, address, email, password FROM jobseeker WHERE seeker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seeker_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $position  = trim($_POST['position']);
    $age       = trim($_POST['age']);
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);

    // Keep the existing password
    $hashedPassword = $user['password'];

    // Update user info
    $update_sql = "UPDATE jobseeker 
                   SET firstname=?, lastname=?, position=?, age=?, address=?, email=?, password=? 
                   WHERE seeker_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param(
        "sssisssi",
        $firstname,
        $lastname,
        $position,
        $age,
        $address,
        $email,
        $hashedPassword,
        $seeker_id
    );

    if ($update_stmt->execute()) {
        $success = "Profile updated successfully!";
        // Update session firstname/email
        $_SESSION['firstname'] = $firstname;
        $_SESSION['email'] = $email;
        // Refresh user data
        $user['firstname'] = $firstname;
        $user['lastname'] = $lastname;
        $user['position'] = $position;
        $user['age'] = $age;
        $user['address'] = $address;
        $user['email'] = $email;
    } else {
        $error = "Update failed!";
    }
}
?>

<main>
    <div class="profile-container">
        <h2>Your Profile</h2>

        <?php if ($error) { echo "<p class='error'>".htmlspecialchars($error)."</p>"; } ?>
        <?php if ($success) { echo "<p class='success'>".htmlspecialchars($success)."</p>"; } ?>

        <form method="POST" action="">
            <input type="text" name="firstname" required placeholder="First Name" value="<?php echo htmlspecialchars($user['firstname']); ?>">
            <input type="text" name="lastname" required placeholder="Last Name" value="<?php echo htmlspecialchars($user['lastname']); ?>">
            <input type="text" name="position" required placeholder="Position" value="<?php echo htmlspecialchars($user['position']); ?>">
            <input type="number" name="age" required placeholder="Age" value="<?php echo htmlspecialchars($user['age']); ?>">
            <input type="text" name="address" required placeholder="Address" value="<?php echo htmlspecialchars($user['address']); ?>">
            <input type="email" name="email" required placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>">

            <button type="submit" class="btn update-btn">Update Profile</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>

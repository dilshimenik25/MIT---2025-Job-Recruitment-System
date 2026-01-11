<?php
session_start();

// Only logged-in normal users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

include 'header.php';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$seeker_id = $_SESSION['user_id'];
$error = '';
$success = '';

$sql = "SELECT firstname, lastname, position, age, address, email, password FROM jobseeker WHERE seeker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seeker_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $position  = trim($_POST['position']);
    $age       = trim($_POST['age']);
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);

    $hashedPassword = $user['password'];

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
        $_SESSION['firstname'] = $firstname;
        $_SESSION['email'] = $email;
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

<link rel="stylesheet" href="unique-profile.css">

<main>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <div class="uprof-wrapper">
        <div class="uprof-card">
            <h2>Your Profile</h2>

            <?php if ($error) { echo "<div class='uprof-error'>".htmlspecialchars($error)."</div>"; } ?>
            <?php if ($success) { echo "<div class='uprof-success'>".htmlspecialchars($success)."</div>"; } ?>

            <form method="POST" action="">
                <div class="uprof-group">
                    <label>First Name</label>
                    <input type="text" name="firstname" required value="<?php echo htmlspecialchars($user['firstname']); ?>">
                </div>

                <div class="uprof-group">
                    <label>Last Name</label>
                    <input type="text" name="lastname" required value="<?php echo htmlspecialchars($user['lastname']); ?>">
                </div>

                <div class="uprof-group">
                    <label>Position</label>
                    <input type="text" name="position" required value="<?php echo htmlspecialchars($user['position']); ?>">
                </div>

                <div class="uprof-group">
                    <label>Age</label>
                    <input type="number" name="age" required value="<?php echo htmlspecialchars($user['age']); ?>">
                </div>

                <div class="uprof-group">
                    <label>Address</label>
                    <input type="text" name="address" required value="<?php echo htmlspecialchars($user['address']); ?>">
                </div>

                <div class="uprof-group">
                    <label>Email</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <button type="submit" class="uprof-btn">Update Profile</button>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

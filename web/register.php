<?php
$error = '';
$success = '';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed");
}

include 'header.php';

// Initialize variables to keep values after submission
$firstname = $lastname = $position = $age = $address = $email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $position  = trim($_POST['position']);
    $age       = trim($_POST['age']);
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];

    // Check if passwords match
    if ($password !== $reenter_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL
        $sql = "INSERT INTO jobseeker 
            (firstname, lastname, position, age, address, email, password, reenter_password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param(
                "sssissss",
                $firstname,
                $lastname,
                $position,
                $age,
                $address,
                $email,
                $hashedPassword,
                $hashedPassword
            );

            if ($stmt->execute()) {
                // Success: redirect to login page
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Email already exists or registration failed!";
            }
        }
    }
}
?>

<main>
    <div class="regis-container">
        <h2>Create Account</h2>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="">

            <input type="text" name="firstname" required placeholder="Enter your First Name" value="<?php echo htmlspecialchars($firstname); ?>">

            <input type="text" name="lastname" required placeholder="Enter your Last Name" value="<?php echo htmlspecialchars($lastname); ?>">

            <input type="text" name="position" required placeholder="Enter your Position" value="<?php echo htmlspecialchars($position); ?>">

            <input type="number" name="age" required placeholder="Enter your Age" value="<?php echo htmlspecialchars($age); ?>">

            <input type="text" name="address" required placeholder="Enter your Address" value="<?php echo htmlspecialchars($address); ?>">

            <input type="email" name="email" required placeholder="Enter your Email" value="<?php echo htmlspecialchars($email); ?>">

            <input type="password" name="password" required placeholder="Enter your Password">

            <input type="password" name="reenter_password" required placeholder="Re-enter your Password">

            <button type="submit" class="btn signup-btn">Sign Up</button>
        </form>

        <p>If you have an account? <a href="login.php">Sign In</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>

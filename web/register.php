<?php
$error = '';
$success = '';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed");
}

include 'header.php';

// Keep values after submit
$firstname = $lastname = $username = $position = $age = $address = $email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $username  = trim($_POST['username']);
    $position  = trim($_POST['position']);
    $age       = trim($_POST['age']);
    $address   = trim($_POST['address']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];

    // 1️⃣ Check password match
    if ($password !== $reenter_password) {
        $error = "Passwords do not match!";
    } else {

        // 2️⃣ Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 3️⃣ Insert ONLY one password
        $sql = "INSERT INTO jobseeker
                (firstname, lastname, username, position, age, address, email, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {

            // 4️⃣ Bind params (COUNT MATCHES!)
            $stmt->bind_param(
                "ssssisss",
                $firstname,
                $lastname,
                $username,
                $position,
                $age,
                $address,
                $email,
                $hashedPassword
            );

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Username or Email already exists!";
            }
        }
    }
}
?>

<main>
    <div class="regis-container">
        <h2>Create Account</h2>

        <?php if ($error) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form method="POST" action="">

            <input type="text" name="firstname" required
                   placeholder="Enter your First Name"
                   value="<?php echo htmlspecialchars($firstname); ?>">

            <input type="text" name="lastname" required
                   placeholder="Enter your Last Name"
                   value="<?php echo htmlspecialchars($lastname); ?>">

            <input type="text" name="username" required
                   placeholder="Enter your User Name"
                   value="<?php echo htmlspecialchars($username); ?>">

            <input type="text" name="position" required
                   placeholder="Enter your Position"
                   value="<?php echo htmlspecialchars($position); ?>">

            <input type="number" name="age" required
                   placeholder="Enter your Age"
                   value="<?php echo htmlspecialchars($age); ?>">

            <input type="text" name="address" required
                   placeholder="Enter your Address"
                   value="<?php echo htmlspecialchars($address); ?>">

            <input type="email" name="email" required
                   placeholder="Enter your Email"
                   value="<?php echo htmlspecialchars($email); ?>">

            <input type="password" name="password" required
                   placeholder="Enter your Password">

            <input type="password" name="reenter_password" required
                   placeholder="Re-enter your Password">

            <button type="submit" class="btn signup-btn">Sign Up</button>
        </form>

        <p>If you already have an account? <a href="login.php">Sign In</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>

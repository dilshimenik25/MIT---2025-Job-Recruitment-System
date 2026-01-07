<?php
session_start();

$error = '';

// Database connection
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Include header (for web pages)
include 'header.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password!";
    } else {

        // 🔹 Hardcoded admin login
        if ($email === 'admin@example.com' && $password === 'admin1234') {
            $_SESSION['user_id'] = 0;
            $_SESSION['firstname'] = 'Admin';
            $_SESSION['role'] = 'admin';
            header("Location: /job_recruitment/admin/dashboard.php");
            exit;
        }

        // 🔹 Normal user login
        $sql = "SELECT seeker_id, firstname, password FROM jobseeker WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['seeker_id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['role'] = 'user'; // normal user

                // Redirect to vacancies page
                header("Location: /job_recruitment/web/vacancy.php");
                exit;
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>

<main>
    <div class="login-container">
        <h2>MEMBER LOGIN</h2>

        <?php if ($error) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form method="POST" action="">
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">
            <button type="submit" class="btn signin-btn">Sign In</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>

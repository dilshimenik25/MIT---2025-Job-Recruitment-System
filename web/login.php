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

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter email and password!";
    } else {

        // 🔹 Hardcoded admin login
        if ($username === 'admin' && $password === 'admin1234') {
            $_SESSION['user_id'] = 0;
            $_SESSION['firstname'] = 'Admin';
             $_SESSION['username'] = 'admin';
            $_SESSION['role'] = 'admin';
            header("Location: /job_recruitment/admin/dashboard.php");
            exit;
        }

        // 🔹 Default ordinary user (required)
     if ($username === 'uocc' && $password === 'uocc'){
       $_SESSION['user_id'] = -1;
       $_SESSION['firstname'] = 'UOCCUser';
       $_SESSION['username'] = 'uocc';
       $_SESSION['role'] = 'user';
 
          header("Location: /job_recruitment/web/vacancy.php");
      exit;
      }


        // 🔹 Normal user login
        $sql = "SELECT seeker_id, firstname, username, password FROM jobseeker WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['seeker_id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['username'] = $username; // ✅ added
                $_SESSION['role'] = 'user'; // normal user

                // Redirect to vacancy.php, if a job was selected
                $redirectJobId = isset($_SESSION['selected_job_id']) ? $_SESSION['selected_job_id'] : '';
                if($redirectJobId){
                    header("Location: vacancy.php?job_id=$redirectJobId");
                    unset($_SESSION['selected_job_id']);
                } else {
                    header("Location: vacancy.php");
                }
            } else {
                $error = "Invalid usename or password!";
            }
        } else {
            $error = "Invalid username or password!";
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
            <input type="username" name="username" required placeholder="Enter your username">
            <input type="password" name="password" required placeholder="Enter your password">
            <button type="submit" class="btn signin-btn">Sign In</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>

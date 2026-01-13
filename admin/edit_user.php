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
$stmt = $conn->prepare("SELECT firstname, lastname, email, position, age, address, photo FROM jobseeker WHERE seeker_id = ?");
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
    $age  = trim($_POST['age']);
    $address  = trim($_POST['address']);

    // Keep current photo by default
   // $photo = $user['photo'];
/*
    // Handle file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = array('jpg','jpeg','png','gif');

        if (in_array($fileExtension, $allowedExts) && $fileSize < 2*1024*1024) { // 2MB limit
            $newFileName = $id . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = '../uploads/';

            // Create uploads folder if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {

               // DELETE OLD PHOTO (if exists)
                if (!empty($user['photo']) && file_exists('../uploads/' . $user['photo'])) {
                unlink('../uploads/' . $user['photo']);
                }

                $photo = $newFileName;
                }
            
            else {
                $error = "Error moving uploaded file. Check folder permissions.";
            }
        } else {
            $error = "Invalid file type or size (max 2MB).";
        }
    }*/

    // Only update DB if no error
 if (!$error) {
    $stmt = $conn->prepare(
        "UPDATE jobseeker 
         SET firstname=?, lastname=?, email=?, position=?, age=?, address=?, photo=? 
         WHERE seeker_id=?"
    );

    $stmt->bind_param(
        "ssssissi",
        $firstname,
        $lastname,
        $email,
        $position,
        $age,
        $address,
        $photo,
        $id
    );

    $stmt->execute();
}
}
?>

<main>
    <div class="edit-user-container">
        <h2>Edit User</h2>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Centered profile photo and file input -->
          
            <!-- Other fields -->
            <label>Firstname:</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

            <label>Lastname:</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" required>

            <label>Age:</label>
            <input type="text" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>

            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

            <button type="submit" class="btn submit ">Update</button>
            <a href="user.php" class="btn cancel">Cancel</a>
        </form>
    </div>
</main>

<!-- CSS for circular photo and centered input -->


<?php include '../web/footer.php'; ?>

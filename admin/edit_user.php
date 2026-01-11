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
$stmt = $conn->prepare("SELECT firstname, lastname, email, position, photo FROM jobseeker WHERE seeker_id = ?");
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

    // Keep current photo by default
    $photo = $user['photo'];

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
                $photo = $newFileName; // update photo for DB
            } else {
                $error = "Error moving uploaded file. Check folder permissions.";
            }
        } else {
            $error = "Invalid file type or size (max 2MB).";
        }
    }

    // Only update DB if no error
    if (!$error) {
        $stmt = $conn->prepare("UPDATE jobseeker SET firstname=?, lastname=?, email=?, position=?, photo=? WHERE seeker_id=?");
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $position, $photo, $id);

        if ($stmt->execute()) {
            $success = "User updated successfully!";
            // Update local $user array so the new photo shows immediately
            $user['firstname'] = $firstname;
            $user['lastname']  = $lastname;
            $user['email']     = $email;
            $user['position']  = $position;
            $user['photo']     = $photo;
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

        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Centered profile photo and file input -->
            <div class="profile-photo-container">
                <div class="profile-photo-preview">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Photo" id="photoPreview">
                    <?php else: ?>
                        <img src="../uploads/default.png" alt="Profile Photo" id="photoPreview">
                    <?php endif; ?>
                </div>

                <div class="profile-photo-input">
                    <label>Upload Profile Photo:</label>
                    <input type="file" name="profile_photo" accept="image/*" id="profilePhotoInput">
                </div>
            </div>

            <!-- Other fields -->
            <label>Firstname:</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

            <label>Lastname:</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" required>

            <button type="submit" class="btn submit ">Update</button>
            <a href="user.php" class="btn cancel">Cancel</a>
        </form>
    </div>
</main>

<!-- CSS for circular photo and centered input -->


<?php include '../web/footer.php'; ?>

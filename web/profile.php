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

// Fetch user data
$sql = "SELECT firstname, lastname, position, age, address, email, password, photo FROM jobseeker WHERE seeker_id = ?";
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

    $photo = $user['photo']; // keep current photo

    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = array('jpg','jpeg','png','gif');

        if (in_array($fileExt, $allowedExts) && $fileSize < 2*1024*1024) { // 2MB limit
            $newFileName = $seeker_id . '_' . time() . '.' . $fileExt;
            $uploadDir = 'uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $dest_path = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo = $newFileName; // update photo for DB
            } else {
                $error = "Error moving uploaded file. Check folder permissions.";
            }
        } else {
            $error = "Invalid file type or size (max 2MB).";
        }
    }

    // Update database if no error
    if (!$error) {
        $hashedPassword = $user['password']; // keep old password
        $update_sql = "UPDATE jobseeker 
                       SET firstname=?, lastname=?, position=?, age=?, address=?, email=?, password=?, photo=? 
                       WHERE seeker_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param(
            "sssissssi",
            $firstname,
            $lastname,
            $position,
            $age,
            $address,
            $email,
            $hashedPassword,
            $photo,
            $seeker_id
        );

        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['firstname'] = $firstname;
            $_SESSION['email'] = $email;

            // Update local user array for display
            $user['firstname'] = $firstname;
            $user['lastname']  = $lastname;
            $user['position']  = $position;
            $user['age']       = $age;
            $user['address']   = $address;
            $user['email']     = $email;
            $user['photo']     = $photo;
        } else {
            $error = "Update failed!";
        }
    }
}
?>

<main>
    <div class="profile-wrapper">
        <div class="profile-card">
            <h2>Your Profile</h2>

            <?php if ($error) { echo "<div class='profile-error'>".htmlspecialchars($error)."</div>"; } ?>
            <?php if ($success) { echo "<div class='profile-success'>".htmlspecialchars($success)."</div>"; } ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="profile-photo-container">
                    <div class="profile-photo-preview">
                        <?php if (!empty($user['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Photo" id="userPhotoPreview">
                        <?php else: ?>
                            <img src="uploads/default.png" alt="Profile Photo" id="userPhotoPreview">
                        <?php endif; ?>
                    </div>
                    <div class="profile-photo-input">
                        <label>Upload Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/*" id="userPhotoInput">
                    </div>
                </div>

                <div class="profile-group">
                    <label>First Name</label>
                    <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>

                <div class="profile-group">
                    <label>Last Name</label>
                    <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>

                <div class="profile-group">
                    <label>Position</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" required>
                </div>

                <div class="profile-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                </div>

                <div class="profile-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>

                <div class="profile-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <button type="submit" class="profile-btn">Update Profile</button>
            </form>
        </div>
    </div>
</main>



<?php include 'footer.php'; ?>

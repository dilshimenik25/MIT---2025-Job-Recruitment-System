<?php
session_start();

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../web/header.php';

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $salary = isset($_POST['salary']) ? trim($_POST['salary']) : '';
    $posted_date = isset($_POST['posted_date']) ? trim($_POST['posted_date']) : '';
    $closing_date = isset($_POST['closing_date']) ? trim($_POST['closing_date']) : '';
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    if (!$title) $errors[] = "Job title is required.";
    if (!$description) $errors[] = "Job description is required.";
    if (!$location) $errors[] = "Location is required.";
    if (!$posted_date) $errors[] = "Posted date is required.";
    if (!$closing_date) $errors[] = "Closing date is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO jobs (title, description, location, salary, posted_date, closing_date, is_visible, jd_downloads) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssssi", $title, $description, $location, $salary, $posted_date, $closing_date, $is_visible);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Failed to add job: " . $stmt->error;
        }
    }
}
?>

<div class="addjob-wrapper">
    <div class="addjob-container">
        <header class="addjob-header">Add New Job</header>

        <?php if (!empty($errors)): ?>
            <div class="addjob-error">
                <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
            </div>
        <?php endif; ?>

        <div class="addjob-card">
    <form method="post" class="addjob-form">

        <div class="addjob-group">
            <label>Job Title</label>
            <input type="text" name="title" class="addjob-input" value="<?= htmlspecialchars(isset($_POST['title']) ? $_POST['title'] : '') ?>" required>
        </div>

        <div class="addjob-group">
            <label>Description</label>
            <textarea name="description" class="addjob-input" rows="4" required><?= htmlspecialchars(isset($_POST['description']) ? $_POST['description'] : '') ?></textarea>

        </div>

        <div class="addjob-group">
    <label>Location</label>
    <input type="text" name="location" class="addjob-input"
           value="<?= htmlspecialchars(isset($_POST['location']) ? $_POST['location'] : '') ?>"
           required>
</div>

<div class="addjob-group">
    <label>Salary</label>
    <input type="text" name="salary" class="addjob-input"
           value="<?= htmlspecialchars(isset($_POST['salary']) ? $_POST['salary'] : '') ?>">
</div>

<div class="addjob-group">
    <label>Posted Date</label>
    <input type="date" name="posted_date" class="addjob-input"
           value="<?= htmlspecialchars(isset($_POST['posted_date']) ? $_POST['posted_date'] : '') ?>"
           required>
</div>

<div class="addjob-group">
    <label>Closing Date</label>
    <input type="date" name="closing_date" class="addjob-input"
           value="<?= htmlspecialchars(isset($_POST['closing_date']) ? $_POST['closing_date'] : '') ?>"
           required>
</div>

        <div class="addjob-group addjob-checkbox">
            <label>
                <input type="checkbox" name="is_visible" <?= isset($_POST['is_visible']) ? 'checked' : '' ?>> Visible
            </label>
        </div>

        <button type="submit" class="addjob-btn">Add Job</button>
    </form>
</div>
    </div>
</div>



<?php include '../web/footer.php'; ?>

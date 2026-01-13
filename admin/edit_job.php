<?php
session_start();

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../web/header.php';

$conn = new mysqli("localhost","root","","job_recruitment");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
if(!$job_id) die("Invalid Job ID");

$res = $conn->query("SELECT * FROM jobs WHERE job_id=$job_id");
if(!$res || $res->num_rows==0) die("Job not found");
$job = $res->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD']=="POST"){
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $closing_date = $_POST['closing_date'];

    // Update job WITHOUT touching is_visible
    $update = $conn->query("
        UPDATE jobs 
        SET title='$title', description='$description', closing_date='$closing_date' 
        WHERE job_id=$job_id
    ");

    if($update){
        $_SESSION['edit_success'] = "Job updated successfully!";
        header("Location: edit_job.php?job_id=$job_id");
        exit;
    } else {
        $error = "Failed to update job: " . $conn->error;
    }
}
?>
<main>
<div class="editjob-container">
    <h1 class="editjob-header">Edit Job</h1>

    <?php if(isset($_SESSION['edit_success'])): ?>
        <div class="editjob-success"><?= $_SESSION['edit_success']; unset($_SESSION['edit_success']); ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="editjob-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="editjob-form">
        <div class="editjob-group">
            <label for="title">Job Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($job['title']) ?>" required>
        </div>

        <div class="editjob-group">
            <label for="description">Job Description</label>
            <textarea name="description" id="description"><?= htmlspecialchars($job['description']) ?></textarea>
        </div>

       <div class="editjob-group">
    <label for="closing_date">Closing Date</label>
    <input type="date" name="closing_date" id="closing_date" value="<?= date('Y-m-d', strtotime($job['closing_date'])) ?>">
</div>

        <button type="submit" class="editjob-btn">Update Job</button>
     
    </form>
</div>

    </main>

<?php include '../web/footer.php'; ?>

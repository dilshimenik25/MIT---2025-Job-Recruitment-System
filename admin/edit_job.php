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
            <input type="date" name="closing_date" id="closing_date" value="<?= $job['closing_date'] ?>">
        </div>

        <button type="submit" class="editjob-btn">Update Job</button>
        <a href="dashboard.php" class="editjob-btn editjob-cancel">Cancel</a>
    </form>
</div>

<style>
.editjob-container {
    max-width: 700px;
    margin: 30px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.editjob-header {
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
    color: #4f46e5;
}
.editjob-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.editjob-group label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}
.editjob-group input, .editjob-group textarea {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 16px;
}
.editjob-group textarea {
    min-height: 120px;
}
.editjob-btn {
    padding: 10px 20px;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}
.editjob-btn:hover {
    background: #4338ca;
}
.editjob-cancel {
    text-decoration: none;
    display: inline-block;
    background: #e0e0e0;
    color: #333;
    margin-left: 10px;
}
.editjob-success {
    background: #d1fae5;
    color: #065f46;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}
.editjob-error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

<?php include '../web/footer.php'; ?>

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
?>

<main>
<div class="viewjob-container">
    <h1 class="viewjob-header">Job Details</h1>

    <div class="viewjob-group">
        <label>Job Title:</label>
        <div class="viewjob-value"><?= htmlspecialchars($job['title']) ?></div>
    </div>

    <div class="viewjob-group">
        <label>Job Description:</label>
        <div class="viewjob-value"><?= nl2br(htmlspecialchars($job['description'])) ?></div>
    </div>

    <div class="viewjob-group">
        <label>Closing Date:</label>
        <div class="viewjob-value"><?= date('Y-m-d', strtotime($job['closing_date'])) ?></div>
    </div>

    <div class="viewjob-group">
        <label>Visible:</label>
        <div class="viewjob-value"><?= $job['is_visible'] ? 'Yes' : 'No' ?></div>
    </div>

    <div class="viewjob-back">
        <a href="dashboard.php" class="btn btn-back"> Back to Dashboard</a>
    </div>
</div>
</main>


<?php include '../web/footer.php'; ?>

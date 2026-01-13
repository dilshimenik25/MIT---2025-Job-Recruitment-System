<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['job_id'])){
    die("Invalid job ID");
}

$job_id = intval($_GET['job_id']);

$conn = new mysqli("localhost","root","","job_recruitment");
if($conn->connect_error) die("Database connection failed");

// Fetch job
$stmt = $conn->prepare("SELECT title, location, salary, posted_date, closing_date, description FROM jobs WHERE job_id=?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Job not found");
}

$job = $result->fetch_assoc();

// Prepare file name
$filename = preg_replace("/[^a-zA-Z0-9_-]/", "_", $job['title']) . "_JD.txt";

// Set headers for download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Output content
echo "Job Title: " . $job['title'] . "\n";
echo "Location: " . $job['location'] . "\n";
echo "Salary: LKR " . number_format($job['salary']) . "\n";
echo "Posted Date: " . date('d M Y', strtotime($job['posted_date'])) . "\n";
echo "Closing Date: " . date('d M Y', strtotime($job['closing_date'])) . "\n\n";
echo "Job Description:\n";
echo $job['description'];
exit;

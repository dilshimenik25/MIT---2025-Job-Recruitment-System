<?php
session_start();

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) die("Connection failed");

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM jobs WHERE job_id = $id");

if ($row = $result->fetch_assoc()) {
    echo "
        <h2>{$row['title']}</h2>
        <p><strong>Location:</strong> {$row['location']}</p>
        <p><strong>Salary:</strong> LKR {$row['salary']}</p>
        <p><strong>Closing Date:</strong> {$row['closing_date']}</p>
        <p><strong>Description:</strong><br>{$row['description']}</p>
        <button class='apply-btn'>Apply Now</button>
    ";
} else {
    echo '<p>Job not found</p>';
}

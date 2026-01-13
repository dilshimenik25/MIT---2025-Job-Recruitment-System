<?php
session_start();
include('header.php');

// Store selected job in session if user clicked Apply Now
if(isset($_GET['job_id'])){
    $_SESSION['selected_job_id'] = intval($_GET['job_id']);
}

$conn = new mysqli("localhost", "root", "", "job_recruitment");
if($conn->connect_error) die("Database connection failed");

$jobs = $conn->query("
    SELECT *
    FROM jobs
    WHERE is_visible = 1
      AND closing_date >= CURDATE()
    ORDER BY job_id DESC
");

if(!$jobs) {
    die("Query failed: " . $conn->error);
}

?>

<main>
<div class="jobseek-body">
<div class="jobseek-container">

<div style="grid-column:1/-1;">
<div class="jobseek-search">
<input type="text" id="jobseekSearchInput" placeholder="Search job title..." onkeyup="filterJobsSeek()">
<select id="jobseekLocationFilter" onchange="filterJobsSeek()">
    <option value="">All Locations</option>
    <option value="Colombo">Colombo</option>
    <option value="Kandy">Kandy</option>
    <option value="Galle">Galle</option>
</select>
<button class="jobseek-clear" onclick="clearFiltersSeek()">Clear Filters</button>
</div>
</div>

<?php while($row = $jobs->fetch_assoc()): ?>
<div class="jobseek-card" data-title="<?= strtolower($row['title']) ?>" data-location="<?= $row['location'] ?>">
    <div>
        <div class="jobseek-title"><?= htmlspecialchars($row['title']) ?></div>
        <div class="jobseek-location"><?= htmlspecialchars($row['location']) ?> | Closes: <?= date('d M Y', strtotime($row['closing_date'])) ?></div>
        <div class="jobseek-salary">LKR <?= number_format($row['salary']) ?></div>

        <div class="jobseek-desc">
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
        </div>
    </div>
    <a href="login.php?job_id=<?= $row['job_id'] ?>" class="jobseek-btn">Apply Now</a>
</div>
<?php endwhile; ?>

</div>
</div>
</main>


<?php include('footer.php'); ?>

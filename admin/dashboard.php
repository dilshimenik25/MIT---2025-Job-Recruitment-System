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

/// ---- TOGGLE JOB VISIBILITY ----
if (isset($_GET['toggle_job'])) {
    $job_id = intval($_GET['toggle_job']);
    $result = $conn->query("SELECT is_visible FROM jobs WHERE job_id=$job_id");
    if ($result && $row = $result->fetch_assoc()) {
        $new_status = $row['is_visible'] ? 0 : 1;
        $conn->query("UPDATE jobs SET is_visible=$new_status WHERE job_id=$job_id");
    }
    // reload page after toggle
    header("Location: dashboard.php");
    exit;
}



// Hide expired jobs automatically
//$conn->query("UPDATE jobs SET is_visible=0 WHERE closing_date < CURDATE()");

// Stats
// Total jobs
$res = $conn->query("SELECT COUNT(*) AS cnt FROM jobs");
$row = $res ? $res->fetch_assoc() : null;
$total_jobs = $row ? intval($row['cnt']) : 0;

// Total applications
$res = $conn->query("SELECT COUNT(*) AS cnt FROM applications");
$row = $res ? $res->fetch_assoc() : null;
$total_apps = $row ? intval($row['cnt']) : 0;

// Total JD downloads
$res = $conn->query("SELECT COUNT(*) AS cnt FROM jd_downloads");
$row = $res ? $res->fetch_assoc() : null;
$total_downloads = $row ? intval($row['cnt']) : 0;


// Fetch jobs
$jobs_result = $conn->query("SELECT * FROM jobs");

// Fetch applications with jobseeker info
$apps_result = $conn->query("
    SELECT a.id, a.job_id, a.cv_file, a.applied_date, a.status, 
           j.title AS job_title,
           s.firstname, s.lastname, s.email
    FROM applications a
    LEFT JOIN jobseeker s ON a.seeker_id = s.seeker_id
    LEFT JOIN jobs j ON a.job_id = j.job_id
");


// Optional: check if query failed
if (!$apps_result) {
    die("Applications query failed: " . $conn->error);
}



// For chart
$jobs_chart = $conn->query("SELECT job_id, title FROM jobs");
?>

<main>
<div class="dash-main-container">
    <header class="dash-main-header">Admin Dashboard</header>

    <!-- Stats Cards -->
    <div class="dash-stats-container">
        <div class="dash-card-stats"><h3><?= $total_jobs ?></h3><p>Total Jobs</p></div>
        <div class="dash-card-stats"><h3><?= $total_apps ?></h3><p>Total Applications</p></div>
        <div class="dash-card-stats"><h3><?= $total_downloads ?></h3><p>Total JD Downloads</p></div>
    </div>

    <!-- Add Job -->
    <div class="dash-card">
        <a href="add_job.php"><button class="dash-btn dash-btn-add"><i class="fa fa-plus"></i> Add Job</button></a>
    </div>

    <!-- Jobs Table -->
  <!-- Jobs Table -->
<div class="dash-card">
    <h2>Jobs</h2>
    <table class="dash-table">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>JD Downloads</th>
            <th>Visible</th>
            <th>Actions</th>
        </tr>
        <?php if ($jobs_result && $jobs_result->num_rows > 0): ?>
            <?php while($job = $jobs_result->fetch_assoc()): 
                $job_id = intval($job['job_id']);
                $title = htmlspecialchars($job['title']);
                $is_visible = isset($job['is_visible']) ? $job['is_visible'] : 0;

                // Get JD downloads count
                $downloads = 0;
                $res = $conn->query("SELECT COUNT(*) AS cnt FROM jd_downloads WHERE job_id=$job_id");
                if ($res) {
                    $row = $res->fetch_assoc();
                    $downloads = isset($row['cnt']) ? intval($row['cnt']) : 0;
                }
            ?>
            <tr>
                <td><?= $job_id ?></td>
                <td><?= $title ?></td>
                <td><?= $downloads ?></td>
                <td><?= $is_visible ? "Yes" : "No" ?></td>
                <td>
                       
    <!-- Toggle visibility -->
   <!-- Toggle visibility -->
    <a href="?toggle_job=<?= $job_id ?>">
        <button type="button" class="dash-btn dash-btn-toggle">
            <i class="fa fa-eye"></i> Toggle
        </button>
    </a>

    <!-- Edit job -->
    <a href="edit_job.php?job_id=<?= $job_id ?>">
        <button class="dash-btn dash-btn-edit"><i class="fa fa-edit"></i> Edit</button>
    </a>
    

                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No jobs found</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- Applications Table -->
<div class="dash-card">
    <h2>Applications</h2>
    <table class="dash-table">
        <tr>
            <th>ID</th>
            <th>Job Title</th>
            <th>Seeker Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php if ($apps_result && $apps_result->num_rows > 0): ?>
            <?php while($app = $apps_result->fetch_assoc()): 
                $app_id = $app['id'];
                $job_title = htmlspecialchars($app['job_title']);
                $seeker_name = htmlspecialchars($app['firstname'].' '.$app['lastname']);
                $email = htmlspecialchars($app['email']);
            ?>
            <tr>
                <td><?= $app_id ?></td>
                <td><?= $job_title ?></td>
                <td><?= $seeker_name ?></td>
                <td><?= $email ?></td>
                <td>
                    <a href="?delete_application=<?= $app_id ?>" onclick="return confirm('Delete this application?')">
                        <button class="dash-btn dash-btn-delete"><i class="fa fa-trash"></i> Delete</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No applications found</td></tr>
        <?php endif; ?>

    </table>
</div>


    <!-- Chart -->
    <div class="dash-card">
        <h2>Applications per Job Chart</h2>
        <div class="dash-chart-container">
            <canvas id="jobsChart"></canvas>
        </div>
    </div>
</div>
          

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('jobsChart').getContext('2d');
const jobsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            <?php $jobs_chart->data_seek(0); while($j=$jobs_chart->fetch_assoc()) echo "'".addslashes($j['title'])."',"; ?>
        ],
        datasets: [{
            label: 'Applications per Job',
            data: [
                <?php
$res = $conn->query("SELECT job_id FROM jobs");
while($row = $res->fetch_assoc()){
    $job_id = intval($row['job_id']);
    
    // Get applications count safely
    $count = 0;
    $app_res = $conn->query("SELECT COUNT(*) AS c FROM applications WHERE job_id=$job_id");
    if ($app_res) {
        $app_row = $app_res->fetch_assoc();
        if ($app_row && isset($app_row['c'])) {
            $count = intval($app_row['c']);
        }
    }

    echo $count . ",";
}
?>

            ],
            backgroundColor: 'rgba(79,70,229,0.7)',
            borderRadius: 6
        }]
    },
    options: {
        responsive:true,
        plugins: { legend:{ display:false } },
        scales: { y:{ beginAtZero:true } }
    }
});
</script>

  </main>

<?php include '../web/footer.php'; ?>

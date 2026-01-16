<?php
session_start();

/* ---------------- ADMIN ONLY ---------------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../web/header.php';

/* ---------------- DB CONNECTION ---------------- */
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

/* ---------------- TOGGLE JOB VISIBILITY ---------------- */
if (isset($_GET['toggle_job'])) {
    $job_id = (int)$_GET['toggle_job'];
    $stmt = $conn->prepare("SELECT is_visible FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $new_status = $row['is_visible'] ? 0 : 1;
        $up = $conn->prepare("UPDATE jobs SET is_visible = ? WHERE job_id = ?");
        $up->bind_param("ii", $new_status, $job_id);
        $up->execute();
    }
    header("Location: dashboard.php");
    exit;
}

/* ---------------- DELETE APPLICATION ---------------- */
if (isset($_GET['delete_application'])) {
    $app_id = (int)$_GET['delete_application'];
    $stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
    $stmt->bind_param("i", $app_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

/* ---------------- AUTO HIDE EXPIRED JOBS ---------------- */
$conn->query("UPDATE jobs SET is_visible = 0 WHERE closing_date < CURDATE()");

/* ---------------- DASHBOARD STATS ---------------- */
$res = $conn->query("SELECT COUNT(*) c FROM jobs");
$row = $res ? $res->fetch_assoc() : null;
$total_jobs = $row ? (int)$row['c'] : 0;

$res = $conn->query("SELECT COUNT(*) c FROM applications");
$row = $res ? $res->fetch_assoc() : null;
$total_apps = $row ? (int)$row['c'] : 0;

$res = $conn->query("SELECT COUNT(*) c FROM jd_downloads");
$row = $res ? $res->fetch_assoc() : null;
$total_downloads = $row ? (int)$row['c'] : 0;

/* ---------------- FETCH JOBS ---------------- */
$jobs_result = $conn->query("SELECT * FROM jobs");

/* ---------------- JD DOWNLOAD COUNTS ---------------- */
$downloads_count = array();
$res = $conn->query("SELECT job_id, COUNT(*) cnt FROM jd_downloads GROUP BY job_id");
while ($r = $res->fetch_assoc()) {
    $downloads_count[(int)$r['job_id']] = (int)$r['cnt'];
}

/* ---------------- APPLICATIONS LIST ---------------- */
$apps_result = $conn->query("
    SELECT a.id, j.title job_title, s.firstname, s.lastname, s.email
    FROM applications a
    LEFT JOIN jobs j ON a.job_id = j.job_id
    LEFT JOIN jobseeker s ON a.seeker_id = s.seeker_id
");

/* ---------------- CHART DATA ---------------- */
$app_counts = array();
$res = $conn->query("SELECT job_id, COUNT(*) cnt FROM applications GROUP BY job_id");
while ($r = $res->fetch_assoc()) {
    $app_counts[(int)$r['job_id']] = (int)$r['cnt'];
}

$labels = array();
$data = array();
$jobs_chart = $conn->query("SELECT job_id, title FROM jobs");
while ($j = $jobs_chart->fetch_assoc()) {
    $labels[] = $j['title'];
    $jid = (int)$j['job_id'];
    $data[] = isset($app_counts[$jid]) ? $app_counts[$jid] : 0;
}
?>

<main>
<div class="dashboard-container">

<h1 class="dashboard-header">Admin Dashboard</h1>

<!-- STATS -->
<div class="dashboard-stats">
    <div class="stat-card blue">
        <h3><?= $total_jobs ?></h3>
        <p>Total Jobs</p>
    </div>
    <div class="stat-card green">
        <h3><?= $total_apps ?></h3>
        <p>Total Applications</p>
    </div>
    <div class="stat-card orange">
        <h3><?= $total_downloads ?></h3>
        <p>Total JD Downloads</p>
    </div>
</div>

<!-- ADD JOB -->
<div class="add-job">
    <a href="add_job.php"><button class="btn btn-add">+ Add Job</button></a>
</div>

<!-- JOBS TABLE -->
<div class="table-container">
<h2>Jobs</h2>
<table class="dashboard-table">
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>JD Downloads</th>
    <th>Visible</th>
    <th>Actions</th>
</tr>
<?php if ($jobs_result->num_rows): ?>
<?php while ($job = $jobs_result->fetch_assoc()):
    $jid = (int)$job['job_id'];
?>
<tr>
    <td><?= $jid ?></td>
    <td><?= htmlspecialchars($job['title']) ?></td>
    <td><?= isset($downloads_count[$jid]) ? $downloads_count[$jid] : 0 ?></td>
    <td><?= $job['is_visible'] ? 'Yes' : 'No' ?></td>
    <td>
        <a href="?toggle_job=<?= $jid ?>"><button class="btn btn-toggle">Toggle</button></a>
        <a href="edit_job.php?job_id=<?= $jid ?>"><button class="btn btn-edit">Edit</button></a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center">No jobs found</td></tr>
<?php endif; ?>
</table>
</div>

<!-- APPLICATIONS TABLE -->
<div class="table-container">
<h2>Applications</h2>
<table class="dashboard-table">
<tr>
    <th>ID</th>
    <th>Job</th>
    <th>Name</th>
    <th>Email</th>
    <th>Actions</th>
</tr>
<?php if ($apps_result->num_rows): ?>
<?php while ($a = $apps_result->fetch_assoc()): ?>
<tr>
    <td><?= $a['id'] ?></td>
    <td><?= htmlspecialchars($a['job_title']) ?></td>
    <td><?= htmlspecialchars($a['firstname'].' '.$a['lastname']) ?></td>
    <td><?= htmlspecialchars($a['email']) ?></td>
    <td>
        <a href="?delete_application=<?= $a['id'] ?>" onclick="return confirm('Delete this application?')">
            <button class="btn btn-delete">Delete</button>
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center">No applications found</td></tr>
<?php endif; ?>
</table>
</div>

<!-- CHART -->
<div class="chart-container">
<h2>Applications per Job</h2>
<canvas id="jobsChart" width="1200" height="400"></canvas>
</div>

</div>
</main>


<script>
var canvas = document.getElementById('jobsChart');
var ctx = canvas.getContext('2d');
var labels = <?= json_encode($labels) ?>;
var data = <?= json_encode($data) ?>;
var max = Math.max.apply(null, data);
var barWidth = 80;
var gap = 38;
for(var i=0;i<data.length;i++){
    var x = i*(barWidth+gap)+50;
    var y = canvas.height - (data[i]/max)*(canvas.height-50);
    ctx.fillStyle = '#4F46E5';
    ctx.fillRect(x, y, barWidth, (data[i]/max)*(canvas.height-50));
    ctx.fillStyle = '#000';
    ctx.fillText(labels[i], x, canvas.height-5);
    ctx.fillText(data[i], x, y-5);
}
</script>

<?php include '../web/footer.php'; ?>

<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='user'){
    header("Location: login.php?redirect=vacancy.php");
    exit;
}

include('header.php');

$conn = new mysqli("localhost","root","","job_recruitment");
if($conn->connect_error) die("Database connection failed");

// Get selected job from jobseeker.php
$selected_job_id = isset($_SESSION['selected_job_id']) ? intval($_SESSION['selected_job_id']) : 0;

// Fetch all jobs
$jobs_res = $conn->query("
    SELECT *
    FROM jobs
    WHERE is_visible = 1
      AND closing_date >= CURDATE()
    ORDER BY job_id DESC
");
$jobs_arr = array();
while($row = $jobs_res->fetch_assoc()) $jobs_arr[] = $row;

// Reorder: selected job first
if($selected_job_id){
    usort($jobs_arr,function($a,$b) use ($selected_job_id){
        if($a['job_id']==$selected_job_id) return -1;
        if($b['job_id']==$selected_job_id) return 1;
        return 0;
    });
    unset($_SESSION['selected_job_id']); // clear after reorder
}

// Fetch seeker applications
$seeker_id = intval($_SESSION['user_id']);
$applications_res = $conn->query("SELECT job_id, cv_file, applied_date FROM applications WHERE seeker_id=$seeker_id");
$applied_jobs = array();
if($applications_res){
    while($app = $applications_res->fetch_assoc()){
        $applied_jobs[$app['job_id']] = $app;
    }
}

// Track "removed" applications from grid
if(!isset($_SESSION['removed_applications'])) $_SESSION['removed_applications'] = array();

// Handle "remove CV" from application grid
if(isset($_POST['remove_cv'])){
    $job_id_remove = intval($_POST['job_id']);
    $_SESSION['removed_applications'][$job_id_remove] = true;
}

// Handle CV Upload
$cv_msg = '';
if(isset($_POST['submit_cv'])){
    $job_id_apply = intval($_POST['job_id']);
    if(isset($_FILES['cv']) && $_FILES['cv']['error']==0){
        $fileTmp = $_FILES['cv']['tmp_name'];
        $fileName = $_FILES['cv']['name'];
        $ext = pathinfo($fileName,PATHINFO_EXTENSION);
        $allowed = array('pdf','doc','docx');
        if(in_array(strtolower($ext), $allowed)){
            $newFile = "cv_".$seeker_id."_".time().".".$ext;
            $uploadPath = "uploads/".$newFile;
            if(move_uploaded_file($fileTmp,$uploadPath)){
                if(isset($applied_jobs[$job_id_apply])){
                    $conn->query("UPDATE applications SET cv_file='$newFile' WHERE seeker_id=$seeker_id AND job_id=$job_id_apply");
                } else {
                    $conn->query("INSERT INTO applications(seeker_id, job_id, cv_file) VALUES($seeker_id, $job_id_apply, '$newFile')");
                }
                $cv_msg = "✅ Application submitted successfully!";
                header("Location: vacancy.php"); exit;
            } else $cv_msg = "⚠️ Error uploading CV.";
        } else $cv_msg = "⚠️ Only PDF/DOC/DOCX allowed.";
    } else $cv_msg = "⚠️ Please select a CV to upload.";
}
?>


<div class="vac-root">
<div class="vac-container">

<div class="vac-left">
<h3>Job Vacancies</h3>
<?php if($cv_msg) echo "<p style='color:#059669; font-weight:600;'>$cv_msg</p>"; ?>

<?php foreach($jobs_arr as $row): 
    $job_id = $row['job_id'];
    $is_applied = isset($applied_jobs[$job_id]);
?>
<div class="vac-card">
    <div class="vac-title"><?= htmlspecialchars($row['title']) ?></div>
    <div class="vac-meta">
        <?= htmlspecialchars($row['location']) ?> | LKR <?= number_format($row['salary']) ?> | Posted: <?= date('d M Y', strtotime($row['posted_date'])) ?> | Closes: <?= date('d M Y', strtotime($row['closing_date'])) ?>
    </div>

    <div class="vac-actions">
        <button class="vac-btn vac-btn-apply" <?= $is_applied?'disabled':'' ?> onclick="toggleDesc(this)">
            <?= $is_applied?'Already Applied':'Apply Now' ?>
        </button>
        <button class="vac-btn vac-btn-download" onclick="window.location.href='download_job.php?job_id=<?= $job_id ?>'">
            Download JD
        </button>
    </div>

    <div class="vac-desc">
        <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>

        <?php if(!$is_applied): ?>
        <div class="vac-cvform">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="job_id" value="<?= $job_id ?>">
                <input type="file" name="cv" required><br>
                <button type="submit" name="submit_cv">Submit Application</button>
            </form>
        </div>
        <?php else: ?>
            <p style="color:#059669; font-weight:600;">✅ You have already applied to this job</p>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="vac-right">
<h3>My Applications</h3>
<?php
if(!$applications_res){
    echo "<p>Error: ".$conn->error."</p>";
} else {
    $has_applications = false;
    echo "<table>
        <tr><th>Job Title</th><th>Applied Date</th><th>CV</th><th>Action</th></tr>";
    foreach($applied_jobs as $job_id => $app){
        if(isset($_SESSION['removed_applications'][$job_id])) continue; // skip removed
        $has_applications = true;
        $job_title_res = $conn->query("SELECT title FROM jobs WHERE job_id=$job_id");
    $job_title = ($job_title_res && $row=$job_title_res->fetch_assoc()) ? $row['title'] : 'Unknown Job';

        echo "<tr>";
        echo "<td>".htmlspecialchars($job_title)."</td>";
        echo "<td>".date('d M Y', strtotime($app['applied_date']))."</td>";
        echo "<td>".(!empty($app['cv_file']) ? "<a href='uploads/".htmlspecialchars($app['cv_file'])."' target='_blank'>Download</a>" : "Not uploaded")."</td>";
        echo "<td>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='job_id' value='$job_id'>";
        echo "<button type='submit' name='remove_cv' class='vac-remove-btn'>Remove</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    if(!$has_applications) echo "<p>You have not applied to any jobs yet.</p>";
}
?>
</div>

</div>
</div>



<?php include('footer.php'); ?>

<?php
session_start();

// 🔒 Only allow logged-in normal users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /job_recruitment/web/login.php");
    exit;
}

// Include dynamic header
include 'header.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "job_recruitment");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch vacancies (replace with your real table)
$vacancies = array(); // use array() instead of []

$sql = "SELECT * FROM vacancies"; // your table name
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vacancies[] = $row; // appending is okay
    }
}

?>

<main>
    <h1>Available Vacancies</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</p>

    <div class="vacancies-grid">

        <?php if (!empty($vacancies)): ?>
            <?php foreach ($vacancies as $vacancy): ?>
                <div class="vacancy-card">
                    <h3><?php echo htmlspecialchars($vacancy['title']); ?></h3>
                    <p>Location: <?php echo htmlspecialchars($vacancy['location']); ?></p>
                    <p>Type: <?php echo htmlspecialchars($vacancy['type']); ?></p>
                    <button>Apply Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Static examples if table is empty -->
            <div class="vacancy-card">
                <h3>Software Developer</h3>
                <p>Location: Colombo</p>
                <p>Full-Time</p>
                <button>Apply Now</button>
            </div>
            <div class="vacancy-card">
                <h3>UI/UX Designer</h3>
                <p>Location: Kandy</p>
                <p>Part-Time</p>
                <button>Apply Now</button>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include 'footer.php'; ?>

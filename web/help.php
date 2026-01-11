<?php
// help.php
session_start();

// Public Help page, no login required
include 'header.php';
?>

<main>
    <div class="help-wrapper">
        <div class="help-card">
            <h2>Help & Support</h2>
            <p class="intro">Welcome! Here’s how to use the Job Recruitment System.</p>

            <!-- Accordion Sections -->
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-btn"> > Profile Management</button>
                    <div class="accordion-content">
                        <p>Go to <strong>Profile</strong> to view or edit your information. You can update your name, position, age, address, email, and upload a profile picture.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-btn"> > Job Applications</button>
                    <div class="accordion-content">
                        <p>Go to <strong>Jobs</strong> to view available positions and apply. Track your applications in your dashboard.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-btn"> > Admin Tasks</button>
                    <div class="accordion-content">
                        <p>Admins can manage users, edit profiles, and assign roles. Access these options from the <strong>Admin Dashboard</strong>.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-btn"> > Support</button>
                    <div class="accordion-content">
                        <p>If you need further help, contact us at: <a href="mailto:support@company.com">support@job_recruitment.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>



<?php include 'footer.php'; ?>

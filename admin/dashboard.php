<?php include '../web/header.php'; ?>




<!-- Features -->
<section class="features">
    <h2>Jobs</h2>
    <div class="features-container">
    <div class="card clickable" onclick="openPopup('trustedJobsModal')">
        <!-- Image -->
        <img src="https://images.pexels.com/photos/3184298/pexels-photo-3184298.jpeg?auto=compress&cs=tinysrgb&h=350"
             alt="Trusted Jobs" class="modal-image">
        <h3>Trusted Jobs</h3>
        <p>Click to know more</p>
    </div>

    <div class="card clickable" onclick="openPopup('easyAppModal')">
        <img src="https://images.pexels.com/photos/3183137/pexels-photo-3183137.jpeg?auto=compress&cs=tinysrgb&h=350" 
     alt="Apply Online" class="modal-image">
        <h3>Easy Application</h3>
        <p>Apply online with your resume</p>
    </div>

    <div class="card clickable" onclick="openPopup('fastProcessModal')">
        <img src="https://images.pexels.com/photos/3184293/pexels-photo-3184293.jpeg?auto=compress&cs=tinysrgb&h=350" 
     alt="Fast Process" class="modal-image">
        <h3>Fast Process</h3>
        <p>Simple and quick recruitment system</p>
    </div>

    <div class="card clickable" onclick="openPopup('trustedJobsModal')">
        <!-- Image -->
        <img src="https://images.pexels.com/photos/3184298/pexels-photo-3184298.jpeg?auto=compress&cs=tinysrgb&h=350"
             alt="Trusted Jobs" class="modal-image">
        <h3>Trusted Jobs</h3>
        <p>Click to know more</p>
    </div>

    <div class="card clickable" onclick="openPopup('easyAppModal')">
        <img src="https://images.pexels.com/photos/3183137/pexels-photo-3183137.jpeg?auto=compress&cs=tinysrgb&h=350" 
     alt="Apply Online" class="modal-image">
        <h3>Easy Application</h3>
        <p>Apply online with your resume</p>
    </div>

    <div class="card clickable" onclick="openPopup('fastProcessModal')">
        <img src="https://images.pexels.com/photos/3184293/pexels-photo-3184293.jpeg?auto=compress&cs=tinysrgb&h=350" 
     alt="Fast Process" class="modal-image">
        <h3>Fast Process</h3>
        <p>Simple and quick recruitment system</p>
    </div>
    </div>
</section>

<!-- Popup Modal -->
<div id="trustedJobsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup('trustedJobsModal')">&times;</span>
        <h2>Trusted Jobs</h2>
        <p>
            All job vacancies in this system are posted only by the administrator.
            This ensures that every job is verified, authentic, and safe for job seekers.
        </p>
        <p>
            Every job listing is carefully reviewed and approved before being published.
            This process eliminates fake job postings and protects job seekers from
            misleading or unauthorized opportunities.
        </p>

        <p>
            By centralizing job posting responsibilities with the admin,
            the system maintains data accuracy, trustworthiness, and security.
            Job seekers can confidently apply knowing that all opportunities
            are verified and authentic.
        </p>

        <p>
            This feature improves user trust, enhances system credibility,
            and ensures a safe and professional recruitment environment.
        </p>
    </div>
</div>
<!-- Easy Application Modal -->
<div id="easyAppModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup('easyAppModal')">&times;</span>
        <h2>Easy Application</h2>
        <p>Job seekers can apply online quickly and effortlessly using their resumes. The system allows you to submit applications directly through the platform without any manual paperwork.</p>
        <p>Applications are automatically formatted and delivered securely to employers, saving time and ensuring accuracy for both job seekers and recruiters.</p>
        <p>This streamlined process simplifies job applications, reduces errors, and helps you focus on finding the right job opportunities efficiently.</p>
    </div>
</div>

<!-- Fast Process Modal -->
<div id="fastProcessModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup('fastProcessModal')">&times;</span>
        <h2>Fast Process</h2>
        <p>Our recruitment system is designed to be fast and efficient, allowing job seekers to quickly browse and apply for opportunities.</p>
        <p>The streamlined workflow ensures that applications reach employers immediately and reduces delays in communication.</p>
        <p>By automating key steps in the recruitment process, the system saves time for both applicants and recruiters, making hiring faster and simpler.</p>
    </div>
</div>
<?php include '../web/footer.php'; ?>

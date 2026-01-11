// Redirect to jobs page
function goToJobss() {
    window.location.href = "jobseeker.php"; // redirect
}


// Simple alert for demo
function showMessage() {
    alert("Welcome to Job Recruitment System");
}
f// Open any modal by ID
function openPopup(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

// Close any modal by ID
function closePopup(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}

//login fuction
document.addEventListener("DOMContentLoaded", function() {
    const loginModal = document.getElementById("loginModal");
    const loginBtn = document.querySelector(".btn.login-btn");
    const closeLogin = document.querySelector(".close-login");

    if(loginBtn && loginModal && closeLogin){
        // Open login modal
        loginBtn.addEventListener("click", function(e){
            e.preventDefault();
            loginModal.style.display = "block";
        });

        // Close login modal
        closeLogin.addEventListener("click", function(){
            loginModal.style.display = "none";
        });

        // Close if clicked outside
        window.addEventListener("click", function(e){
            if(e.target == loginModal){
                loginModal.style.display = "none";
            }
        });
    }
});


// Open View User popup and fetch user details
function openViewPopup(userId) {
    fetch('view_user.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('popupBody').innerHTML = data;
            document.getElementById('viewUserPopup').style.display = 'flex';
        });
}

// Close the View User popup
function closeViewPopup() {
    document.getElementById('viewUserPopup').style.display = 'none';
}

// Close popup if clicked outside content
window.addEventListener('click', function(event) {
    const popup = document.getElementById('viewUserPopup');
    if (event.target === popup) {
        popup.style.display = 'none';
    }
});
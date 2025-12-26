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


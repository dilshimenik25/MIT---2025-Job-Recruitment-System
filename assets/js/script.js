// Redirect to jobs page
function goToJobss() {
    window.location.href = "jobseeker.php";
}

// Simple alert for demo
function showMessage() {
    alert("Welcome to Job Recruitment System");
}

// Open any modal by ID
function openPopup(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) modal.style.display = "flex";
}

// Close any modal by ID
function closePopup(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) modal.style.display = "none";
}

// Login modal
document.addEventListener("DOMContentLoaded", function() {
    const loginModal = document.getElementById("loginModal");
    const loginBtn = document.querySelector(".btn.login-btn");
    const closeLogin = document.querySelector(".close-login");

    if(loginBtn && loginModal && closeLogin){
        loginBtn.addEventListener("click", function(e){
            e.preventDefault();
            loginModal.style.display = "block";
        });

        closeLogin.addEventListener("click", function(){
            loginModal.style.display = "none";
        });

        window.addEventListener("click", function(e){
            if(e.target == loginModal){
                loginModal.style.display = "none";
            }
        });
    }
});

// View User popup
function openViewPopup(userId) {
    fetch('view_user.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            const popupBody = document.getElementById('popupBody');
            const viewPopup = document.getElementById('viewUserPopup');
            if(popupBody && viewPopup){
                popupBody.innerHTML = data;
                viewPopup.style.display = 'flex';
            }
        });
}

function closeViewPopup() {
    const viewPopup = document.getElementById('viewUserPopup');
    if(viewPopup) viewPopup.style.display = 'none';
}

// Close popup when clicking outside
window.addEventListener('click', function(event) {
    const popup = document.getElementById('viewUserPopup');
    if (popup && event.target === popup) {
        popup.style.display = 'none';
    }
});

// Profile photo preview (safe)
function setupPhotoPreview(inputId, previewId) {
    const inputEl = document.getElementById(inputId);
    const previewEl = document.getElementById(previewId);

    if(inputEl && previewEl){
        inputEl.addEventListener('change', function() {
            const file = this.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){
                    previewEl.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
}

// Example usage:
document.addEventListener('DOMContentLoaded', function(){
    setupPhotoPreview('profilePhotoInput', 'photoPreview');
    setupPhotoPreview('userPhotoInput', 'userPhotoPreview');

    // Accordion toggle
    const buttons = document.querySelectorAll('.accordion-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            if(!content) return;

            if(content.style.display === "block"){
                content.style.display = "none";
            } else {
                // Close other accordions
                document.querySelectorAll('.accordion-content').forEach(c => c.style.display = "none");
                content.style.display = "block";
            }
        });
    });
});

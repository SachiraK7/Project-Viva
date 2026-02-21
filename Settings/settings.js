// Variable to track which field we are currently updating (name, email, or password)
let activeFieldId = "";

/**
 * Triggers the Modal based on which 'Change' link was clicked
 * @param {string} id - The ID of the field to edit ('name', 'email', or 'password')
 */
function handleEdit(id) {
    activeFieldId = id;
    
    const modal = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const singleFieldContainer = document.getElementById('single-field-container');
    const passwordFieldsContainer = document.getElementById('password-fields-container');
    const modalInput = document.getElementById('modal-input');
    const originalInput = document.getElementById(id);

    // Show the modal overlay
    modal.style.display = 'flex';

    if (id === 'password') {
        // Layout for "Change Password" with two inputs and labels
        modalTitle.innerText = "Change Password";
        singleFieldContainer.style.display = 'none';
        passwordFieldsContainer.style.display = 'block';
        
        // Clear fields for security
        document.getElementById('new-password').value = "";
        document.getElementById('confirm-password').value = "";
    } else {
        // Layout for "Name" or "Email" with a single input
        modalTitle.innerText = (id === 'name') ? "Change Name" : "Change Email";
        singleFieldContainer.style.display = 'block';
        passwordFieldsContainer.style.display = 'none';
        
        // Populate modal with current value from the main page
        modalInput.value = originalInput.value;
    }
}

/**
 * Validates the modal data and saves it back to the main UI and Database via PHP
 */
function saveModalData() {
    let newValue = "";

    // 1. DATA VALIDATION
    if (activeFieldId === 'password') {
        const pass = document.getElementById('new-password').value.trim();
        const confirm = document.getElementById('confirm-password').value.trim();

        if (pass === "" || confirm === "") {
            alert("This field cannot be empty.");
            return;
        }
        if (pass !== confirm) {
            alert("Passwords do not match!");
            return;
        }
        newValue = pass; 
    } else {
        newValue = document.getElementById('modal-input').value.trim();

        if (newValue === "") {
            alert("This field cannot be empty.");
            return;
        }
    }

    // 2. DATABASE UPDATE SECTION
    // Sends the data to update_profile.php using POST
    fetch('update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `field=${activeFieldId}&value=${encodeURIComponent(newValue)}`
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            // 3. UI UPDATE (Only if database update was successful)
            if (activeFieldId === 'password') {
                document.getElementById('password').value = "********"; 
            } else {
                // Update the hidden input value on the main page
                document.getElementById(activeFieldId).value = newValue;

                // Sync header and profile hero text if name changed
                if (activeFieldId === 'name') {
                    const profileName = document.querySelector('.display-name');
                    const topHeaderName = document.querySelector('.profile-top span');
                    if (profileName) profileName.innerText = newValue;
                    if (topHeaderName) topHeaderName.innerText = newValue;
                }
            }
            
            closeModal();
            alert(activeFieldId.charAt(0).toUpperCase() + activeFieldId.slice(1) + " updated successfully!");
        } else {
            alert("Error updating: " + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while saving.");
    });
}

/**
 * Close modal when clicking on the dark background overlay
 */
window.onclick = function(event) {
    const modal = document.getElementById('modal-overlay');
    if (event.target == modal) {
        closeModal();
    }
}    

function handleDeleteAccount() {
    // Shows the new warning modal we just added
    document.getElementById('delete-modal-overlay').style.display = 'flex';
}

function processAccountDeletion() {
   
    window.location.href = 'delete_process.php'; 
}

function closeModal() {
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'none';
    
    // Reset Modal for Name/Email/Password usage
    const modalFooter = document.getElementById('modal-footer-buttons');
    const modalBody = document.getElementById('modal-body-content');

    modalBody.innerHTML = `
        <div id="single-field-container">
            <input type="text" id="modal-input" class="modal-field">
        </div>
        <div id="password-fields-container" style="display: none;">
            <label class="modal-label">New Password</label>
            <input type="password" id="new-password" class="modal-field">
            <label class="modal-label">Confirm Password</label>
            <input type="password" id="confirm-password" class="modal-field">
        </div>
    `;

    modalFooter.innerHTML = `
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button class="btn-confirm" onclick="saveModalData()">Confirm</button>
    `;
}

function handleProfilePicChange() {
    const overlay = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body-content');
    const modalFooter = document.getElementById('modal-footer-buttons');

    modalTitle.innerText = "Change Profile Picture";

    // Create the upload UI
    modalBody.innerHTML = `
        <div class="upload-container">
            <img src="image 10.png" class="modal-preview-img" id="preview">
            <br>
            <label for="profile-upload" class="file-input-label">
                Click to select new image
            </label>
            <input type="file" id="profile-upload" accept="image/*" onchange="previewImage(event)">
        </div>
    `;

    // Buttons for Saving
    modalFooter.innerHTML = `
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button class="btn-confirm" style="background: #8A56E2;" onclick="saveProfilePic()">Save Picture</button>
    `;

    overlay.style.display = 'flex';
}

// Function to preview the image before uploading
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('preview');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function saveProfilePic() {
    const fileInput = document.getElementById('profile-upload');
    if (fileInput.files.length === 0) {
        alert("Please select an image first.");
        return;
    }

    const formData = new FormData();
    formData.append('profile_pic', fileInput.files[0]);

    // Send the file to PHP using fetch
    fetch('settings.php', { // <--- CHANGE THIS LINE HERE
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const parts = data.split('|');
        if (parts[0] === "success") {
            const newImagePath = parts[1];
            
            // Update UI images
            document.querySelector('.large-avatar').src = newImagePath;
            document.querySelector('.mini-avatar').src = newImagePath;
            
            closeModal();
            alert("Profile picture updated successfully!");
        } else {
            alert("Error: " + parts[1]);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during upload.");
    });
}
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
 * Closes the modal popup
 */
function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
}

/**
 * Close modal when clicking on the dark background overlay
 */
window.onclick = function(event) {
    const modal = document.getElementById('modal-overlay');
    if (event.target == modal) {
        closeModal();
    }
};
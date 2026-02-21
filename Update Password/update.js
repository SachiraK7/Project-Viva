document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('updatePasswordForm');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    form.addEventListener('submit', (event) => {
        // Clear custom validity messages
        newPassword.setCustomValidity('');
        confirmPassword.setCustomValidity('');

        // Check if passwords match
        if (newPassword.value !== confirmPassword.value) {
            // Prevent the form from submitting to PHP
            event.preventDefault(); 
            
            // Show an alert (or you can inject HTML error text)
            alert('Your passwords do not match. Please check them and try again.');
            
            // Highlight the confirm password box
            confirmPassword.focus();
        }
    });
});
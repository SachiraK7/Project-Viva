// --- Modal Handling ---

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Close modal if clicked outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}

// --- Delete Logic ---
let deleteId = null;

function openDeleteModal(id) {
    deleteId = id;
    openModal('deleteModal');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteId) {
        const formData = new FormData();
        formData.append('action', 'delete_expense');
        formData.append('id', deleteId);

        fetch('expense.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteModal');
                location.reload(); // Reload to show changes
            } else {
                alert('Error deleting expense: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
    }
});


// --- Add Expense Form Logic ---
document.getElementById('addExpenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('expense.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Successfully saved to spendify_db expenses table
            location.reload(); // Reload to refresh table and totals
        } else {
            // Shows the specific error from PHP (e.g., "Please fill all required fields correctly.")
            alert('Error adding expense: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('An error occurred while connecting to the server.');
    });
});

// --- Update Expense Logic ---

// Attach click event to table rows for updating
document.querySelectorAll('.data-row').forEach(row => {
    row.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        
        // Fetch data for this row
        const formData = new FormData();
        formData.append('action', 'get_expense');
        formData.append('id', id);

        fetch('expense.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Fill the update form (Safely checking if elements exist first)
            const updateIdElem = document.getElementById('update_id');
            
            if (updateIdElem) {
                // Updated variable names mapped to your database columns
                updateIdElem.value = data.expense_id; 
                document.getElementById('update_date').value = data.expense_date;
                document.getElementById('update_amount').value = data.amount;
                document.getElementById('update_description').value = data.description;
                document.getElementById('update_category').value = data.category_id;
                
                // Check if category exists in dropdown, if not add it (handling custom cats)
                const updateCategorySelect = document.getElementById('update_category');
                // Updated data.category to data.category_id here as well
                const exists = Array.from(updateCategorySelect.options).some(option => option.value == data.category_id);
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = data.category_id;
                    opt.text = data.category_id;
                    updateCategorySelect.add(opt);
                    updateCategorySelect.value = data.category_id;
                }

                openModal('updateModal');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

// Safely attach event listener to update form
const updateForm = document.getElementById('updateExpenseForm');
if (updateForm) {
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('expense.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('updateModal');
                location.reload();
            } else {
                 alert('Error updating expense: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
    });
}
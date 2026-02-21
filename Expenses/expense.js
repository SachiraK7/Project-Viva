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
            }
        });
    }
});

// --- Add Category Logic ---
function openCategoryModal() {
    openModal('categoryModal');
}

function addNewCategory() {
    const newCat = document.getElementById('newCategoryInput').value;
    if (newCat) {
        // Add to main dropdown safely
        const select = document.getElementById('categorySelect');
        if (select) {
            const option = document.createElement('option');
            option.text = newCat;
            option.value = newCat;
            select.add(option);
            select.value = newCat; // Select it
        }

        // Add to update dropdown safely (prevents errors if the update modal isn't built yet)
        const updateSelect = document.getElementById('update_category');
        if (updateSelect) {
            const updateOption = document.createElement('option');
            updateOption.text = newCat;
            updateOption.value = newCat;
            updateSelect.add(updateOption);
        }

        closeModal('categoryModal');
        // Clear the input field so it's empty the next time you open it
        document.getElementById('newCategoryInput').value = "";
    }
}

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
            location.reload(); // Reload to refresh table and totals
        } else {
            alert('Error adding expense');
        }
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
                updateIdElem.value = data.id;
                document.getElementById('update_date').value = data.date;
                document.getElementById('update_amount').value = data.amount;
                document.getElementById('update_description').value = data.description;
                document.getElementById('update_category').value = data.category;
                
                // Check if category exists in dropdown, if not add it (handling custom cats)
                const updateCategorySelect = document.getElementById('update_category');
                const exists = Array.from(updateCategorySelect.options).some(option => option.value === data.category);
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = data.category;
                    opt.text = data.category;
                    updateCategorySelect.add(opt);
                    updateCategorySelect.value = data.category;
                }

                openModal('updateModal');
            }
        });
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
            }
        });
    });
}
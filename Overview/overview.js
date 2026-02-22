document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. SIDEBAR TOGGLE ---
    const menuToggle = document.querySelector(".menu-toggle");
    const sidebar = document.querySelector(".sidebar");
    
    // Close sidebar if clicking outside of it
    document.addEventListener("click", (e) => {
        if(sidebar && sidebar.classList.contains("active") && 
           !sidebar.contains(e.target) && 
           (menuToggle && !menuToggle.contains(e.target))) {
             sidebar.classList.remove("active");
        }
    });

    if(menuToggle) {
        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }

    // --- 2. CALENDAR RENDERING LOGIC ---
    const calendarGrid = document.querySelector(".calendar-grid");
    const calendarHeader = document.querySelector("#month-year");
    const prevBtn = document.querySelector("#prev");
    const nextBtn = document.querySelector("#next");

    let date = new Date();
    let currYear = date.getFullYear();
    let currMonth = date.getMonth();

    const months = [
        "January", "February", "March", "April", "May", "June", 
        "July", "August", "September", "October", "November", "December"
    ];

    function renderCalendar() {
        // First day of the month (0-6, Sun-Sat)
        let firstDayIndex = new Date(currYear, currMonth, 1).getDay();
        // Last date of the current month (e.g., 30 or 31)
        let lastDateOfMonth = new Date(currYear, currMonth + 1, 0).getDate();
        
        // Update Header
        calendarHeader.innerText = `${months[currMonth]} ${currYear}`;

        let liTag = "";

        // Add Weekday Headers
        const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        days.forEach(day => {
            liTag += `<div class="day-name">${day}</div>`;
        });

        // Adjust firstDayIndex for Monday start
        let adjustedFirstDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

        // Add Empty Cells for previous month
        for (let i = 0; i < adjustedFirstDay; i++) {
            liTag += `<div class="date-box empty"></div>`;
        }

        // Add Days of Current Month
        for (let i = 1; i <= lastDateOfMonth; i++) {
            let isToday = i === new Date().getDate() && 
                          currMonth === new Date().getMonth() && 
                          currYear === new Date().getFullYear() ? "active-date" : "";
            
            let displayNum = i < 10 ? '0' + i : i;
            liTag += `<div class="date-box ${isToday}">${displayNum}</div>`;
        }

        // Fill remaining grid cells dynamically
        let totalCells = adjustedFirstDay + lastDateOfMonth;
        let totalRequiredCells = Math.ceil(totalCells / 7) * 7; 
        let remainingCells = totalRequiredCells - totalCells;

        for (let i = 0; i < remainingCells; i++) {
            liTag += `<div class="date-box empty"></div>`;
        }

        calendarGrid.innerHTML = liTag;
    }

    renderCalendar();

    // Button Listeners
    prevBtn.addEventListener("click", () => {
        currMonth--;
        if(currMonth < 0) {
            currMonth = 11;
            currYear--;
        }
        renderCalendar();
    });

    nextBtn.addEventListener("click", () => {
        currMonth++;
        if(currMonth > 11) {
            currMonth = 0;
            currYear++;
        }
        renderCalendar();
    });

    // --- 3. MODAL & CALENDAR CLICK LOGIC ---
    const modal = document.getElementById('descModal');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const modalDescContent = document.getElementById('modalDescContent');

    // Listen for clicks on the calendar grid
    if(calendarGrid) {
        calendarGrid.addEventListener('click', (e) => {
            // Check if the clicked element is a valid date-box with a number
            if(e.target.classList.contains('date-box') && !e.target.classList.contains('empty')) {
                let dayText = e.target.innerText.trim();
                
                if(dayText !== '' && !isNaN(dayText)) {
                    // Format month and day to matching database format (YYYY-MM-DD)
                    let monthIndex = currMonth + 1; // Convert 0-indexed to 1-indexed
                    let formattedMonth = monthIndex < 10 ? '0' + monthIndex : monthIndex;
                    let formattedDay = dayText.length === 1 ? '0' + dayText : dayText;
                    
                    let targetDate = `${currYear}-${formattedMonth}-${formattedDay}`;

                    // Filter our PHP data to find matches (safeguard added in case expensesData is missing)
                    let matchingExpenses = (typeof expensesData !== 'undefined') ? 
                        expensesData.filter(exp => exp.exp_date === targetDate) : [];

                    // Populate Modal content
                    if (matchingExpenses.length > 0) {
                        modalDescContent.innerHTML = matchingExpenses.map(exp => `<p>• ${exp.description}</p>`).join('');
                    } else {
                        modalDescContent.innerHTML = '<p style="color: #888;">No expenses recorded for this date.</p>';
                    }

                    // Show Modal
                    if(modal) modal.style.display = 'block';
                }
            }
        });
    }

    // Close Modal Logic
    const closeModal = () => { if(modal) modal.style.display = 'none'; };
    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if(confirmBtn) confirmBtn.addEventListener('click', closeModal);

    // Close if user clicks outside the modal content
    window.addEventListener('click', (e) => {
        if (e.target == modal) {
            closeModal();
        }
    });
});
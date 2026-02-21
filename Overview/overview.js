document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. SIDEBAR TOGGLE ---
    const menuToggle = document.querySelector(".menu-toggle");
    const sidebar = document.querySelector(".sidebar");
    
    // Close sidebar if clicking outside of it (optional better UX)
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

    // --- 2. CALENDAR LOGIC ---
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

        // 1. Add Weekday Headers (Using Full Names to match the image)
        const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        days.forEach(day => {
            liTag += `<div class="day-name">${day}</div>`;
        });

        // Adjust firstDayIndex for Monday start (Sun(0) becomes 6, Mon(1) becomes 0)
        let adjustedFirstDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

        // 2. Add Empty Cells for previous month
        for (let i = 0; i < adjustedFirstDay; i++) {
            liTag += `<div class="date-box empty"></div>`;
        }

        // 3. Add Days of Current Month
        for (let i = 1; i <= lastDateOfMonth; i++) {
            // Highlight "Today"
            let isToday = i === new Date().getDate() && 
                          currMonth === new Date().getMonth() && 
                          currYear === new Date().getFullYear() ? "active-date" : "";
            
            // Add leading zeros (01, 02, etc.) to match the image
            let displayNum = i < 10 ? '0' + i : i;
            
            liTag += `<div class="date-box ${isToday}">${displayNum}</div>`;
        }

        // 4. Fill remaining grid cells dynamically (Fixes the missing line issue)
        let totalCells = adjustedFirstDay + lastDateOfMonth;
        
        // This calculates exactly how many rows are needed (e.g., 28, 35, or 42 cells)
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
});
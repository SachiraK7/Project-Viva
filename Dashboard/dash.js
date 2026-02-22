document.addEventListener("DOMContentLoaded", function() {

    // --- CUSTOM PLUGIN FOR BAR CHART GREY BACKGROUND ---
    const chartAreaBackground = {
        id: 'chartAreaBackground',
        beforeDraw: (chart) => {
            const {ctx, chartArea: {top, bottom, left, right}} = chart;
            ctx.save();
            ctx.fillStyle = '#E2E2E2'; // Grey background from the design
            ctx.fillRect(left, top, right - left, bottom - top);
            ctx.restore();
        }
    };

    // --- INITIAL DATA FROM PHP ---
    const totalExpense = catData.reduce((acc, val) => Number(acc) + Number(val), 0);
    const legendContainer = document.getElementById('custom-legend');

    // --- 1. BAR CHART: Today’s Category Expenses ---
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: catLabels,
            datasets: [{
                label: 'Today\'s Expense ($)',
                data: catData,
                backgroundColor: catColors,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#FFFFFF', drawBorder: false },
                    ticks: {
                        stepSize: 50,
                        callback: function(value) { return '$' + value; },
                        font: { family: 'Poppins', size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 11 } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        },
        plugins: [chartAreaBackground]
    });

    // --- 2. PIE CHART: Today’s Category Distribution ---
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    let pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData,
                backgroundColor: catColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            let percentage = totalExpense > 0 
                                ? ((value / totalExpense) * 100).toFixed(1) + '%'
                                : '0%';
                            return context.label + ': ' + percentage;
                        }
                    }
                }
            }
        }
    });

    // --- 3. FUNCTION TO UPDATE CUSTOM LEGEND ---
    function updateLegend(labels, colors) {
        if (legendContainer) {
            let legendHTML = '';
            labels.forEach((label, index) => {
                legendHTML += `
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: ${colors[index]}; width: 14px; height: 14px; display: inline-block;"></span>
                        <span class="legend-text" style="font-size: 11px; font-family: 'Poppins';">${label}</span>
                    </div>
                `;
            });
            legendContainer.innerHTML = legendHTML;
        }
    }

    updateLegend(catLabels, catColors);

    // --- 4. FUNCTION TO FETCH LATEST DATA AND UPDATE CHARTS ---
    function updateDashboardCharts() {
        fetch('dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=fetch_charts'
        })
        .then(res => res.json())
        .then(data => {
            // Filter today's expenses for pie chart & bar chart
            const todayLabels = data.catLabels;
            const todayData = data.catData;
            const todayColors = data.catColors;

            const totalTodayExpense = todayData.reduce((acc, val) => Number(acc) + Number(val), 0);

            // Update bar chart
            barChart.data.labels = todayLabels;
            barChart.data.datasets[0].data = todayData;
            barChart.data.datasets[0].backgroundColor = todayColors;
            barChart.update();

            // Update pie chart
            pieChart.data.labels = todayLabels;
            pieChart.data.datasets[0].data = todayData;
            pieChart.data.datasets[0].backgroundColor = todayColors;
            pieChart.update();

            // Update custom legend
            updateLegend(todayLabels, todayColors);

            // Update cards
            document.querySelector('.cards-container .card:nth-child(1) .card-amount').textContent = '$' + Number(data.today_expense).toLocaleString();
            document.querySelector('.cards-container .card:nth-child(2) .card-amount').textContent = '$' + Number(data.week_expense).toLocaleString();
            document.querySelector('.cards-container .card:nth-child(3) .card-amount').textContent = '$' + Number(data.month_expense).toLocaleString();
            document.querySelector('.cards-container .card:nth-child(4) .card-amount').textContent = '$' + Number(data.total_expense).toLocaleString();
        })
        .catch(err => console.error(err));
    }

    // --- 5. AUTO-UPDATE EVERY 5 SECONDS ---
    setInterval(updateDashboardCharts, 5000);

    // --- INITIAL UPDATE ---
    updateDashboardCharts();

});
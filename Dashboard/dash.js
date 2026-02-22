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

    // --- 1. BAR CHART CONFIGURATION ---
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: weeklyLabels, 
            datasets: [{
                label: 'Expense',
                data: weeklyData, 
                backgroundColor: '#8294F8',
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#FFFFFF', 
                        drawBorder: false 
                    },
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

    // --- 2. PIE CHART CONFIGURATION ---
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const totalExpense = catData.reduce((acc, val) => Number(acc) + Number(val), 0);
    const pieChart = new Chart(ctxPie, {
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

    // --- 3. DYNAMIC CUSTOM PIE CHART LEGEND ---
    const legendContainer = document.getElementById('custom-legend');
    if (legendContainer) {
        let legendHTML = '';
        catLabels.forEach((label, index) => {
            if (catColors[index]) { 
                legendHTML += `
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: ${catColors[index]}; width: 14px; height: 14px; display: inline-block;"></span>
                        <span class="legend-text" style="font-size: 11px; font-family: 'Poppins';">${label}</span>
                    </div>
                `;
            }
        });
        legendContainer.innerHTML = legendHTML;
    }

    // --- 4. AUTO-UPDATE CHARTS EVERY 5 SECONDS ---
    setInterval(() => {
        fetch('dashboard_data.php') // PHP endpoint returns JSON with weeklyLabels, weeklyData, catLabels, catData, catColors
            .then(res => res.json())
            .then(data => {
                // Update bar chart
                barChart.data.labels = data.weeklyLabels;
                barChart.data.datasets[0].data = data.weeklyData;
                barChart.update();

                // Update pie chart
                pieChart.data.labels = data.catLabels;
                pieChart.data.datasets[0].data = data.catData;
                pieChart.data.datasets[0].backgroundColor = data.catColors;
                pieChart.update();

                // Update custom legend
                if (legendContainer) {
                    let legendHTML = '';
                    data.catLabels.forEach((label, index) => {
                        legendHTML += `
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: ${data.catColors[index]}; width: 14px; height: 14px; display: inline-block;"></span>
                                <span class="legend-text" style="font-size: 11px; font-family: 'Poppins';">${label}</span>
                            </div>
                        `;
                    });
                    legendContainer.innerHTML = legendHTML;
                }
            });
    }, 5000);

});
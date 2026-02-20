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
    new Chart(ctxBar, {
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
                        color: '#FFFFFF', // White grid lines
                        drawBorder: false 
                    },
                    ticks: {
                        stepSize: 50, // Jump by 50 dollars
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

    new Chart(ctxPie, {
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
                legend: { display: false }, // Turned off for custom HTML legend
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
});
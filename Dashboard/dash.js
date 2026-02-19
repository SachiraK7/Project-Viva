document.addEventListener("DOMContentLoaded", function() {

    // --- 1. BAR CHART CONFIGURATION ---
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: weeklyLabels, // Passed from PHP
            datasets: [{
                label: 'Expense',
                data: weeklyData, // Passed from PHP
                backgroundColor: '#7F85F8', 
                borderRadius: 5,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' },
                    ticks: {
                        callback: function(value) { return '$' + value; },
                        font: { family: 'Poppins' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins' } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // --- 2. PIE CHART CONFIGURATION ---
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    
    // Calculate total safely (prevent division by zero)
    const totalExpense = catData.reduce((acc, val) => Number(acc) + Number(val), 0);

    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: catLabels, // Passed from PHP
            datasets: [{
                data: catData, // Passed from PHP
                backgroundColor: catColors, // Passed from PHP
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 15,
                        font: { size: 10, family: 'Poppins' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            // Check if total is 0 to avoid "NaN%"
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
});
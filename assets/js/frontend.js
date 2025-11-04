document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Check if we have projection data
    if (typeof tgpFrontendData === 'undefined') {
        return;
    }
    
    let trafficChart = null;
    const projectionsData = tgpFrontendData;
    
    // Initialize chart
    renderChart(projectionsData);
    
    // Chart toggle handlers
    const chartToggles = document.querySelectorAll('.tgp-frontend-chart-toggle');
    chartToggles.forEach(function(toggle) {
        toggle.addEventListener('change', updateChart);
    });
    
    /**
     * Render chart
     */
    function renderChart(data) {
        const ctx = document.getElementById('tgp-frontend-traffic-chart');
        
        if (!ctx) {
            return;
        }
        
        // Prepare datasets
        const datasets = [];
        const colors = {
            'Current Trajectory': '#6c757d',
            'Existing Keywords': '#007bff',
            'Must-Have Keywords': '#28a745',
            'New Keywords': '#ffc107'
        };
        
        Object.keys(data.projections).forEach(function(category) {
            const projection = data.projections[category];
            const traffic = projection.map(function(item) { return item.traffic; });
            
            datasets.push({
                label: category,
                data: traffic,
                borderColor: colors[category] || '#333',
                backgroundColor: colors[category] || '#333',
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            });
        });
        
        // Create chart
        trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' visits';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }
    
    /**
     * Update chart based on toggles
     */
    function updateChart() {
        if (!trafficChart || !projectionsData) {
            return;
        }
        
        const activeCategories = [];
        const checkedToggles = document.querySelectorAll('.tgp-frontend-chart-toggle:checked');
        checkedToggles.forEach(function(toggle) {
            activeCategories.push(toggle.dataset.category);
        });
        
        trafficChart.data.datasets.forEach(function(dataset) {
            dataset.hidden = !activeCategories.includes(dataset.label);
        });
        
        trafficChart.update();
    }
});


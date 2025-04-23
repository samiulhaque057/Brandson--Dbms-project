document.addEventListener('DOMContentLoaded', function() {
    // Initialize Overview Chart
    initOverviewChart();
    
    // Initialize Tabs
    initTabs();
    
    // Add event listeners
    addEventListeners();
});







function initOverviewChart() {
    const ctx = document.getElementById('overviewChart').getContext('2d');
    
    // Chart data
    const labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const beefData = [3000, 2700, 3200, 4800, 4000, 3800, 4000];
    const chickenData = [1200, 1800, 2500, 3000, 2800, 3200, 2000];
    const lambData = [800, 1500, 2000, 1500, 2800, 3000, 3000];
    
    // Create chart
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Beef',
                    data: beefData,
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#a855f7',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Chicken',
                    data: chickenData,
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: '#ec4899',
                    pointBorderColor: '#ec4899',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Lamb',
                    data: lambData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#f97316',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#171717',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#333333',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' kg';
                        }
                    }
                }
            },


            
            scales: {
                x: {
                    grid: {
                        color: 'rgba(51, 51, 51, 0.5)',
                        borderDash: [3, 3]
                    },
                    ticks: {
                        color: '#888888'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(51, 51, 51, 0.5)',
                        borderDash: [3, 3]
                    },
                    ticks: {
                        color: '#888888',
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            }
        }
    });
}

function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown');
    dropdown.classList.toggle('active');
}


// Close the dropdown when clicking anywhere outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown');
    const dropdownButton = document.querySelector('.dropdown-btn');
    
    // Check if the click is outside the dropdown and dropdown button
    if (!dropdown.contains(event.target) && !dropdownButton.contains(event.target)) {
        dropdown.classList.remove('active'); // Close the dropdown
    }
});


function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and content
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab');
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // For now, we only have the "all" tab implemented
            document.getElementById('all-tab').classList.add('active');
        });
    });
}

function addEventListeners() {
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.inventory-table tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Add New Stock button
    const addStockButton = document.querySelector('.btn-primary');
    addStockButton.addEventListener('click', function() {
        // In a real application, this would open a form modal
        alert('Add New Stock form would open here');
    });
    
    // View Reports button
    const viewReportsButton = document.querySelector('.btn-outline');
    viewReportsButton.addEventListener('click', function() {
        // In a real application, this would navigate to reports page
        alert('Navigate to Reports page');
    });


    
}

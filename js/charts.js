let energyChart = null;
let weightChart = null;
let macrosChart = null;
let waterChart = null;
let bmiChart = null;

let currentChartType = 'energy';
let chartData = null;

const chartColors = {
    energy: {
        background: 'rgba(245, 87, 108, 0.2)',
        border: 'rgba(245, 87, 108, 1)',
        hover: 'rgba(245, 87, 108, 0.4)'
    },
    weight: {
        background: 'rgba(79, 172, 254, 0.2)',
        border: 'rgba(79, 172, 254, 1)',
        hover: 'rgba(79, 172, 254, 0.4)'
    },
    protein: {
        background: 'rgba(102, 126, 234, 0.8)',
        border: 'rgba(102, 126, 234, 1)'
    },
    carbs: {
        background: 'rgba(251, 191, 36, 0.8)',
        border: 'rgba(251, 191, 36, 1)'
    },
    fat: {
        background: 'rgba(0, 242, 254, 0.8)',
        border: 'rgba(0, 242, 254, 1)'
    },
    water: {
        background: 'rgba(0, 242, 254, 0.3)',
        border: 'rgba(0, 242, 254, 1)',
        fill: 'rgba(0, 242, 254, 0.1)'
    },
    bmi: {
        background: 'rgba(240, 147, 251, 0.2)',
        border: 'rgba(240, 147, 251, 1)'
    },
    ffmi: {
        background: 'rgba(102, 126, 234, 0.2)',
        border: 'rgba(102, 126, 234, 1)'
    }
};

const commonChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                    family: "'Montserrat', sans-serif",
                    size: 12,
                    weight: '600'
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
                family: "'Montserrat', sans-serif",
                size: 13,
                weight: '700'
            },
            bodyFont: {
                family: "'Montserrat', sans-serif",
                size: 12
            },
            cornerRadius: 8,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += context.parsed.y.toFixed(1);
                        if (context.dataset.label === 'Energy Consumed') {
                            label += ' kcal';
                        } else if (context.dataset.label === 'Weight') {
                            label += ' kg';
                        } else if (context.dataset.label === 'Water Intake') {
                            label += ' ml';
                        } else if (context.dataset.label.includes('Protein') || 
                                   context.dataset.label.includes('Carbs') || 
                                   context.dataset.label.includes('Fat')) {
                            label += ' g';
                        }
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                font: {
                    family: "'Montserrat', sans-serif",
                    size: 11
                },
                maxRotation: 45,
                minRotation: 0
            }
        },
        y: {
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
                font: {
                    family: "'Montserrat', sans-serif",
                    size: 11
                }
            },
            beginAtZero: true
        }
    }
};

async function fetchChartData(range = '30') {
    try {
        const response = await fetch(`api/get_charts_data.php?range=${range}`);
        const data = await response.json();
        
        if (!data.success) {
            console.error('Error fetching chart data:', data.error);
            showNoDataMessage();
            return null;
        }
        
        if (data.energy_data.length === 0) {
            showNoDataMessage();
            return null;
        }
        
        hideNoDataMessage();
        return data;
    } catch (error) {
        console.error('Error fetching chart data:', error);
        showNoDataMessage();
        return null;
    }
}

function showNoDataMessage() {
    document.getElementById('noDataMessage').style.display = 'block';
    document.getElementById('chartDisplay').style.display = 'none';
    destroyAllCharts();
}

function hideNoDataMessage() {
    document.getElementById('noDataMessage').style.display = 'none';
    document.getElementById('chartDisplay').style.display = 'block';
}

function destroyAllCharts() {
    if (energyChart) {
        energyChart.destroy();
        energyChart = null;
    }
    if (weightChart) {
        weightChart.destroy();
        weightChart = null;
    }
    if (macrosChart) {
        macrosChart.destroy();
        macrosChart = null;
    }
    if (waterChart) {
        waterChart.destroy();
        waterChart = null;
    }
    if (bmiChart) {
        bmiChart.destroy();
        bmiChart = null;
    }
}

function switchChart(chartType) {
    document.querySelectorAll('.chart-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const selectedCard = document.querySelector(`[data-chart-type="${chartType}"]`);
    if (selectedCard) {
        selectedCard.classList.add('active');
    }
    
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeBtn = document.querySelector(`[data-chart="${chartType}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    
    currentChartType = chartType;
    
    if (chartData) {
        createChartForType(chartType);
    }
}

function createChartForType(chartType) {
    switch(chartType) {
        case 'energy':
            createEnergyChart(chartData);
            break;
        case 'weight':
            createWeightChart(chartData);
            break;
        case 'macros':
            createMacrosChart(chartData);
            break;
        case 'water':
            createWaterChart(chartData);
            break;
        case 'bmi':
            createBMIChart(chartData);
            break;
    }
}

function createEnergyChart(data) {
    const ctx = document.getElementById('energyChart').getContext('2d');
    
    if (energyChart) {
        energyChart.destroy();
    }
    
    energyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.energy_data.map(item => item.date_formatted),
            datasets: [{
                label: 'Energy Consumed',
                data: data.energy_data.map(item => item.calories),
                backgroundColor: chartColors.energy.background,
                borderColor: chartColors.energy.border,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            ...commonChartOptions,
            plugins: {
                ...commonChartOptions.plugins,
                title: {
                    display: false
                }
            },
            scales: {
                ...commonChartOptions.scales,
                y: {
                    ...commonChartOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Calories (kcal)',
                        font: {
                            family: "'Montserrat', sans-serif",
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
}

function createWeightChart(data) {
    const ctx = document.getElementById('weightChart').getContext('2d');
    
    if (weightChart) {
        weightChart.destroy();
    }
    
    weightChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.weight_data.map(item => item.date_formatted),
            datasets: [{
                label: 'Weight',
                data: data.weight_data.map(item => item.weight),
                backgroundColor: chartColors.weight.background,
                borderColor: chartColors.weight.border,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: chartColors.weight.border,
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                y: {
                    ...commonChartOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Weight (kg)',
                        font: {
                            family: "'Montserrat', sans-serif",
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
}

function createMacrosChart(data) {
    const ctx = document.getElementById('macrosChart').getContext('2d');
    
    if (macrosChart) {
        macrosChart.destroy();
    }
    
    macrosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.macros_data.map(item => item.date_formatted),
            datasets: [
                {
                    label: 'Protein',
                    data: data.macros_data.map(item => item.protein),
                    backgroundColor: chartColors.protein.background,
                    borderColor: chartColors.protein.border,
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Carbs',
                    data: data.macros_data.map(item => item.carbs),
                    backgroundColor: chartColors.carbs.background,
                    borderColor: chartColors.carbs.border,
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Fat',
                    data: data.macros_data.map(item => item.fat),
                    backgroundColor: chartColors.fat.background,
                    borderColor: chartColors.fat.border,
                    borderWidth: 2,
                    borderRadius: 8
                }
            ]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                x: {
                    ...commonChartOptions.scales.x,
                    stacked: true
                },
                y: {
                    ...commonChartOptions.scales.y,
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Amount (g)',
                        font: {
                            family: "'Montserrat', sans-serif",
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
}

function createWaterChart(data) {
    const ctx = document.getElementById('waterChart').getContext('2d');
    
    if (waterChart) {
        waterChart.destroy();
    }
    
    waterChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.water_data.map(item => item.date_formatted),
            datasets: [{
                label: 'Water Intake',
                data: data.water_data.map(item => item.water),
                backgroundColor: chartColors.water.fill,
                borderColor: chartColors.water.border,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: chartColors.water.border,
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                y: {
                    ...commonChartOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Water (ml)',
                        font: {
                            family: "'Montserrat', sans-serif",
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
}

function createBMIChart(data) {
    const ctx = document.getElementById('bmiChart').getContext('2d');
    
    if (bmiChart) {
        bmiChart.destroy();
    }
    
    bmiChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.bmi_data.map(item => item.date_formatted),
            datasets: [
                {
                    label: 'BMI',
                    data: data.bmi_data.map(item => item.bmi),
                    backgroundColor: chartColors.bmi.background,
                    borderColor: chartColors.bmi.border,
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: chartColors.bmi.border,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'FFMI',
                    data: data.bmi_data.map(item => item.ffmi),
                    backgroundColor: chartColors.ffmi.background,
                    borderColor: chartColors.ffmi.border,
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: chartColors.ffmi.border,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            ...commonChartOptions,
            scales: {
                ...commonChartOptions.scales,
                y: {
                    ...commonChartOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Value',
                        font: {
                            family: "'Montserrat', sans-serif",
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
}

async function loadCharts(range = '30') {
    const data = await fetchChartData(range);
    
    if (!data) {
        return;
    }
    
    chartData = data;
    
    createChartForType(currentChartType);
}

document.addEventListener('DOMContentLoaded', function() {
    const timeRangeSelect = document.getElementById('timeRange');
    
    loadCharts(timeRangeSelect.value);
    
    timeRangeSelect.addEventListener('change', function() {
        loadCharts(this.value);
    });
    
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const chartType = this.getAttribute('data-chart');
            switchChart(chartType);
        });
    });
    
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            switch(currentChartType) {
                case 'energy':
                    if (energyChart) energyChart.resize();
                    break;
                case 'weight':
                    if (weightChart) weightChart.resize();
                    break;
                case 'macros':
                    if (macrosChart) macrosChart.resize();
                    break;
                case 'water':
                    if (waterChart) waterChart.resize();
                    break;
                case 'bmi':
                    if (bmiChart) bmiChart.resize();
                    break;
            }
        }, 250);
    });
});


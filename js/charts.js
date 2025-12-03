let energyChart = null;
let weightChart = null;
let macrosChart = null;
let waterChart = null;

let currentChartType = 'energy';
let chartData = null;

const chartColors = {
    energy: {
        gradient: ['#f5576c', '#f093fb'],
        solid: '#f5576c'
    },
    weight: {
        gradient: ['#4facfe', '#00f2fe'],
        solid: '#4facfe'
    },
    protein: '#667eea',
    carbs: '#fbbf24',
    fat: '#00f2fe',
    water: {
        gradient: ['#00f2fe', '#4facfe'],
        solid: '#00f2fe'
    }
};

const commonChartOptions = {
    chart: {
        type: 'line',
        height: 350,
        fontFamily: "'Montserrat', sans-serif",
        toolbar: {
            show: false
        },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800,
            animateGradually: {
                enabled: true,
                delay: 150
            },
            dynamicAnimation: {
                enabled: true,
                speed: 350
            }
        }
    },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
            stops: [0, 90, 100]
        }
    },
            grid: {
        borderColor: '#e7e7e7',
        strokeDashArray: 5,
        xaxis: {
            lines: {
                show: false
            }
        },
        yaxis: {
            lines: {
                show: true
            }
        },
        padding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }
    },
    xaxis: {
        labels: {
            style: {
                colors: '#666',
                fontSize: '11px',
                fontFamily: "'Montserrat', sans-serif",
                fontWeight: 500
            }
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#666',
                fontSize: '11px',
                fontFamily: "'Montserrat', sans-serif",
                fontWeight: 500
            }
        }
    },
    tooltip: {
        theme: 'dark',
        style: {
            fontSize: '12px',
            fontFamily: "'Montserrat', sans-serif"
        },
        y: {
            formatter: function(val) {
                return val.toFixed(1);
            }
        }
    },
    legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'right',
        fontSize: '12px',
        fontFamily: "'Montserrat', sans-serif",
        fontWeight: 600,
        markers: {
            width: 12,
            height: 12,
            radius: 6
        },
        itemMargin: {
            horizontal: 15,
            vertical: 5
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
        
        // Sprawdź czy są jakieś dane w ogóle (energia, waga, makroskładniki, woda)
        const hasAnyData = (data.energy_data && data.energy_data.length > 0) ||
                          (data.weight_data && data.weight_data.length > 0) ||
                          (data.macros_data && data.macros_data.length > 0) ||
                          (data.water_data && data.water_data.length > 0);
        
        if (!hasAnyData) {
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
    const noDataMsg = document.getElementById('noDataMessage');
    const chartDisplay = document.getElementById('chartDisplay');
    if (noDataMsg) noDataMsg.style.display = 'block';
    if (chartDisplay) chartDisplay.style.display = 'none';
    destroyAllCharts();
}

function hideNoDataMessage() {
    const noDataMsg = document.getElementById('noDataMessage');
    const chartDisplay = document.getElementById('chartDisplay');
    if (noDataMsg) noDataMsg.style.display = 'none';
    if (chartDisplay) chartDisplay.style.display = 'block';
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
    }
}

function createEnergyChart(data) {
    const chartElement = document.getElementById('energyChart');
    if (!chartElement) return;
    
    if (energyChart) {
        energyChart.destroy();
    }
    
    if (!data.energy_data || data.energy_data.length === 0) {
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">No energy data available</div>';
        return;
    }
    
    const options = {
        ...commonChartOptions,
        chart: {
            ...commonChartOptions.chart,
            type: 'area'
        },
        series: [{
            name: 'Energy Consumed',
            data: data.energy_data.map(item => item.calories)
        }],
        colors: [chartColors.energy.solid],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: [chartColors.energy.gradient[1]],
                inverseColors: false,
                opacityFrom: 0.8,
                opacityTo: 0.2,
                stops: [0, 50, 100]
            }
        },
        stroke: {
            ...commonChartOptions.stroke,
            width: 3
        },
        markers: {
            size: 5,
            colors: [chartColors.energy.solid],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            ...commonChartOptions.xaxis,
            categories: data.energy_data.map(item => item.date_formatted)
        },
        yaxis: {
            ...commonChartOptions.yaxis,
                    title: {
                        text: 'Calories (kcal)',
                style: {
                    color: '#666',
                    fontSize: '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                }
            }
        },
        tooltip: {
            ...commonChartOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(0) + ' kcal';
                }
            }
        }
    };
    
    energyChart = new ApexCharts(chartElement, options);
    energyChart.render();
}

function createWeightChart(data) {
    const chartElement = document.getElementById('weightChart');
    if (!chartElement) return;
    
    if (weightChart) {
        weightChart.destroy();
    }
    
    if (!data.weight_data || data.weight_data.length === 0) {
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">No weight data available</div>';
        return;
    }
    
    const weightValues = data.weight_data.map(item => item.weight);
    const minWeight = Math.min(...weightValues);
    const maxWeight = Math.max(...weightValues);
    const range = Math.max(1, maxWeight - minWeight);
    
    // Lepsze obliczanie zakresu - jeśli zakres jest mały, użyj mniejszego padding
    let padding;
    if (range < 5) {
        padding = Math.max(0.5, range * 0.15);
    } else {
        padding = range * 0.1;
    }
    
    const minAxis = Math.max(0, Math.floor((minWeight - padding) * 10) / 10);
    const maxAxis = Math.ceil((maxWeight + padding) * 10) / 10;
    
    const options = {
        ...commonChartOptions,
        chart: {
            ...commonChartOptions.chart,
            type: 'line'
        },
        series: [{
            name: 'Weight',
            data: data.weight_data.map(item => item.weight)
        }],
        colors: [chartColors.weight.solid],
        fill: {
            enabled: false
        },
        stroke: {
            ...commonChartOptions.stroke,
            width: 3,
            curve: 'smooth'
        },
        markers: {
            size: 5,
            colors: [chartColors.weight.solid],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            ...commonChartOptions.xaxis,
            categories: data.weight_data.map(item => item.date_formatted)
        },
        yaxis: {
            ...commonChartOptions.yaxis,
                    min: minAxis,
                    max: maxAxis,
            forceNiceScale: true,
            decimalsInFloat: 1,
                    title: {
                        text: 'Weight (kg)',
                style: {
                    color: '#666',
                    fontSize: '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                }
            },
            labels: {
                ...commonChartOptions.yaxis.labels,
                formatter: function(val) {
                    return val.toFixed(1);
                }
            }
                },
                tooltip: {
            ...commonChartOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + ' kg';
                }
            }
        },
        legend: {
            ...commonChartOptions.legend,
            show: false
        },
        grid: {
            ...commonChartOptions.grid,
            yaxis: {
                lines: {
                    show: true
                }
            },
            padding: {
                top: 10,
                right: 10,
                bottom: 0,
                left: 10
            }
        }
    };
    
    weightChart = new ApexCharts(chartElement, options);
    weightChart.render();
}

function createMacrosChart(data) {
    const chartElement = document.getElementById('macrosChart');
    if (!chartElement) return;
    
    if (macrosChart) {
        macrosChart.destroy();
    }
    
    if (!data.macros_data || data.macros_data.length === 0) {
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">No macros data available</div>';
        return;
    }
    
    const options = {
        ...commonChartOptions,
        chart: {
            ...commonChartOptions.chart,
        type: 'bar',
            stacked: true
        },
        series: [
            {
                name: 'Protein',
                data: data.macros_data.map(item => item.protein)
            },
            {
                name: 'Carbs',
                data: data.macros_data.map(item => item.carbs)
            },
            {
                name: 'Fat',
                data: data.macros_data.map(item => item.fat)
            }
        ],
        colors: [chartColors.protein, chartColors.carbs, chartColors.fat],
        fill: {
            type: 'solid',
            opacity: 0.9
        },
        stroke: {
            ...commonChartOptions.stroke,
            width: 2,
            colors: ['#fff']
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '60%',
                dataLabels: {
                    position: 'center'
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            ...commonChartOptions.xaxis,
            categories: data.macros_data.map(item => item.date_formatted)
        },
        yaxis: {
            ...commonChartOptions.yaxis,
                    title: {
                        text: 'Amount (g)',
                style: {
                    color: '#666',
                    fontSize: '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                }
            }
        },
        tooltip: {
            ...commonChartOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + ' g';
                }
            }
        }
    };
    
    macrosChart = new ApexCharts(chartElement, options);
    macrosChart.render();
}

function createWaterChart(data) {
    const chartElement = document.getElementById('waterChart');
    if (!chartElement) return;
    
    if (waterChart) {
        waterChart.destroy();
    }
    
    if (!data.water_data || data.water_data.length === 0) {
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">No water data available</div>';
        return;
    }
    
    const options = {
        ...commonChartOptions,
        chart: {
            ...commonChartOptions.chart,
            type: 'area'
        },
        series: [{
            name: 'Water Intake',
            data: data.water_data.map(item => item.water)
        }],
        colors: [chartColors.water.solid],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: [chartColors.water.gradient[1]],
                inverseColors: false,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 50, 100]
            }
        },
        stroke: {
            ...commonChartOptions.stroke,
            width: 3
        },
        markers: {
            size: 5,
            colors: [chartColors.water.solid],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            ...commonChartOptions.xaxis,
            categories: data.water_data.map(item => item.date_formatted)
        },
        yaxis: {
            ...commonChartOptions.yaxis,
                    title: {
                        text: 'Water (ml)',
                style: {
                    color: '#666',
                    fontSize: '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                }
            }
        },
        tooltip: {
            ...commonChartOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(0) + ' ml';
                }
            }
        }
    };
    
    waterChart = new ApexCharts(chartElement, options);
    waterChart.render();
}

async function loadCharts(range = '30') {
    const data = await fetchChartData(range);
    
    if (!data) {
        console.log('No chart data available');
        return;
    }
    
    chartData = data;
    console.log('Chart data loaded:', data);
    
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
            if (energyChart) energyChart.updateOptions({}, false, true, true);
            if (weightChart) weightChart.updateOptions({}, false, true, true);
            if (macrosChart) macrosChart.updateOptions({}, false, true, true);
            if (waterChart) waterChart.updateOptions({}, false, true, true);
        }, 250);
    });
});

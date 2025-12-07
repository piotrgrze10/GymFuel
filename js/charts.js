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

function getScreenSize() {
    const width = window.innerWidth;
    if (width < 480) return 'xs';
    if (width < 768) return 'sm';
    if (width < 1024) return 'md';
    return 'lg';
}

function truncateLabel(label, maxLength) {
    if (!label) return '';
    if (label.length <= maxLength) return label;
    return label.substring(0, maxLength - 3) + '...';
}

function formatDateLabel(dateStr, screenSize) {
    if (screenSize === 'xs' || screenSize === 'sm') {
            try {
                const dateFormattedPattern = /^[A-Za-z]{3}\s+\d{1,2}$/;
                if (dateFormattedPattern.test(dateStr)) {
                    const parts = dateStr.split(' ');
                    const day = parts[1];
                    const month = parts[0];
                    
                    if (screenSize === 'xs') {
                        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        const monthNum = monthNames.indexOf(month) + 1;
                        return `${day}/${monthNum}`;
                    } else {
                        return `${day} ${month}`;
                    }
                }
                
                let date;
                if (dateStr.includes('-') && dateStr.length >= 10) {
                    date = new Date(dateStr);
                } else {
                    date = new Date(dateStr);
                }
                
                if (!isNaN(date.getTime())) {
                    const day = date.getDate();
                    const month = date.getMonth() + 1;
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    
                    if (screenSize === 'xs') {
                        return `${day}/${month}`;
                    } else {
                        return `${day} ${monthNames[month - 1]}`;
                    }
                }
                
                return dateStr;
        } catch (e) {
            return dateStr;
        }
    }
    return dateStr;
}

function formatDateForChart(dateStr) {
    try {
        let date;
        if (dateStr.includes('-') && dateStr.length >= 10) {
            date = new Date(dateStr);
        } else {
            date = new Date(dateStr);
        }
        
        if (!isNaN(date.getTime())) {
            const day = date.getDate();
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = monthNames[date.getMonth()];
            return `${month} ${day}`;
        }
        return dateStr;
    } catch (e) {
        return dateStr;
    }
}

function formatDateRange(startDate, endDate) {
    try {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
            return '';
        }
        
        const startMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][start.getMonth()];
        const endMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][end.getMonth()];
        const startDay = start.getDate();
        const endDay = end.getDate();
        const year = end.getFullYear();
        
        return `${startMonth} ${startDay} – ${endMonth} ${endDay}, ${year}`;
    } catch (e) {
        return '';
    }
}

function getResponsiveChartOptions(screenSize, dataLength = 0) {
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    const isSmallMobile = screenSize === 'xs';
    
    const needsHorizontalScroll = dataLength > (isSmallMobile ? 7 : isMobile ? 10 : 15);
    
    const baseOptions = {
        chart: {
            height: isSmallMobile ? 280 : isMobile ? 300 : 350,
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
            },
            ...(needsHorizontalScroll && {
                scrollbar: {
                    enabled: true,
                    offsetX: 0,
                    offsetY: 0,
                    height: isMobile ? 8 : 10,
                    width: '100%'
                }
            })
        },
        xaxis: {
            labels: {
                style: {
                    colors: '#666',
                    fontSize: isSmallMobile ? '9px' : isMobile ? '10px' : '11px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 500
                },
                rotate: isMobile ? (needsHorizontalScroll ? 0 : -45) : 0,
                rotateAlways: isMobile && needsHorizontalScroll,
                maxHeight: isMobile ? 60 : undefined,
                trim: true,
                hideOverlappingLabels: true,
                showDuplicates: false
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            ...(needsHorizontalScroll && {
                tickAmount: undefined,
                min: undefined,
                max: undefined
            })
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#666',
                    fontSize: isSmallMobile ? '9px' : isMobile ? '10px' : '11px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 500
                },
                maxWidth: isMobile ? 40 : 50,
                formatter: function(val) {
                    if (isMobile && val >= 1000) {
                        return (val / 1000).toFixed(1) + 'k';
                    }
                    return val.toFixed(1);
                }
            },
            forceNiceScale: true
        },
        tooltip: {
            theme: 'light',
            style: {
                fontSize: isSmallMobile ? '11px' : isMobile ? '12px' : '13px',
                fontFamily: "'Montserrat', sans-serif"
            },
            backgroundColor: '#ffffff',
            borderColor: '#e5e5e5',
            textColor: '#333333',
            titleColor: '#333333',
            fixed: {
                enabled: false
            },
            ...(isMobile && {
                followCursor: true,
                offsetY: 20
            })
        },
        legend: {
            show: true,
            position: isMobile ? 'bottom' : 'top',
            horizontalAlign: isMobile ? 'center' : 'right',
            fontSize: isSmallMobile ? '10px' : isMobile ? '11px' : '12px',
            fontFamily: "'Montserrat', sans-serif",
            fontWeight: 600,
            markers: {
                width: isMobile ? 10 : 12,
                height: isMobile ? 10 : 12,
                radius: isMobile ? 5 : 6
            },
            itemMargin: {
                horizontal: isMobile ? 10 : 15,
                vertical: isMobile ? 3 : 5
            },
            offsetY: isMobile ? 10 : 0,
            floating: false
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
                top: isMobile ? 10 : 0,
                right: isMobile ? 10 : 0,
                bottom: isMobile ? (needsHorizontalScroll ? 40 : 20) : 0,
                left: isMobile ? 5 : 0
            }
        },
        dataLabels: {
            enabled: false
        }
    };
    
    return baseOptions;
}

function getBaseChartOptions() {
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize);
    
    return {
        ...responsiveOptions,
        stroke: {
            curve: 'smooth',
            width: screenSize === 'xs' ? 2.5 : 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        }
    };
}

async function fetchChartData(range = '30') {
    try {
        const response = await fetch(`api/get_charts_data.php?range=${range}`);
        const data = await response.json();
        
        if (!data.success) {
            console.error('Error fetching chart data:', data.error);
            showNoDataMessage();
        return null;
    }
    
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
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No energy data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.energy_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
    const categories = data.energy_data.map(item => {
        const dateToFormat = item.date_formatted || item.date;
        return formatDateLabel(dateToFormat, screenSize);
    });
    
    const options = {
        ...getBaseChartOptions(),
        chart: {
            ...responsiveOptions.chart,
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
            curve: 'smooth',
            width: isMobile ? 2.5 : 3
        },
        markers: {
            size: isMobile ? 4 : 5,
            colors: [chartColors.energy.solid],
            strokeColors: '#fff',
            strokeWidth: isMobile ? 1.5 : 2,
            hover: {
                size: isMobile ? 6 : 7
            }
        },
        xaxis: {
            ...responsiveOptions.xaxis,
            categories: categories
        },
        yaxis: {
            ...responsiveOptions.yaxis,
            title: {
                text: 'Calories (kcal)',
                style: {
                    color: '#666',
                    fontSize: isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                },
                offsetX: isMobile ? -5 : 0
            }
        },
        tooltip: {
            ...responsiveOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(0) + ' kcal';
                }
            }
        },
        legend: {
            ...responsiveOptions.legend,
            show: false
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
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No weight data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    const isSmallMobile = screenSize === 'xs';
    
    const weightValues = data.weight_data.map(item => item.weight);
    const minWeight = Math.min(...weightValues);
    const maxWeight = Math.max(...weightValues);
    const range = Math.max(1, maxWeight - minWeight);
    
    let padding;
    if (range < 5) {
        padding = Math.max(0.5, range * 0.15);
    } else {
        padding = range * 0.1;
    }
    
    const minAxis = Math.max(0, Math.floor((minWeight - padding) * 10) / 10);
    const maxAxis = Math.ceil((maxWeight + padding) * 10) / 10;
    
    // Format dates in style "May 1", "July 15", "Sep 24"
    const categories = data.weight_data.map(item => {
        const dateStr = item.date || item.date_formatted;
        return formatDateForChart(dateStr);
    });
    
    // Get date range for subtitle
    const startDate = data.start_date || (data.weight_data.length > 0 ? data.weight_data[0].date : '');
    const endDate = data.end_date || (data.weight_data.length > 0 ? data.weight_data[data.weight_data.length - 1].date : '');
    const dateRangeText = formatDateRange(startDate, endDate);
    
    const options = {
        chart: {
            type: 'line',
            height: isSmallMobile ? 280 : isMobile ? 300 : 350,
            fontFamily: "'Montserrat', sans-serif",
            toolbar: {
                show: false
            },
            background: '#ffffff',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        series: [{
            name: 'Weight',
            data: data.weight_data.map(item => item.weight)
        }],
        colors: ['#4facfe'], // Blue color
        fill: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: isMobile ? 2.5 : 3,
            colors: ['#4facfe']
        },
        markers: {
            size: isMobile ? 5 : 6,
            colors: ['#4facfe'],
            strokeColors: '#ffffff',
            strokeWidth: isMobile ? 2 : 2.5,
            hover: {
                size: isMobile ? 7 : 8
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    colors: '#333',
                    fontSize: isSmallMobile ? '10px' : isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 500
                },
                rotate: 0
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            min: minAxis,
            max: maxAxis,
            forceNiceScale: true,
            decimalsInFloat: 1,
            title: {
                text: 'Weight (kg)',
                style: {
                    color: '#333',
                    fontSize: isSmallMobile ? '11px' : isMobile ? '12px' : '13px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                },
                offsetX: isMobile ? -5 : 0
            },
            labels: {
                style: {
                    colors: '#333',
                    fontSize: isSmallMobile ? '10px' : isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 500
                },
                formatter: function(val) {
                    return val.toFixed(1);
                }
            }
        },
        grid: {
            borderColor: '#e5e5e5',
            strokeDashArray: 3,
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
                top: 10,
                right: 10,
                bottom: isMobile ? 30 : 40,
                left: isMobile ? 5 : 10
            }
        },
        tooltip: {
            theme: 'light',
            style: {
                fontSize: isSmallMobile ? '11px' : isMobile ? '12px' : '13px',
                fontFamily: "'Montserrat', sans-serif"
            },
            backgroundColor: '#ffffff',
            borderColor: '#e5e5e5',
            textColor: '#333333',
            titleColor: '#333333',
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + ' kg';
                }
            },
            x: {
                formatter: function(val, opts) {
                    const dataPointIndex = opts.dataPointIndex;
                    if (data.weight_data && data.weight_data[dataPointIndex]) {
                        const dateStr = data.weight_data[dataPointIndex].date || data.weight_data[dataPointIndex].date_formatted;
                        return formatDateForChart(dateStr);
                    }
                    return val;
                }
            }
        },
        legend: {
            show: false
        }
    };
    
    weightChart = new ApexCharts(chartElement, options);
    weightChart.render().then(() => {
        // Add date range text below the chart
        const chartContainer = chartElement.closest('.chart-container');
        if (chartContainer) {
            // Remove existing date range if present
            const existingRange = chartContainer.querySelector('.chart-date-range');
            if (existingRange) {
                existingRange.remove();
            }
            
            // Add date range element
            const dateRangeEl = document.createElement('div');
            dateRangeEl.className = 'chart-date-range';
            dateRangeEl.textContent = dateRangeText;
            chartContainer.appendChild(dateRangeEl);
        }
    });
}

function createMacrosChart(data) {
    const chartElement = document.getElementById('macrosChart');
    if (!chartElement) return;
    
    if (macrosChart) {
        macrosChart.destroy();
    }
    
    if (!data.macros_data || data.macros_data.length === 0) {
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No macros data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.macros_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
    const categories = data.macros_data.map(item => {
        const dateToFormat = item.date_formatted || item.date;
        return formatDateLabel(dateToFormat, screenSize);
    });
    
    const options = {
        ...getBaseChartOptions(),
        chart: {
            ...responsiveOptions.chart,
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
            width: isMobile ? 1.5 : 2,
            colors: ['#fff']
        },
        plotOptions: {
            bar: {
                borderRadius: isMobile ? 6 : 8,
                columnWidth: isMobile ? '70%' : '60%',
                dataLabels: {
                    position: 'center'
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            ...responsiveOptions.xaxis,
            categories: categories
        },
        yaxis: {
            ...responsiveOptions.yaxis,
            title: {
                text: 'Amount (g)',
                style: {
                    color: '#666',
                    fontSize: isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                },
                offsetX: isMobile ? -5 : 0
            }
        },
        tooltip: {
            ...responsiveOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + ' g';
                }
            }
        },
        legend: {
            ...responsiveOptions.legend
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
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No water data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.water_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
    const categories = data.water_data.map(item => {
        const dateToFormat = item.date_formatted || item.date;
        return formatDateLabel(dateToFormat, screenSize);
    });
    
    const options = {
        ...getBaseChartOptions(),
        chart: {
            ...responsiveOptions.chart,
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
            curve: 'smooth',
            width: isMobile ? 2.5 : 3
        },
        markers: {
            size: isMobile ? 4 : 5,
            colors: [chartColors.water.solid],
            strokeColors: '#fff',
            strokeWidth: isMobile ? 1.5 : 2,
            hover: {
                size: isMobile ? 6 : 7
            }
        },
        xaxis: {
            ...responsiveOptions.xaxis,
            categories: categories
        },
        yaxis: {
            ...responsiveOptions.yaxis,
            title: {
                text: 'Water (ml)',
                style: {
                    color: '#666',
                    fontSize: isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                },
                offsetX: isMobile ? -5 : 0
            }
        },
        tooltip: {
            ...responsiveOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(0) + ' ml';
                }
            }
        },
        legend: {
            ...responsiveOptions.legend,
            show: false
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
            if (chartData) {
                createChartForType(currentChartType);
            }
        }, 300);
    });
});

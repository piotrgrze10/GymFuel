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

// Funkcja do wykrywania rozmiaru ekranu
function getScreenSize() {
    const width = window.innerWidth;
    if (width < 480) return 'xs';
    if (width < 768) return 'sm';
    if (width < 1024) return 'md';
    return 'lg';
}

// Funkcja do skracania etykiet
function truncateLabel(label, maxLength) {
    if (!label) return '';
    if (label.length <= maxLength) return label;
    return label.substring(0, maxLength - 3) + '...';
}

// Funkcja do formatowania etykiet daty dla mobile
function formatDateLabel(dateStr, screenSize) {
    if (screenSize === 'xs' || screenSize === 'sm') {
        try {
            // Sprawdź czy to już sformatowana data (np. "Jan 15" lub "Mar 3")
            const dateFormattedPattern = /^[A-Za-z]{3}\s+\d{1,2}$/;
            if (dateFormattedPattern.test(dateStr)) {
                // To już sformatowana data - zawsze pokazuj dzień i miesiąc
                const parts = dateStr.split(' ');
                const day = parts[1];
                const month = parts[0]; // np. "Jan", "Feb", etc.
                
                if (screenSize === 'xs') {
                    // Dla bardzo małych ekranów - dzień/miesiąc w formacie numerycznym lub skróconym
                    // Konwertuj nazwę miesiąca na numer
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const monthNum = monthNames.indexOf(month) + 1;
                    return `${day}/${monthNum}`;
                } else {
                    // Dla małych ekranów - dzień i skrócony miesiąc (pierwsze 3 litery)
                    return `${day} ${month}`;
                }
            }
            
            // Spróbuj sparsować jako datę (format YYYY-MM-DD)
            let date;
            if (dateStr.includes('-') && dateStr.length >= 10) {
                date = new Date(dateStr);
            } else {
                date = new Date(dateStr);
            }
            
            // Sprawdź czy data jest poprawna
            if (!isNaN(date.getTime())) {
                const day = date.getDate();
                const month = date.getMonth() + 1;
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                if (screenSize === 'xs') {
                    // Dla bardzo małych ekranów - dzień/miesiąc w formacie numerycznym
                    return `${day}/${month}`;
                } else {
                    // Dla małych ekranów - dzień i skrócony miesiąc
                    return `${day} ${monthNames[month - 1]}`;
                }
            }
            
            // Jeśli nie można sparsować, zwróć oryginalny string (lub jego część)
            return dateStr;
        } catch (e) {
            // W razie błędu zwróć oryginalny string
            return dateStr;
        }
    }
    return dateStr;
}

// Funkcja zwracająca responsywne opcje wykresu
function getResponsiveChartOptions(screenSize, dataLength = 0) {
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    const isSmallMobile = screenSize === 'xs';
    
    // Oblicz czy potrzebny jest horizontal scroll (dla gęstych danych)
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
            // Dla gęstych danych - włącz horizontal scroll
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
            // Dla gęstych danych - ustaw minimalną szerokość kolumny
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
                    // Skróć duże liczby dla mobile
                    if (isMobile && val >= 1000) {
                        return (val / 1000).toFixed(1) + 'k';
                    }
                    return val.toFixed(1);
                }
            },
            forceNiceScale: true
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: isSmallMobile ? '11px' : isMobile ? '12px' : '13px',
                fontFamily: "'Montserrat', sans-serif"
            },
            fixed: {
                enabled: false
            },
            // Na mobile - tooltip poniżej, nie zasłania danych
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
            // Na mobile - legendę na dole, nie zasłania wykresu
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

// Funkcja zwracająca podstawowe opcje wykresu (używana jako baza)
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
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No energy data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.energy_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
    // Formatuj etykiety dat dla mobile - użyj date_formatted jeśli dostępne, w przeciwnym razie date
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
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.weight_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
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
    
    // Formatuj etykiety dat dla mobile - użyj date_formatted jeśli dostępne, w przeciwnym razie date
    const categories = data.weight_data.map(item => {
        const dateToFormat = item.date_formatted || item.date;
        return formatDateLabel(dateToFormat, screenSize);
    });
    
    const options = {
        ...getBaseChartOptions(),
        chart: {
            ...responsiveOptions.chart,
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
            curve: 'smooth',
            width: isMobile ? 2.5 : 3
        },
        markers: {
            size: isMobile ? 4 : 5,
            colors: [chartColors.weight.solid],
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
            min: minAxis,
            max: maxAxis,
            forceNiceScale: true,
            decimalsInFloat: 1,
            title: {
                text: 'Weight (kg)',
                style: {
                    color: '#666',
                    fontSize: isMobile ? '11px' : '12px',
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 600
                },
                offsetX: isMobile ? -5 : 0
            },
            labels: {
                ...responsiveOptions.yaxis.labels,
                formatter: function(val) {
                    return val.toFixed(1);
                }
            }
        },
        tooltip: {
            ...responsiveOptions.tooltip,
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + ' kg';
                }
            }
        },
        legend: {
            ...responsiveOptions.legend,
            show: false
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
        chartElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 14px; padding: 20px;">No macros data available</div>';
        return;
    }
    
    const screenSize = getScreenSize();
    const responsiveOptions = getResponsiveChartOptions(screenSize, data.macros_data.length);
    const isMobile = screenSize === 'xs' || screenSize === 'sm';
    
    // Formatuj etykiety dat dla mobile - użyj date_formatted jeśli dostępne, w przeciwnym razie date
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
    
    // Formatuj etykiety dat dla mobile - użyj date_formatted jeśli dostępne, w przeciwnym razie date
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
            // Przy zmianie rozmiaru - przebuduj wykresy z nowymi opcjami responsywnymi
            if (chartData) {
                createChartForType(currentChartType);
            }
        }, 300);
    });
});

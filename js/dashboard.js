let selectedFood = null;
let searchTimeout = null;
let currentSelectedUnit = null;

document.getElementById('foodSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (searchTerm.length < 2) {
        document.getElementById('foodResults').innerHTML = '';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchFoods(searchTerm);
    }, 300);
});

async function searchFoods(searchTerm) {
    const resultsDiv = document.getElementById('foodResults');
    resultsDiv.innerHTML = '<p class="text-center">Searching...</p>';
    
    try {
        const formData = new FormData();
        formData.append('action', 'search_food');
        formData.append('search_term', searchTerm);
        
        const response = await fetch('api/dashboard_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayFoodResults(result.results);
        } else {
            resultsDiv.innerHTML = `<p class="text-danger">${result.error}</p>`;
        }
    } catch (error) {
        resultsDiv.innerHTML = '<p class="text-danger">Error searching for foods</p>';
    }
}

function displayFoodResults(foods) {
    const resultsDiv = document.getElementById('foodResults');
    
    if (foods.length === 0) {
        resultsDiv.innerHTML = '<p class="text-muted text-center" style="padding: 1rem;">No foods found</p>';
        return;
    }
    
    resultsDiv.innerHTML = foods.map(food => {
        const unitsJson = JSON.stringify(food.available_units || []);
        const defaultUnit = food.available_units && food.available_units[0] ? food.available_units[0].unit_display : 'g';
        
        return `
        <div class="food-item" data-food-id="${food.id}" 
             data-food-calories="${food.calories}" 
             data-food-protein="${food.protein}" 
             data-food-carbs="${food.carbs}" 
             data-food-fat="${food.fat}" 
             data-food-fiber="${food.fiber}" 
             data-food-sugar="${food.sugar}"
             data-food-units='${unitsJson.replace(/'/g, "&#39;")}'>
            <h6>${food.name}</h6>
            <small>
                ${food.calories} kcal per 100g
                • P: ${food.protein}g • C: ${food.carbs}g • F: ${food.fat}g
            </small>
        </div>
        `;
    }).join('');
    
    document.querySelectorAll('.food-item').forEach(item => {
        item.addEventListener('click', function() {
            selectFood(this);
        });
    });
}

function formatNumber(num) {
    return num.toString().replace(/\.0+$/, '');
}

function getUnitIcon(unitName) {
    const iconMap = {
        'gram': '⚖️',
        'ml': '💧',
        'egg': '🥚',
        'slice': '🍞',
        'tbsp': '🥄',
        'cup': '☕',
        'serving': '🍽️',
        'piece': '🔸',
        'avocado': '🥑',
        'orange': '🍊',
        'banana': '🍌',
        'apple': '🍎'
    };
    return iconMap[unitName] || '📏';
}

function createUnitSelector() {
    if (!selectedFood || !selectedFood.availableUnits || selectedFood.availableUnits.length <= 1) {
        document.getElementById('unitSelectorContainer').style.display = 'none';
        return;
    }
    
    const container = document.getElementById('unitSelectorContainer');
    container.style.display = 'block';
    
    const defaultUnit = selectedFood.availableUnits.find(u => u.is_default) || selectedFood.availableUnits[0];
    currentSelectedUnit = defaultUnit;
    
    const customDropdownHTML = `
        <div class="custom-dropdown-wrapper">
            <div class="custom-dropdown-selected" id="customDropdownSelected">
                <div class="custom-dropdown-text">
                    <div class="custom-dropdown-icon">${getUnitIcon(defaultUnit.unit_name)}</div>
                    <div>
                        <div id="selectedUnitText">${defaultUnit.unit_display}</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-down custom-dropdown-arrow"></i>
            </div>
            <div class="custom-dropdown-options" id="customDropdownOptions">
                ${selectedFood.availableUnits.map(unit => `
                    <div class="custom-dropdown-option ${unit.unit_name === defaultUnit.unit_name ? 'selected' : ''}" 
                         data-unit-name="${unit.unit_name}"
                         data-weight="${unit.weight_in_grams}"
                         data-display="${unit.unit_display}"
                         data-plural="${unit.unit_plural}">
                        <div class="custom-dropdown-option-icon">${getUnitIcon(unit.unit_name)}</div>
                        <div class="custom-dropdown-option-text">
                            ${unit.unit_display}
                            <span>${unit.weight_in_grams}g ${unit.unit_name === 'gram' ? '' : 'per unit'}</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
        <select class="form-select modern-select" id="unitSelector" style="display: none;">
            ${selectedFood.availableUnits.map(unit => `
                <option value="${unit.unit_name}" 
                        data-weight="${unit.weight_in_grams}"
                        data-display="${unit.unit_display}"
                        data-plural="${unit.unit_plural}">
                    ${unit.unit_display}
                </option>
            `).join('')}
        </select>
    `;
    
    const existingWrapper = container.querySelector('.custom-dropdown-wrapper');
    if (existingWrapper) {
        existingWrapper.remove();
    }
    const existingSelect = container.querySelector('#unitSelector');
    if (existingSelect) {
        existingSelect.remove();
    }
    container.insertAdjacentHTML('beforeend', customDropdownHTML);
    
    setupCustomDropdown();
    
    document.getElementById('unitSelector').value = defaultUnit.unit_name;
}

function setupCustomDropdown() {
    const selected = document.getElementById('customDropdownSelected');
    const optionsContainer = document.getElementById('customDropdownOptions');
    const options = optionsContainer.querySelectorAll('.custom-dropdown-option');
    let currentFocusedIndex = -1;
    
    selected.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        optionsContainer.classList.toggle('show');
        
        if (optionsContainer.classList.contains('show')) {
            // Focus first selected option or first option
            const selectedOption = optionsContainer.querySelector('.custom-dropdown-option.selected');
            if (selectedOption) {
                currentFocusedIndex = Array.from(options).indexOf(selectedOption);
            }
        }
    });
    
    // Keyboard navigation
    selected.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!optionsContainer.classList.contains('show')) {
                this.click();
            } else {
                navigateOptions(e.key === 'ArrowDown' ? 1 : -1);
            }
        } else if (e.key === 'Escape') {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
    
    function navigateOptions(direction) {
        currentFocusedIndex += direction;
        if (currentFocusedIndex < 0) currentFocusedIndex = options.length - 1;
        if (currentFocusedIndex >= options.length) currentFocusedIndex = 0;
        
        // Visual feedback
        options.forEach((opt, idx) => {
            if (idx === currentFocusedIndex) {
                opt.style.background = 'linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%)';
                opt.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else if (!opt.classList.contains('selected')) {
                opt.style.background = '';
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-wrapper')) {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
    
    // Close dropdown on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && optionsContainer.classList.contains('show')) {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
    
    // Handle option selection
    options.forEach((option, index) => {
        option.addEventListener('click', function() {
            selectOption(this);
        });
        
        option.addEventListener('mouseenter', function() {
            currentFocusedIndex = index;
        });
    });
    
    function selectOption(option) {
        // Remove previous selection
        options.forEach(opt => {
            opt.classList.remove('selected');
            opt.style.background = '';
        });
        option.classList.add('selected');
        
        // Update current unit
        currentSelectedUnit = {
            unit_name: option.dataset.unitName,
            weight_in_grams: parseFloat(option.dataset.weight),
            unit_display: option.dataset.display,
            unit_plural: option.dataset.plural
        };
        
        // Update selected display
        const iconElement = option.querySelector('.custom-dropdown-option-icon').innerHTML;
        const textElement = option.querySelector('.custom-dropdown-option-text').childNodes[0].textContent.trim();
        
        document.querySelector('.custom-dropdown-icon').innerHTML = iconElement;
        document.getElementById('selectedUnitText').textContent = textElement;
        
        // Sync hidden select
        document.getElementById('unitSelector').value = currentSelectedUnit.unit_name;
        
        // Close dropdown
        selected.classList.remove('active');
        optionsContainer.classList.remove('show');
        
        // Convert value when changing unit
        const currentGrams = getCurrentWeightInGrams();
        const newQuantity = currentGrams / currentSelectedUnit.weight_in_grams;
        document.getElementById('quantity').value = Math.max(0.1, Math.round(newQuantity * 10) / 10);
        
        updateUnitDisplay();
        createQuickAmountButtons();
        updateQuantityDisplay();
    }
    
    // Make selected element focusable
    selected.setAttribute('tabindex', '0');
    
    // Add unit count badge
    const container = document.getElementById('unitSelectorContainer');
    if (options.length > 1) {
        container.setAttribute('data-unit-count', options.length + ' units');
    }
}

// Gets current weight in grams
function getCurrentWeightInGrams() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    if (!currentSelectedUnit) return 100;
    return quantity * currentSelectedUnit.weight_in_grams;
}

// Updates unit display
function updateUnitDisplay() {
    if (!currentSelectedUnit) return;
    
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    const unitText = quantity === 1 ? currentSelectedUnit.unit_display : currentSelectedUnit.unit_plural;
    
    document.getElementById('quantityUnit').textContent = unitText;
    document.getElementById('quantityLabel').textContent = `Amount (${currentSelectedUnit.unit_plural})`;
}


function selectFood(element) {
    // Remove previous selection
    document.querySelectorAll('.food-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    // Parse available units
    let availableUnits = [];
    try {
        availableUnits = JSON.parse(element.dataset.foodUnits || '[]');
    } catch(e) {
        console.error('Error parsing units:', e);
    }
    
    selectedFood = {
        id: element.dataset.foodId,
        name: element.querySelector('h6').textContent,
        calories: parseFloat(element.dataset.foodCalories),
        protein: parseFloat(element.dataset.foodProtein),
        carbs: parseFloat(element.dataset.foodCarbs),
        fat: parseFloat(element.dataset.foodFat),
        fiber: parseFloat(element.dataset.foodFiber),
        sugar: parseFloat(element.dataset.foodSugar),
        availableUnits: availableUnits
    };
    
    // Show sections
    document.getElementById('selectedFood').style.display = 'block';
    document.getElementById('quantityInput').style.display = 'block';
    document.getElementById('selectedFoodName').textContent = selectedFood.name;
    
    // Display macros (per 100g)
    document.getElementById('selectedFoodCalories').textContent = formatNumber(selectedFood.calories);
    document.getElementById('selectedFoodProtein').textContent = formatNumber(selectedFood.protein);
    document.getElementById('selectedFoodCarbs').textContent = formatNumber(selectedFood.carbs);
    document.getElementById('selectedFoodFat').textContent = formatNumber(selectedFood.fat);
    
    // Set default unit
    if (availableUnits.length > 0) {
        const defaultUnit = availableUnits.find(u => u.is_default == 1) || availableUnits[0];
        currentSelectedUnit = defaultUnit;
        
        // Set initial quantity
        if (defaultUnit.unit_name === 'gram') {
            document.getElementById('quantity').value = 100;
        } else {
            document.getElementById('quantity').value = 1;
        }
    } else {
        // Fallback - grams only
        currentSelectedUnit = {
            unit_name: 'gram',
            weight_in_grams: 1,
            unit_display: 'gram',
            unit_plural: 'grams'
        };
        document.getElementById('quantity').value = 100;
    }
    
    // Create unit selector
    createUnitSelector();
    updateUnitDisplay();
    
    // Create quick amount buttons
    createQuickAmountButtons();
    
    // Enable add button
    document.getElementById('addFoodBtn').disabled = false;
    
    // Update display
    updateQuantityDisplay();
}


function createQuickAmountButtons() {
    const buttonsContainer = document.getElementById('quickAmountButtons');
    buttonsContainer.innerHTML = '';
    
    if (!currentSelectedUnit) return;
    
    let amounts = [];
    
    // Generate suggestions based on unit type
    if (currentSelectedUnit.unit_name === 'gram') {
        amounts = [50, 100, 150, 200];
    } else if (currentSelectedUnit.unit_name === 'ml') {
        amounts = [100, 200, 250, 500];
    } else {
        // For pieces, slices, servings, etc.
        amounts = [1, 2, 3, 4];
    }
    
        amounts.forEach(amount => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary';
        
        // Calculate calories
        const weightInGrams = amount * currentSelectedUnit.weight_in_grams;
        const multiplier = weightInGrams / 100; // Database values are per 100g
        const calories = Math.round(selectedFood.calories * multiplier);
        
        // Determine unit text
        let unitText;
        if (amount === 1) {
            unitText = currentSelectedUnit.unit_display;
        } else {
            unitText = currentSelectedUnit.unit_plural;
        }
        
        btn.innerHTML = `<div style="font-weight: 700;">${amount} ${unitText}</div><div style="font-size: 0.75rem; opacity: 0.8;">${calories} kcal</div>`;
            
            btn.addEventListener('click', function() {
                document.getElementById('quantity').value = amount;
                updateQuantityDisplay();
            });
            
            buttonsContainer.appendChild(btn);
        });
}


document.getElementById('increaseQty').addEventListener('click', function() {
    if (!selectedFood || !currentSelectedUnit) return;
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseFloat(quantityInput.value) || 0;
    
    // Determine step based on unit
    const increment = currentSelectedUnit.unit_name === 'gram' || currentSelectedUnit.unit_name === 'ml' ? 10 : 1;
    quantityInput.value = currentValue + increment;
    updateQuantityDisplay();
});

document.getElementById('decreaseQty').addEventListener('click', function() {
    if (!selectedFood || !currentSelectedUnit) return;
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseFloat(quantityInput.value) || 0;
    
    // Determine step and minimum based on unit
    const decrement = currentSelectedUnit.unit_name === 'gram' || currentSelectedUnit.unit_name === 'ml' ? 10 : 1;
    const minValue = currentSelectedUnit.unit_name === 'gram' || currentSelectedUnit.unit_name === 'ml' ? 10 : 0.1;
    
    if (currentValue > minValue) {
        quantityInput.value = Math.max(minValue, currentValue - decrement);
        updateQuantityDisplay();
    }
});

document.getElementById('quantity').addEventListener('input', function() {
    updateQuantityDisplay();
});

function updateQuantityDisplay() {
    if (!selectedFood || !currentSelectedUnit) return;
    
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    
    // Calculate weight in grams
    const weightInGrams = quantity * currentSelectedUnit.weight_in_grams;
    
    // Calculate nutritional values (database values are per 100g)
    const multiplier = weightInGrams / 100;
    
    const calories = selectedFood.calories * multiplier;
    const protein = selectedFood.protein * multiplier;
    const carbs = selectedFood.carbs * multiplier;
    const fat = selectedFood.fat * multiplier;
    
    // Update displayed values
    document.getElementById('selectedFoodCalories').textContent = formatNumber(Math.round(calories * 10) / 10);
    document.getElementById('selectedFoodProtein').textContent = formatNumber(Math.round(protein * 10) / 10);
    document.getElementById('selectedFoodCarbs').textContent = formatNumber(Math.round(carbs * 10) / 10);
    document.getElementById('selectedFoodFat').textContent = formatNumber(Math.round(fat * 10) / 10);
    
    // Determine unit text
    const unitText = quantity === 1 ? currentSelectedUnit.unit_display : currentSelectedUnit.unit_plural;
    
    // Update calculation info
    document.getElementById('calculatedInfo').textContent = `${quantity} ${unitText} = ${Math.round(calories * 10) / 10} kcal`;
}


document.getElementById('addFoodBtn').addEventListener('click', async function() {
    if (!selectedFood || !currentSelectedUnit) return;
    
    const mealType = document.getElementById('mealType').value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    
    try {
        const formData = new FormData();
        formData.append('action', 'add_food');
        formData.append('food_id', selectedFood.id);
        formData.append('meal_type', mealType);
        formData.append('quantity', quantity);
        formData.append('selected_unit', currentSelectedUnit.unit_name);
        
        const response = await fetch('api/dashboard_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Reload page after adding
            window.location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding food');
    }
});

let pendingDeleteEntryId = null;


document.querySelectorAll('.btn-remove-entry').forEach(btn => {
    btn.addEventListener('click', function() {
        const entryId = this.dataset.entryId;
        pendingDeleteEntryId = entryId;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    });
});


document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!pendingDeleteEntryId) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'remove_food');
        formData.append('entry_id', pendingDeleteEntryId);
        
        const response = await fetch('api/dashboard_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            modal.hide();
            
            
            window.location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error removing food');
    }
});


const addFoodModal = document.getElementById('addFoodModal');
addFoodModal.addEventListener('hidden.bs.modal', function() {
    document.getElementById('foodSearch').value = '';
    document.getElementById('foodResults').innerHTML = '';
    document.getElementById('selectedFood').style.display = 'none';
    document.getElementById('quantityInput').style.display = 'none';
    document.getElementById('unitSelectorContainer').style.display = 'none';
    document.getElementById('quantity').value = 1;
    document.getElementById('addFoodBtn').disabled = true;
    selectedFood = null;
    currentSelectedUnit = null;
});

class WaterTracker {
    constructor() {
        this.storageKey = 'waterIntake';
        this.dateKey = 'waterIntakeDate';
        this.defaultGoal = 2000;
        this.currentIntake = 0;
        this.dailyGoal = this.defaultGoal;
        
        this.elements = {
            current: document.getElementById('waterCurrent'),
            goal: document.getElementById('waterGoal'),
            percentage: document.getElementById('waterPercentage'),
            progressFill: document.getElementById('waterProgressFill'),
            addBtn250: document.getElementById('addWater250'),
            addBtn500: document.getElementById('addWater500'),
            resetBtn: document.getElementById('waterResetBtn'),
            card: document.getElementById('waterTrackerCard'),
            tips: document.getElementById('waterTips')
        };
        
        this.init();
    }
    
    init() {
        this.checkAndResetDaily();
        this.loadFromStorage();
        this.updateDisplay();
        this.attachEventListeners();
        this.startMidnightChecker();
    }
    
    checkAndResetDaily() {
        const today = new Date().toDateString();
        const savedDate = localStorage.getItem(this.dateKey);
        
        if (savedDate !== today) {
            localStorage.setItem(this.dateKey, today);
            localStorage.setItem(this.storageKey, '0');
            this.currentIntake = 0;
        }
    }
    
    loadFromStorage() {
        const saved = localStorage.getItem(this.storageKey);
        this.currentIntake = saved ? parseInt(saved, 10) : 0;
        
        if (this.currentIntake > 10000) {
            this.currentIntake = 0;
        }
    }
    
    saveToStorage() {
        localStorage.setItem(this.storageKey, this.currentIntake.toString());
    }
    
    addWater(amount) {
        this.currentIntake += amount;
        
        if (this.currentIntake > 10000) {
            this.currentIntake = 10000;
        }
        
        this.saveToStorage();
        this.updateDisplay();
        this.showToast(amount);
        this.checkGoalReached();
    }
    
    reset() {
        const waterResetModal = new bootstrap.Modal(document.getElementById('waterResetModal'));
        waterResetModal.show();
    }
    
    confirmReset() {
        this.currentIntake = 0;
        this.saveToStorage();
        this.updateDisplay();
        this.elements.card.classList.remove('goal-reached');
        
        const waterResetModal = bootstrap.Modal.getInstance(document.getElementById('waterResetModal'));
        if (waterResetModal) {
            waterResetModal.hide();
        }
    }
    
    updateDisplay() {
        const percentage = Math.min((this.currentIntake / this.dailyGoal) * 100, 100);
        
        this.elements.current.textContent = this.currentIntake;
        this.elements.goal.textContent = this.dailyGoal;
        this.elements.percentage.textContent = `${Math.round(percentage)}%`;
        
        this.elements.progressFill.style.width = `${percentage}%`;
        
        this.updateTip(percentage);
        
        this.elements.progressFill.classList.add('water-ripple');
        setTimeout(() => {
            this.elements.progressFill.classList.remove('water-ripple');
        }, 600);
    }
    
    updateTip(percentage) {
        const tips = [
            { threshold: 0, message: 'Start your hydration journey!', icon: '💧' },
            { threshold: 25, message: 'Great start! Keep it up!', icon: '👍' },
            { threshold: 50, message: 'Halfway there! You\'re doing amazing!', icon: '🌟' },
            { threshold: 75, message: 'Almost at your goal! Keep going!', icon: '🎯' },
            { threshold: 100, message: 'Goal reached! Excellent work!', icon: '🎉' },
            { threshold: 110, message: 'You\'re well hydrated!', icon: '✨' }
        ];
        
        let currentTip = tips[0];
        for (const tip of tips) {
            if (percentage >= tip.threshold) {
                currentTip = tip;
            }
        }
        
        this.elements.tips.innerHTML = `
            <i class="fa-solid fa-lightbulb"></i>
            <span>${currentTip.message}</span>
        `;
    }
    
    checkGoalReached() {
        const percentage = (this.currentIntake / this.dailyGoal) * 100;
        
        if (percentage >= 100 && !this.elements.card.classList.contains('goal-reached')) {
            this.elements.card.classList.add('goal-reached');
            this.showCelebration();
        }
    }
    
    showCelebration() {
        const celebration = document.createElement('div');
        celebration.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            z-index: 10000;
            animation: celebrationPop 1s ease-out;
            pointer-events: none;
        `;
        celebration.innerHTML = '🎉';
        document.body.appendChild(celebration);
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes celebrationPop {
                0% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
                50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
                100% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
        setTimeout(() => {
            celebration.remove();
            style.remove();
        }, 1000);
    }
    
    showToast(amount) {
        const existingToast = document.querySelector('.water-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = 'water-toast';
        toast.innerHTML = `
            <i class="fa-solid fa-droplet"></i>
            <span>Added ${amount} ml of water!</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 2500);
    }
    
    attachEventListeners() {
        this.elements.addBtn250.addEventListener('click', (e) => {
            this.addButtonRipple(e.currentTarget);
            this.addWater(250);
        });
        
        this.elements.addBtn500.addEventListener('click', (e) => {
            this.addButtonRipple(e.currentTarget);
            this.addWater(500);
        });
        
        this.elements.resetBtn.addEventListener('click', () => {
            this.reset();
        });
    }
    
    addButtonRipple(button) {
        button.classList.add('water-ripple');
        setTimeout(() => {
            button.classList.remove('water-ripple');
        }, 600);
    }
    
    startMidnightChecker() {
        setInterval(() => {
            this.checkAndResetDaily();
            this.loadFromStorage();
            this.updateDisplay();
        }, 60000);
    }
}

if (document.getElementById('waterTrackerCard')) {
    const waterTracker = new WaterTracker();
    
    document.getElementById('confirmWaterResetBtn').addEventListener('click', function() {
        waterTracker.confirmReset();
    });
}


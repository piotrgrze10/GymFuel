let selectedFood = null;
let searchTimeout = null;
let currentSelectedUnit = null;

function formatNumber(num, maxDecimals = 1) {
    if (num === null || num === undefined) return '0';
    
    const parsed = Number(num);
    if (!isFinite(parsed)) return '0';
    
    const rounded = Math.round(parsed * Math.pow(10, maxDecimals)) / Math.pow(10, maxDecimals);
    const fixed = rounded.toFixed(maxDecimals);
    
    return fixed.replace(/\.?0+$/, '');
}

function cleanupModalBackdrop() {
    setTimeout(() => {
        document.body.classList.remove('modal-open');
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 100);
}

async function reloadCurrentDate() {
    const currentDateElement = document.getElementById('currentDate');
    const currentDate = currentDateElement.dataset.date;
    
    dayDataCache.delete(currentDate);
    
    await loadDayData(currentDate);
}

function setupMealDropdown() {
    const selected = document.getElementById('mealDropdownSelected');
    const optionsContainer = document.getElementById('mealDropdownOptions');
    const options = optionsContainer.querySelectorAll('.meal-dropdown-option');
    const hiddenSelect = document.getElementById('mealType');
    
    selected.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        optionsContainer.classList.toggle('show');
    });
    
    options.forEach(option => {
        option.addEventListener('click', function() {
            const mealValue = this.dataset.meal;
            const iconHTML = this.querySelector('.meal-dropdown-option-icon').innerHTML;
            const text = this.querySelector('.meal-dropdown-option-text').textContent;
            
            document.querySelector('.meal-dropdown-icon').innerHTML = iconHTML;
            document.getElementById('mealDropdownText').textContent = text;
            
            hiddenSelect.value = mealValue;
            
            options.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        });
    });
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.meal-dropdown-wrapper')) {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
}

if (document.getElementById('mealDropdownSelected')) {
    setupMealDropdown();
}

const foodSearchInput = document.getElementById('foodSearch');
const foodSearchClearBtn = document.getElementById('foodSearchClearBtn');

if (foodSearchInput) {
    foodSearchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (foodSearchClearBtn) {
            if (this.value.length > 0) {
                foodSearchClearBtn.classList.add('show');
            } else {
                foodSearchClearBtn.classList.remove('show');
            }
        }
        
        if (searchTerm.length < 2) {
            document.getElementById('foodResults').innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchFoods(searchTerm);
        }, 300);
    });
}

if (foodSearchClearBtn && foodSearchInput) {
    foodSearchClearBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        foodSearchInput.value = '';
        foodSearchInput.focus();
        foodSearchClearBtn.classList.remove('show');
        document.getElementById('foodResults').innerHTML = '';
        
        document.getElementById('selectedFood').style.display = 'none';
        document.getElementById('quantityInput').style.display = 'none';
        document.getElementById('unitSelectorContainer').style.display = 'none';
        selectedFood = null;
        currentSelectedUnit = null;
        document.getElementById('addFoodBtn').disabled = true;
        
        document.querySelectorAll('.food-item').forEach(item => {
            item.classList.remove('selected');
        });
    });
}

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

function getFoodIcon(foodName) {
    const name = foodName.toLowerCase();
    const iconMap = {
        'chicken': 'fa-drumstick-bite',
        'egg': 'fa-egg',
        'milk': 'fa-cheese',
        'cheese': 'fa-cheese',
        'rice': 'fa-wheat-awn',
        'oats': 'fa-wheat-awn',
        'bread': 'fa-bread-slice',
        'apple': 'fa-apple-whole',
        'banana': 'fa-apple-whole',
        'orange': 'fa-apple-whole',
        'grape': 'fa-apple-whole',
        'spinach': 'fa-carrot',
        'carrot': 'fa-carrot',
        'potato': 'fa-carrot',
        'fish': 'fa-fish',
        'meat': 'fa-drumstick-bite'
    };
    
    for (let key in iconMap) {
        if (name.includes(key)) {
            return iconMap[key];
        }
    }
    return 'fa-bowl-food';
}

function displayFoodResults(foods) {
    const resultsDiv = document.getElementById('foodResults');
    
    if (foods.length === 0) {
        resultsDiv.innerHTML = `
            <div style="text-align: center; padding: 2rem 1rem;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;"></i>
                <p class="text-muted" style="font-weight: 600;">No foods found</p>
                <p style="font-size: 0.875rem; color: #6c757d;">Try different keywords</p>
            </div>
        `;
        return;
    }
    
    resultsDiv.innerHTML = foods.map(food => {
        const unitsJson = JSON.stringify(food.available_units || []);
        const iconClass = getFoodIcon(food.name);
        const category = food.category || 'General';
        
        return `
        <div class="food-item" data-food-id="${food.id}" 
             data-food-name="${food.name}"
             data-food-category="${category}"
             data-food-calories="${food.calories}" 
             data-food-protein="${food.protein}" 
             data-food-carbs="${food.carbs}" 
             data-food-fat="${food.fat}" 
             data-food-fiber="${food.fiber}" 
             data-food-sugar="${food.sugar}"
             data-food-icon="${iconClass}"
             data-food-units='${unitsJson.replace(/'/g, "&#39;")}'>
            <h6>
                <i class="fa-solid ${iconClass}" style="color: #0d6efd; margin-right: 0.5rem;"></i>
                ${food.name}
            </h6>
            <small>
                <strong>${formatNumber(food.calories, 0)} kcal</strong> per 100g
                • P: ${formatNumber(food.protein, 1)}g • C: ${formatNumber(food.carbs, 1)}g • F: ${formatNumber(food.fat, 1)}g
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
    
    const unitSelector = document.getElementById('unitSelector');
    unitSelector.innerHTML = selectedFood.availableUnits.map(unit => {
        const weightInfo = unit.weight_in_grams > 1 ? ` (${unit.weight_in_grams}g)` : '';
        return `
            <option value="${unit.unit_name}" 
                    data-weight="${unit.weight_in_grams}"
                    data-display="${unit.unit_display}"
                    data-plural="${unit.unit_plural}"
                    ${unit.unit_name === defaultUnit.unit_name ? 'selected' : ''}>
                ${unit.unit_display}${weightInfo}
            </option>
        `;
    }).join('');
    
    unitSelector.onchange = function() {
        const selectedOption = this.options[this.selectedIndex];
        currentSelectedUnit = {
            unit_name: selectedOption.value,
            weight_in_grams: parseFloat(selectedOption.dataset.weight),
            unit_display: selectedOption.dataset.display,
            unit_plural: selectedOption.dataset.plural
        };
        
        updateUnitDisplay();
        createQuickAmountButtons();
        updateQuantityDisplay();
    };
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
            const selectedOption = optionsContainer.querySelector('.custom-dropdown-option.selected');
            if (selectedOption) {
                currentFocusedIndex = Array.from(options).indexOf(selectedOption);
            }
        }
    });
    
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
        
        options.forEach((opt, idx) => {
            if (idx === currentFocusedIndex) {
                opt.style.background = 'linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%)';
                opt.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else if (!opt.classList.contains('selected')) {
                opt.style.background = '';
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-wrapper')) {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && optionsContainer.classList.contains('show')) {
            selected.classList.remove('active');
            optionsContainer.classList.remove('show');
        }
    });
    
    options.forEach((option, index) => {
        option.addEventListener('click', function() {
            selectOption(this);
        });
        
        option.addEventListener('mouseenter', function() {
            currentFocusedIndex = index;
        });
    });
    
    function selectOption(option) {
        options.forEach(opt => {
            opt.classList.remove('selected');
            opt.style.background = '';
        });
        option.classList.add('selected');
        
        currentSelectedUnit = {
            unit_name: option.dataset.unitName,
            weight_in_grams: parseFloat(option.dataset.weight),
            unit_display: option.dataset.display,
            unit_plural: option.dataset.plural
        };
        
        const iconElement = option.querySelector('.custom-dropdown-option-icon').innerHTML;
        const textElement = option.querySelector('.custom-dropdown-option-text').childNodes[0].textContent.trim();
        
        document.querySelector('.custom-dropdown-icon').innerHTML = iconElement;
        document.getElementById('selectedUnitText').textContent = textElement;
        
        document.getElementById('unitSelector').value = currentSelectedUnit.unit_name;
        
        selected.classList.remove('active');
        optionsContainer.classList.remove('show');
        
        const currentGrams = getCurrentWeightInGrams();
        const newQuantity = currentGrams / currentSelectedUnit.weight_in_grams;
        document.getElementById('quantity').value = Math.max(0.1, Math.round(newQuantity * 10) / 10);
        
        updateUnitDisplay();
        createQuickAmountButtons();
        updateQuantityDisplay();
    }
    
    selected.setAttribute('tabindex', '0');
    
    const container = document.getElementById('unitSelectorContainer');
    if (options.length > 1) {
        container.setAttribute('data-unit-count', options.length + ' units');
    }
}

function getCurrentWeightInGrams() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    if (!currentSelectedUnit) return 100;
    return quantity * currentSelectedUnit.weight_in_grams;
}

function updateUnitDisplay() {
    if (!currentSelectedUnit) return;
    
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    const unitText = quantity === 1 ? currentSelectedUnit.unit_display : currentSelectedUnit.unit_plural;
    
    document.getElementById('quantityUnit').textContent = unitText;
    document.getElementById('quantityLabel').textContent = `Amount (${currentSelectedUnit.unit_plural})`;
}


function selectFood(element) {
    document.querySelectorAll('.food-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    let availableUnits = [];
    try {
        availableUnits = JSON.parse(element.dataset.foodUnits || '[]');
    } catch(e) {
        console.error('Error parsing units:', e);
    }
    
    const foodName = element.dataset.foodName || element.querySelector('h6').textContent.trim();
    const iconClass = element.dataset.foodIcon || getFoodIcon(foodName);
    const category = element.dataset.foodCategory || 'General';
    
    selectedFood = {
        id: element.dataset.foodId,
        name: foodName,
        icon: iconClass,
        category: category,
        calories: parseFloat(element.dataset.foodCalories),
        protein: parseFloat(element.dataset.foodProtein),
        carbs: parseFloat(element.dataset.foodCarbs),
        fat: parseFloat(element.dataset.foodFat),
        fiber: parseFloat(element.dataset.foodFiber),
        sugar: parseFloat(element.dataset.foodSugar),
        availableUnits: availableUnits
    };
    
    const selectedFoodCard = document.getElementById('selectedFood');
    selectedFoodCard.style.display = 'block';
    document.getElementById('quantityInput').style.display = 'block';
    
    document.getElementById('selectedFoodName').textContent = selectedFood.name;
    document.getElementById('selectedFoodCategory').textContent = selectedFood.category;
    document.getElementById('selectedFoodIcon').innerHTML = `<i class="fa-solid ${iconClass}"></i>`;
    
    document.getElementById('selectedFoodCalories').textContent = formatNumber(selectedFood.calories);
    document.getElementById('selectedFoodProtein').textContent = formatNumber(selectedFood.protein) + 'g';
    document.getElementById('selectedFoodCarbs').textContent = formatNumber(selectedFood.carbs) + 'g';
    document.getElementById('selectedFoodFat').textContent = formatNumber(selectedFood.fat) + 'g';
    
    if (availableUnits.length > 0) {
        const defaultUnit = availableUnits.find(u => u.is_default == 1) || availableUnits[0];
        currentSelectedUnit = defaultUnit;
        
        if (defaultUnit.unit_name === 'gram') {
            document.getElementById('quantity').value = 100;
        } else {
            document.getElementById('quantity').value = 1;
        }
    } else {
        currentSelectedUnit = {
            unit_name: 'gram',
            weight_in_grams: 1,
            unit_display: 'gram',
            unit_plural: 'grams'
        };
        document.getElementById('quantity').value = 100;
    }
    
    createUnitSelector();
    updateUnitDisplay();
    
    createQuickAmountButtons();
    
    document.getElementById('addFoodBtn').disabled = false;
    
    updateQuantityDisplay();
}


function createQuickAmountButtons() {
    const buttonsContainer = document.getElementById('quickAmountButtons');
    buttonsContainer.innerHTML = '';
    
    if (!currentSelectedUnit) return;
    
    let amounts = [];
    
    if (currentSelectedUnit.unit_name === 'gram') {
        amounts = [50, 100, 150, 200];
    } else if (currentSelectedUnit.unit_name === 'ml') {
        amounts = [100, 200, 250, 500];
    } else {
        amounts = [1, 2, 3, 4];
    }
    
    amounts.forEach(amount => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary';
        
        const weightInGrams = amount * currentSelectedUnit.weight_in_grams;
        const multiplier = weightInGrams / 100;
        const calories = Math.round(selectedFood.calories * multiplier);
        
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
    
    const increment = currentSelectedUnit.unit_name === 'gram' || currentSelectedUnit.unit_name === 'ml' ? 10 : 1;
    quantityInput.value = currentValue + increment;
    updateQuantityDisplay();
});

document.getElementById('decreaseQty').addEventListener('click', function() {
    if (!selectedFood || !currentSelectedUnit) return;
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseFloat(quantityInput.value) || 0;
    
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
    
    const weightInGrams = quantity * currentSelectedUnit.weight_in_grams;
    
    const multiplier = weightInGrams / 100;
    
    const calories = selectedFood.calories * multiplier;
    const protein = selectedFood.protein * multiplier;
    const carbs = selectedFood.carbs * multiplier;
    const fat = selectedFood.fat * multiplier;
    
    const conversionInfo = document.getElementById('unitConversionInfo');
    const conversionText = document.getElementById('conversionText');
    
    if (currentSelectedUnit.unit_name !== 'gram' && currentSelectedUnit.weight_in_grams > 1) {
        conversionInfo.style.display = 'flex';
        const unitName = quantity === 1 ? currentSelectedUnit.unit_display : currentSelectedUnit.unit_plural;
        conversionText.textContent = `${quantity} ${unitName} = ${Math.round(weightInGrams)}g`;
    } else {
        conversionInfo.style.display = 'none';
    }
    
    const calculatedInfo = document.getElementById('calculatedInfo');
    calculatedInfo.innerHTML = `
        <div class="nutrition-preview-title">
            <i class="fa-solid fa-utensils"></i>
            Your Serving
        </div>
        <div class="nutrition-preview-grid">
            <div class="nutrition-preview-item total-calories-highlight">
                <div class="nutrition-preview-value">${formatNumber(calories, 0)}</div>
                <div class="nutrition-preview-label">Calories</div>
            </div>
            <div class="nutrition-preview-item">
                <div class="nutrition-preview-value">${formatNumber(protein, 1)}g</div>
                <div class="nutrition-preview-label">Protein</div>
            </div>
            <div class="nutrition-preview-item">
                <div class="nutrition-preview-value">${formatNumber(carbs, 1)}g</div>
                <div class="nutrition-preview-label">Carbs</div>
            </div>
            <div class="nutrition-preview-item">
                <div class="nutrition-preview-value">${formatNumber(fat, 1)}g</div>
                <div class="nutrition-preview-label">Fat</div>
            </div>
        </div>
    `;
    
    const unitText = quantity === 1 ? currentSelectedUnit.unit_display : currentSelectedUnit.unit_plural;
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFoodModal'));
            modal.hide();
            
            await reloadCurrentDate();
            
            const toast = document.createElement('div');
            toast.className = 'water-toast';
            toast.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            toast.innerHTML = `
                <i class="fa-solid fa-check-circle"></i>
                <span>Food added successfully!</span>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }, 2500);
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
            if (modal) {
                modal.hide();
            }
            
            cleanupModalBackdrop();
            
            await reloadCurrentDate();
            
            const toast = document.createElement('div');
            toast.className = 'water-toast';
            toast.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
            toast.innerHTML = `
                <i class="fa-solid fa-trash"></i>
                <span>Food removed successfully!</span>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }, 2500);
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
    
    if (foodSearchClearBtn) {
        foodSearchClearBtn.classList.remove('show');
    }
});

class WaterTracker {
    constructor() {
        this.storageKey = 'waterIntakeByDate';
        this.currentDate = this.getTodayString();
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
        const pageDate = document.getElementById('currentDate')?.dataset.date;
        if (pageDate) {
            this.currentDate = pageDate;
        }
        this.ensureStorageShape();
        this.loadForDate(this.currentDate);
        this.updateDisplay();
        this.attachEventListeners();
        this.startMidnightChecker();
    }
    
    ensureStorageShape() {
        const raw = localStorage.getItem(this.storageKey);
        if (!raw) {
            localStorage.setItem(this.storageKey, JSON.stringify({}));
        } else {
            try {
                const parsed = JSON.parse(raw);
                if (parsed === null || typeof parsed !== 'object' || Array.isArray(parsed)) {
                    localStorage.setItem(this.storageKey, JSON.stringify({}));
                }
            } catch (e) {
                localStorage.setItem(this.storageKey, JSON.stringify({}));
            }
        }
    }
    
    loadForDate(dateString) {
        this.ensureStorageShape();
        try {
            const map = JSON.parse(localStorage.getItem(this.storageKey) || '{}');
            const value = parseInt(map[dateString] || '0', 10);
            this.currentIntake = isNaN(value) ? 0 : value;
        } catch (e) {
            this.currentIntake = 0;
        }
    }
    
    saveForDate(dateString) {
        this.ensureStorageShape();
        const map = JSON.parse(localStorage.getItem(this.storageKey) || '{}');
        map[dateString] = this.currentIntake;
        localStorage.setItem(this.storageKey, JSON.stringify(map));
    }

    setDate(dateString) {
        this.addTransition();
        this.currentDate = dateString;
        this.loadForDate(dateString);
        this.updateDisplay();
        this.removeTransition();
    }
    
    addWater(amount) {
        this.currentIntake += amount;
        
        if (this.currentIntake > 10000) {
            this.currentIntake = 10000;
        }
        
        this.saveForDate(this.currentDate);
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
        this.saveForDate(this.currentDate);
        this.updateDisplay();
        this.elements.card.classList.remove('goal-reached');
        
        const waterResetModal = bootstrap.Modal.getInstance(document.getElementById('waterResetModal'));
        if (waterResetModal) {
            waterResetModal.hide();
        }
        
        cleanupModalBackdrop();
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
            const today = this.getTodayString();
            const pageDate = document.getElementById('currentDate')?.dataset.date;
            if (pageDate === today && this.currentDate !== today) {
                this.setDate(today);
            }
            if (this.currentDate === today) {
                this.loadForDate(today);
                this.updateDisplay();
            }
        }, 60000);
    }

    addTransition() {
        if (!this.elements.card) return;
        const el = this.elements.card;
        el.style.transition = 'opacity 220ms ease, transform 220ms ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(6px)';
    }
    removeTransition() {
        if (!this.elements.card) return;
        const el = this.elements.card;
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
            setTimeout(() => {
                el.style.transition = '';
            }, 260);
        });
    }

    getTodayString() {
        return new Date().toISOString().split('T')[0];
    }
}

let waterTrackerInstance = null;
if (document.getElementById('waterTrackerCard')) {
    waterTrackerInstance = new WaterTracker();
    document.getElementById('confirmWaterResetBtn').addEventListener('click', function() {
        waterTrackerInstance.confirmReset();
    });
}

document.querySelectorAll('.goals-clickable').forEach(item => {
    item.addEventListener('click', function() {
        const infoType = this.dataset.infoType;
        let modalId = '';
        
        if (infoType === 'goal') {
            modalId = 'goalInfoModal';
        } else if (infoType === 'tdee') {
            modalId = 'tdeeInfoModal';
        } else if (infoType === 'bmr') {
            modalId = 'bmrInfoModal';
        }
        
        if (modalId) {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }
    });
});

async function loadDayData(dateString) {
    const navigator = document.querySelector('.date-navigator');
    const allButtons = document.querySelectorAll('.date-nav-btn, .date-today-btn');
    
    navigator.classList.add('loading');
    allButtons.forEach(btn => btn.classList.add('loading'));
    addContentLoadingState();
    
    try {
        let data;
        
        if (dayDataCache.has(dateString)) {
            data = dayDataCache.get(dateString);
            await new Promise(resolve => setTimeout(resolve, 150));
        } else {
            const response = await fetch(`api/get_day_data.php?date=${dateString}`);
            data = await response.json();
            
            if (data.success) {
                dayDataCache.set(dateString, data);
            }
        }
        
        if (!data.success) {
            console.error('Error loading day data:', data.error);
            navigator.classList.remove('loading');
            allButtons.forEach(btn => btn.classList.remove('loading'));
            removeContentLoadingState();
            
            showErrorToast('Unable to load date. Please try again.');
            return;
        }
        
        updateUI(data);
        
        const url = dateString === new Date().toISOString().split('T')[0] 
            ? 'dashboard.php' 
            : `dashboard.php?date=${dateString}`;
        history.pushState({ date: dateString }, '', url);
        
        preloadAdjacentDays(dateString);
        
        setTimeout(() => {
            navigator.classList.remove('loading');
            allButtons.forEach(btn => btn.classList.remove('loading'));
            removeContentLoadingState();
        }, 300);
        
    } catch (error) {
        console.error('Error fetching day data:', error);
        navigator.classList.remove('loading');
        allButtons.forEach(btn => btn.classList.remove('loading'));
        removeContentLoadingState();
        
        showErrorToast('Network error. Please check your connection.');
    }
}

function showErrorToast(message) {
    const toast = document.createElement('div');
    toast.className = 'water-toast';
    toast.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
    toast.innerHTML = `
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

function updateUI(data) {
    const currentDateElement = document.getElementById('currentDate');
    currentDateElement.dataset.date = data.date;
    currentDateElement.textContent = data.formatted_date;
    
    const dateLabel = document.querySelector('.date-label');
    dateLabel.innerHTML = data.date_label;
    
    const nutritionLabel = document.querySelector('.calorie-overview .text-muted');
    if (nutritionLabel) {
        nutritionLabel.textContent = data.nutrition_label;
    }
    
    const dateHeader = document.querySelector('.calorie-overview h3');
    if (dateHeader) {
        dateHeader.textContent = data.formatted_date.split(',').slice(0, 2).join(',');
    }
    
    updateCalorieRing(data);
    updateMacros(data.daily_log);
    updateMeals(data.entries_by_meal, data.meal_calories);
    updateDailyProgress(data);
    if (waterTrackerInstance) {
        waterTrackerInstance.setDate(data.date);
    }
}

function updateDailyProgress(data) {
    const progressValue = document.querySelector('.progress-tracker-value');
    if (progressValue) {
        const totalCaloriesSpan = progressValue.childNodes[0];
        if (totalCaloriesSpan) {
            totalCaloriesSpan.textContent = Math.round(data.daily_log.total_calories).toLocaleString() + ' ';
        }
    }
    
    const progressPercentage = document.querySelector('.progress-tracker-percentage');
    if (progressPercentage) {
        progressPercentage.textContent = `${formatNumber(data.calorie_percentage, 1)}%`;
    }
    
    const progressFill = document.querySelector('.progress-tracker-fill');
    if (progressFill) {
        progressFill.style.width = `${Math.min(data.calorie_percentage, 100)}%`;
    }
    
    const progressStatus = document.querySelector('.progress-tracker-status');
    if (progressStatus) {
        if (data.remaining_calories > 0) {
            progressStatus.innerHTML = `
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                <span style="color: #10b981;">${Math.round(data.remaining_calories).toLocaleString()} kcal remaining</span>
            `;
        } else if (data.remaining_calories < 0) {
            progressStatus.innerHTML = `
                <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i>
                <span style="color: #f59e0b;">${Math.round(Math.abs(data.remaining_calories)).toLocaleString()} kcal over goal</span>
            `;
        } else {
            progressStatus.innerHTML = `
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                <span style="color: #10b981;">Perfect! Goal reached!</span>
            `;
        }
    }
}

function updateCalorieRing(data) {
    const caloriesElement = document.querySelector('.calorie-ring-text h2');
    if (caloriesElement) {
        caloriesElement.textContent = Math.round(data.daily_log.total_calories).toLocaleString();
    }
    
    const percentageElement = document.querySelector('.calorie-percentage');
    if (percentageElement) {
        percentageElement.textContent = `${Math.round(data.calorie_percentage)}%`;
    }
    
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const radius = 95;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference * (1 - Math.min(data.calorie_percentage, 100) / 100);
        progressCircle.style.strokeDashoffset = offset;
        
        const isOver = data.calorie_percentage >= 100;
        const gradientId = isOver ? 'overGradient' : 'progressGradient';
        progressCircle.setAttribute('stroke', `url(#${gradientId})`);
    }
    
    const remainingBadge = document.querySelector('.remaining-calories .badge-remaining');
    if (remainingBadge) {
        if (data.remaining_calories > 0) {
            remainingBadge.className = 'badge-remaining success';
            remainingBadge.textContent = `${Math.round(data.remaining_calories).toLocaleString()} left`;
        } else if (data.remaining_calories < 0) {
            remainingBadge.className = 'badge-remaining danger';
            remainingBadge.textContent = `${Math.round(Math.abs(data.remaining_calories)).toLocaleString()} over`;
        } else {
            remainingBadge.className = 'badge-remaining perfect';
            remainingBadge.textContent = 'Perfect!';
        }
    }
}

function updateMacros(dailyLog) {
    const macroCards = document.querySelectorAll('.macro-card');
    
    macroCards.forEach(card => {
        const icon = card.querySelector('.macro-icon');
        const h5 = card.querySelector('h5');
        
        if (!icon || !h5) return;
        
        if (icon.classList.contains('protein')) {
            h5.textContent = `${formatNumber(parseFloat(dailyLog.total_protein), 1)}g`;
        } else if (icon.classList.contains('carbs')) {
            h5.textContent = `${formatNumber(parseFloat(dailyLog.total_carbs), 1)}g`;
        } else if (icon.classList.contains('fat')) {
            h5.textContent = `${formatNumber(parseFloat(dailyLog.total_fat), 1)}g`;
        }
    });
}

function updateMeals(entriesByMeal, mealCalories) {
    const meals = ['breakfast', 'lunch', 'dinner', 'snack'];
    
    meals.forEach(meal => {
        const mealContainer = document.getElementById(`${meal}-entries`);
        const caloriesSpan = mealContainer.closest('.meal-card').querySelector('.meal-calories');
        
        if (caloriesSpan) {
            caloriesSpan.textContent = `${Math.round(mealCalories[meal]).toLocaleString()} kcal`;
        }
        
        if (!entriesByMeal[meal] || entriesByMeal[meal].length === 0) {
            mealContainer.innerHTML = `
                <div class="empty-meal-placeholder">
                    <i class="fa-solid fa-utensils-slash"></i>
                    <p>No food added yet</p>
                </div>
            `;
            return;
        }
        
        mealContainer.innerHTML = entriesByMeal[meal].map(entry => {
            const quantity = Math.round(entry.quantity);
            let unit = entry.quantity_unit || 'g';
            
            if (unit !== 'g' && parseFloat(entry.quantity) > 1) {
                const pluralMap = {
                    'egg': 'eggs',
                    'slice': 'slices',
                    'tbsp': 'tbsp',
                    'cup': 'cups',
                    'serving': 'servings',
                    'avocado': 'avocados',
                    'orange': 'oranges'
                };
                unit = pluralMap[unit] || unit + 's';
            }
            
            return `
                <div class="entry-item" data-entry-id="${entry.id}">
                    <div class="entry-name">
                        <strong>${entry.food_name}</strong>
                        <small class="text-muted d-block">${quantity} ${unit}</small>
                        <div class="entry-macros mt-2">
                            <small style="color: #6c757d;">
                                ${Math.round(entry.calories)} kcal | 
                                P: ${formatNumber(parseFloat(entry.protein), 1)}g | 
                                C: ${formatNumber(parseFloat(entry.carbs), 1)}g | 
                                F: ${formatNumber(parseFloat(entry.fat), 1)}g
                            </small>
                        </div>
                    </div>
                    <div class="entry-calories">
                        <button class="btn-remove-entry" data-entry-id="${entry.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        attachDeleteHandlers();
    });
}

function attachDeleteHandlers() {
    document.querySelectorAll('.btn-remove-entry').forEach(btn => {
        btn.addEventListener('click', function() {
            const entryId = this.dataset.entryId;
            pendingDeleteEntryId = entryId;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });
    });
}

function navigateToDate(dateString) {
    const dateDisplay = document.querySelector('.date-display');
    if (dateDisplay) {
        dateDisplay.classList.add('date-changing');
    }
    
    setTimeout(() => {
        loadDayData(dateString);
        setTimeout(() => {
            if (dateDisplay) {
                dateDisplay.classList.remove('date-changing');
            }
        }, 400);
    }, 200);
}

function changeDate(days) {
    const currentDateElement = document.getElementById('currentDate');
    const currentDate = currentDateElement.dataset.date;
    
    const date = new Date(currentDate);
    date.setDate(date.getDate() + days);
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const newDate = `${year}-${month}-${day}`;
    
    navigateToDate(newDate);
}

const prevDayBtn = document.getElementById('prevDayBtn');
if (prevDayBtn) {
    prevDayBtn.addEventListener('click', function() {
        changeDate(-1);
    });
}

const nextDayBtn = document.getElementById('nextDayBtn');
if (nextDayBtn) {
    nextDayBtn.addEventListener('click', function() {
        changeDate(1);
    });
}

const todayBtn = document.getElementById('todayBtn');
if (todayBtn) {
    todayBtn.addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        navigateToDate(today);
    });
}

document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        return;
    }
    
    if (e.key === 'ArrowLeft') {
        e.preventDefault();
        changeDate(-1);
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        changeDate(1);
    } else if (e.key === 'h' || e.key === 'H') {
        const today = new Date().toISOString().split('T')[0];
        navigateToDate(today);
    }
});

window.addEventListener('popstate', function(event) {
    if (event.state && event.state.date) {
        loadDayData(event.state.date);
    } else {
        const today = new Date().toISOString().split('T')[0];
        loadDayData(today);
    }
});

const dateDisplay = document.getElementById('dateDisplay');
if (dateDisplay) {
    dateDisplay.addEventListener('click', function() {
        const datePickerModal = new bootstrap.Modal(document.getElementById('datePickerModal'));
        const currentDate = document.getElementById('currentDate').dataset.date;
        document.getElementById('customDateInput').value = currentDate;
        datePickerModal.show();
    });
}

document.querySelectorAll('.quick-date-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const days = parseInt(this.dataset.days);
        const today = new Date();
        const targetDate = new Date(today);
        targetDate.setDate(targetDate.getDate() + days);
        
        const year = targetDate.getFullYear();
        const month = String(targetDate.getMonth() + 1).padStart(2, '0');
        const day = String(targetDate.getDate()).padStart(2, '0');
        const dateString = `${year}-${month}-${day}`;
        
        const datePickerModal = bootstrap.Modal.getInstance(document.getElementById('datePickerModal'));
        if (datePickerModal) {
            datePickerModal.hide();
        }
        
        navigateToDate(dateString);
    });
});

const applyCustomDateBtn = document.getElementById('applyCustomDate');
if (applyCustomDateBtn) {
    applyCustomDateBtn.addEventListener('click', function() {
        const customDate = document.getElementById('customDateInput').value;
        
        const datePickerModal = bootstrap.Modal.getInstance(document.getElementById('datePickerModal'));
        if (datePickerModal) {
            datePickerModal.hide();
        }
        
        navigateToDate(customDate);
    });
}

let touchStartX = 0;
let touchEndX = 0;
let touchStartY = 0;
let touchEndY = 0;

const mainContent = document.querySelector('.container-fluid');
if (mainContent) {
    mainContent.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });
    
    mainContent.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, { passive: true });
}

function handleSwipe() {
    const swipeThreshold = 80;
    const xDiff = touchEndX - touchStartX;
    const yDiff = Math.abs(touchEndY - touchStartY);
    
    if (yDiff > 100) {
        return;
    }
    
    if (xDiff > swipeThreshold) {
        showSwipeIndicator('left');
        changeDate(-1);
    } else if (xDiff < -swipeThreshold) {
        showSwipeIndicator('right');
        changeDate(1);
    }
}

function showSwipeIndicator(direction) {
    const indicator = document.getElementById(direction === 'left' ? 'swipeLeft' : 'swipeRight');
    if (indicator) {
        indicator.classList.add('show');
        setTimeout(() => {
            indicator.classList.remove('show');
        }, 600);
    }
}

function addContentLoadingState() {
    const mainContent = document.querySelector('.col-lg-8');
    if (mainContent) {
        mainContent.classList.add('content-loading');
    }
}

function removeContentLoadingState() {
    const mainContent = document.querySelector('.col-lg-8');
    if (mainContent) {
        mainContent.classList.remove('content-loading');
    }
}

const dayDataCache = new Map();

document.addEventListener('DOMContentLoaded', function() {
    const currentDate = document.getElementById('currentDate')?.dataset.date;
    if (currentDate) {
        preloadAdjacentDays(currentDate);
    }
    
    attachDeleteHandlers();
    
    const changeFoodBtn = document.getElementById('changeFoodBtn');
    if (changeFoodBtn) {
        changeFoodBtn.addEventListener('click', function() {
            document.getElementById('selectedFood').style.display = 'none';
            document.getElementById('quantityInput').style.display = 'none';
            document.getElementById('unitSelectorContainer').style.display = 'none';
            document.getElementById('foodSearch').value = '';
            document.getElementById('foodResults').innerHTML = '';
            document.querySelectorAll('.food-item').forEach(item => {
                item.classList.remove('selected');
            });
            selectedFood = null;
            document.getElementById('addFoodBtn').disabled = true;
        });
    }
    
    const addFoodModal = document.getElementById('addFoodModal');
    if (addFoodModal) {
        addFoodModal.addEventListener('show.bs.modal', function(e) {
            document.getElementById('selectedFood').style.display = 'none';
            document.getElementById('quantityInput').style.display = 'none';
            document.getElementById('unitSelectorContainer').style.display = 'none';
            document.getElementById('foodResults').innerHTML = '';
            
            const foodSearch = document.getElementById('foodSearch');
            if (foodSearch) {
                foodSearch.value = '';
                foodSearch.focus();
            }
            const searchClearBtn = document.getElementById('foodSearchClearBtn');
            if (searchClearBtn) {
                searchClearBtn.classList.remove('show');
            }
            
            selectedFood = null;
            
            const button = e.relatedTarget;
            if (button && button.dataset.mealType) {
                const mealType = button.dataset.mealType;
                document.getElementById('mealType').value = mealType;
                
                const mealOptions = {
                    'breakfast': { icon: 'fa-sun', text: 'Breakfast' },
                    'lunch': { icon: 'fa-bowl-rice', text: 'Lunch' },
                    'dinner': { icon: 'fa-moon', text: 'Dinner' },
                    'snack': { icon: 'fa-cookie-bite', text: 'Snacks' }
                };
                
                if (mealOptions[mealType]) {
                    const mealIcon = document.querySelector('.meal-dropdown-icon');
                    const mealText = document.getElementById('mealDropdownText');
                    if (mealIcon && mealText) {
                        mealIcon.innerHTML = `<i class="fa-solid ${mealOptions[mealType].icon}"></i>`;
                        mealText.textContent = mealOptions[mealType].text;
                    }
                    
                    document.querySelectorAll('.meal-dropdown-option').forEach(opt => {
                        opt.classList.remove('selected');
                        if (opt.dataset.meal === mealType) {
                            opt.classList.add('selected');
                        }
                    });
                }
            }
        });
    }
});

async function preloadAdjacentDays(currentDate) {
    const date = new Date(currentDate);
    
    const prevDate = new Date(date);
    prevDate.setDate(prevDate.getDate() - 1);
    const prevDateString = prevDate.toISOString().split('T')[0];
    
    const nextDate = new Date(date);
    nextDate.setDate(nextDate.getDate() + 1);
    const nextDateString = nextDate.toISOString().split('T')[0];
    
    [prevDateString, nextDateString].forEach(async (dateString) => {
        if (!dayDataCache.has(dateString)) {
            try {
                const response = await fetch(`api/get_day_data.php?date=${dateString}`);
                const data = await response.json();
                if (data.success) {
                    dayDataCache.set(dateString, data);
                }
            } catch (error) {
                console.error('Preload error:', error);
            }
        }
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-add-meal-item')) {
        const btn = e.target.closest('.btn-add-meal-item');
        const mealType = btn.dataset.mealType;
        if (mealType && document.getElementById('mealType')) {
            document.getElementById('mealType').value = mealType;
            
            const mealDropdownOptions = document.getElementById('mealDropdownOptions');
            if (mealDropdownOptions) {
                const selectedOption = mealDropdownOptions.querySelector(`[data-meal="${mealType}"]`);
                if (selectedOption) {
                    const iconHTML = selectedOption.querySelector('.meal-dropdown-option-icon').innerHTML;
                    const text = selectedOption.querySelector('.meal-dropdown-option-text').textContent;
                    
                    const mealDropdownIcon = document.querySelector('.meal-dropdown-icon');
                    const mealDropdownText = document.getElementById('mealDropdownText');
                    if (mealDropdownIcon) mealDropdownIcon.innerHTML = iconHTML;
                    if (mealDropdownText) mealDropdownText.textContent = text;
                    
                    mealDropdownOptions.querySelectorAll('.meal-dropdown-option').forEach(opt => opt.classList.remove('selected'));
                    selectedOption.classList.add('selected');
                }
            }
            
            const event = new Event('change', { bubbles: true });
            document.getElementById('mealType').dispatchEvent(event);
        }
    }
});


let selectedFood = null;
let searchTimeout = null;
let currentInputMode = 'pieces'; 


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
        resultsDiv.innerHTML = '<p class="text-muted text-center">No foods found</p>';
        return;
    }
    
    resultsDiv.innerHTML = foods.map(food => `
        <div class="food-item" data-food-id="${food.id}" data-food-calories="${food.calories}" 
             data-food-protein="${food.protein}" data-food-carbs="${food.carbs}" 
             data-food-fat="${food.fat}" data-food-fiber="${food.fiber}" 
             data-food-sugar="${food.sugar}"
             data-food-unit-type="${food.unit_type || 'grams'}" 
             data-food-unit-name="${food.unit_name || 'g'}"
             data-food-weight-per-unit="${food.weight_per_unit || 100}">
            <h6>${food.name}</h6>
            <small>${food.calories} kcal per ${food.unit_type === 'pieces' ? (food.unit_name || 'piece') : '100g'} | Protein: ${food.protein}g | Carbs: ${food.carbs}g | Fat: ${food.fat}g</small>
        </div>
    `).join('');
    
    
    document.querySelectorAll('.food-item').forEach(item => {
        item.addEventListener('click', function() {
            selectFood(this);
        });
    });
}


function formatNumber(num) {
    
    return num.toString().replace(/\.0+$/, '');
}


function showUnitToggle() {
    document.getElementById('toggleUnit').style.display = 'block';
}

function hideUnitToggle() {
    document.getElementById('toggleUnit').style.display = 'none';
}


document.getElementById('toggleUnit')?.addEventListener('click', function() {
    if (!selectedFood || selectedFood.unitType !== 'pieces') return;
    
    const toggleBtn = this;
    const currentValue = parseFloat(document.getElementById('quantity').value);
    
    if (currentInputMode === 'pieces') {
        
        currentInputMode = 'grams';
        toggleBtn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Switch to pieces';
        
        
        const gramsValue = Math.round(currentValue * selectedFood.weightPerUnit);
        document.getElementById('quantity').value = gramsValue;
        document.getElementById('quantityLabel').textContent = 'Amount (g)';
        document.getElementById('quantityUnit').textContent = 'g';
        
        
        createQuickAmountButtons();
        updateQuantityDisplay();
    } else {
        
        currentInputMode = 'pieces';
        toggleBtn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Switch to grams';
        
        
        const piecesValue = Math.round(currentValue / selectedFood.weightPerUnit);
        document.getElementById('quantity').value = Math.max(1, piecesValue);
        document.getElementById('quantityLabel').textContent = 'Amount';
        document.getElementById('quantityUnit').textContent = selectedFood.unitName;
        
        
        createQuickAmountButtons();
        updateQuantityDisplay();
    }
});


function selectFood(element) {
    
    document.querySelectorAll('.food-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    selectedFood = {
        id: element.dataset.foodId,
        name: element.querySelector('h6').textContent,
        calories: parseFloat(element.dataset.foodCalories),
        protein: parseFloat(element.dataset.foodProtein),
        carbs: parseFloat(element.dataset.foodCarbs),
        fat: parseFloat(element.dataset.foodFat),
        fiber: parseFloat(element.dataset.foodFiber),
        sugar: parseFloat(element.dataset.foodSugar),
        unitType: element.dataset.foodUnitType || 'grams',
        unitName: element.dataset.foodUnitName || 'g',
        weightPerUnit: parseFloat(element.dataset.foodWeightPerUnit) || 100
    };
    
    
    document.getElementById('selectedFood').style.display = 'block';
    document.getElementById('quantityInput').style.display = 'block';
    document.getElementById('selectedFoodName').textContent = selectedFood.name;
    
    
    document.getElementById('selectedFoodCalories').textContent = formatNumber(selectedFood.calories);
    document.getElementById('selectedFoodProtein').textContent = formatNumber(selectedFood.protein);
    document.getElementById('selectedFoodCarbs').textContent = formatNumber(selectedFood.carbs);
    document.getElementById('selectedFoodFat').textContent = formatNumber(selectedFood.fat);
    
    
    if (selectedFood.unitType === 'pieces') {
        currentInputMode = 'pieces';
        document.getElementById('quantityLabel').textContent = 'Amount';
        document.getElementById('quantityUnit').textContent = selectedFood.unitName;
        document.getElementById('quantity').value = 1;
        document.getElementById('quantity').step = '1';
        
        
        showUnitToggle();
    } else {
        currentInputMode = 'grams';
        document.getElementById('quantityLabel').textContent = 'Amount (g)';
        document.getElementById('quantityUnit').textContent = 'g';
        document.getElementById('quantity').value = 100;
        document.getElementById('quantity').step = '1';
        
        
        hideUnitToggle();
    }
    
    
    createQuickAmountButtons();
    
    
    document.getElementById('addFoodBtn').disabled = false;
    
    
    updateQuantityDisplay();
}


function createQuickAmountButtons() {
    const buttonsContainer = document.getElementById('quickAmountButtons');
    buttonsContainer.innerHTML = '';
    
    if (selectedFood.unitType === 'pieces' && currentInputMode === 'grams') {
        
        const weights = [selectedFood.weightPerUnit * 0.5, selectedFood.weightPerUnit, selectedFood.weightPerUnit * 1.5, selectedFood.weightPerUnit * 2];
        weights.forEach(weight => {
            const rounded = Math.round(weight);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary btn-sm me-2 mb-2';
            btn.style.fontSize = '0.85rem';
            const multiplier = weight / 100;
            const calories = (selectedFood.calories * multiplier).toFixed(0);
            btn.innerHTML = `
                <div><strong>${rounded} g</strong></div>
                <div style="font-size: 0.75rem; font-weight: normal;">${calories} kcal</div>
            `;
            
            btn.addEventListener('click', function() {
                document.getElementById('quantity').value = rounded;
                updateQuantityDisplay();
            });
            
            buttonsContainer.appendChild(btn);
        });
    } else if (selectedFood.unitType === 'pieces') {
        
        const amounts = [1, 2, 3, 4];
        amounts.forEach(amount => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary btn-sm me-2 mb-2';
            btn.style.fontSize = '0.85rem';
            const calories = (selectedFood.calories * amount).toFixed(0);
            btn.innerHTML = `
                <div><strong>${amount} ${selectedFood.unitName}${amount > 1 ? 's' : ''}</strong></div>
                <div style="font-size: 0.75rem; font-weight: normal;">${calories} kcal</div>
            `;
            
            btn.addEventListener('click', function() {
                document.getElementById('quantity').value = amount;
                updateQuantityDisplay();
            });
            
            buttonsContainer.appendChild(btn);
        });
    } else {
        
        const amounts = [50, 100, 150, 200];
        amounts.forEach(amount => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary btn-sm me-2 mb-2';
            btn.style.fontSize = '0.85rem';
            const calories = (selectedFood.calories * (amount / 100)).toFixed(0);
            btn.innerHTML = `
                <div><strong>${amount} g</strong></div>
                <div style="font-size: 0.75rem; font-weight: normal;">${calories} kcal</div>
            `;
            
            btn.addEventListener('click', function() {
                document.getElementById('quantity').value = amount;
                updateQuantityDisplay();
            });
            
            buttonsContainer.appendChild(btn);
        });
    }
}


document.getElementById('increaseQty').addEventListener('click', function() {
    if (!selectedFood) return;
    const quantityInput = document.getElementById('quantity');
    const increment = selectedFood.unitType === 'pieces' ? 1 : 10;
    quantityInput.value = parseFloat(quantityInput.value) + increment;
    updateQuantityDisplay();
});

document.getElementById('decreaseQty').addEventListener('click', function() {
    if (!selectedFood) return;
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseFloat(quantityInput.value);
    const decrement = selectedFood.unitType === 'pieces' ? 1 : 10;
    const minValue = selectedFood.unitType === 'pieces' ? 1 : 10;
    if (currentValue > minValue) {
        quantityInput.value = currentValue - decrement;
        updateQuantityDisplay();
    }
});

document.getElementById('quantity').addEventListener('input', function() {
    updateQuantityDisplay();
});

function updateQuantityDisplay() {
    if (!selectedFood) return;
    
    const quantity = parseFloat(document.getElementById('quantity').value) || (selectedFood.unitType === 'pieces' ? 1 : 100);
    
    let multiplier;
    let unitDisplay;
    
    if (selectedFood.unitType === 'pieces') {
        multiplier = quantity;
        unitDisplay = `${quantity} ${selectedFood.unitName}${quantity > 1 ? 's' : ''}`;
    } else {
        multiplier = quantity / 100; 
        unitDisplay = `${quantity} g`;
    }
    
    
    const calories = selectedFood.calories * multiplier;
    const protein = selectedFood.protein * multiplier;
    const carbs = selectedFood.carbs * multiplier;
    const fat = selectedFood.fat * multiplier;
    
    
    document.getElementById('selectedFoodCalories').textContent = formatNumber(Math.round(calories * 10) / 10);
    document.getElementById('selectedFoodProtein').textContent = formatNumber(Math.round(protein * 10) / 10);
    document.getElementById('selectedFoodCarbs').textContent = formatNumber(Math.round(carbs * 10) / 10);
    document.getElementById('selectedFoodFat').textContent = formatNumber(Math.round(fat * 10) / 10);
    
    
    document.getElementById('calculatedInfo').textContent = `${unitDisplay} = ${Math.round(calories * 10) / 10} kcal`;
}


document.getElementById('addFoodBtn').addEventListener('click', async function() {
    if (!selectedFood) return;
    
    const mealType = document.getElementById('mealType').value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 100; 
    
    try {
        const formData = new FormData();
        formData.append('action', 'add_food');
        formData.append('food_id', selectedFood.id);
        formData.append('meal_type', mealType);
        formData.append('quantity', quantity);
        
        const response = await fetch('api/dashboard_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            
            window.location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
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
    document.getElementById('quantity').value = 1;
    document.getElementById('addFoodBtn').disabled = true;
    selectedFood = null;
});


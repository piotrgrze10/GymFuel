
const rawFoods = {
    'banana': { name: 'Banana', calories: 89, carbs: 23, protein: 1, fat: 0, fiber: 3, image: null },
    'apple': { name: 'Apple', calories: 52, carbs: 14, protein: 0.3, fat: 0.2, fiber: 2.4, image: null },
    'orange': { name: 'Orange', calories: 47, carbs: 12, protein: 0.9, fat: 0.1, fiber: 2.4, image: null },
    'chicken': { name: 'Chicken Breast', calories: 165, carbs: 0, protein: 31, fat: 3.6, fiber: 0, image: null },
    'eggs': { name: 'Eggs', calories: 155, carbs: 1.1, protein: 13, fat: 11, fiber: 0, image: null },
    'milk': { name: 'Milk', calories: 42, carbs: 5, protein: 3.4, fat: 1, fiber: 0, image: null },
    'rice': { name: 'White Rice', calories: 130, carbs: 28, protein: 2.7, fat: 0.3, fiber: 0.4, image: null },
    'oats': { name: 'Oats', calories: 389, carbs: 66, protein: 17, fat: 7, fiber: 11, image: null },
    'spinach': { name: 'Spinach', calories: 23, carbs: 4, protein: 2.9, fat: 0.4, fiber: 2.2, image: null },
    'cheese': { name: 'Cheese', calories: 361, carbs: 1.3, protein: 23, fat: 29, fiber: 0, image: null }
};

let searchTimeout;
let allProducts = [];
let currentPage = 1;
const productsPerPage = 12;
let currentProduct = null;

const categoryTerms = {
    'fruits': ['apple', 'banana', 'orange'],
    'vegetables': ['spinach'],
    'meat': ['chicken'],
    'dairy': ['milk', 'cheese', 'eggs'],
    'grains': ['rice', 'oats']
};

function addToRecentSearches(query) {
    if (!query || query.trim().length < 2) return;
    let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    recentSearches = recentSearches.filter(item => item !== query);
    recentSearches.unshift(query);
    recentSearches = recentSearches.slice(0, 5);
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
    renderRecentSearches();
}

function renderRecentSearches() {
    const container = document.getElementById('recentSearchesList');
    const containerWrapper = document.getElementById('recentSearchesContainer');
    const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    
    if (recentSearches.length === 0) {
        containerWrapper.style.display = 'none';
        return;
    }
    
    containerWrapper.style.display = 'block';
    let html = '';
    
    recentSearches.forEach(search => {
        html += `
            <div class="recent-search-chip" data-query="${search}">
                <i class="fa-solid fa-clock"></i>
                <span>${search}</span>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    document.querySelectorAll('.recent-search-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const query = this.getAttribute('data-query');
            document.getElementById('searchInput').value = query;
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('loadingSpinner').classList.add('show');
            document.getElementById('results').innerHTML = '';
            searchProducts(query);
        });
    });
}

window.clearCategorySelection = function() {
    document.getElementById('results').innerHTML = '';
    allProducts = [];
    currentPage = 1;
    document.getElementById('paginationContainer').style.display = 'none';
    document.getElementById('categoriesSection').classList.remove('hidden');
    renderRecentSearches();
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('clearCategoryWrapper').style.display = 'none';
    document.getElementById('searchInput').value = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

function searchProducts(query) {
    try {
        addToRecentSearches(query);
        document.getElementById('loadingSpinner').classList.remove('show');
        
        const lowerQuery = query.toLowerCase();
        const matchedProducts = [];
        
        for (const [key, food] of Object.entries(rawFoods)) {
            if (key === lowerQuery || key.includes(lowerQuery) || lowerQuery.includes(key) || food.name.toLowerCase().includes(lowerQuery)) {
                const product = {
                    product_name: food.name,
                    brands: 'Natural Food',
                    nutriments: {
                        'energy-kcal': food.calories,
                        'carbohydrates': food.carbs,
                        'proteins': food.protein,
                        'fat': food.fat,
                        'fiber': food.fiber || 0
                    },
                    is_raw_food: true,
                    image_url: food.image || null
                };
                matchedProducts.push(product);
            }
        }
        
        matchedProducts.sort((a, b) => {
            const nameA = a.product_name.toLowerCase();
            const nameB = b.product_name.toLowerCase();
            const aExact = nameA === lowerQuery;
            const bExact = nameB === lowerQuery;
            
            if (aExact && !bExact) return -1;
            if (!aExact && bExact) return 1;
            
            const aStarts = nameA.startsWith(lowerQuery);
            const bStarts = nameB.startsWith(lowerQuery);
            
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return 0;
        });
        
        if (matchedProducts.length > 0) {
            displayProducts(matchedProducts);
        } else {
            showNoResults();
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('show');
        showError('Error searching products. Please try again.');
    }
}

function searchByCategory(category) {
    try {
        const terms = categoryTerms[category];
        let productsArray = [];
        
        for (let term of terms) {
            const lowerTerm = term.toLowerCase();
            if (rawFoods[lowerTerm]) {
                const rawFood = rawFoods[lowerTerm];
                const rawProduct = {
                    product_name: rawFood.name,
                    brands: 'Raw Food',
                    nutriments: {
                        'energy-kcal': rawFood.calories,
                        'carbohydrates': rawFood.carbs,
                        'proteins': rawFood.protein,
                        'fat': rawFood.fat
                    },
                    is_raw_food: true,
                    image_url: rawFood.image || null
                };
                productsArray.push(rawProduct);
            }
        }
        
        document.getElementById('loadingSpinner').classList.remove('show');
        
        if (productsArray.length > 0) {
            const categoryNames = {
                'fruits': 'Fruits',
                'vegetables': 'Vegetables',
                'meat': 'Meat & Fish',
                'dairy': 'Dairy',
                'grains': 'Grains & Cereals'
            };
            
            document.getElementById('activeCategoryName').textContent = `Showing: ${categoryNames[category]}`;
            document.getElementById('clearCategoryWrapper').style.display = 'block';
            displayProducts(productsArray);
            document.getElementById('categoriesSection').classList.add('hidden');
        } else {
            showNoResults();
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('show');
        showError('Error searching products. Please try again.');
    }
}

function displayProducts(products) {
    allProducts = products;
    currentPage = 1;
    
    if (products.length > 0) {
        document.getElementById('categoriesSection').classList.add('hidden');
        document.getElementById('recentSearchesContainer').style.display = 'none';
    }
    
    document.getElementById('paginationContainer').style.display = 'none';
    
    const activeCategory = document.querySelector('.category-btn.active');
    if (!activeCategory && document.getElementById('searchInput').value.length >= 2) {
        document.getElementById('clearCategoryWrapper').style.display = 'none';
    }
    
    renderProducts();
    
    setTimeout(() => {
        document.getElementById('resultsScrollTarget').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

function renderProducts() {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';
    
    if (allProducts.length === 0) {
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    const wrapper = document.createElement('div');
    wrapper.className = 'results-wrapper';
    
    const totalPages = Math.ceil(allProducts.length / productsPerPage);
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const productsToShow = allProducts.slice(startIndex, endIndex);
    
    productsToShow.forEach(product => {
        if (!product.product_name || !product.nutriments) return;
        
        const nutrition = product.nutriments;
        const calories = nutrition['energy-kcal'] != null ? Math.round(nutrition['energy-kcal']) : null;
        const carbs = nutrition['carbohydrates'] != null ? Math.round(nutrition['carbohydrates']) : null;
        const protein = nutrition['proteins'] != null ? Math.round(nutrition['proteins']) : null;
        const fat = nutrition['fat'] != null ? Math.round(nutrition['fat']) : null;
        
        let productName = product.product_name.trim().replace(/\s+/g, ' ');
        
        if (productName.length > 60) {
            productName = productName.substring(0, 60) + '...';
        }
        
        const card = document.createElement('div');
        card.className = 'product-card';
        
        const placeholderIcon = getPlaceholderIcon(productName);
        const imageHTML = `<div class="product-image-wrapper"><div class="product-image-placeholder"><i class="fa-solid ${placeholderIcon}"></i></div></div>`;
        
        const displayCal = calories !== null && calories !== undefined ? calories : '-';
        const displayProtein = protein !== null && protein !== undefined ? protein : '-';
        const displayCarbs = carbs !== null && carbs !== undefined ? carbs : '-';
        const displayFat = fat !== null && fat !== undefined ? fat : '-';
        
        card.innerHTML = `
            ${imageHTML}
            <div class="product-content">
                <div class="product-info">
                    <div class="product-name">${productName}</div>
                    <div class="product-brand">${product.brands || 'No brand'}</div>
                </div>
                <div class="nutrition-section">
                    <div class="nutrition-badges">
                        <div class="nutrition-badge"><div class="badge-value">${displayCal}</div><div class="badge-label">Cal</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayProtein}g</div><div class="badge-label">Protein</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayCarbs}g</div><div class="badge-label">Carbs</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayFat}g</div><div class="badge-label">Fat</div></div>
                    </div>
                    <div class="nutrition-note">per 100g</div>
                </div>
            </div>
        `;
        
        card.onclick = () => openCalculator(product);
        card.style.cursor = 'pointer';
        wrapper.appendChild(card);
    });
    
    resultsDiv.appendChild(wrapper);
    updatePaginationUI(totalPages);
}

function getPlaceholderIcon(productName) {
    const name = productName.toLowerCase();
    
    const iconMap = {
        'apple': 'fa-apple-whole',
        'banana': 'fa-apple-whole',
        'orange': 'fa-apple-whole',
        'chicken': 'fa-drumstick-bite',
        'chicken breast': 'fa-drumstick-bite',
        'eggs': 'fa-egg',
        'egg': 'fa-egg',
        'milk': 'fa-cheese',
        'cheese': 'fa-cheese',
        'rice': 'fa-wheat-awn',
        'white rice': 'fa-wheat-awn',
        'oats': 'fa-wheat-awn',
        'spinach': 'fa-carrot'
    };
    
    if (iconMap[name]) {
        return iconMap[name];
    }
    
    if (name.includes('chicken')) return 'fa-drumstick-bite';
    if (name.includes('egg')) return 'fa-egg';
    if (name.includes('milk') || name.includes('cheese')) return 'fa-cheese';
    if (name.includes('rice') || name.includes('oats')) return 'fa-wheat-awn';
    if (name.includes('apple') || name.includes('banana') || name.includes('orange')) return 'fa-apple-whole';
    if (name.includes('spinach')) return 'fa-carrot';
    
    return 'fa-bowl-food';
}

function updatePaginationUI(totalPages) {
    const paginationContainer = document.getElementById('paginationContainer');
    const paginationInfo = document.getElementById('paginationInfo');
    
    if (totalPages <= 1 || allProducts.length === 0) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'flex';
    paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function changePage(direction) {
    const totalPages = Math.ceil(allProducts.length / productsPerPage);
    
    if (direction === -1 && currentPage > 1) {
        currentPage--;
    } else if (direction === 1 && currentPage < totalPages) {
        currentPage++;
    }
    
    renderProducts();
    
    setTimeout(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 100);
}

function openCalculator(product) {
    currentProduct = product;
    document.getElementById('calcProductName').textContent = product.product_name;
    
    const imageContainer = document.getElementById('calcProductImage');
    const placeholderIcon = getPlaceholderIcon(product.product_name);
        imageContainer.innerHTML = `<i class="fa-solid ${placeholderIcon}" style="font-size: 32px; color: white;"></i>`;
    
    const servingOptions = document.getElementById('servingOptions');
    servingOptions.innerHTML = '';
    
    const servings = [
        { multiplier: 0.5, label: '0.5×', weight: '50 g' },
        { multiplier: 1, label: '1×', weight: '100 g' },
        { multiplier: 2, label: '2×', weight: '200 g' },
        { multiplier: 3, label: '3×', weight: '300 g' },
        { multiplier: 0.25, label: '0.25×', weight: '25 g' },
        { multiplier: 1.5, label: '1.5×', weight: '150 g' }
    ];
    
    servings.forEach(serving => {
        const btn = document.createElement('div');
        btn.className = 'serving-btn';
        btn.dataset.multiplier = serving.multiplier;
        btn.innerHTML = `${serving.label}<br><small>${serving.weight}</small>`;
        btn.onclick = () => selectServing(serving.multiplier);
        servingOptions.appendChild(btn);
    });
    
    selectServing(1);
    document.getElementById('calculatorModal').classList.add('active');
}

function selectServing(multiplier) {
    document.querySelectorAll('.serving-btn').forEach(b => b.classList.remove('active'));
    const activeBtn = Array.from(document.querySelectorAll('.serving-btn'))
        .find(b => parseFloat(b.dataset.multiplier) === multiplier);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    calculateNutrition(100 * multiplier);
}

function calculateNutrition(weightInGrams) {
    const nutrition = currentProduct.nutriments;
    const multiplier = weightInGrams / 100;
    
    const calories = nutrition['energy-kcal'] != null ? Math.round(nutrition['energy-kcal'] * multiplier) : 0;
    const carbs = nutrition['carbohydrates'] != null ? Math.round(nutrition['carbohydrates'] * multiplier) : 0;
    const protein = nutrition['proteins'] != null ? Math.round(nutrition['proteins'] * multiplier) : 0;
    const fat = nutrition['fat'] != null ? Math.round(nutrition['fat'] * multiplier) : 0;
    
    const display = document.getElementById('nutritionDisplay');
    display.innerHTML = `
        <div class="calories-container">
            <div class="main-value">${calories}</div>
            <div class="main-label">Calories</div>
        </div>
        <div class="nutrition-details">
            <div class="nutrition-item">
                <div class="nutrition-item-value">${protein}g</div>
                <div class="nutrition-item-label">Protein</div>
            </div>
            <div class="nutrition-item">
                <div class="nutrition-item-value">${carbs}g</div>
                <div class="nutrition-item-label">Carbs</div>
            </div>
            <div class="nutrition-item">
                <div class="nutrition-item-value">${fat}g</div>
                <div class="nutrition-item-label">Fat</div>
            </div>
        </div>
    `;
}

function closeCalculator() {
    document.getElementById('calculatorModal').classList.remove('active');
    document.getElementById('customWeight').value = '';
}

function showNoResults() {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = `
        <div class="no-results">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3 style="margin-top: 16px; color: #333;">No products found</h3>
            <p style="margin-top: 8px; color: #666;">Try searching for something else</p>
        </div>
    `;
    document.getElementById('categoriesSection').classList.remove('hidden');
    document.getElementById('paginationContainer').style.display = 'none';
    
    const activeCategory = document.querySelector('.category-btn.active');
    if (!activeCategory) {
        document.getElementById('clearCategoryWrapper').style.display = 'none';
    }
    
    allProducts = [];
    currentPage = 1;
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    renderRecentSearches();
    
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            document.getElementById('searchInput').value = '';
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('loadingSpinner').classList.add('show');
            document.getElementById('results').innerHTML = '';
            searchByCategory(category);
        });
    });
    
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);
        
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('clearCategoryWrapper').style.display = 'none';
        
        if (query.length >= 2) {
            document.getElementById('loadingSpinner').classList.add('show');
        } else {
            document.getElementById('loadingSpinner').classList.remove('show');
        }
        
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('results').innerHTML = '';
        
        if (query.length === 0) {
            document.getElementById('categoriesSection').classList.remove('hidden');
            document.getElementById('paginationContainer').style.display = 'none';
            document.getElementById('clearCategoryWrapper').style.display = 'none';
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            allProducts = [];
            currentPage = 1;
            renderRecentSearches();
        }
        
        searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                searchProducts(query);
            } else if (query.length === 0) {
                document.getElementById('loadingSpinner').classList.remove('show');
            }
        }, 500);
    });
    
    const customWeightInput = document.getElementById('customWeight');
    if (customWeightInput) {
        customWeightInput.addEventListener('input', function() {
            if (currentProduct) {
                const customWeight = parseFloat(this.value);
                if (customWeight > 0) {
                    calculateNutrition(customWeight);
                    document.querySelectorAll('.serving-btn').forEach(b => b.classList.remove('active'));
                }
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('calculatorModal');
        if (e.target === modal) {
            closeCalculator();
        }
    });
});

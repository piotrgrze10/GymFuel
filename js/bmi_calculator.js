class BMICalculator {
    constructor() {
        this.currentUnit = 'metric';
        this.isLoggedIn = window.isUserLoggedIn || false;
        this.history = this.isLoggedIn ? this.loadHistory() : [];
        this.calculatorType = this.detectCalculatorType();
        this.init();
    }

    detectCalculatorType() {
        // Detect which calculator page we're on
        if (document.getElementById('ffmiPanel') || document.getElementById('ffmiHeight')) {
            return 'ffmi';
        } else if (document.getElementById('bmiPanel') || document.getElementById('height')) {
            return 'bmi';
        }
        return null;
    }

    init() {
        this.cacheElements();
        if (this.calculatorType) {
            this.attachEventListeners();
            if (this.isLoggedIn) {
                this.displayHistory();
            }
        }
    }

    cacheElements() {
        if (this.calculatorType === 'bmi') {
            this.heightInput = document.getElementById('height');
            this.weightInput = document.getElementById('weight');
            this.calculateBtn = document.getElementById('calculateBtn');
            this.errorMessage = document.getElementById('errorMessage');
            this.bmiResult = document.getElementById('bmiResult');
            this.bmiValue = document.getElementById('bmiValue');
            this.categoryBadge = document.getElementById('categoryBadge');
            this.categoryDescription = document.getElementById('categoryDescription');
            this.historyList = document.getElementById('historyList');
            this.unitButtons = document.querySelectorAll('.unit-btn');
        } else if (this.calculatorType === 'ffmi') {
            this.ffmiHeightInput = document.getElementById('ffmiHeight');
            this.ffmiWeightInput = document.getElementById('ffmiWeight');
            this.bodyFatInput = document.getElementById('bodyFat');
            this.calculateFfmiBtn = document.getElementById('calculateFfmiBtn');
            this.ffmiErrorMessage = document.getElementById('ffmiErrorMessage');
            this.ffmiResult = document.getElementById('ffmiResult');
            this.ffmiValue = document.getElementById('ffmiValue');
            this.ffmiCategoryBadge = document.getElementById('ffmiCategoryBadge');
            this.ffmiCategoryDescription = document.getElementById('ffmiCategoryDescription');
            this.historyList = document.getElementById('historyList');
        }
    }

    attachEventListeners() {
        if (this.calculatorType === 'bmi') {
            if (this.calculateBtn) {
                this.calculateBtn.addEventListener('click', () => this.calculateBMI());
            }
            
            if (this.heightInput) {
                this.heightInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.calculateBMI();
                });
                this.heightInput.addEventListener('input', () => this.hideError());
            }
            
            if (this.weightInput) {
                this.weightInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.calculateBMI();
                });
                this.weightInput.addEventListener('input', () => this.hideError());
            }

            if (this.unitButtons && this.unitButtons.length > 0) {
                this.unitButtons.forEach(btn => {
                    btn.addEventListener('click', () => this.switchUnit(btn.dataset.unit));
                });
            }
        } else if (this.calculatorType === 'ffmi') {
            if (this.calculateFfmiBtn) {
                this.calculateFfmiBtn.addEventListener('click', () => this.calculateFFMI());
            }
            
            if (this.ffmiHeightInput) {
                this.ffmiHeightInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.calculateFFMI();
                });
                this.ffmiHeightInput.addEventListener('input', () => this.hideFfmiError());
            }
            
            if (this.ffmiWeightInput) {
                this.ffmiWeightInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.calculateFFMI();
                });
                this.ffmiWeightInput.addEventListener('input', () => this.hideFfmiError());
            }
            
            if (this.bodyFatInput) {
                this.bodyFatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.calculateFFMI();
                });
                this.bodyFatInput.addEventListener('input', () => this.hideFfmiError());
            }
        }
    }
    
    calculateFFMI() {
        if (!this.ffmiHeightInput || !this.ffmiWeightInput || !this.bodyFatInput) return;
        
        const height = parseFloat(this.ffmiHeightInput.value);
        const weight = parseFloat(this.ffmiWeightInput.value);
        const bodyFat = parseFloat(this.bodyFatInput.value);

        if (!this.validateFfmiInputs(height, weight, bodyFat)) {
            return;
        }

        const heightInMeters = height / 100;
        const fatFreeMass = weight * (1 - bodyFat / 100);
        const ffmi = fatFreeMass / (heightInMeters * heightInMeters);

        this.displayFfmiResult(ffmi, height, weight, bodyFat);

        if (this.isLoggedIn) {
            this.saveFfmiToHistory(ffmi, height, weight, bodyFat);
        }
    }
    
    validateFfmiInputs(height, weight, bodyFat) {
        if (!height || !weight || !bodyFat) {
            this.showFfmiError('Please enter height, weight, and body fat percentage.');
            return false;
        }

        if (height <= 0 || weight <= 0 || bodyFat <= 0) {
            this.showFfmiError('All values must be positive numbers.');
            return false;
        }

        if (height < 50 || height > 300) {
            this.showFfmiError('Please enter a valid height (50-300 cm).');
            return false;
        }
        
        if (weight < 20 || weight > 500) {
            this.showFfmiError('Please enter a valid weight (20-500 kg).');
            return false;
        }
        
        if (bodyFat < 1 || bodyFat > 60) {
            this.showFfmiError('Please enter a valid body fat percentage (1-60%).');
            return false;
        }

        return true;
    }
    
    displayFfmiResult(ffmi, height, weight, bodyFat) {
        const ffmiRounded = ffmi.toFixed(1);
        
        const category = this.getFFMICategory(ffmi);

        if (this.ffmiValue) this.ffmiValue.textContent = ffmiRounded;
        if (this.ffmiCategoryBadge) {
            this.ffmiCategoryBadge.textContent = category.name;
            this.ffmiCategoryBadge.className = `category-badge ${category.class}`;
        }
        if (this.ffmiCategoryDescription) {
            this.ffmiCategoryDescription.textContent = category.description;
        }

        this.showFfmiResult();
    }
    
    getFFMICategory(ffmi) {
        if (ffmi < 16) {
            return {
                name: 'Below Average',
                class: 'underweight',
                description: 'Your FFMI is below average. Focus on building muscle mass through resistance training and proper nutrition.'
            };
        } else if (ffmi >= 16 && ffmi < 18) {
            return {
                name: 'Average',
                class: 'normal',
                description: 'Your FFMI is in the average range. Continue with your training to build more muscle mass.'
            };
        } else if (ffmi >= 18 && ffmi < 20) {
            return {
                name: 'Above Average',
                class: 'overweight',
                description: 'Great! Your FFMI is above average. You have good muscle mass development.'
            };
        } else if (ffmi >= 20 && ffmi < 22) {
            return {
                name: 'Superior',
                class: 'obese',
                description: 'Excellent! Your FFMI indicates superior muscle mass. You\'re in great shape!'
            };
        } else {
            return {
                name: 'Excellent',
                class: 'excellent',
                description: 'Outstanding! Your FFMI is in the excellent range. You have exceptional muscle mass development.'
            };
        }
    }
    
    showFfmiResult() {
        if (this.ffmiResult) this.ffmiResult.classList.add('show');
        this.hideFfmiError();
    }
    
    hideFfmiResult() {
        if (this.ffmiResult) this.ffmiResult.classList.remove('show');
    }
    
    showFfmiError(message) {
        if (this.ffmiErrorMessage) {
            this.ffmiErrorMessage.textContent = message;
            this.ffmiErrorMessage.classList.add('show');
        }
        this.hideFfmiResult();
    }
    
    hideFfmiError() {
        if (this.ffmiErrorMessage) {
            this.ffmiErrorMessage.classList.remove('show');
        }
    }
    
    saveFfmiToHistory(ffmi, height, weight, bodyFat) {
        const category = this.getFFMICategory(ffmi);
        const entry = {
            type: 'ffmi',
            value: ffmi.toFixed(1),
            height: height.toFixed(1),
            weight: weight.toFixed(1),
            bodyFat: bodyFat.toFixed(1),
            category: category.name,
            categoryClass: category.class,
            timestamp: new Date().toISOString()
        };

        this.history.unshift(entry);

        if (this.history.length > 3) {
            this.history = this.history.slice(0, 3);
        }

        localStorage.setItem('bmiHistory', JSON.stringify(this.history));

        this.displayHistory();
    }

    switchUnit(unit) {
        if (this.currentUnit === unit || !this.unitButtons) return;

        this.currentUnit = unit;
        
        this.unitButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.unit === unit);
        });

        if (this.heightInput && this.weightInput) {
            if (unit === 'metric') {
                this.heightInput.placeholder = 'Enter height in cm';
                this.weightInput.placeholder = 'Enter weight in kg';
                const units = document.querySelectorAll('.input-unit');
                if (units[0]) units[0].textContent = 'cm';
                if (units[1]) units[1].textContent = 'kg';
            } else {
                this.heightInput.placeholder = 'Enter height in inches';
                this.weightInput.placeholder = 'Enter weight in lbs';
                const units = document.querySelectorAll('.input-unit');
                if (units[0]) units[0].textContent = 'in';
                if (units[1]) units[1].textContent = 'lbs';
            }

            this.heightInput.value = '';
            this.weightInput.value = '';
            this.hideResult();
            this.hideError();
        }
    }

    calculateBMI() {
        if (!this.heightInput || !this.weightInput) return;
        
        const height = parseFloat(this.heightInput.value);
        const weight = parseFloat(this.weightInput.value);

        if (!this.validateInputs(height, weight)) {
            return;
        }

        let bmi;
        if (this.currentUnit === 'metric') {
            const heightInMeters = height / 100;
            bmi = weight / (heightInMeters * heightInMeters);
        } else {
            bmi = (weight / (height * height)) * 703;
        }

        this.displayResult(bmi, height, weight);

        if (this.isLoggedIn) {
            this.saveToHistory(bmi, height, weight);
        }
    }

    validateInputs(height, weight) {
        if (!height || !weight) {
            this.showError('Please enter both height and weight.');
            return false;
        }

        if (height <= 0 || weight <= 0) {
            this.showError('Height and weight must be positive numbers.');
            return false;
        }

        if (this.currentUnit === 'metric') {
            if (height < 50 || height > 300) {
                this.showError('Please enter a valid height (50-300 cm).');
                return false;
            }
            if (weight < 20 || weight > 500) {
                this.showError('Please enter a valid weight (20-500 kg).');
                return false;
            }
        } else {
            if (height < 20 || height > 120) {
                this.showError('Please enter a valid height (20-120 inches).');
                return false;
            }
            if (weight < 44 || weight > 1100) {
                this.showError('Please enter a valid weight (44-1100 lbs).');
                return false;
            }
        }

        return true;
    }

    displayResult(bmi, height, weight) {
        if (!this.bmiValue || !this.categoryBadge || !this.categoryDescription) return;
        
        const bmiRounded = bmi.toFixed(1);
        
        const category = this.getBMICategory(bmi);

        this.bmiValue.textContent = bmiRounded;
        this.categoryBadge.textContent = category.name;
        this.categoryBadge.className = `category-badge ${category.class}`;
        this.categoryDescription.textContent = category.description;

        this.showResult();
    }

    getBMICategory(bmi) {
        if (bmi < 18.5) {
            return {
                name: 'Underweight',
                class: 'underweight',
                description: 'Your BMI indicates that you are underweight. Consider consulting with a healthcare provider for personalized advice.'
            };
        } else if (bmi >= 18.5 && bmi < 25) {
            return {
                name: 'Normal Weight',
                class: 'normal',
                description: 'Great! Your BMI is in the healthy weight range. Keep up your healthy lifestyle!'
            };
        } else if (bmi >= 25 && bmi < 30) {
            return {
                name: 'Overweight',
                class: 'overweight',
                description: 'Your BMI indicates that you are overweight. Consider increasing physical activity and improving diet.'
            };
        } else {
            return {
                name: 'Obese',
                class: 'obese',
                description: 'Your BMI indicates obesity. We recommend consulting with a healthcare provider for a personalized health plan.'
            };
        }
    }

    showResult() {
        if (this.bmiResult) {
            this.bmiResult.classList.add('show');
        }
        this.hideError();
    }

    hideResult() {
        if (this.bmiResult) {
            this.bmiResult.classList.remove('show');
        }
    }

    showError(message) {
        if (this.errorMessage) {
            this.errorMessage.textContent = message;
            this.errorMessage.classList.add('show');
        }
        this.hideResult();
    }

    hideError() {
        if (this.errorMessage) {
            this.errorMessage.classList.remove('show');
        }
    }

    saveToHistory(bmi, height, weight) {
        const category = this.getBMICategory(bmi);
        const entry = {
            type: 'bmi',
            bmi: bmi.toFixed(1),
            height: height.toFixed(1),
            weight: weight.toFixed(1),
            unit: this.currentUnit,
            category: category.name,
            categoryClass: category.class,
            timestamp: new Date().toISOString()
        };

        this.history.unshift(entry);

        if (this.history.length > 3) {
            this.history = this.history.slice(0, 3);
        }

        localStorage.setItem('bmiHistory', JSON.stringify(this.history));

        this.displayHistory();
    }

    loadHistory() {
        const stored = localStorage.getItem('bmiHistory');
        return stored ? JSON.parse(stored) : [];
    }

    displayHistory() {
        if (!this.historyList) return;
        
        // Filter history by calculator type if on separate pages
        let filteredHistory = this.history;
        if (this.calculatorType === 'bmi') {
            filteredHistory = this.history.filter(entry => entry.type === 'bmi' || !entry.type);
        } else if (this.calculatorType === 'ffmi') {
            filteredHistory = this.history.filter(entry => entry.type === 'ffmi');
        }
        
        if (filteredHistory.length === 0) {
            this.historyList.innerHTML = '<p class="no-history">No calculations yet</p>';
            return;
        }

        this.historyList.innerHTML = filteredHistory.map(entry => {
            const date = new Date(entry.timestamp);
            const timeStr = date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            const dateStr = date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });

            if (entry.type === 'ffmi') {
                const unitLabel = `${entry.height} cm / ${entry.weight} kg / ${entry.bodyFat}% BF`;
                return `
                    <div class="history-item">
                        <div class="history-info">
                            <span class="history-bmi">FFMI: ${entry.value}</span>
                            <div class="history-details">
                                ${unitLabel}<br>
                                <small>${dateStr} at ${timeStr}</small>
                            </div>
                        </div>
                        <span class="history-badge ${entry.categoryClass}">${entry.category}</span>
                    </div>
                `;
            } else {
                const unitLabel = entry.unit === 'metric' ? 
                    `${entry.height} cm / ${entry.weight} kg` : 
                    `${entry.height} in / ${entry.weight} lbs`;

                return `
                    <div class="history-item">
                        <div class="history-info">
                            <span class="history-bmi">BMI: ${entry.bmi}</span>
                            <div class="history-details">
                                ${unitLabel}<br>
                                <small>${dateStr} at ${timeStr}</small>
                            </div>
                        </div>
                        <span class="history-badge ${entry.categoryClass}">${entry.category}</span>
                    </div>
                `;
            }
        }).join('');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new BMICalculator();
});


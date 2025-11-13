class BMICalculator {
    constructor() {
        this.currentUnit = 'metric';
        this.isLoggedIn = window.isUserLoggedIn || false;
        this.history = this.isLoggedIn ? this.loadHistory() : [];
        this.init();
    }

    init() {
        this.cacheElements();
        this.attachEventListeners();
        if (this.isLoggedIn) {
            this.displayHistory();
        }
    }

    cacheElements() {
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
    }

    attachEventListeners() {
        this.calculateBtn.addEventListener('click', () => this.calculateBMI());
        
        this.heightInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.calculateBMI();
        });
        
        this.weightInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.calculateBMI();
        });

        this.heightInput.addEventListener('input', () => this.hideError());
        this.weightInput.addEventListener('input', () => this.hideError());

        this.unitButtons.forEach(btn => {
            btn.addEventListener('click', () => this.switchUnit(btn.dataset.unit));
        });
    }

    switchUnit(unit) {
        if (this.currentUnit === unit) return;

        this.currentUnit = unit;
        
        this.unitButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.unit === unit);
        });

        if (unit === 'metric') {
            this.heightInput.placeholder = 'Enter height in cm';
            this.weightInput.placeholder = 'Enter weight in kg';
            document.querySelectorAll('.input-unit')[0].textContent = 'cm';
            document.querySelectorAll('.input-unit')[1].textContent = 'kg';
        } else {
            this.heightInput.placeholder = 'Enter height in inches';
            this.weightInput.placeholder = 'Enter weight in lbs';
            document.querySelectorAll('.input-unit')[0].textContent = 'in';
            document.querySelectorAll('.input-unit')[1].textContent = 'lbs';
        }

        this.heightInput.value = '';
        this.weightInput.value = '';
        this.hideResult();
        this.hideError();
    }

    calculateBMI() {
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
        this.bmiResult.classList.add('show');
        this.hideError();
    }

    hideResult() {
        this.bmiResult.classList.remove('show');
    }

    showError(message) {
        this.errorMessage.textContent = message;
        this.errorMessage.classList.add('show');
        this.hideResult();
    }

    hideError() {
        this.errorMessage.classList.remove('show');
    }

    saveToHistory(bmi, height, weight) {
        const category = this.getBMICategory(bmi);
        const entry = {
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
        
        if (this.history.length === 0) {
            this.historyList.innerHTML = '<p class="no-history">No calculations yet</p>';
            return;
        }

        this.historyList.innerHTML = this.history.map(entry => {
            const date = new Date(entry.timestamp);
            const timeStr = date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            const dateStr = date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });

            const unitLabel = entry.unit === 'metric' ? 
                `${entry.height} cm / ${entry.weight} kg` : 
                `${entry.height} in / ${entry.weight} lbs`;

            return `
                <div class="history-item">
                    <div class="history-info">
                        <span class="history-bmi">${entry.bmi}</span>
                        <div class="history-details">
                            ${unitLabel}<br>
                            <small>${dateStr} at ${timeStr}</small>
                        </div>
                    </div>
                    <span class="history-badge ${entry.categoryClass}">${entry.category}</span>
                </div>
            `;
        }).join('');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new BMICalculator();
});


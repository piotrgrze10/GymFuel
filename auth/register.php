<?php
require_once '../includes/config.php';
redirectIfLoggedIn();


$preview_calories = null;
if (isset($_SESSION['reg_gender']) && isset($_SESSION['reg_age']) && isset($_SESSION['reg_height']) && isset($_SESSION['reg_weight']) && isset($_SESSION['reg_activity_level'])) {
    function calculateBMR($weight, $height, $age, $gender) {
        if ($gender === 'male') {
            return 10 * $weight + 6.25 * $height - 5 * $age + 5;
        } else {
            return 10 * $weight + 6.25 * $height - 5 * $age - 161;
        }
    }
    
    $activity_multipliers = [
        'sedentary' => 1.2,
        'lightly_active' => 1.375,
        'moderately_active' => 1.55,
        'very_active' => 1.725,
        'extra_active' => 1.9
    ];
    
    $preview_bmr = calculateBMR($_SESSION['reg_weight'], $_SESSION['reg_height'], $_SESSION['reg_age'], $_SESSION['reg_gender']);
    $preview_tdee = $preview_bmr * $activity_multipliers[$_SESSION['reg_activity_level']];
    
    $preview_calories = [
        'lose_weight' => round($preview_tdee - 500),
        'maintain' => round($preview_tdee),
        'gain_weight' => round($preview_tdee + 500)
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/auth.css?v=unified-1">
</head>
<body>
    <div class="auth-container narrow">
        <div class="auth-card registration-card">
            
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="progress-steps" id="progress-steps">
                    <span class="step active"><i class="fa-solid fa-1"></i></span>
                    <span class="step"><i class="fa-solid fa-2"></i></span>
                    <span class="step"><i class="fa-solid fa-3"></i></span>
                    <span class="step"><i class="fa-solid fa-4"></i></span>
                    <span class="step"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info" id="step-info">Step 1 of 5 - Create Account</p>
            </div>

            <div class="auth-header">
                <a href="../index.php" class="auth-logo logo-home">
                    <i class="fa-solid fa-fire-flame-curved logo-icon"></i>
                    <span>Gym<span class="blue-text">Fuel</span></span>
                </a>
                <h2 id="step-title">Create Your Account</h2>
                <p id="step-subtitle">Start your fitness journey with us</p>
            </div>

            <div id="error-message" class="alert alert-danger" style="display:none;" role="alert"></div>

            <div class="modal fade" id="successModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                        <div class="modal-body text-center p-5">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);">
                                <i class="fa-solid fa-circle-check" style="color: white; font-size: 2.5rem;"></i>
                            </div>
                            <h2 class="mb-3 fw-bold" style="color: #2c3e50;">Account Created Successfully!</h2>
                            <p class="text-muted mb-4" id="success-message" style="font-size: 0.95rem;"></p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn-auth" onclick="window.location.href='login.php'">
                                    <i class="fa-solid fa-sign-in-alt"></i> Sign In
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="registration-form" class="auth-form" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="reg-step" id="step1">
                    <div class="form-group">
                        <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" id="email" name="email" data-required="1" placeholder="Enter your email" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                        <input type="password" id="password" name="password" data-required="1" placeholder="Create a password (min. 6 characters)" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" data-required="1" placeholder="Confirm your password" autocomplete="new-password">
                    </div>
                </div>

                <div class="reg-step" id="step2" style="display:none;">
                    <div class="form-group">
                        <label for="first_name"><i class="fa-solid fa-user"></i> First Name</label>
                        <input type="text" id="first_name" name="first_name" data-required="2" placeholder="Your first name" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="last_name"><i class="fa-solid fa-user"></i> Last Name</label>
                        <input type="text" id="last_name" name="last_name" data-required="2" placeholder="Your last name" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="gender"><i class="fa-solid fa-venus-mars"></i> Gender</label>
                        <select id="gender" name="gender" data-required="2" autocomplete="off">
                            <option value="">Select your gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="reg-step" id="step3" style="display:none;">
                    <div class="form-group">
                        <label for="age"><i class="fa-solid fa-calendar"></i> Age</label>
                        <input type="number" id="age" name="age" data-required="3" placeholder="Your age" min="13" max="120" autocomplete="off">
                        <small>Must be between 13 and 120 years</small>
                    </div>
                    <div class="form-group">
                        <label for="height"><i class="fa-solid fa-ruler-vertical"></i> Height (cm)</label>
                        <input type="number" id="height" name="height" data-required="3" placeholder="Your height in centimeters" min="50" max="250" step="0.1" autocomplete="off">
                        <small>Enter your height in centimeters (e.g., 175 cm)</small>
                    </div>
                    <div class="form-group">
                        <label for="weight"><i class="fa-solid fa-weight-scale"></i> Weight (kg)</label>
                        <input type="number" id="weight" name="weight" data-required="3" placeholder="Your weight in kilograms" min="20" max="300" step="0.1" autocomplete="off">
                        <small>Enter your weight in kilograms (e.g., 70 kg)</small>
                    </div>
                </div>

                <div class="reg-step" id="step4" style="display:none;">
                    <div class="form-group">
                        <label><i class="fa-solid fa-dumbbell"></i> Select Your Activity Level</label>
                        <div class="activity-options">
                            <div class="activity-card" data-value="sedentary">
                                <i class="fa-solid fa-couch"></i>
                                <h4>Sedentary</h4>
                                <p>Little or no exercise</p>
                                <small>Desk job, minimal movement</small>
                            </div>
                            <div class="activity-card" data-value="lightly_active">
                                <i class="fa-solid fa-walking"></i>
                                <h4>Lightly Active</h4>
                                <p>Light exercise 1-3 days/week</p>
                                <small>30 min light exercise</small>
                            </div>
                            <div class="activity-card" data-value="moderately_active">
                                <i class="fa-solid fa-bicycle"></i>
                                <h4>Moderately Active</h4>
                                <p>Moderate exercise 3-5 days/week</p>
                                <small>45 min moderate exercise</small>
                            </div>
                            <div class="activity-card" data-value="very_active">
                                <i class="fa-solid fa-dumbbell"></i>
                                <h4>Very Active</h4>
                                <p>Hard exercise 6-7 days/week</p>
                                <small>60 min intense training</small>
                            </div>
                            <div class="activity-card" data-value="extra_active">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                                <h4>Extra Active</h4>
                                <p>Very hard exercise & physical job</p>
                                <small>Athlete or manual labor</small>
                            </div>
                        </div>
                        <input type="hidden" id="activity_level" name="activity_level" data-required="4">
                    </div>
                </div>

                <div class="reg-step" id="step5" style="display:none;">
                    <div class="form-group">
                        <label><i class="fa-solid fa-bullseye"></i> Select Your Goal</label>
                        <div class="goal-options">
                            <div class="goal-card" data-value="lose_weight" data-calories="<?php echo $preview_calories ? $preview_calories['lose_weight'] : 0; ?>">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                                <h4>Lose Weight</h4>
                                <p class="goal-calories">~<?php echo $preview_calories ? $preview_calories['lose_weight'] : 0; ?> kcal/day</p>
                                <small>500 calorie deficit for safe weight loss</small>
                            </div>
                            <div class="goal-card" data-value="maintain" data-calories="<?php echo $preview_calories ? $preview_calories['maintain'] : 0; ?>">
                                <i class="fa-solid fa-equals"></i>
                                <h4>Maintain Weight</h4>
                                <p class="goal-calories">~<?php echo $preview_calories ? $preview_calories['maintain'] : 0; ?> kcal/day</p>
                                <small>Maintain your current weight</small>
                            </div>
                            <div class="goal-card" data-value="gain_weight" data-calories="<?php echo $preview_calories ? $preview_calories['gain_weight'] : 0; ?>">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                <h4>Gain Weight</h4>
                                <p class="goal-calories">~<?php echo $preview_calories ? $preview_calories['gain_weight'] : 0; ?> kcal/day</p>
                                <small>500 calorie surplus for muscle gain</small>
                            </div>
                        </div>
                        <input type="hidden" id="goal" name="goal" data-required="5">
                    </div>
                </div>

                <div class="form-group" id="nav-buttons">
                    <button type="button" class="btn-auth btn-secondary" id="prev-btn" style="display:none;">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-auth" id="continue-btn" disabled>
                        <i class="fa-solid fa-arrow-right"></i> Continue
                    </button>
                </div>

                <div class="auth-footer" id="auth-footer" style="display:none;">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 5;

        const steps = {
            1: {
                title: 'Create Your Account',
                subtitle: 'Start your fitness journey with us',
                progress: 20,
                showFooter: true
            },
            2: {
                title: 'Tell Us About Yourself',
                subtitle: 'Basic information to personalize your experience',
                progress: 40
            },
            3: {
                title: 'Physical Information',
                subtitle: 'We need this to calculate your daily caloric needs',
                progress: 60
            },
            4: {
                title: 'Activity Level',
                subtitle: 'How active are you? This helps us calculate your caloric needs',
                progress: 80
            },
            5: {
                title: 'What\'s Your Goal?',
                subtitle: 'Choose your fitness objective to get personalized calorie recommendations',
                progress: 100
            }
        };

        function restoreData() {
            const savedData = JSON.parse(sessionStorage.getItem('reg_form_data') || '{}');
            
            if (savedData.email) document.getElementById('email').value = savedData.email;
            if (savedData.first_name) document.getElementById('first_name').value = savedData.first_name;
            if (savedData.last_name) document.getElementById('last_name').value = savedData.last_name;
            if (savedData.gender) document.getElementById('gender').value = savedData.gender;
            if (savedData.age) document.getElementById('age').value = savedData.age;
            if (savedData.height) document.getElementById('height').value = savedData.height;
            if (savedData.weight) document.getElementById('weight').value = savedData.weight;
            if (savedData.activity_level) {
                document.getElementById('activity_level').value = savedData.activity_level;
                document.querySelector(`.activity-card[data-value="${savedData.activity_level}"]`)?.classList.add('selected');
            }
            if (savedData.goal) {
                document.getElementById('goal').value = savedData.goal;
                document.querySelector(`.goal-card[data-value="${savedData.goal}"]`)?.classList.add('selected');
            }
        }

        function saveData() {
            const data = {
                email: document.getElementById('email').value,
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                gender: document.getElementById('gender').value,
                age: document.getElementById('age').value,
                height: document.getElementById('height').value,
                weight: document.getElementById('weight').value,
                activity_level: document.getElementById('activity_level').value,
                goal: document.getElementById('goal').value
            };
            sessionStorage.setItem('reg_form_data', JSON.stringify(data));
        }

        window.addEventListener('beforeunload', function() {
            try {
                const nav = performance.getEntriesByType('navigation')[0];
                if (nav && nav.type === 'reload') {
                    sessionStorage.removeItem('reg_form_data');
                }
            } catch(_) {}
        });

        function updateStep(step) {
            currentStep = step;
            
            document.querySelectorAll('.reg-step').forEach(s => s.style.display = 'none');
            
            document.getElementById(`step${step}`).style.display = 'block';
            
            document.getElementById('step-title').textContent = steps[step].title;
            document.getElementById('step-subtitle').textContent = steps[step].subtitle;
            
            document.getElementById('progress-fill').style.width = steps[step].progress + '%';
            document.getElementById('step-info').textContent = `Step ${step} of ${totalSteps} - ${steps[step].title}`;
            
            document.querySelectorAll('#progress-steps .step').forEach((el, idx) => {
                if (idx < step) {
                    el.className = 'step active';
                    el.innerHTML = '<i class="fa-solid fa-check"></i>';
                } else if (idx === step - 1) {
                    el.className = 'step active';
                } else {
                    el.className = 'step';
                }
            });
            
            document.querySelectorAll('[data-required]').forEach(input => {
                const requiredStep = parseInt(input.getAttribute('data-required'));
                if (requiredStep === step) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
            
            document.getElementById('prev-btn').style.display = step > 1 ? 'inline-flex' : 'none';
            document.getElementById('continue-btn').innerHTML = step === totalSteps 
                ? '<i class="fa-solid fa-check"></i> Complete Registration'
                : '<i class="fa-solid fa-arrow-right"></i> Continue';
            
            document.getElementById('auth-footer').style.display = step === 1 ? 'block' : 'none';
            
            validateCurrentStep();
        }

        function validateStep1() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            const passValid = password.length >= 6;
            const match = password && password === confirm;
            
            return emailValid && passValid && match;
        }

        function validateStep2() {
            const first = document.getElementById('first_name').value.trim();
            const last = document.getElementById('last_name').value.trim();
            const gender = document.getElementById('gender').value;
            return first && last && gender;
        }

        function validateStep3() {
            const age = parseInt(document.getElementById('age').value, 10);
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);
            
            const ageOk = !isNaN(age) && age >= 13 && age <= 120;
            const heightOk = !isNaN(height) && height >= 50 && height <= 250;
            const weightOk = !isNaN(weight) && weight >= 20 && weight <= 300;
            
            return ageOk && heightOk && weightOk;
        }

        function validateStep4() {
            return document.getElementById('activity_level').value !== '';
        }

        function validateStep5() {
            return document.getElementById('goal').value !== '';
        }

        function validateCurrentStep() {
            let valid = false;
            switch(currentStep) {
                case 1: valid = validateStep1(); break;
                case 2: valid = validateStep2(); break;
                case 3: valid = validateStep3(); break;
                case 4: valid = validateStep4(); break;
                case 5: valid = validateStep5(); break;
            }
            document.getElementById('continue-btn').disabled = !valid;
        }

        document.getElementById('email').addEventListener('input', () => { saveData(); validateCurrentStep(); });
        document.getElementById('password').addEventListener('input', () => { validateCurrentStep(); });
        document.getElementById('confirm_password').addEventListener('input', () => { validateCurrentStep(); });
        document.getElementById('first_name').addEventListener('input', () => { saveData(); validateCurrentStep(); });
        document.getElementById('last_name').addEventListener('input', () => { saveData(); validateCurrentStep(); });
        document.getElementById('gender').addEventListener('change', () => { saveData(); validateCurrentStep(); });
        document.getElementById('age').addEventListener('input', () => { saveData(); validateCurrentStep(); });
        document.getElementById('height').addEventListener('input', () => { saveData(); validateCurrentStep(); });
        document.getElementById('weight').addEventListener('input', () => { saveData(); validateCurrentStep(); });

        document.querySelectorAll('.activity-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.activity-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('activity_level').value = this.dataset.value;
                saveData();
                validateCurrentStep();
            });
        });

        document.querySelectorAll('.goal-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.goal-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('goal').value = this.dataset.value;
                saveData();
                validateCurrentStep();
            });
        });

        document.getElementById('prev-btn').addEventListener('click', () => {
            if (currentStep > 1) {
                updateStep(currentStep - 1);
            }
        });

        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            
            const formData = new FormData(this);
            formData.append('action', `step${currentStep}`);
            
            const btn = document.getElementById('continue-btn');
            const originalHtml = btn.innerHTML;
            if (currentStep === totalSteps) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Account...';
            }
            
            try {
                const response = await fetch('handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (currentStep === totalSteps) {
                        const successMessage = document.getElementById('success-message');
                        successMessage.innerHTML = `
                            <div style="background: #f8f9fa; padding: 1.25rem; border-radius: 10px; text-align: left; max-width: 400px; margin: 0 auto;">
                                <strong style="color: #495057;">Your calculated values:</strong><br><br>
                                <div style="margin-bottom: 0.5rem;">
                                    <i class="fa-solid fa-fire text-danger"></i> <strong>BMR:</strong> <span style="color: #0d6efd;">${result.bmr.toFixed(0)} kcal</span>
                                </div>
                                <div>
                                    <i class="fa-solid fa-bullseye text-primary"></i> <strong>Daily Target:</strong> <span style="color: #0d6efd;">${result.calories.toFixed(0)} kcal</span>
                                </div>
                            </div>
                        `;
                        
                        sessionStorage.removeItem('reg_form_data');
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    } else {
                        updateStep(currentStep + 1);
                    }
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    errorDiv.textContent = result.error || 'An error occurred';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            }
        });

        restoreData();
        updateStep(1);
    </script>
</body>
</html>

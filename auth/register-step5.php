<?php
require_once '../includes/config.php';
redirectIfLoggedIn();

if (!isset($_SESSION['reg_step']) || $_SESSION['reg_step'] != 4) {
    header('Location: auth/register.php');
    exit();
}

$preview_data = [
    'gender' => $_SESSION['reg_gender'] ?? 'male',
    'age' => $_SESSION['reg_age'] ?? 25,
    'height' => $_SESSION['reg_height'] ?? 175,
    'weight' => $_SESSION['reg_weight'] ?? 70,
    'activity_level' => $_SESSION['reg_activity_level'] ?? 'moderately_active'
];

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

$preview_bmr = calculateBMR($preview_data['weight'], $preview_data['height'], $preview_data['age'], $preview_data['gender']);
$preview_tdee = $preview_bmr * $activity_multipliers[$preview_data['activity_level']];

$preview_calories = [
    'lose_weight' => round($preview_tdee - 500),
    'maintain' => round($preview_tdee),
    'gain_weight' => round($preview_tdee + 500)
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 5 of 5 - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card registration-card">
            
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 100%"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info">Step 5 of 5 - Set Your Goal</p>
            </div>

            <div class="auth-header">
                <h2>What's Your Goal?</h2>
                <p>Choose your fitness objective to get personalized calorie recommendations</p>
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
                                <button type="button" class="btn btn-primary btn-lg px-4" onclick="window.location.href='login.php'" style="border-radius: 8px;">
                                    <i class="fa-solid fa-sign-in-alt"></i> Sign In
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="registration-form" class="auth-form">
                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-bullseye"></i> Select Your Goal
                    </label>
                    <div class="goal-options">
                        <div class="goal-card" data-value="lose_weight" data-calories="<?php echo $preview_calories['lose_weight']; ?>">
                            <i class="fa-solid fa-arrow-trend-down"></i>
                            <h4>Lose Weight</h4>
                            <p class="goal-calories">~<?php echo $preview_calories['lose_weight']; ?> kcal/day</p>
                            <small>500 calorie deficit for safe weight loss</small>
                        </div>
                        <div class="goal-card" data-value="maintain" data-calories="<?php echo $preview_calories['maintain']; ?>">
                            <i class="fa-solid fa-equals"></i>
                            <h4>Maintain Weight</h4>
                            <p class="goal-calories">~<?php echo $preview_calories['maintain']; ?> kcal/day</p>
                            <small>Maintain your current weight</small>
                        </div>
                        <div class="goal-card" data-value="gain_weight" data-calories="<?php echo $preview_calories['gain_weight']; ?>">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            <h4>Gain Weight</h4>
                            <p class="goal-calories">~<?php echo $preview_calories['gain_weight']; ?> kcal/day</p>
                            <small>500 calorie surplus for muscle gain</small>
                        </div>
                    </div>
                    <input type="hidden" id="goal" name="goal" required>
                </div>

                <div class="form-group">
                    <button type="button" class="btn-auth btn-secondary" onclick="window.location.href='auth/register-step4.php'">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-auth" id="complete-btn" disabled>
                        <i class="fa-solid fa-check"></i> Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const goalCards = document.querySelectorAll('.goal-card');
        const goalInput = document.getElementById('goal');
        const completeBtn = document.getElementById('complete-btn');
        
        goalCards.forEach(card => {
            card.addEventListener('click', function() {
                goalCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                goalInput.value = this.dataset.value;
                completeBtn.disabled = false;
            });
        });
        
        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            
            if (!goalInput.value) {
                errorDiv.textContent = 'Please select a goal';
                errorDiv.style.display = 'block';
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'step5');
            
            completeBtn.disabled = true;
            completeBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Account...';
            
            try {
                const response = await fetch('handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {

                    const successMessage = document.getElementById('success-message');
                    successMessage.innerHTML = `
                        <div style="background: #f8f9fa; padding: 1.25rem; border-radius: 10px; text-align: left; max-width: 400px; margin: 0 auto;">
                            <strong style="color: #495057;">Your calculated values:</strong><br><br>
                            <div style="margin-bottom: 0.5rem;">
                                <i class="fa-solid fa-fire text-danger"></i> <strong>BMR (Basal Metabolic Rate):</strong> <span style="color: #0d6efd;">${result.bmr.toFixed(0)} kcal</span>
                            </div>
                            <div>
                                <i class="fa-solid fa-bullseye text-primary"></i> <strong>Daily Calorie Target:</strong> <span style="color: #0d6efd;">${result.calories.toFixed(0)} kcal</span>
                            </div>
                        </div>
                    `;
                    
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    completeBtn.disabled = false;
                    completeBtn.innerHTML = '<i class="fa-solid fa-check"></i> Complete Registration';
                    errorDiv.textContent = result.error || 'An error occurred';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                completeBtn.disabled = false;
                completeBtn.innerHTML = '<i class="fa-solid fa-check"></i> Complete Registration';
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>


<?php
require_once '../includes/config.php';
redirectIfLoggedIn();

if (!isset($_SESSION['reg_step']) || $_SESSION['reg_step'] != 3) {
    header('Location: auth/register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 4 of 5 - GymFuel</title>
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
                    <div class="progress-fill" style="width: 80%"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-4"></i></span>
                    <span class="step"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info">Step 4 of 5 - Activity Level</p>
            </div>

            <div class="auth-header">
                <h2>Activity Level</h2>
                <p>How active are you? This helps us calculate your caloric needs</p>
            </div>

            <div id="error-message" class="alert alert-danger" style="display:none;" role="alert"></div>

            <form id="registration-form" class="auth-form">
                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-dumbbell"></i> Select Your Activity Level
                    </label>
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
                    <input type="hidden" id="activity_level" name="activity_level" required>
                </div>

                <div class="form-group">
                    <button type="button" class="btn-auth btn-secondary" onclick="window.location.href='auth/register-step3.php'">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-auth" id="continue-btn" disabled>
                        Continue <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const activityCards = document.querySelectorAll('.activity-card');
        const activityInput = document.getElementById('activity_level');
        const continueBtn = document.getElementById('continue-btn');
        
        activityCards.forEach(card => {
            card.addEventListener('click', function() {
                activityCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                activityInput.value = this.dataset.value;
                continueBtn.disabled = false;
            });
        });
        
        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            
            if (!activityInput.value) {
                errorDiv.textContent = 'Please select an activity level';
                errorDiv.style.display = 'block';
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'step4');
            
            try {
                const response = await fetch('handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorDiv.textContent = result.error || 'An error occurred';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>


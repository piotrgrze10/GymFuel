<?php
require_once '../includes/config.php';
redirectIfLoggedIn();

if (!isset($_SESSION['reg_step']) || $_SESSION['reg_step'] != 2) {
    header('Location: auth/register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 3 of 5 - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/auth.css?v=register-narrow-1">
</head>
<body>
    <div class="auth-container narrow">
        <div class="auth-card registration-card">
            
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-3"></i></span>
                    <span class="step"><i class="fa-solid fa-4"></i></span>
                    <span class="step"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info">Step 3 of 5 - Physical Metrics</p>
            </div>

            <div class="auth-header">
                <h2>Physical Information</h2>
                <p>We need this to calculate your daily caloric needs</p>
            </div>

            <div id="error-message" class="alert alert-danger" style="display:none;" role="alert"></div>

            <form id="registration-form" class="auth-form">
                <div class="form-group">
                    <label for="age">
                        <i class="fa-solid fa-calendar"></i> Age
                    </label>
                    <input type="number" id="age" name="age" required placeholder="Your age" min="13" max="120">
                    <small>Must be between 13 and 120 years</small>
                </div>

                <div class="form-group">
                    <label for="height">
                        <i class="fa-solid fa-ruler-vertical"></i> Height (cm)
                    </label>
                    <input type="number" id="height" name="height" required placeholder="Your height in centimeters" min="50" max="250" step="0.1">
                    <small>Enter your height in centimeters (e.g., 175 cm)</small>
                </div>

                <div class="form-group">
                    <label for="weight">
                        <i class="fa-solid fa-weight-scale"></i> Weight (kg)
                    </label>
                    <input type="number" id="weight" name="weight" required placeholder="Your weight in kilograms" min="20" max="300" step="0.1">
                    <small>Enter your weight in kilograms (e.g., 70 kg)</small>
                </div>

                <div class="form-group">
                    <button type="button" class="btn-auth btn-secondary" onclick="window.location.href='auth/register-step2.php'">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-auth">
                        Continue <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            
            const formData = new FormData(this);
            formData.append('action', 'step3');
            
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


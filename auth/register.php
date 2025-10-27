<?php
require_once '../includes/config.php';
redirectIfLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 1 of 5 - GymFuel</title>
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
                    <div class="progress-fill" style="width: 20%"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active"><i class="fa-solid fa-1"></i></span>
                    <span class="step"><i class="fa-solid fa-2"></i></span>
                    <span class="step"><i class="fa-solid fa-3"></i></span>
                    <span class="step"><i class="fa-solid fa-4"></i></span>
                    <span class="step"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info">Step 1 of 5 - Create Account</p>
            </div>

            <div class="auth-header">
                <a href="../index.html" class="auth-logo">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                    <span>Gym<span class="blue-text">Fuel</span></span>
                </a>
                <h2>Create Your Account</h2>
                <p>Start your fitness journey with us</p>
            </div>

            <div id="error-message" class="alert alert-danger" style="display:none;" role="alert"></div>

            <form id="registration-form" class="auth-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fa-solid fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Create a password (min. 6 characters)">
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fa-solid fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-auth">
                        <i class="fa-solid fa-arrow-right"></i> Continue
                    </button>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>
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
            formData.append('action', 'step1');
            
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


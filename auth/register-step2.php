<?php
require_once '../includes/config.php';
redirectIfLoggedIn();

if (!isset($_SESSION['reg_step']) || $_SESSION['reg_step'] != 1) {
    header('Location: auth/register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 2 of 5 - GymFuel</title>
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
                    <div class="progress-fill" style="width: 40%"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active"><i class="fa-solid fa-check"></i></span>
                    <span class="step active"><i class="fa-solid fa-2"></i></span>
                    <span class="step"><i class="fa-solid fa-3"></i></span>
                    <span class="step"><i class="fa-solid fa-4"></i></span>
                    <span class="step"><i class="fa-solid fa-5"></i></span>
                </div>
                <p class="step-info">Step 2 of 5 - Personal Information</p>
            </div>

            <div class="auth-header">
                <h2>Tell Us About Yourself</h2>
                <p>Basic information to personalize your experience</p>
            </div>

            <div id="error-message" class="alert alert-danger" style="display:none;" role="alert"></div>

            <form id="registration-form" class="auth-form">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fa-solid fa-user"></i> First Name
                    </label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Your first name">
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fa-solid fa-user"></i> Last Name
                    </label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Your last name">
                </div>

                <div class="form-group">
                    <label for="gender">
                        <i class="fa-solid fa-venus-mars"></i> Gender
                    </label>
                    <select id="gender" name="gender" required>
                        <option value="">Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="button" class="btn-auth btn-secondary" onclick="window.location.href='auth/register.php'">
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
            formData.append('action', 'step2');
            
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


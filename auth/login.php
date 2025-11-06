<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            header('Location: ../dashboard.php');
            exit();
        } else {
            $error = $result['error'];
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Login - GymFuel</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/897067be39.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/auth.css?v=login-split-4">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card split">
            <div class="auth-left">
                <div class="auth-header">
                <a href="../index.php" class="auth-logo logo-home">
                    <i class="fa-solid fa-fire-flame-curved logo-icon"></i>
                    <span>Gym<span class="blue-text">Fuel</span></span>
                </a>
                <h2>Welcome Back</h2>
                <p>Sign in to continue your fitness journey</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="email">
                            <i class="fa-solid fa-envelope"></i> Email
                        </label>
                        <input type="email" id="email" name="email" required autofocus autocomplete="email" inputmode="email">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fa-solid fa-lock"></i> Password
                        </label>
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-auth">
                            <i class="fa-solid fa-sign-in-alt"></i> Sign In
                        </button>
                    </div>

                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php">Create one</a></p>
                    </div>
                </form>
            </div>
            <div class="auth-right">
                <img src="../img/woman_login.png" alt="GymFuel Login" loading="eager" decoding="sync">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


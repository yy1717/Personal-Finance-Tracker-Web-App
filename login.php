<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {

        $stmt = $pdo->prepare("SELECT userID, userName, password_hash FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {

            session_regenerate_id(true); 

            $_SESSION['user_id']   = $user['userID'];
            $_SESSION['user_name'] = $user['userName'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meow Finance - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="auth-container">
        
        <div class="register-wrapper">
            
            <div class="login-left-form">
                <h2>Welcome Back!</h2>

                <?php if (!empty($error)): ?>
                    <p style="color: red; text-align: center; font-size: 1.3rem; margin-top: 0;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="example@meow.com" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="create-btn">Login</button>
                </form>

                <p class="login-link">
                    Don't have an account yet? 
                    <a href="register.php" style="color: var(--accent-pink); font-weight: bold;">Click here to register</a>.
                </p>
            </div>

            <div class="login-right-decor">
                <img src="images/register.webp" alt="Writing Cat" class="cat-logo" style="width: 200px; border-radius: 20px;">
                
                <h1>Meow Finance</h1>
                <p style="font-size: 1.5rem;">Let's manage your fish treats!</p>
            </div>

        </div>
    </div>

</body>
</html>
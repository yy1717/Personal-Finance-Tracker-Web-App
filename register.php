<?php
require_once 'db.php';

$message = "";
$msgColor = "red";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    }
    else {
        $checkSql = "SELECT userID FROM Users WHERE email = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            $message = "Email already registered.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Users (userName, email, password_hash)
                    VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$username, $email, $passwordHash])) {
                $message = "Registration successful. You may now login.";
                $msgColor = "green";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="register-wrapper">
            <div class="register-left">
            <img src="images/register.webp" alt="Writing Cat" class="cat-logo" style="width: 200px; border-radius: 20px;">
            <h1> Personal Finance Tracker </h1>
                <p style="font-size: 1.5rem;"> Join the cutest finance community! 🐾 </p>
            </div>

            <div class="register-right">
                <h2>User Registration</h2>

                <?php if (!empty($message)): ?>
                    <p style="color: <?php echo $msgColor; ?>; text-align: center; font-size: 1.4rem; margin-top:0;">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>

                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="example@meow.com" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder="Min 6 characters" required>
                        <small id="passwordStrength" style="display:block; margin-top: 5px; font-size: 1.2rem;"></small>
                    </div>

                    <div class="input-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>

                    <button type="submit" class="create-btn">Create Account</button>
                </form>

                <p class="login-link">
                    Already have an account? <a href="login.php" style="color: var(--accent-pink); font-weight: bold;">Login here</a>
                </p>
            </div>
        </div>
    </div>

<script>
const passwordInput = document.getElementById("password");
const strengthText = document.getElementById("passwordStrength");

passwordInput.addEventListener("input", () => {
    const pwd = passwordInput.value;

    if (pwd.length === 0) {
        strengthText.textContent = "";
        return;
    }

    if (pwd.length < 6) {
        strengthText.textContent = "Password too short";
        strengthText.style.color = "red";
    }
    else if (/[0-9]/.test(pwd) && /[a-zA-Z]/.test(pwd)) {
        strengthText.textContent = "Strong password";
        strengthText.style.color = "green";
    }
    else {
        strengthText.textContent = "Medium password";
        strengthText.style.color = "orange";
    }
});
</script>

</body> 
</html>

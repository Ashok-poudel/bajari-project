<?php
include 'backend/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($userId, $userName, $hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userName;
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "Email not registered";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
<img src="assets/images/photos/logo.png" class="auth-logo">
<h2>Login</h2>

<p id="formError" class="form-error"><?php echo $error; ?></p>

<form id="loginForm" method="POST" action="login.php" novalidate>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>

<div class="social-login">
<button class="google">Continue with Google</button>
<button class="github">Continue with GitHub</button>
<button class="facebook">Continue with Facebook</button>
</div>

<p>Don't have an account? <a href="signup.php">Signup</a></p>
</div>
</body>
</html>
<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare error/success
    $_SESSION['error'] = '';
    $_SESSION['success'] = '';

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful. You can now log in.";
            header("Location: account_created.php");
            exit();
        } else {
            $_SESSION['error'] = "Something went wrong. Try again.";
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect to avoid resubmission
    header("Location: signup.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup - DevNotes</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
    form { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
    input, button { display: block; width: 100%; margin-bottom: 1rem; padding: 0.8rem; font-size: 1rem; }
    button { background: #28a745; color: white; border: none; cursor: pointer; }
    .error { color: red; margin-bottom: 1rem; }
    .success { color: green; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <form action="signup.php" method="POST">
    <h2>Create Account</h2>
    <?php
      if (!empty($_SESSION['error'])) {
          echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
          unset($_SESSION['error']);
      }
      if (!empty($_SESSION['success'])) {
          echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
          unset($_SESSION['success']);
      }
    ?>
    <input type="email" name="email" placeholder="Email address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
  </form>
</body>
</html>


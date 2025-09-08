<?php
session_start();
require 'db.php';

$error = "";

if (isset($_SESSION['user_id'])) {
    // If already logged in, redirect directly
    header("Location: dashboard.php");
    exit();
}

// Only show success message once
$success = "";
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // remove message after showing
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            header("Location: login.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - DevNotes</title>
  <style>
    body {
        font-family: Arial;
        background: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    form {
        background: white;
        padding: 2.5rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 300px;
    }
    input, button {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
        padding: 0.8rem;
        font-size: 1rem;
    }
    button {
        background: #00a82d;
        color: white;
        border: none;
        cursor: pointer;
    }
    .error {
        color: red;
        margin-bottom: 1rem;
    }
    .success {
        color: green;
        margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <form action="login.php" method="POST">
    <h2>Login to DevNotes</h2>

    <?php if (!empty($success)): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Email address" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
  <title>Account Created</title>
  <style>
    body { 
        font-family: Arial; 
        background: #f0f0f0; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh; 
    }
    .box {
        background: white; 
        padding: 2rem; 
        border-radius: 8px; 
        text-align: center; 
        box-shadow: 0 0 10px rgba(0,0,0,0.1); 
    }
    .success { 
        color: green; 
        margin-bottom: 1rem; 
    }
    a.button {
      padding: 0.6rem 1.2rem;
      background: #07450C;
      color: white;
      text-decoration: none;
      border-radius: 5px; 
      display: inline-block;
      margin: 0.5rem;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2 class="success">Account created successfully!</h2> 
    <a href="login.php" class="button">Go to Login</a>
  </div>
</body>
</html>

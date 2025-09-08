<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'No Name';
$university = $_SESSION['university'] ?? 'Not set';
$semester = $_SESSION['semester'] ?? 'Not set';
$profile_pic = $_SESSION['profile_pic'] ?? 'user_images/default.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Profile</title>
  <style>
    
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: cornsilk;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .profile-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
      width: 350px;
    }
    .profile-pic {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid green;
      margin-bottom: 20px;
    }
    h1 {
      margin: 0 0 10px 0;
      color: green;
      font-size: 26px;
    }
    p {
      margin: 5px 0;
      font-size: 18px;
      color: #444;
    }
    a.edit-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 25px;
      background: green;
      color: white;
      text-decoration: none; 
      border-radius: 8px;
      font-weight: 600; 
      transition: background 0.3s;
    }
    a.edit-btn:hover {
      background: darkgreen;
    }
    a.home-btn {
      margin-top: 30px;
      padding: 10px 25px;
      background: #052a00;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: background 0.3s;
    }
    a.home-btn:hover {
      background: #052a00;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <img class="profile-pic" src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" />
    <h1><?= htmlspecialchars($fullname) ?></h1>
    <p><strong>University:</strong> <?= htmlspecialchars($university) ?></p>
    <p><strong>Semester:</strong> <?= htmlspecialchars($semester) ?></p>
    <a href="edit_profile.php" class="edit-btn">✏️ Edit Profile</a>
  </div>

  <a href="dashboard.php" class="home-btn">🏠 Home</a>

</body>
</html>

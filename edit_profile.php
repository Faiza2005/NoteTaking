<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$sql = "SELECT fullname, university, semester, profile_pic FROM user_details WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $fullname = trim($_POST['fullname']);
  $university = trim($_POST['university']);
  $semester = trim($_POST['semester']);
  $profile_pic = $user['profile_pic']; // default existing pic

  // Validate inputs (simple example)
  if (empty($fullname) || empty($university) || empty($semester)) {
    $error = "Please fill in all required fields.";
  } else {
    // Handle profile picture upload if a file was chosen
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $file_tmp = $_FILES['profile_pic']['tmp_name'];
      $file_type = mime_content_type($file_tmp);

      if (!in_array($file_type, $allowed_types)) {
        $error = "Only JPG, PNG, and GIF images are allowed for profile picture.";
      } else {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_filename = "user_images/" . $user_id . "_" . time() . "." . $ext;

        if (!move_uploaded_file($file_tmp, $new_filename)) {
          $error = "Failed to upload profile picture.";
        } else {
          $profile_pic = $new_filename;
        }
      }
    }

    // If no errors, update DB
    if ($error === "") {
      $update_sql = "UPDATE user_details SET fullname=?, university=?, semester=?, profile_pic=? WHERE user_id=?";
      $update_stmt = $conn->prepare($update_sql);
      $update_stmt->bind_param("ssssi", $fullname, $university, $semester, $profile_pic, $user_id);

      if ($update_stmt->execute()) {
        // Update session data
        $_SESSION['fullname'] = $fullname;
        $_SESSION['university'] = $university;
        $_SESSION['semester'] = $semester;
        $_SESSION['profile_pic'] = $profile_pic;

        $update_stmt->close();
        $conn->close();

        header("Location: profile.php"); // Redirect to profile display page
        exit();
      } else {
        $error = "Database update failed. Please try again.";
      }
      $update_stmt->close();
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: cornsilk;
      margin: 0; padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #07450C;
      margin-bottom: 20px;
      text-align: center;
    }
    label {
      font-weight: 600;
      display: block;
      margin-bottom: 8px;
      margin-top: 15px;
    }
    input[type="text"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    .profile-pic-preview {
      margin-top: 15px;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover; 
      border: 3px solid #07450C;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
    .btn {
      background: #07450C;
      color: white;
      padding: 12px 0;
      margin-top: 25px;
      width: 100%;
      border: none;
      border-radius: 7px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background: #07450C;
    }
    .error-msg {
      color: red;
      margin-bottom: 15px;
      font-weight: 700;
      text-align: center;
    }
    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #07450C;
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>✏️ Edit Profile</h2>

    <?php if (!empty($error)): ?>
      <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <label for="fullname">Full Name:</label>
      <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

      <label for="university">University:</label>
      <input type="text" id="university" name="university" value="<?= htmlspecialchars($user['university'] ?? '') ?>" required>

      <label for="semester">Semester:</label>
      <input type="text" id="semester" name="semester" value="<?= htmlspecialchars($user['semester'] ?? '') ?>" required>

      <label for="profile_pic">Profile Picture:</label>
      <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

      <?php if (!empty($user['profile_pic'])): ?>
        <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Current Profile Picture" class="profile-pic-preview" />
      <?php endif; ?>

      <button type="submit" class="btn">Save Changes</button>
    </form>

    <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
  </div>
</body>
</html>

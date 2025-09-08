<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'No Name';
$university = $_SESSION['university'] ?? '';
$semester = $_SESSION['semester'] ?? '';
$profile_pic = $_SESSION['profile_pic'] ?? 'user_images/default.png';

// Fetch notes
$notes_sql = "SELECT title, uploaded_at FROM notes WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 5";
$notes_stmt = $conn->prepare($notes_sql);
$notes_stmt->bind_param("i", $user_id);
$notes_stmt->execute();
$notes_result = $notes_stmt->get_result();
$notes = $notes_result->fetch_all(MYSQLI_ASSOC);
$notes_stmt->close();

// Fetch quizzes
$quiz_sql = "SELECT subject, score, total_questions, quiz_date FROM quiz_results WHERE user_id = ? ORDER BY quiz_date DESC LIMIT 5";
$quiz_stmt = $conn->prepare($quiz_sql);
$quiz_stmt->bind_param("i", $user_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();
$quizzes = $quiz_result->fetch_all(MYSQLI_ASSOC);
$quiz_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>DevNotes Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Reset */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fef8e7;
      color: #333;
      transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
      background: #121212;
      color: #eee;
    }

    /* Layout */
    .container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #000;
      color:  #5c8a00;
      padding: 20px 15px;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      box-shadow: 2px 0 5px rgba(0,0,0,0.7);
      display: flex;
      flex-direction: column;
      z-index: 100;
    }
    .sidebar .logo {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
      letter-spacing: 1.5px;
      color:  #5c8a00;
      user-select: none;
    }
    .sidebar a {
      color: #5c8a00;
      text-decoration: none;
      margin: 10px 0;
      padding: 12px 15px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      transition: background 0.3s, color 0.3s;
      user-select: none;
    }
    .sidebar a:hover {
      background: #222;
      color: #5c8a00;
    }

    /* Main content */
    .main {
      margin-left: 220px;
      padding: 20px 40px;
      flex: 1;
    }

    /* Top bar */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: cornsilk;
      padding: 10px 20px;
      position: sticky;
      top: 0;
      z-index: 90;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      user-select: none;
    }

    /* Search box */
    .search-box {
      flex-grow: 1;
      max-width: 500px;
      margin: 0 auto;
    }
    .search-box input {
      width: 100%;
      padding: 10px 16px;
      border-radius: 20px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    /* Right icons */
    .right-icons {
      display: flex;
      align-items: center;
      gap: 20px;
      position: relative;
    }
    .right-icons i {
      font-size: 22px;
      cursor: pointer;
      color: #555;
      transition: color 0.3s;
    }
    .right-icons i:hover {
      color: #5c8a00;
    }

    /* Notification panel */
    .notif-panel {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      background: white;
      color: black;
      padding: 12px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      width: 280px;
      font-size: 15px;
      font-weight: 600;
      user-select: none;
      z-index: 1000;
    }
    .notif-panel.show {
      display: block;
    }
    body.dark-mode .notif-panel {
      background: #2c2c2c;
      color: white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.8);
    }
    .notif-panel ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .notif-panel ul li {
      padding: 10px 0;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    body.dark-mode .notif-panel ul li {
      border-bottom: 1px solid #444;
    }
    .notif-panel ul li:last-child {
      border-bottom: none;
    }

    /* Profile dropdown */
    .profile-dropdown {
      position: relative;
    }
    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #5c8a00;
      cursor: pointer;
      user-select: none;
    }
    .profile-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 50px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      width: 180px;
      font-weight: 600;
      user-select: none;
      z-index: 1000;
    }
    .profile-menu a {
      display: block;
      padding: 12px 20px;
      color: black;
      text-decoration: none;
      border-bottom: 1px solid #eee;
      transition: background 0.3s;
    }
    .profile-menu a:last-child {
      border-bottom: none;
    }
    .profile-menu a:hover {
      background: #5c8a00;
      color: black;
    }
    body.dark-mode .profile-menu {
      background: #2c2c2c;
      color: white;
    }
    body.dark-mode .profile-menu a {
      color: white;
      border-bottom: 1px solid #444;
    }
    body.dark-mode .profile-menu a:hover {
      background: #5c8a00;
      color: white;
    }

    /* Content sections */
    h2.section-title {
      color: #5c8a00;
      margin-bottom: 10px;
      user-select: none;
    }
    .section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 0 12px rgba(0,0,0,0.05);
      user-select: none;
    }
    body.dark-mode .section {
      background: #222;
      box-shadow: 0 0 12px rgba(0,0,0,0.8);
    }
    ul.list-items {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }
    ul.list-items li {
      padding: 12px;
      border-bottom: 1px solid #eee;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      user-select: text;
    }
    ul.list-items li:last-child {
      border-bottom: none;
    }
    body.dark-mode ul.list-items li {
      border-bottom: 1px solid #444;
    }
    .note-title {
      font-weight: 700;
    }
    .quiz-score {
      color: green ;
    }

    /* Profile info box */
    .profile-info {
      background: #e9f0fe;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      user-select: none;
    }
    .profile-info img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #5c8a00;
      margin-bottom: 10px;
    }
    .profile-info h3 {
      margin: 10px 0 5px 0;
      color: #5c8a00;
    }
    .profile-info p {
      margin: 5px 0;
      font-weight: 600;
    }

    /* Responsive */
    @media(max-width: 768px) {
      .container {
        flex-direction: column;
      }
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        box-shadow: none;
        display: flex;
        justify-content: center;
        gap: 15px;
      }
      .main {
        margin-left: 0;
        padding: 15px 10px;
      }
      .top-bar {
        flex-wrap: wrap;
        gap: 10px;
      }
      .search-box {
        max-width: 100%;
        flex-grow: 1;
        order: 2;
      }
      .right-icons {
        order: 1;
        gap: 15px;
      }
      .profile-menu {
        right: 10px;
        top: 60px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Sidebar -->
  <nav class="sidebar" aria-label="Main Navigation">
    <div class="logo" tabindex="0">DevNotes</div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="upload.php"><i class="fas fa-upload"></i> Upload</a>
    <a href="downloads.php"><i class="fas fa-download"></i> Downloads</a>
    <a href="books.php"><i class="fas fa-book"></i> Books</a>
    <a href="quiz.php"><i class="fas fa-question-circle"></i> Quiz</a>
    <a href="library.php"><i class="fas fa-book"></i> Library</a>
    <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>

  <!-- Main -->
  <main class="main">
    <!-- Top Bar -->
    <header class="top-bar" role="banner">
      <div class="search-box" role="search">
        <input type="search" aria-label="Search documents, quizzes, notes" placeholder="Search for documents, quizzes, notes..." />
      </div>

      <div class="right-icons" aria-label="User actions">
        <i id="notifIcon" class="fas fa-bell" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Toggle notifications"></i>

        <div class="profile-dropdown" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="User profile menu">
          <img src="<?= htmlspecialchars($profile_pic) ?>" alt="User Profile Picture" class="profile-pic" id="profilePic" tabindex="0" />
          <div class="profile-menu" id="profileMenu" role="menu" aria-hidden="true">
            <a href="profile.php" role="menuitem">View Profile</a>
            <a href="edit_profile.php" role="menuitem">✏️ Edit Profile</a>
            <a href="logout.php" role="menuitem">🚪 Logout</a>
          </div>
        </div>
      </div>

      <!-- Notification Panel -->
      <div class="notif-panel" id="notifPanel" aria-live="polite" aria-hidden="true" role="region" aria-label="Notifications panel">
        <ul>
          <li>📥 Note uploaded</li>
          <li>✅ Quiz results released</li>
          <li>📌 Assignment reminder</li>
        </ul>
      </div>
    </header>

    <!-- Content -->
    <section aria-label="User information and recent activity">
      <div class="profile-info" role="region" aria-label="User profile info">
        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" />
        <h3><?= htmlspecialchars($fullname) ?></h3>
        <p><strong>University:</strong> <?= htmlspecialchars($university) ?></p>
        <p><strong>Semester:</strong> <?= htmlspecialchars($semester) ?></p>
      </div>

      <div class="section" role="region" aria-label="Recent notes uploaded by user">
        <h2 class="section-title">📄 Recent Notes</h2>
        <ul class="list-items">
          <?php if (count($notes) > 0): ?>
            <?php foreach ($notes as $note): ?>
              <li>
                <span class="note-title"><?= htmlspecialchars($note['title']) ?></span>
                <span><?= date("M d, Y", strtotime($note['uploaded_at'])) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No notes uploaded yet.</li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="section" role="region" aria-label="Recent quizzes taken by user">
        <h2 class="section-title">📝 Recent Quiz Scores</h2>
        <ul class="list-items">
          <?php if (count($quizzes) > 0): ?>
            <?php foreach ($quizzes as $quiz): ?>
              <li>
                <span class="note-title"><?= htmlspecialchars($quiz['subject']) ?></span>
                <span class="quiz-score"><?= htmlspecialchars($quiz['score']) ?>/<?= htmlspecialchars($quiz['total_questions']) ?> (<?= date("M d, Y", strtotime($quiz['quiz_date'])) ?>)</span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No quizzes taken yet.</li>
          <?php endif; ?>
        </ul>
      </div>
    </section>
  </main>
</div>

<script>
  // Notification toggle
  const notifIcon = document.getElementById('notifIcon');
  const notifPanel = document.getElementById('notifPanel');
  notifIcon.addEventListener('click', () => {
    const isVisible = notifPanel.classList.toggle('show');
    notifIcon.setAttribute('aria-expanded', isVisible);
    notifPanel.setAttribute('aria-hidden', !isVisible);
  });

  // Profile menu toggle
  const profilePic = document.getElementById('profilePic');
  const profileMenu = document.getElementById('profileMenu');
  profilePic.addEventListener('click', () => {
    const isVisible = profileMenu.style.display === 'block';
    profileMenu.style.display = isVisible ? 'none' : 'block';
    profilePic.setAttribute('aria-expanded', !isVisible);
    profileMenu.setAttribute('aria-hidden', isVisible);
  });

  // Close menus if clicked outside
  window.addEventListener('click', e => {
    if (!notifIcon.contains(e.target) && !notifPanel.contains(e.target)) {
      notifPanel.classList.remove('show');
      notifIcon.setAttribute('aria-expanded', false);
      notifPanel.setAttribute('aria-hidden', true);
    }
    if (!profilePic.contains(e.target) && !profileMenu.contains(e.target)) {
      profileMenu.style.display = 'none';
      profilePic.setAttribute('aria-expanded', false);
      profileMenu.setAttribute('aria-hidden', true);
    }
  });
</script>

</body>
</html> 

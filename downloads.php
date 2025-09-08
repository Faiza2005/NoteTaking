<?php
require 'db.php';

$filter = '';
$whereClause = 'WHERE approved = 1';

if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $cat = intval($_GET['category']);
    $whereClause .= " AND category_id = $cat";
    $filter = "&category=$cat";
}

$notes = $conn->query("SELECT notes.*, categories.name AS catname FROM notes
    JOIN categories ON notes.category_id = categories.id
    $whereClause ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Downloads | DevNotes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: cornsilk;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #111;
      color: white;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      padding: 2rem 1rem;
    }

    .sidebar h1 {
      color: green;
      margin-bottom: 1rem;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
    }

    .main {
      margin-left: 220px;
      padding: 3rem;
      width: calc(100% - 220px);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .container {
      width: 100%;
      max-width: 1000px;
      background: white;
      padding: 2rem;
      border-radius: 24px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
    }

    h2 {
      margin-top: 0;
      color: #222;
    }

    .search-area form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .search-area input,
    .search-area select {
      padding: 0.7rem;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 1rem;
      flex: 1;
    }

    .search-area button {
      padding: 0.7rem 1.5rem;
      background: green;
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
    }

    .note {
      background: #f8f8f8;
      padding: 1.2rem;
      margin-bottom: 1.2rem;
      border-radius: 12px;
      box-shadow: 0 0 5px rgba(0,0,0,0.08);
    }

    .note h3 {
      margin: 0 0 0.3rem;
    }

    .note p {
      margin: 0.4rem 0;
    }

    .note small {
      color: gray;
    }

    .note a {
      display: inline-block;
      margin-top: 0.5rem;
      padding: 0.4rem 1rem;
      background: green;
      color: white;
      border-radius: 20px;
      text-decoration: none;
    }

    .filter select {
      padding: 0.6rem;
      border-radius: 8px;
      margin-top: 1rem;
      width: 100%;
      max-width: 300px;
    }

    /* FAQ Box */
    .faq-box {
      width: 100%;
      max-width: 1000px;
      background: wheat;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }

    details {
      margin-bottom: 1rem;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 1rem;
      background: #fff;
      cursor: pointer;
    }

    summary {
      font-weight: bold;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h1>Dev<span style="color:white;">Notes</span></h1>
  <ul>
    <li><a href="index.php">🏠 Home</a></li>
    <li><a href="upload.php">📤 Upload</a></li>
    <li><a href="downloads.php">📥 Download</a></li>
    <li><a href="books.php">📚 Books</a></li>
    <li><a href="quiz.php">📝 Quiz</a></li>
    <li><a href="library.php">📂 My Library</a></li>
  </ul>
</div>

<!-- Main Content -->
<div class="main">

  <!-- Notes Container -->
  <div class="container">
    <h2>📥 Browse & Download Notes</h2>

    <!-- Search -->
    <div class="search-area">
      <form action="search_results.php" method="GET">
        <input type="text" name="query" placeholder="What are you studying today?" required>
        <select name="course_type" required>
          <option value="">Select Course Type</option>
          <option value="cse">CSE Course</option>
          <option value="non-cse">Non-CSE Course</option>
        </select>
        <button type="submit">🔍 Search</button>
      </form>
    </div>

    <!-- Filter -->
    <div class="filter">
      <form method="GET">
        <label><strong>Filter by Category:</strong></label><br>
        <select name="category" onchange="this.form.submit()">
          <option value="">-- All Categories --</option>
          <option value="1">DSA – Semester 2</option>
          <option value="2">Database – Semester 3</option>
          <option value="3">Operating Systems – Semester 4</option>
          <option value="4">Computer Networks – Semester 5</option>
          <option value="5">Web Development – Semester 6</option>
          <option value="6">Machine Learning – Semester 7</option>
          <option value="7">Project Report / Thesis</option>
          <option value="8">Syllabus + Previous Years</option>
        </select>
      </form>
    </div>

    <!-- Notes List -->
    <?php if ($notes && $notes->num_rows > 0): ?>
      <?php while ($row = $notes->fetch_assoc()): ?>
        <div class="note">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          <small>📂 Category: <?= $row['catname'] ?> | Uploaded by User #<?= $row['user_id'] ?></small><br>
          <a href="<?= htmlspecialchars($row['file_path']) ?>" download>📥 Download</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No notes found for this category.</p>
    <?php endif; ?>
  </div>

  <!-- FAQ Box -->
  <div class="faq-box">
    <h2>❓ Frequently Asked Questions</h2>
    <details>
      <summary>Who can download notes?</summary>
      <p>Any verified user can download notes for review.</p>
    </details>
    <details>
      <summary>Are notes reviewed before appearing?</summary>
      <p>Yes. Notes must be admin approved before they appear on the site.</p>
    </details>
    <details>
      <summary>Can I edit or delete my uploaded notes?</summary>
      <p>Currently, you can request changes through support. Editing features will come soon.</p>
    </details>
  </div>

</div>

</body>
</html>


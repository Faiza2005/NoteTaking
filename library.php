<!DOCTYPE html>
<html>
<head>
  <title>📚 My Library | DevNotes</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: cornsilk;
      display: flex;
    }

    .sidebar {
      width: 200px;
      background: #111;
      color: white;
      height: 100vh;
      padding: 2rem 1rem;
      position: fixed;
      top: 0;
      left: 0;
    }

    .sidebar h1 {
      color: green;
      margin-bottom: 2rem;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
    }

    .main {
      margin-left: 220px;
      padding: 2rem;
      width: 100%;
    }

    h2 {
      margin-top: 2rem;
    }

    .section {
      background: white;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-header h2 {
      margin: 0;
    }

    .item-box {
      padding: 1rem;
      background: #f7f7f7;
      border: 1px solid #ddd;
      margin-top: 1rem;
      border-radius: 8px;
    }

    .search-bar {
      margin-bottom: 2rem;
    }

    .search-bar input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .add-btn {
      background: #28a745;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .add-btn:hover {
      background: #218838;
    }

    .empty {
      color: #888;
      font-style: italic;
      margin-top: 1rem;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h1>Dev<span style="color:white;">Notes</span></h1>
    <a href="dashboard.php">🏠 Home</a>
    <a href="upload.php">📤 Upload</a>
    <a href="downloads.php">📥 Download</a>
    <a href="books.php">📚 Books</a>
    <a href="quiz.php">📝 Quiz</a>
    <a href="library.php">📂 My Library</a>
  </div>

  <div class="main">
    <h1>📂 My Library</h1>

    <div class="search-bar">
      <input type="text" placeholder="🔍 Search your library...">
    </div>

    <!-- Courses -->
    <div class="section">
      <div class="section-header">
        <h2>📘 My Courses</h2>
        <button class="add-btn">+ Add Course </button>
      </div>
      <div class="empty">No courses followed yet.</div>
    </div>

    <!-- Quizzes -->
    <div class="section">
      <div class="section-header">
        <h2>📝 My Quizzes</h2>
        <button class="add-btn">+ Create Quiz</button>
      </div>
      <div class="empty">No quizzes created or attempted yet.</div>
    </div>

    <!-- Books -->
    <div class="section">
      <div class="section-header">
        <h2>📚 My Books</h2>
        <button class="add-btn">+ Add Book</button>
      </div>
      <div class="empty">No books added to your collection yet.</div>
    </div>

    <!-- Uploaded Files -->
    <div class="section">
      <div class="section-header">
        <h2>📤 My Uploads</h2>
      </div>
      <div class="empty">You haven't uploaded any files yet.</div>
    </div>

    <!-- Downloads -->
    <div class="section">
      <div class="section-header">
        <h2>📥 My Downloads</h2>
      </div>
      <div class="empty">You haven't downloaded any files yet.</div>
    </div>

  </div>

  <?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch courses
$courses = [];
$sql = "SELECT * FROM user_courses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

// Fetch quizzes
$quizzes = [];
$sql = "SELECT * FROM quiz_results WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}
$stmt->close();

// Fetch books
$books = [];
$sql = "SELECT * FROM user_books WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
$stmt->close();

// Fetch uploads
$uploads = [];
$sql = "SELECT * FROM notes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $uploads[] = $row;
}
$stmt->close();

// Fetch downloads
$downloads = [];
$sql = "SELECT * FROM downloads WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $downloads[] = $row;
}
$stmt->close();

$conn->close();
?>
</body> 
</html>

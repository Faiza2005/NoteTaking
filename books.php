<?php
require 'db.php';
session_start();

$user_id = 1; // Replace this with actual session user ID

// Handle Add to Library from curated/static or dynamic books
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $cover = $_POST['cover'];

    // Check if book exists
    $stmt = $conn->prepare("SELECT id FROM books WHERE title = ? AND author = ?");
    $stmt->bind_param("ss", $title, $author);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book_id = $result->fetch_assoc()['id'];
    } else {
        $insert = $conn->prepare("INSERT INTO books (title, author, description, category, cover_image) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $title, $author, $desc, $category, $cover);
        $insert->execute();
        $book_id = $insert->insert_id;
    }

    // Add to library if not already there
    $checkLib = $conn->prepare("SELECT * FROM library WHERE user_id = ? AND book_id = ?");
    $checkLib->bind_param("ii", $user_id, $book_id);
    $checkLib->execute();
    $exists = $checkLib->get_result();

    if ($exists->num_rows === 0) {
        $conn->query("INSERT INTO library (user_id, book_id) VALUES ($user_id, $book_id)");
    }
}

// Handle Add Book via form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $desc = $_POST['description'];
    $category = $_POST['category'];

    $cover = '';
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
        $coverName = uniqid() . '_' . $_FILES['cover']['name'];
        move_uploaded_file($_FILES['cover']['tmp_name'], "covers/$coverName");
        $cover = "covers/$coverName";
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, description, category, cover_image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $author, $desc, $category, $cover);
    $stmt->execute();
}

// Fetch all books
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterSql = $search ? "WHERE title LIKE '%$search%' OR author LIKE '%$search%'" : '';
$books = $conn->query("SELECT * FROM books $filterSql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>📚 Books | DevNotes</title>
  <style>
    body {
      background: cornsilk;
      font-family: Arial;
      padding: 2rem;
      display: flex;
    }
    .sidebar {
      width: 200px;
      background: #111;
      color: white;
      height: 100vh;
      padding: 2rem;
      position: fixed;
      top: 0;
      left: 0;
    }
    .sidebar h1 { color: green; }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
    }
    .container {
      margin-left: 220px;
      width: 100%;
      max-width: 1000px;
      padding: 2rem;
    }
    h2 { margin-top: 0; }

    .book {
      background: white;
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      display: flex;
      gap: 1rem;
    }
    .book img {
      max-width: 100px;
      height: auto;
      border-radius: 8px;
    }
    .book-details { flex: 1; }
    .book-details h3 { margin: 0; }
    .book-details p { margin: 0.5rem 0; }
    .book-details button {
      background: green;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 10px;
      cursor: pointer;
    }

    .search-bar {
      margin-bottom: 2rem;
    }
    .search-bar input {
      padding: 0.7rem;
      width: 70%;
      border-radius: 10px;
      border: 1px solid #ccc;
    }
    .search-bar button {
      padding: 0.7rem 1rem;
      background: green;
      color: white;
      border: none;
      border-radius: 10px;
      margin-left: 0.5rem;
    }

    .admin-form {
      background: #fff;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
    }
    .admin-form input, .admin-form textarea {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.8rem;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .admin-form button {
      background: green;
      color: white;
      padding: 0.7rem 2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h1><span>Dev</span><span style="color:white;">Notes</span></h1>
  <a href="dashboard.php">🏠 Home</a>
  <a href="upload.php">📤 Upload</a>
  <a href="downloads.php">📥 Download</a>
  <a href="books.php">📚 Books</a>
  <a href="quiz.php">📝 Quiz</a>
  <a href="library.php">📂 My Library</a>
</div>

<div class="container">

  <div class="admin-form">
    <h2>📘 Add a Book</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Book Title" required>
      <input type="text" name="author" placeholder="Author Name">
      <textarea name="description" placeholder="Book Description"></textarea>
      <input type="text" name="category" placeholder="Category (e.g., CSE)">
      <label>Upload Cover Image:</label>
      <input type="file" name="cover" accept="image/*">
      <input type="hidden" name="add_book" value="1">
      <button type="submit">➕ Add Book</button>
    </form>
  </div>

  <div class="search-bar">
    <form method="GET">
      <input type="text" name="search" placeholder="Search for your books..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit">🔍</button>
    </form>
  </div>

  <h2>📚 Here are some popular books</h2>

  <?php
  $curatedBooks = [
    [
      "title" => "Data Structures & Algorithms",
      "author" => "Narasimha Karumanchi",
      "category" => "CSE",
      "description" => "Master the fundamentals of data structures and algorithms with practical examples.",
      "cover" => "https://m.media-amazon.com/images/I/61CVP-MfUoL._SL1360_.jpg"
    ],
    [ 
      "title" => "Database Management Systems",
      "author" => "Raghu Ramakrishnan",
      "category" => "CSE",
      "description" => "A foundational book on database architecture and design principles.",
      "cover" => "https://m.media-amazon.com/images/I/81tEgsxpNZS.jpg"
    ],
    [
      "title" => "Operating System Concepts",
      "author" => "Silberschatz",
      "category" => "CSE",
      "description" => "The 'Dinosaur Book' that explains OS fundamentals and process management.",
      "cover" => "https://www.zybooks.com/wp-content/uploads/2006/09/OSC-cover.png"
    ],
    [
      "title" => "Computer Networking",
      "author" => "Kurose & Ross",
      "category" => "CSE",
      "description" => "A comprehensive guide to how networks function, from basics to advanced topics.",
      "cover" => "https://m.media-amazon.com/images/I/81ewUnANZPL._SY385_.jpg" 
    ],
    [
      "title" => "Machine Learning - Tom Mitchell",
      "author" => "Tom Mitchell",
      "category" => "AI/ML",
      "description" => "An accessible introduction to the core concepts in machine learning.",
      "cover" => "https://m.media-amazon.com/images/I/71KilybDOoL.jpg"
    ]
  ];

  foreach ($curatedBooks as $book): ?>
    <div class="book">
      <img src="<?= $book['cover'] ?>" alt="Cover">
      <div class="book-details">
        <h3><?= $book['title'] ?></h3>
        <small>Author: <?= $book['author'] ?> | <?= $book['category'] ?></small>
        <p><?= $book['description'] ?></p>
        <form method="POST">
          <input type="hidden" name="title" value="<?= $book['title'] ?>">
          <input type="hidden" name="author" value="<?= $book['author'] ?>">
          <input type="hidden" name="description" value="<?= $book['description'] ?>">
          <input type="hidden" name="category" value="<?= $book['category'] ?>">
          <input type="hidden" name="cover" value="<?= $book['cover'] ?>">
          <button type="submit">📥 Add to My Library</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

  <?php while ($book = $books->fetch_assoc()): ?>
    <div class="book">
      <?php if ($book['cover_image']): ?>
        <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover">
      <?php endif; ?>
      <div class="book-details">
        <h3><?= htmlspecialchars($book['title']) ?></h3>
        <small>Author: <?= htmlspecialchars($book['author']) ?> | <?= htmlspecialchars($book['category']) ?></small>
        <p><?= htmlspecialchars($book['description']) ?></p>
        <form method="POST">
          <input type="hidden" name="title" value="<?= $book['title'] ?>">
          <input type="hidden" name="author" value="<?= $book['author'] ?>">
          <input type="hidden" name="description" value="<?= $book['description'] ?>">
          <input type="hidden" name="category" value="<?= $book['category'] ?>">
          <input type="hidden" name="cover" value="<?= $book['cover_image'] ?>">
          <button type="submit">📥 Add to My Library</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>

</div>
</body>
</html>

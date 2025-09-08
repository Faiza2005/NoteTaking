<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $user = $_SESSION['user_id'];

    // Handle category: new or existing
    if (!empty($_POST['new_category'])) {
        $newCat = trim($_POST['new_category']);
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $newCat);
        $stmt->execute();
        $cat = $stmt->insert_id;
    } else {
        $cat = intval($_POST['category']);
    }

    // Check if file uploaded
    if (!isset($_FILES['note']) || $_FILES['note']['error'] == 4) {
        $msg = "❌ No file was uploaded.";
    } else {
        $file = $_FILES['note'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];
        $maxSize = 30 * 1024 * 1024; // 30 MB

        if (!in_array($ext, $allowed)) {
            $msg = "❌ Only PDF or DOC/DOCX files are allowed.";
        } elseif ($file['size'] > $maxSize) {
            $msg = "❌ File too large (max 30 MB).";
        } elseif ($file['error'] !== 0) {
            $msg = "❌ Upload error code: " . $file['error'];
        } else {
            $dir = 'user_upload_notes/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $name = time() . "_" . basename($file['name']);
            $path = $dir . $name;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $conn->prepare("INSERT INTO notes(user_id, title, description, file_path, category_id, approved) VALUES (?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("isssi", $user, $title, $desc, $path, $cat);
                if ($stmt->execute()) {
                    $msg = "✅ Successfully uploaded and pending review.";
                } else {
                    $msg = "❌ Database error: " . $stmt->error;
                    if (file_exists($path)) unlink($path);
                }
            } else {
                $msg = "❌ File upload failed.";
            }
        }
    }
}

// Fetch categories for dropdown
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload | DevNotes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: cornsilk; font-family: 'Segoe UI', sans-serif; display: flex; }
.sidebar { width: 220px; padding: 2rem; background-color: #1e1e1e; color: white; height: 100vh; position: fixed; left: 0; top: 0; }
.sidebar h1 { font-size: 1.5rem; margin-bottom: 1rem; }
.sidebar span { color: green; }
.sidebar ul { list-style: none; }
.sidebar ul li { margin: 15px 0; }
.sidebar ul li a { color: white; text-decoration: none; display: block; padding: 8px; border-radius: 6px; }
.sidebar ul li a:hover { background-color: green; }
.main-content { margin-left: 240px; width: calc(100% - 240px); padding: 3rem 2rem; display: flex; flex-direction: column; align-items: center; gap: 2.5rem; }
.upload-section, .faq-section { width: 100%; max-width: 1000px; background: white; padding: 2rem 2.5rem; border-radius: 20px; box-shadow: 0 0 12px rgba(0,0,0,0.1); }
h2, h3 { text-align: center; margin-bottom: 1.2rem; color: green; }
.form-group { margin-bottom: 1.2rem; }
label { font-weight: bold; }
input[type="text"], textarea, select { width: 100%; padding: 0.7rem; border-radius: 8px; border: 1px solid #ccc; }
.upload-box { border: 2px dashed #90caff; background: #f0f8ff; padding: 2rem; text-align: center; border-radius: 12px; margin-top: 1rem; cursor: pointer; }
.upload-box:hover { background: #e7f6ff; }
.upload-box i { font-size: 2.2rem; color: green; }
.upload-box input[type="file"] { display: none; }
.btn-blue { background: green; color: white; border: none; padding: 0.6rem 1.3rem; border-radius: 20px; font-size: 1rem; cursor: pointer; margin-top: 1rem; }
.message { background: #f0fff0; color: green; border: 1px solid green; padding: 1rem; border-radius: 8px; margin-bottom: 1.2rem; text-align: center; }
.faq-section { background: wheat; }
.faq-item { margin-bottom: 0.8rem; }
.faq-question { background: cornsilk; border: 1px solid green; padding: 0.7rem 1rem; border-radius: 6px; font-weight: bold; width: 100%; text-align: left; cursor: pointer; }
.faq-answer { display: none; padding: 0.7rem 1.5rem; border-left: 3px solid green; background: #fafafa; border-radius: 0 0 6px 6px; }
.faq-answer p { margin: 0; }
</style>
</head>
<body>

<div class="sidebar">
  <h1><span>Dev</span><span style="color:white;">Notes</span></h1>
  <ul>
    <li><a href="dashboard.php">🏠 Home</a></li>
    <li><a href="upload.php">📤 Upload</a></li>
    <li><a href="downloads.php">📥 Download</a></li>
    <li><a href="books.php">📚 Books</a></li>
    <li><a href="quiz.php">📝 Quiz</a></li>
    <li><a href="library.php">📂 My Library</a></li>
  </ul>
</div>

<div class="main-content">
<div class="upload-section">
  <h2>📤 Upload a Note</h2>

  <?php if ($msg): ?>
    <div class="message"><?= $msg ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Note Title:</label>
      <input type="text" name="title" required placeholder="e.g. DBMS Final Notes">
    </div>

    <div class="form-group">
      <label>Description:</label>
      <textarea name="description" rows="3" placeholder="Write a short description..."></textarea>
    </div>

    <div class="form-group">
      <label>Category:</label>
      <select name="category">
        <option value="">-- Select Category --</option>
        <?php while($row = $categories->fetch_assoc()): ?>
          <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
      </select>
      <p style="margin-top:0.5rem;">
        Or add a new category: <input type="text" name="new_category" placeholder="New category name">
      </p>
    </div>

    <div class="upload-box" onclick="document.getElementById('fileInput').click();">
      <i class="fa-solid fa-cloud-arrow-up"></i>
      <p><strong>Drag your file here</strong></p>
      <p>or</p>
      <button type="button" class="btn-blue">Select File</button>
      <p><small>Supported: PDF, DOC, DOCX — Max 30MB</small></p>
      <input type="file" name="note" id="fileInput" accept=".pdf,.doc,.docx" required>
      <p id="fileName"></p>
    </div>

    <button type="submit" class="btn-blue" style="width:100%; margin-top:1.5rem;">
      <i class="fa-solid fa-upload"></i> Upload Now
    </button>
  </form>
</div>

<div class="faq-section">
  <h3>📌 Frequently Asked Questions</h3>
  <div class="faq-item">
    <button class="faq-question">📁 What file types are supported?</button>
    <div class="faq-answer"><p>PDF, DOC, DOCX (Max 30MB).</p></div>
  </div>
  <div class="faq-item">
    <button class="faq-question">➕ Can I add new categories?</button>
    <div class="faq-answer"><p>Yes! Type the new category in the input field above.</p></div>
  </div>
  <div class="faq-item">
    <button class="faq-question">⏳ When will my note be visible to others?</button>
    <div class="faq-answer"><p>After an admin approves it from the panel.</p></div>
  </div>
  <div class="faq-item">
    <button class="faq-question">✏️ Can I update or delete notes?</button>
    <div class="faq-answer"><p>Yes, go to your library to manage your uploads.</p></div>
  </div>
</div>

<script>
const fileInput = document.getElementById('fileInput');
const fileNameDisplay = document.getElementById('fileName');

fileInput.addEventListener('change', function () {
  if (fileInput.files.length > 0) {
    fileNameDisplay.innerHTML = "<strong>Selected:</strong> " + fileInput.files[0].name;
  }
});

document.querySelectorAll('.faq-question').forEach(btn => {
  btn.addEventListener('click', () => {
    const answer = btn.nextElementSibling;
    answer.style.display = (answer.style.display === 'block') ? 'none' : 'block';
  });
});
</script>

</div>
</body>
</html>

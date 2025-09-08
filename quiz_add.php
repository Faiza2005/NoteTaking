<?php
include 'db.php'; 

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $question = trim($_POST['question']);
    $a = trim($_POST['a']);
    $b = trim($_POST['b']);
    $c = trim($_POST['c']);
    $d = trim($_POST['d']);
    $correct = $_POST['correct'];

    if ($subject && $question && $a && $b && $c && $d && $correct) {
        // Check if subject exists
        $stmt = $conn->prepare("SELECT id FROM quiz_subjects WHERE subject_name=?");
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($subject_id);
            $stmt->fetch();
        } else {
            // Insert new subject
            $stmt_insert = $conn->prepare("INSERT INTO quiz_subjects (subject_name) VALUES (?)");
            $stmt_insert->bind_param("s", $subject);
            $stmt_insert->execute();
            $subject_id = $stmt_insert->insert_id;
        }
        $stmt->close();

        // Insert question
        $stmt_q = $conn->prepare("INSERT INTO quiz_questions 
            (subject_id, question, option_a, option_b, option_c, option_d, correct_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_q->bind_param("issssss", $subject_id, $question, $a, $b, $c, $d, $correct);

        if ($stmt_q->execute()) {
            $message = "✅ Quiz question added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt_q->close();
    } else {
        $message = "⚠️ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Quiz | DevNotes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: cornsilk;
      padding: 2rem;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input, textarea, select, button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background: green;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: darkgreen;
    }
    .message {
      margin-bottom: 1rem;
      font-weight: bold;
      color: darkblue;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>➕ Add a New Quiz</h2>
    <p class="message"><?= $message ?></p>

    <form method="POST">
      <label>Course/Subject:</label>
      <input type="text" name="subject" placeholder="e.g., DBMS, Operating System" required>

      <label>Question:</label>
      <textarea name="question" rows="3" required></textarea>

      <label>Option A:</label>
      <input type="text" name="a" required>

      <label>Option B:</label>
      <input type="text" name="b" required>

      <label>Option C:</label>
      <input type="text" name="c" required>

      <label>Option D:</label>
      <input type="text" name="d" required>

      <label>Correct Answer:</label>
      <select name="correct" required>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>

      <button type="submit">Add Quiz</button>
    </form>
  </div>
</body>
</html>

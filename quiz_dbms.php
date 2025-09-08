<?php
session_start();
include 'db.php'; // <-- Add this to connect to your database

$questions = [
  [
    "question" => "What is the full form of DBMS?",
    "options" => ["Data of Binary Management System", "Database Management System", "Database Management Service", "Data Backup Management System"],
    "answer" => 1
  ],
  [
    "question" => "What is a database?",
    "options" => ["Organized collection that cannot be accessed", "Data without organizing", "Organized data that can be accessed and updated", "Only uneditable data"],
    "answer" => 2
  ],
  [
    "question" => "What is DBMS?",
    "options" => ["A collection of queries", "A high-level language", "Stores, modifies and retrieves data", "A compiler"],
    "answer" => 2
  ],

  [
    "question" => "Which forms have a relation that contains information about a single entity?",
    "options" => ["4NF", "2NF", "3NF", "5NF"],
    "answer" => 3
  ],

];
?>

<!DOCTYPE html>
<html>
<head>
  <title>DBMS Quiz</title>
  <style>
    body { font-family: Arial; background: #f9f9f9; padding: 20px; }
    .quiz-box { background: white; padding: 20px; max-width: 800px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
    .question { margin-bottom: 20px; }
    h2 { text-align: center; }
    .submit-btn { background: green; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    .result { margin-top: 20px; font-weight: bold; }
  </style>
</head>
<body>

<div class="quiz-box">
  <h2>📘 DBMS Quiz</h2>
  <form method="post">
    <?php foreach ($questions as $index => $q): ?>
      <div class="question">
        <p><strong>Q<?= $index + 1 ?>: <?= $q['question'] ?></strong></p>
        <?php foreach ($q['options'] as $optIndex => $option): ?>
          <label>
            <input type="radio" name="answer<?= $index ?>" value="<?= $optIndex ?>"> <?= $option ?>
          </label><br>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <button type="submit" class="submit-btn">Submit Answers</button>
  </form>

  <?php
  // Evaluation
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    foreach ($questions as $i => $q) {
      $userAnswer = $_POST["answer$i"] ?? -1;
      if ($userAnswer == $q['answer']) {
        $score++;
      }
    }

    echo "<div class='result'>✅ You scored $score out of " . count($questions) . "</div>";

    // Save to session
    $_SESSION['quiz_results']['dbms'] = [
      "score" => $score,
      "total" => count($questions),
      "date" => date("Y-m-d H:i")
    ];

    // --- SERVER SQL PART: Save to database ---
    if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $subject = 'DBMS';
      $quiz_date = date("Y-m-d H:i:s");
      $total_questions = count($questions);

      $sql = "INSERT INTO quiz_results (user_id, subject, score, total_questions, quiz_date) VALUES (?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("isiss", $user_id, $subject, $score, $total_questions, $quiz_date);
      $stmt->execute();
      $stmt->close();
    }
    // -----------------------------------------
  }
  ?>
</div>

</body>
</html>

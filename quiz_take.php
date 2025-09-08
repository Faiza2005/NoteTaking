<?php
session_start();
require 'db.php';

// Get subject_id from URL
if (!isset($_GET['subject_id'])) {
    die("No subject selected.");
}
$subject_id = intval($_GET['subject_id']);

// Fetch subject name
$stmt = $conn->prepare("SELECT subject_name FROM quiz_subjects WHERE id=?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$stmt->bind_result($subject_name);
$stmt->fetch();
$stmt->close();

// Fetch quiz questions
$stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d, correct_answer 
                        FROM quiz_questions WHERE subject_id=? ORDER BY id ASC");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();

// If form is submitted (check answers)
$score = null;
$user_answers = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    foreach ($questions as $q) {
        $qid = $q['id'];
        $user_answers[$qid] = $_POST['answer'][$qid] ?? null;
        if ($user_answers[$qid] === $q['correct_answer']) {
            $score++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz - <?php echo htmlspecialchars($subject_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .question { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .options label { display: block; margin: 5px 0; }
        .submit-btn { margin-top: 20px; padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .submit-btn:hover { background: #218838; }
        .score { padding: 10px; margin-bottom: 20px; background: #f8f9fa; border: 1px solid #ccc; border-radius: 5px; }
        .correct { color: green; font-weight: bold; }
        .wrong { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>Quiz: <?php echo htmlspecialchars($subject_name); ?></h2>

<?php if ($score !== null): ?>
    <div class="score">
        <strong>Your Score:</strong> <?php echo $score . " / " . count($questions); ?>
    </div>
<?php endif; ?>

<form method="post">
    <?php foreach ($questions as $index => $q): ?>
        <div class="question">
            <p><strong>Q<?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></p>
            <div class="options">
                <?php foreach (['A','B','C','D'] as $opt): 
                    $field = "option_" . strtolower($opt);
                    $isChecked = ($user_answers[$q['id']] ?? '') === $opt ? "checked" : "";
                    
                    // Mark answers after submit
                    $extra = "";
                    if ($score !== null) {
                        if ($opt === $q['correct_answer']) {
                            $extra = "<span class='correct'> (Correct Answer)</span>";
                        } elseif (($user_answers[$q['id']] ?? '') === $opt) {
                            $extra = "<span class='wrong'> (Your Answer)</span>";
                        }
                    }
                ?>
                    <label>
                        <input type="radio" name="answer[<?php echo $q['id']; ?>]" value="<?php echo $opt; ?>" <?php echo $isChecked; ?>>
                        <?php echo htmlspecialchars($q[$field]) . " " . $extra; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="submit-btn">Submit Quiz</button>
</form>

</body>
</html>

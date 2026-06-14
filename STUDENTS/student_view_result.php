<?php
include('ExamDatabase.php');

session_start();

$db = new ExamDatabase();
$conn = $db->getConnection();

if (!isset($_SESSION["userid"])) {
    die("User not logged in.");
}

$student_id = $_SESSION["userid"];
$exam_id = intval($_GET['exam_id']);

// ✅ Prepare status message
$status_msg = "";
if (isset($_GET['status']) && $_GET['status'] === 'already_submitted') {
    $status_msg = "<div class='alert alert-warning text-center'>⚠️ You have already submitted this exam. Your result is shown below.</div>";
}

// ✅ Fetch result summary
$stmt = $conn->prepare("SELECT total_points, submitted_at FROM exam_results WHERE exam_id = ? AND student_id = ?");
$stmt->bind_param("ii", $exam_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ✅ Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .correct-answer {
            color: green;
            font-weight: bold;
        }
        .incorrect-answer {
            color: red;
            font-weight: bold;
        }
        .choice-list li {
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
        .question-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .summary-box {
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        h2, h3 {
            font-size: calc(1.3rem + .6vw);
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <?= $status_msg ?>

<?php
if ($row = $result->fetch_assoc()) {
    $total_points = $row['total_points'];
    $submitted_at = $row['submitted_at'];
    echo "<div class='summary-box'>";
    echo "<h2 class='mb-3'>📝 Exam Result</h2>";
    echo "<p><strong>Total Score:</strong> <span class='badge badge-success'>$total_points</span></p>";
    echo "<p><strong>Submitted At:</strong> " . date('F j, Y g:i A', strtotime($submitted_at)) . "</p>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger'>❌ No result found. You may not have taken this exam or an error occurred.</div>";
    echo "</div></div></div></body></html>";
    exit;
}

// ✅ Fetch and display all student answers
$stmt = $conn->prepare("
    SELECT q.question, q.choice_a, q.choice_b, q.choice_c, q.choice_d, q.correct_answer, sa.selected_answer
    FROM student_answers sa
    JOIN exam_questions q ON sa.question_id = q.id
    WHERE sa.exam_id = ? AND sa.student_id = ?
");
$stmt->bind_param("ii", $exam_id, $student_id);
$stmt->execute();
$answers = $stmt->get_result();

$total_questions = 0;
$correct_answers = 0;

echo "<h3 class='mb-4'>📋 Review Your Answers</h3>";
echo "<ol class='pl-3'>";

while ($row = $answers->fetch_assoc()) {
    $total_questions++;
    $is_correct = intval($row['selected_answer']) === intval($row['correct_answer']);
    if ($is_correct) $correct_answers++;

    echo "<li class='question-card'>";
    echo "<p><strong>Question:</strong> " . htmlspecialchars($row['question']) . "</p>";
    echo "<ul class='choice-list'>";

    for ($i = 1; $i <= 4; $i++) {
        $label = chr(64 + $i); // A, B, C, D
        $choice_key = 'choice_' . strtolower($label);
        $choice_text = htmlspecialchars($row[$choice_key]);
        $selected = (intval($row['selected_answer']) === $i);
        $correct = (intval($row['correct_answer']) === $i);

        $class = "";
        if ($correct && $selected) $class = "correct-answer";
        elseif ($selected && !$correct) $class = "incorrect-answer";
        elseif ($correct) $class = "correct-answer";

        echo "<li class='$class'>$label. $choice_text";
        if ($selected) echo " <em>(Your answer)</em>";
        if ($correct) echo " <strong>(Correct)</strong>";
        echo "</li>";
    }

    echo "</ul></li>";
}
echo "</ol>";

// ✅ Final score
$percentage = $total_questions > 0 ? round(($correct_answers / $total_questions) * 100, 2) : 0;
echo "<div class='alert alert-info'><strong>Total Score:</strong> $correct_answers / $total_questions</div>";
echo "<div class='alert alert-primary'><strong>Percentage:</strong> $percentage%</div>";
?>

        </div>
    </div>
</div>
</body>
</html>

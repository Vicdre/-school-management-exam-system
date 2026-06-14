<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('ExamDatabase.php');

$db = new ExamDatabase();
$conn = $db->getConnection();

$student_id = $_SESSION["userid"];

if (!isset($_GET['exam_id'])) {
    die("Exam ID not provided.");
}

$exam_id = intval($_GET['exam_id']);

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY RAND()");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

// Fetch exam title
$title = "Take Exam";
$stmt = $conn->prepare("SELECT title FROM exams WHERE id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $title = "Course Exam: " . htmlspecialchars($row['title']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/favicon.ico">
    <style>
        body {
            background-color: rgb(162, 197, 232);
        }
        .question-block {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .question-block p {
            font-size: 1.25rem;
            background-color: rgba(90, 90, 90, 0.1);
            padding: 10px;
            border-radius: 10px;
        }
        .form-check {
            background-color: rgba(168, 168, 168, 0.2);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .form-check-label {
            font-size: 1.1rem;
            margin-left: 0.4em;
        }
        .btn-submit {
            font-size: 1.2rem;
            padding: 0.6rem 2rem;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <h2 class="text-center mb-4"><?= $title ?></h2>
            <form method="POST" action="submit_exam.php">
                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">

                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-block">
                        <p><strong>Q<?= $index + 1 ?>: <?= htmlspecialchars($q['question']) ?></strong></p>

                        <?php
                        $choices = [
                            1 => ['label' => 'A', 'value' => htmlspecialchars($q['choice_a'])],
                            2 => ['label' => 'B', 'value' => htmlspecialchars($q['choice_b'])],
                            3 => ['label' => 'C', 'value' => htmlspecialchars($q['choice_c'])],
                            4 => ['label' => 'D', 'value' => htmlspecialchars($q['choice_d'])],
                        ];
                        foreach ($choices as $num => $choice) {
                            $id = "q{$q['id']}{$choice['label']}";
                            echo "
                            <div class='form-check'>
                                <input class='form-check-input' type='radio' name='answers[{$q['id']}]' value='$num' id='$id'>
                                <label class='form-check-label' for='$id'>{$choice['label']}. {$choice['value']}</label>
                            </div>";
                        }
                        ?>
                    </div>
                <?php endforeach; ?>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-submit">Submit Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (isset($_GET['error']) && $_GET['error'] === 'incomplete'): ?>
<script>
    alert("Please answer all questions before submitting the exam.");
</script>
<?php endif; ?>

</body>
</html>

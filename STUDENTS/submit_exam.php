<?php
include('ExamDatabase.php');
session_start();

$db = new ExamDatabase();
$conn = $db->getConnection();

$student_id = $_SESSION["userid"];
$exam_id = intval($_POST['exam_id']);
$answers = $_POST['answers'] ?? [];

// Step 1: Check if already submitted
$stmt = $conn->prepare("SELECT id FROM exam_results WHERE exam_id = ? AND student_id = ?");
$stmt->bind_param("ii", $exam_id, $student_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Already submitted
    header("Location: /SISC101/STUDENTS/student_view_result.php?exam_id=$exam_id&status=already_submitted");
exit;
}
// Step 1.5: Ensure all questions are answered
$questionCountStmt = $conn->prepare("SELECT COUNT(*) FROM exam_questions WHERE exam_id = ?");
$questionCountStmt->bind_param("i", $exam_id);
$questionCountStmt->execute();
$questionCountStmt->bind_result($total_questions);
$questionCountStmt->fetch();
$questionCountStmt->close();

if (count($answers) < $total_questions) {
    // Not all questions answered
    header("Location: /SISC101/STUDENTS/student_examination.php?exam_id=$exam_id&error=incomplete");
    exit;

}

// Step 2: Calculate total points
$total_points = 0;

$question_ids = array_keys($answers);
if (!empty($question_ids)) {
    $placeholders = implode(',', array_map('intval', $question_ids));
    $query = "SELECT id, correct_answer FROM exam_questions WHERE id IN ($placeholders)";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $qid = $row['id'];
        if (isset($answers[$qid]) && intval($answers[$qid]) === intval($row['correct_answer'])) {
            $total_points++;
        }
    }
}
$insertAnswerStmt = $conn->prepare("INSERT INTO student_answers (exam_id, student_id, question_id, selected_answer) VALUES (?, ?, ?, ?)");

foreach ($answers as $qid => $selected) {
    $qid = intval($qid);
    $selected = intval($selected);
    $insertAnswerStmt->bind_param("iiii", $exam_id, $student_id, $qid, $selected);
    $insertAnswerStmt->execute();
}

// Step 3: Insert result
$stmt = $conn->prepare("INSERT INTO exam_results (exam_id, student_id, total_points) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $exam_id, $student_id, $total_points);
$stmt->execute();

// Step 4: Redirect
header("Location: student_view_result.php?exam_id=$exam_id&score=$total_points");
exit;

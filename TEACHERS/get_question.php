<?php
include 'ExamDatabase.php';
$db = new ExamDatabase();
$conn = $db->getConnection();

if (isset($_GET['exam_id'])) {
    $exam_id = intval($_GET['exam_id']);
    $query = "SELECT id, question_number FROM exam_questions WHERE exam_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Output each question as: id|question_number (pipe-delimited)
        echo $row['id'] . '|' . $row['question_number'] . "\n";
    }

    $stmt->close();
}
$conn->close();
?>

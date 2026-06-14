<?php
include('ExamDatabase.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $exam_id = $_POST['exam_id'];
    $question = $_POST['question'];
    $choice_a = $_POST['choice_1'];
    $choice_b = $_POST['choice_2'];
    $choice_c = $_POST['choice_3'];
    $choice_d = $_POST['choice_4'];
    $correct_answer = $_POST['correct_answer'];

    $db = new ExamDatabase();
    $conn = $db->getConnection();

    if ($conn) {
        $stmt = $conn->prepare("UPDATE exam_questions SET question = ?, choice_a = ?, choice_b = ?, choice_c = ?, choice_d = ?, correct_answer = ? WHERE id = ? AND exam_id = ?");
        $stmt->bind_param("ssssssii", $question, $choice_a, $choice_b, $choice_c, $choice_d, $correct_answer, $id, $exam_id);

        if ($stmt->execute()) {
            echo "Question updated successfully.";
        } else {
            http_response_code(500);
            echo "Error updating question.";
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(500);
        echo "Database connection failed.";
    }
} else {
    http_response_code(405);
    echo "Invalid request method.";
}
?>

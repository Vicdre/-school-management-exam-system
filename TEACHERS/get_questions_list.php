<?php
include 'ExamDatabase.php';

$db = new ExamDatabase();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $question_id = $_GET['id'];

    $query = "SELECT * FROM exam_questions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Return data as pipe-delimited plain text
        echo implode('|', [
            $row['question'],
            $row['choice_a'],
            $row['choice_b'],
            $row['choice_c'],
            $row['choice_d'],
            $row['correct_answer']
        ]);
    } else {
        echo "No question found.";
    }

    $stmt->close();
}
$conn->close();
?>

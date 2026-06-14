<?php 
include 'ExamDatabase.php';

$db = new ExamDatabase();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
    $question = isset($_POST['question']) ? trim($_POST['question']) : '';
    $correct = isset($_POST['correct_answer']) ? intval($_POST['correct_answer']) : -1;

    // Manually extract individual choices (1-based index)
    $choices = [
        $_POST['choice_1'] ?? '',
        $_POST['choice_2'] ?? '',
        $_POST['choice_3'] ?? '',
        $_POST['choice_4'] ?? ''
    ];

    // Validate all fields
    if (
        $exam_id > 0 &&
        $question !== '' &&
        count($choices) === 4 &&
        $correct >= 1 && $correct <= 4 &&
        !in_array('', $choices)
    ) {
        // Find the next question number for this exam
        $queryNumber = "SELECT MAX(question_number) AS last_number FROM exam_questions WHERE exam_id = ?";
        $stmtNumber = $conn->prepare($queryNumber);
        if ($stmtNumber === false) {
            http_response_code(500);
            echo "Error: " . $conn->error;
            exit;
        }
        $stmtNumber->bind_param("i", $exam_id);
        $stmtNumber->execute();
        $result = $stmtNumber->get_result();
        $row = $result->fetch_assoc();
        $nextQuestionNumber = $row['last_number'] ? $row['last_number'] + 1 : 1;
        $stmtNumber->close();

        // Insert question with question_number
        $query = "INSERT INTO exam_questions (exam_id, question_number, question, choice_a, choice_b, choice_c, choice_d, correct_answer) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            http_response_code(500);
            echo "Error: " . $conn->error;
            exit;
        }

        $stmt->bind_param(
            "iisssssi",
            $exam_id,
            $nextQuestionNumber,
            $question,
            $choices[0],
            $choices[1],
            $choices[2],
            $choices[3],
            $correct
        );

        if ($stmt->execute()) {
            echo "Question saved successfully!";
        } else {
            http_response_code(500);
            echo "Failed to save question: " . $stmt->error;
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo "Invalid data submitted.";
    }
} else {
    http_response_code(405);
    echo "Only POST requests allowed.";
}

$conn->close();
?>

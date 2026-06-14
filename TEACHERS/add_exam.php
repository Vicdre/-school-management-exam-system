<?php
include 'ExamDatabase.php';

$db = new ExamDatabase();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $exam_date = trim($_POST['exam_date'] ?? '');

    if ($title === '' || $exam_date === '') {
        echo "Missing title or date.";
        exit;
    }

    $query = "INSERT INTO exams (title, exam_date) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ss", $title, $exam_date);
    if ($stmt->execute()) {
        echo $conn->insert_id; // return the new ID directly
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

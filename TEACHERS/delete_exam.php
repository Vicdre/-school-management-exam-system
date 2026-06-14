<?php
include 'ExamDatabase.php';

$db = new ExamDatabase();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo "Invalid ID.";
        exit;
    }

    $query = "DELETE FROM exams WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Exam deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

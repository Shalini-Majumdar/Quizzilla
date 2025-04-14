<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate and sanitize quiz ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid quiz ID.");
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Check if the quiz belongs to the logged-in user
$stmt = $conn->prepare("SELECT user_id FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->bind_result($creator_id);
$stmt->fetch();
$stmt->close();

if ($creator_id != $user_id) {
    die("Unauthorized: You are not the creator of this quiz.");
}

// Optional: delete attempts and responses
// You can enable this if needed
$conn->query("DELETE r FROM responses r JOIN attempts a ON r.attempt_id = a.id WHERE a.quiz_id = $quiz_id");
$conn->query("DELETE FROM attempts WHERE quiz_id = $quiz_id");

// Delete questions
$conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");

// Delete quiz
$conn->query("DELETE FROM quizzes WHERE id = $quiz_id");

// Redirect to dashboard
header("Location: dashboard.php?deleted=1");
exit;
?>

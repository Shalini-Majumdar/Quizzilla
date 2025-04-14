<?php
session_start();
include 'db_connect.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Make sure a quiz ID was posted
if (!isset($_POST['quiz_id']) || !is_numeric($_POST['quiz_id'])) {
    die("Invalid quiz.");
}

$quiz_id = intval($_POST['quiz_id']);
$user_id = $_SESSION['user_id'];

// Fetch all questions for the quiz
$stmt = $conn->prepare("SELECT id, correct_option FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$score = 0;
$responses = [];

// Evaluate each question
while ($q = $result->fetch_assoc()) {
    $qid = $q['id'];
    $correct = $q['correct_option'];

    if (isset($_POST["q$qid"])) {
        $selected = $_POST["q$qid"];

        // Save the response
        $responses[] = [
            'question_id' => $qid,
            'selected' => $selected
        ];

        if ($selected === $correct) {
            $score++;
        }
    }
}
$stmt->close();

// Save the attempt
$stmt = $conn->prepare("INSERT INTO attempts (user_id, quiz_id, score) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $user_id, $quiz_id, $score);
$stmt->execute();
$attempt_id = $stmt->insert_id;
$stmt->close();

// Save responses
$stmt = $conn->prepare("INSERT INTO responses (attempt_id, question_id, selected_option) VALUES (?, ?, ?)");
foreach ($responses as $r) {
    $stmt->bind_param("iis", $attempt_id, $r['question_id'], $r['selected']);
    $stmt->execute();
}
$stmt->close();

// Redirect to result page
header("Location: result.php?attempt_id=$attempt_id");
exit;

<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = intval($_POST['quiz_id']);
    $quiz_name = $_POST['name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Ensure this quiz belongs to the logged-in user
    $check = $conn->prepare("SELECT user_id FROM quizzes WHERE id = ?");
    $check->bind_param("i", $quiz_id);
    $check->execute();
    $check->bind_result($owner_id);
    $check->fetch();
    $check->close();

    if ($owner_id != $user_id) {
        die("Unauthorized access.");
    }

    // 1. Update quiz details
    $stmt = $conn->prepare("UPDATE quizzes SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $quiz_name, $description, $quiz_id);
    $stmt->execute();
    $stmt->close();

    // 2. Process deleted question IDs
    if (!empty($_POST['deleted_ids'])) {
        $deleted_ids = explode(",", $_POST['deleted_ids']);
        foreach ($deleted_ids as $qid) {
            $qid = intval($qid);
            $conn->query("DELETE FROM questions WHERE id = $qid AND quiz_id = $quiz_id");
        }
    }

    // 3. Update or Insert questions
    $question_ids = $_POST['question_ids'];
    $questions = $_POST['questions'];
    $option_a = $_POST['a'];
    $option_b = $_POST['b'];
    $option_c = $_POST['c'];
    $option_d = $_POST['d'];
    $correct_option = $_POST['correct'];

    for ($i = 0; $i < count($questions); $i++) {
        $qid = $question_ids[$i];
        $q = $questions[$i];
        $a = $option_a[$i];
        $b = $option_b[$i];
        $c = $option_c[$i];
        $d = $option_d[$i];
        $correct = $correct_option[$i];

        if ($qid === "new") {
            // New question
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $quiz_id, $q, $a, $b, $c, $d, $correct);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update existing question
            $qid = intval($qid);
            $stmt = $conn->prepare("UPDATE questions SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=? WHERE id=? AND quiz_id=?");
            $stmt->bind_param("ssssssii", $q, $a, $b, $c, $d, $correct, $qid, $quiz_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: quiz.php?id=$quiz_id&updated=1");
    exit;
} else {
    echo "Invalid request.";
}
?>

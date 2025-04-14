<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get quiz info
    $quiz_name = $_POST['name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Insert into quizzes table
    $stmt = $conn->prepare("INSERT INTO quizzes (user_id, name, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $quiz_name, $description);
    
    if ($stmt->execute()) {
        $quiz_id = $stmt->insert_id;

        // Loop through questions
        $questions = $_POST['questions'];
        $option_a = $_POST['a'];
        $option_b = $_POST['b'];
        $option_c = $_POST['c'];
        $option_d = $_POST['d'];
        $correct_option = $_POST['correct'];

        $stmt_q = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < count($questions); $i++) {
            $q = $questions[$i];
            $a = $option_a[$i];
            $b = $option_b[$i];
            $c = $option_c[$i];
            $d = $option_d[$i];
            $correct = $correct_option[$i];

            $stmt_q->bind_param("issssss", $quiz_id, $q, $a, $b, $c, $d, $correct);
            $stmt_q->execute();
        }

        // Success
        header("Location: dashboard.php?success=quiz_created");
        exit;
    } else {
        echo "Failed to create quiz: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>

<?php
session_start();
include 'db_connect.php';

// Validate attempt_id
if (!isset($_GET['attempt_id']) || !is_numeric($_GET['attempt_id'])) {
    die("Invalid attempt ID.");
}
$attempt_id = intval($_GET['attempt_id']);

// Ensure the attempt belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT a.quiz_id, a.score, q.name 
    FROM attempts a
    JOIN quizzes q ON a.quiz_id = q.id
    WHERE a.id = ? AND a.user_id = ?
");
$stmt->bind_param("ii", $attempt_id, $_SESSION['user_id']);
$stmt->execute();
$meta = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$meta) {
    die("Attempt not found or unauthorized.");
}

$quiz_name = htmlspecialchars($meta['name']);
$score = $meta['score'];

// Get detailed responses
$stmt = $conn->prepare("
    SELECT q.question, q.correct_option, q.option_a, q.option_b, q.option_c, q.option_d, r.selected_option
    FROM responses r
    JOIN questions q ON r.question_id = q.id
    WHERE r.attempt_id = ?
");
$stmt->bind_param("i", $attempt_id);
$stmt->execute();
$responses = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 900px;">
    <h4 class="mb-3">Result for Quiz: <?= $quiz_name ?></h4>
    <p class="mb-4"><strong>Your Score:</strong> <?= $score ?></p>

    <?php while ($row = $responses->fetch_assoc()): ?>
        <div class="mb-4 p-3 border rounded bg-white">
            <p class="fw-bold"><?= htmlspecialchars($row['question']) ?></p>
            <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                <?php
                    $option_text = htmlspecialchars($row['option_' . strtolower($opt)]);
                    $is_correct = $row['correct_option'] === $opt;
                    $is_selected = $row['selected_option'] === $opt;

                    $classes = "p-2 rounded";
                    if ($is_correct && $is_selected) {
                        $classes .= " bg-success text-white";
                    } elseif ($is_correct) {
                        $classes .= " bg-success-subtle text-success";
                    } elseif ($is_selected) {
                        $classes .= " bg-danger-subtle text-danger";
                    } else {
                        $classes .= " bg-light";
                    }
                ?>
                <div class="<?= $classes ?>">
                    <?= $opt ?>. <?= $option_text ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endwhile; ?>

    <a href="dashboard.php" class="btn btn-outline-primary">← Return to Dashboard</a>
</div>

<?php include 'footer.php'; ?>
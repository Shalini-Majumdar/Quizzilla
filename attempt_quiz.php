<?php
session_start();
include 'db_connect.php';

// Validate quiz ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid quiz ID.");
}

$quiz_id = intval($_GET['id']);

// Fetch quiz name
$stmt = $conn->prepare("SELECT name FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->bind_result($quiz_name);
$stmt->fetch();
$stmt->close();

if (!$quiz_name) {
    die("Quiz not found.");
}

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions_result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 900px;">
    <h4 class="mb-4"><?= htmlspecialchars($quiz_name) ?></h4>

    <form method="POST" action="submit_quiz.php">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

        <?php while ($q = $questions_result->fetch_assoc()): ?>
            <div class="mb-4">
                <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>

                <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="q<?= $q['id'] ?>" value="<?= $opt ?>" id="q<?= $q['id'] ?>_<?= $opt ?>" required>
                        <label class="form-check-label" for="q<?= $q['id'] ?>_<?= $opt ?>">
                            <?= htmlspecialchars($q['option_' . strtolower($opt)]) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>

        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>

<?php include 'footer.php'; ?>

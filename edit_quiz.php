<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate quiz ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid quiz ID.");
}

$quiz_id = intval($_GET['id']);

// Fetch quiz data
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

if (!$quiz) {
    die("Quiz not found.");
}

if ($quiz['user_id'] != $_SESSION['user_id']) {
    die("You are not authorized to edit this quiz.");
}

// Fetch quiz questions
$questions = [];
$qstmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$qstmt->bind_param("i", $quiz_id);
$qstmt->execute();
$qres = $qstmt->get_result();
while ($row = $qres->fetch_assoc()) {
    $questions[] = $row;
}
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 900px;">
    <h4 class="mb-4">Edit Quiz</h4>

    <form method="POST" action="update_quiz.php">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

        <div class="mb-3">
            <label class="form-label">Quiz Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($quiz['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($quiz['description']) ?></textarea>
        </div>

        <h5 class="mb-3">Questions</h5>
        <div id="questions">
            <?php foreach ($questions as $index => $q): ?>
                <div class="question-block border rounded p-3 mb-3 bg-white" data-id="<?= $q['id'] ?>">
                    <input type="hidden" name="question_ids[]" value="<?= $q['id'] ?>">

                    <label class="form-label"><strong>Question <?= $index + 1 ?></strong></label>
                    <input name="questions[]" class="form-control mb-2" value="<?= htmlspecialchars($q['question']) ?>" required>

                    <div class="row mb-2">
                        <div class="col">
                            <label class="form-label">Option A</label>
                            <input name="a[]" class="form-control" value="<?= htmlspecialchars($q['option_a']) ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Option B</label>
                            <input name="b[]" class="form-control" value="<?= htmlspecialchars($q['option_b']) ?>" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label class="form-label">Option C</label>
                            <input name="c[]" class="form-control" value="<?= htmlspecialchars($q['option_c']) ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Option D</label>
                            <input name="d[]" class="form-control" value="<?= htmlspecialchars($q['option_d']) ?>" required>
                        </div>
                    </div>

                    <label class="form-label">Correct Option</label>
                    <select name="correct[]" class="form-select mb-2" required>
                        <option value="A" <?= $q['correct_option'] === 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $q['correct_option'] === 'B' ? 'selected' : '' ?>>B</option>
                        <option value="C" <?= $q['correct_option'] === 'C' ? 'selected' : '' ?>>C</option>
                        <option value="D" <?= $q['correct_option'] === 'D' ? 'selected' : '' ?>>D</option>
                    </select>

                    <div class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this, <?= $q['id'] ?>)">❌ Remove Question</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" name="deleted_ids" id="deleted_ids" value="">

        <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">+ Add New Question</button><br>
        <button type="submit" class="btn btn-primary">Update Quiz</button>
    </form>
</div>

<script>
function addQuestion() {
    const index = document.querySelectorAll('.question-block').length + 1;
    const q = document.createElement('div');
    q.classList.add('question-block', 'border', 'rounded', 'p-3', 'mb-3', 'bg-white');
    q.innerHTML = `
        <input type="hidden" name="question_ids[]" value="new">
        <label class="form-label"><strong>Question ${index}</strong></label>
        <input name="questions[]" class="form-control mb-2" required>

        <div class="row mb-2">
            <div class="col">
                <label class="form-label">Option A</label>
                <input name="a[]" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label">Option B</label>
                <input name="b[]" class="form-control" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <label class="form-label">Option C</label>
                <input name="c[]" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label">Option D</label>
                <input name="d[]" class="form-control" required>
            </div>
        </div>

        <label class="form-label">Correct Option</label>
        <select name="correct[]" class="form-select mb-2" required>
            <option value="" disabled selected>Select the correct answer</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <div class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this, null)">❌ Remove Question</button>
        </div>
    `;
    document.getElementById('questions').appendChild(q);
}

function removeQuestion(button, questionId) {
    const block = button.closest('.question-block');
    block.remove();

    if (questionId !== null) {
        const deletedField = document.getElementById('deleted_ids');
        let deleted = deletedField.value ? deletedField.value.split(',') : [];
        deleted.push(questionId);
        deletedField.value = deleted.join(',');
    }
}
</script>

<?php include 'footer.php'; ?>

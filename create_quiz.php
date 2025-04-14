<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 900px;">
    <h4 class="mb-4">Create a New Quiz</h4>

    <form method="POST" action="save_quiz.php">
        <div class="mb-3">
            <label class="form-label">Quiz Name</label>
            <input type="text" name="name" class="form-control" required placeholder="Enter quiz title">
        </div>

        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required placeholder="Enter quiz description"></textarea>
        </div>

        <h5 class="mb-3">Questions</h5>
        <div id="questions"></div>

        <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">+ Add Question</button><br>
        <button type="submit" class="btn btn-primary">Save Quiz</button>
    </form>
</div>

<script>
function addQuestion() {
    const index = document.querySelectorAll('.question-block').length + 1;
    const q = document.createElement('div');
    q.classList.add('question-block', 'border', 'rounded', 'p-3', 'mb-3', 'bg-white');
    q.innerHTML = `
        <label class="form-label mb-2"><strong>Question ${index}</strong></label>
        <input name="questions[]" class="form-control mb-3" required placeholder="Enter your question here">

        <div class="row mb-2">
            <div class="col">
                <label class="form-label">Option A</label>
                <input name="a[]" class="form-control" required placeholder="Option A">
            </div>
            <div class="col">
                <label class="form-label">Option B</label>
                <input name="b[]" class="form-control" required placeholder="Option B">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <label class="form-label">Option C</label>
                <input name="c[]" class="form-control" required placeholder="Option C">
            </div>
            <div class="col">
                <label class="form-label">Option D</label>
                <input name="d[]" class="form-control" required placeholder="Option D">
            </div>
        </div>

        <div class="mb-2">
            <label class="form-label">Correct Option</label>
            <select name="correct[]" class="form-select" required>
                <option value="" disabled selected>Select the correct answer</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this)">
                ❌ Remove Question
            </button>
        </div>
    `;
    document.getElementById('questions').appendChild(q);
}

function removeQuestion(button) {
    const block = button.closest('.question-block');
    block.remove();
}
</script>


<?php include 'footer.php'; ?>

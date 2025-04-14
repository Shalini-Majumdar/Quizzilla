<?php
session_start();
include 'db_connect.php';

// Check if quiz ID is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid quiz ID.");
}

$quiz_id = intval($_GET['id']); // sanitize

// Prepare query
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);

if (!$stmt->execute()) {
    die("Query failed: " . $stmt->error);
}

$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

if (!$quiz) {
    die("Quiz not found.");
}

// Check if the current user is the creator
$is_creator = isset($_SESSION['user_id']) && $quiz['user_id'] == $_SESSION['user_id'];
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 700px;">
    <h4 class="mb-3"><?= htmlspecialchars($quiz['name']) ?></h4>
    <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>

    <div class="mt-4 d-flex flex-wrap gap-2">
        <?php if ($is_creator): ?>
            <a href="edit_quiz.php?id=<?= $quiz_id ?>" class="btn btn-outline-primary">Edit</a>
            <a href="#" 
                class="btn btn-outline-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#confirmDeleteModal" 
                data-id="<?= $quiz['id'] ?>">
                Delete
            </a>

        <?php else: ?>
            <button class="btn btn-outline-secondary" disabled>Edit</button>
            <button class="btn btn-outline-secondary" disabled>Delete</button>
        <?php endif; ?>

        <a href="attempt_quiz.php?id=<?= $quiz_id ?>" class="btn btn-success">Attempt Quiz</a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <a href="#" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this quiz? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
        <a id="deleteConfirmLink" href="delete_quiz.php?id=<?= $quiz_id?>" class="btn btn-danger">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

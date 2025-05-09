<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$search = $_GET['search'] ?? '';
$search_escaped = $conn->real_escape_string($search);
$quizzes = $conn->query("SELECT * FROM quizzes WHERE name LIKE '%$search_escaped%'");
?>

<?php include 'header.php'; ?>

<div class="container-fluid">
    <form method="GET" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Search quizzes..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-primary">Search</button>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Available Quizzes</h4>
        <a href="create_quiz.php" class="btn btn-success">+ Add Quiz</a>
    </div>

    <div class="row">
        <?php while ($quiz = $quizzes->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($quiz['name']) ?></h5>
                        <p class="card-text">
                            <?= $conn->query("SELECT COUNT(*) as total FROM questions WHERE quiz_id = {$quiz['id']}")->fetch_assoc()['total'] ?> Questions
                        </p>
                        <a href="quiz.php?id=<?= $quiz['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

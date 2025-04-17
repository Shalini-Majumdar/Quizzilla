<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get attempts
$stmt = $conn->prepare("
    SELECT a.id AS attempt_id, a.score, a.timestamp, q.name AS quiz_name
    FROM attempts a
    JOIN quizzes q ON a.quiz_id = q.id
    WHERE a.user_id = ?
    ORDER BY a.timestamp DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<h4 class="mb-4">My Quiz History</h4>

<?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
            <thead class="table-light">
                <tr>
                    <th>Quiz</th>
                    <th>Score</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['quiz_name']) ?></td>
                        <td><?= $row['score'] ?></td>
                        <?php
                            $server_time = new DateTime($row['timestamp'], new DateTimeZone('-07:00'));
                            $server_time->setTimezone(new DateTimeZone('Asia/Kolkata'));
                        ?>
                        <td><?= $server_time->format('d M Y, h:i A') ?></td>
                        <td><a href="result.php?attempt_id=<?= $row['attempt_id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">You haven't attempted any quizzes yet.</div>
<?php endif; ?>

<?php include 'footer.php'; ?>

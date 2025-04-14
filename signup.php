<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connect.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password_raw = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password_raw !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['profile_picture'] = 'default.jpg';

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email already registered.";
        }
    }
}
?>

<?php include 'guest_header.php'; ?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="min-width: 400px;">
        <h4 class="text-center mb-4">Create an Account</h4>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" required placeholder="Name">
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" required placeholder="Email">
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>
            <div class="mb-3">
                <input type="password" name="confirm" class="form-control" required placeholder="Confirm Password">
            </div>
            <button type="submit" class="btn btn-success w-100">Sign Up</button>
        </form>

        <p class="mt-3 text-center">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>

<?php include 'guest_footer.php'; ?>

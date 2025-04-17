<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $stmt->close();

            $stmt = $conn->prepare("SELECT id, name, email, profile_picture FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $name, $email, $profile_picture);
            $stmt->fetch();

            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['profile_picture'] = $profile_picture;

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<?php include 'guest_header.php'; ?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div>
        <div class="card p-4 shadow" style="min-width: 350px;">
            <div class="text-center mb-4">
                <img src="images/big_logo.png" alt="Big Logo" width="100" height="100">
            </div>

            <h4 class="text-center mb-4">Login to Quizzilla</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" required placeholder="Email">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" required placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="mt-3 text-center">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
        </div>
    </div>
</div>

<?php include 'guest_footer.php'; ?>

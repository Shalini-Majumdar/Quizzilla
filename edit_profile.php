<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $profile_picture = $_SESSION['profile_picture'];

    // Handle file upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = basename($_FILES['profile_pic']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'user_' . $user_id . '_' . time() . '.' . $fileExtension;
            $uploadPath = 'uploads/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $profile_picture = $newFileName;
            }
        }
    }

    // Update database
    $stmt = $conn->prepare("UPDATE users SET name = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_name, $profile_picture, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update session
    $_SESSION['name'] = $new_name;
    $_SESSION['profile_picture'] = $profile_picture;

    $success = "Profile updated successfully.";
}
?>

<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 600px;">
    <h4 class="mb-3">Edit Profile</h4>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_SESSION['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Profile Picture</label><br>
            <img src="uploads/<?= htmlspecialchars($_SESSION['profile_picture']) ?>" width="100" height="100" class="rounded-circle" style="object-fit: cover;">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload New Picture</label>
            <input type="file" name="profile_pic" accept="image/*" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="profile.php" class="btn btn-outline-secondary ms-2">← Back to Profile</a>
    </form>
</div>

<?php include 'footer.php'; ?>

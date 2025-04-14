<?php include 'header.php'; ?>

<div class="card p-4 shadow-sm" style="max-width: 500px;">
    <h4 class="mb-3">My Profile</h4>

    <div class="text-center mb-4">
        <img src="uploads/<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg') ?>" 
            width="120" height="120"
            class="rounded-circle shadow"
            style="object-fit: cover;">
    </div>

    <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>

    <div class="d-flex justify-content-between mt-4">
        <a href="edit_profile.php" class="btn btn-outline-primary flex-fill me-2">Edit Profile</a>
        <a href="logout.php" class="btn btn-outline-danger flex-fill ms-2">Logout</a>
    </div>
</div>

<?php include 'footer.php'; ?>

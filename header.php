<?php
if (!isset($_SESSION)) session_start();
include 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quizzilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="bg-dark text-white p-3" style="width: 220px; min-height: 100vh;">
        <div class="d-flex align-items-center mb-4">
            <img src="images/logo.png" alt="Logo" width="50" height="50" class="me-2">
            <h4 class="text-white mb-0">Quizzilla</h4>
        </div>
        <a href="dashboard.php" class="text-white d-block mb-2 text-decoration-none">🏠 Dashboard</a>
        <a href="my_quizzes.php" class="text-white d-block mb-2 text-decoration-none">🧠 My Quizzes</a>
        <a href="history.php" class="text-white d-block mb-2 text-decoration-none">📜 History</a>
        <a href="profile.php" class="text-white d-block mb-2 text-decoration-none">👤 Profile</a>
        <a href="logout.php" class="text-white d-block mb-2 text-decoration-none">🚪 Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light" style="min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h3>
            <img src="uploads/<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg') ?>" width="40" height="40" class="rounded-circle">
        </div>

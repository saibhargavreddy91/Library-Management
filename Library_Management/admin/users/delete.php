<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header('Location: list.php');
    exit();
}

$userId = (int)$_GET['id'];


if ($userId == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header('Location: list.php');
    exit();
}

try {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header('Location: list.php');
        exit();
    }


    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$userId]);
    $activeLoans = $stmt->fetchColumn();

    if ($activeLoans > 0) {
        $_SESSION['error'] = "Cannot delete user with active loans.";
        header('Location: list.php');
        exit();
    }


    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);

    $_SESSION['success'] = "User deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
}

header('Location: list.php');
exit();

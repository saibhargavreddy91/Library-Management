<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

requireAuth();
if (!isUser()) {
    header('Location: /login.php');
    exit();
}

if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    header('Location: dashboard.php?error=invalid_loan');
    exit();
}

$loanId = intval($_GET['loan_id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM loans WHERE loan_id = ? AND user_id = ? AND status = 'active'");
    $stmt->execute([$loanId, $_SESSION['user_id']]);
    $loan = $stmt->fetch();

    if (!$loan) {
        header('Location: dashboard.php?error=loan_not_found');
        exit();
    }

    $stmt = $pdo->prepare("UPDATE loans SET status = 'returned', return_date = NOW() WHERE loan_id = ?");
    $stmt->execute([$loanId]);

    $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?");
    $stmt->execute([$loan['book_id']]);

    header('Location: dashboard.php?success=book_returned');
    exit();
} catch (PDOException $e) {
    error_log("Error returning book: " . $e->getMessage());
    header('Location: dashboard.php?error=unexpected_error');
    exit();
}

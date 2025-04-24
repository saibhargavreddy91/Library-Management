<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

requireAuth();
if (!isUser()) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header('Location: search.php?error=invalid_book');
    exit();
}

$bookId = intval($_GET['book_id']);

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS current_loans FROM loans WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    $currentLoans = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT max_books_allowed FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $maxBooks = $stmt->fetchColumn();

    if ($currentLoans >= $maxBooks) {
        header('Location: dashboard.php?error=limit_reached');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ? AND available_copies > 0");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();

    if (!$book) {
        header('Location: search.php?error=book_unavailable');
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO loans (user_id, book_id, loan_date, due_date, status) 
                           VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'active')");
    $stmt->execute([$_SESSION['user_id'], $bookId]);

    $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?");
    $stmt->execute([$bookId]);

    header('Location: dashboard.php?success=book_borrowed');
    exit();
} catch (PDOException $e) {
    error_log("Error borrowing book: " . $e->getMessage());
    header('Location: dashboard.php?error=unexpected_error');
    exit();
}

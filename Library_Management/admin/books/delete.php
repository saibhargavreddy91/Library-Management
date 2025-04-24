<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: /login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid book ID.";
    header('Location: list.php');
    exit();
}

$bookId = (int)$_GET['id'];

try {
    // Check if the book exists
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();

    if (!$book) {
        $_SESSION['error'] = "Book not found.";
        header('Location: list.php');
        exit();
    }

    // Check if the book has active loans
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE book_id = ? AND status = 'active'");
    $stmt->execute([$bookId]);
    $activeLoans = $stmt->fetchColumn();

    if ($activeLoans > 0) {
        $_SESSION['error'] = "Cannot delete book with active loans.";
        header('Location: list.php');
        exit();
    }

    // Delete the book
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);

    $_SESSION['success'] = "Book deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting book: " . $e->getMessage();
}

header('Location: list.php');
exit();

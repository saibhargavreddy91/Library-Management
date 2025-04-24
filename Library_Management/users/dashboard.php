<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../config/database.php';

requireAuth();
if (!isUser()) {
    header('Location: /login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT l.*, b.title, b.author 
                      FROM loans l 
                      JOIN books b ON l.book_id = b.book_id 
                      WHERE l.user_id = ? AND l.status = 'active'");
$stmt->execute([$_SESSION['user_id']]);
$currentLoans = $stmt->fetchAll();

$maxBooks = 5;
$stmt = $pdo->prepare("SELECT max_books_allowed FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if ($user && $user['max_books_allowed'] > 0) {
    $maxBooks = $user['max_books_allowed'];
}

$canBorrowMore = $maxBooks - count($currentLoans);
?>

<div class="container">
    <div class="user-stats">
        <p>You have borrowed <?= count($currentLoans) ?> of <?= $maxBooks ?> allowed books.</p>
        <?php if ($canBorrowMore > 0): ?>
            <p>You can borrow <?= $canBorrowMore ?> more book<?= $canBorrowMore > 1 ? 's' : '' ?>.</p>
        <?php else: ?>
            <p>You have reached your borrowing limit.</p>
        <?php endif; ?>
    </div>

    <div class="search-section">
        <h2>Search Books</h2>
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search by title, author, or genre" required>
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    <div class="current-loans">
        <h2>Your Current Loans</h2>
        <?php if (count($currentLoans) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Loan Date</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentLoans as $loan): ?>
                        <tr>
                            <td><?= htmlspecialchars($loan['title']) ?></td>
                            <td><?= htmlspecialchars($loan['author']) ?></td>
                            <td><?= htmlspecialchars($loan['loan_date']) ?></td>
                            <td><?= htmlspecialchars($loan['due_date']) ?></td>
                            <td>
                                <a href="return.php?loan_id=<?= $loan['loan_id'] ?>" class="btn" onclick="return confirm('Are you sure you want to return this book?')">Return</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You currently have no active loans.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
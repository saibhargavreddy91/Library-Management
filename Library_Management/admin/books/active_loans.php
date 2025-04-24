<?php
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: /login.php');
    exit();
}


$borrowerFilter = isset($_GET['borrower']) ? trim($_GET['borrower']) : '';


$query = "SELECT l.loan_id, l.loan_date, l.due_date, b.title AS book_title, u.username AS user_name 
          FROM loans l
          JOIN books b ON l.book_id = b.book_id
          JOIN users u ON l.user_id = u.user_id
          WHERE l.status = 'active'";

if (!empty($borrowerFilter)) {
    $query .= " AND u.username LIKE :borrower";
}

$query .= " ORDER BY l.due_date ASC";

$stmt = $pdo->prepare($query);

if (!empty($borrowerFilter)) {
    $stmt->bindValue(':borrower', '%' . $borrowerFilter . '%', PDO::PARAM_STR);
}

$stmt->execute();
$activeLoansDetails = $stmt->fetchAll();
?>

<div class="container">
    <h1>Active Loans</h1>

    <!-- Borrower Filter Form -->
    <form method="GET" class="filter-form">
        <label for="borrower">Filter by Borrower:</label>
        <input type="text" id="borrower" name="borrower" value="<?= htmlspecialchars($borrowerFilter) ?>" placeholder="Enter username">
        <button type="submit" class="btn">Filter</button>
    </form>

    <?php if (count($activeLoansDetails) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Book Title</th>
                    <th>Borrower</th>
                    <th>Loan Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeLoansDetails as $loan): ?>
                    <tr>
                        <td><?= htmlspecialchars($loan['loan_id']) ?></td>
                        <td><?= htmlspecialchars($loan['book_title']) ?></td>
                        <td><?= htmlspecialchars($loan['user_name']) ?></td>
                        <td><?= htmlspecialchars($loan['loan_date']) ?></td>
                        <td><?= htmlspecialchars($loan['due_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No active loans found.</p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>
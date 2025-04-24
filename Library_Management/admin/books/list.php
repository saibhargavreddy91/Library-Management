<?php
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: ../../login.php');
    exit();
}


$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;


$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalPages = ceil($totalBooks / $perPage);


$stmt = $pdo->prepare("
    SELECT 
        b.book_id, b.title, b.author, b.isbn, b.genre, b.quantity, 
        (b.quantity - COALESCE(SUM(l.status = 'active'), 0)) AS available_copies
    FROM books b
    LEFT JOIN loans l ON b.book_id = l.book_id
    GROUP BY b.book_id
    LIMIT :offset, :perPage
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<div class="container">
    <h1>Book List</h1>

    <div class="actions">
        <a href="add.php" class="btn">Add New Book</a>
    </div>

    <?php if (count($books) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Genre</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['isbn']) ?></td>
                        <td><?= htmlspecialchars($book['genre']) ?></td>
                        <td><?= $book['quantity'] ?></td>
                        <td><?= max(0, $book['available_copies']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $book['book_id'] ?>" class="btn">Edit</a>
                            <a href="delete.php?id=<?= $book['book_id'] ?>" class="btn" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn">Next</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No books found.</p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>
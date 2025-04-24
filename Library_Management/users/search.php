<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../config/database.php';

requireAuth();
if (!isUser()) {
    header('Location: /login.php');
    exit();
}

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$books = [];
if ($query) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE (title LIKE ? OR author LIKE ? OR genre LIKE ?) AND available_copies > 0");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching books: " . $e->getMessage());
        $books = [];
    }
}
?>

<div class="container">
    <div class="search-section">
        <h2>Search Books</h2>
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search by title, author, or genre" required>
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    <?php if ($query): ?>
        <h2>Results for "<?= htmlspecialchars($query) ?>"</h2>
        <?php if (count($books) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Available Copies</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['genre']) ?></td>
                            <td><?= htmlspecialchars($book['available_copies']) ?></td>
                            <td>
                                <a href="borrow.php?book_id=<?= $book['book_id'] ?>" class="btn">Borrow</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books found matching your search.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
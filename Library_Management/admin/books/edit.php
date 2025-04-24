<?php
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
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


$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = "Book not found.";
    header('Location: list.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);
    $quantity = (int)$_POST['quantity'];
    $published_year = trim($_POST['published_year']);


    if (empty($title) || empty($author) || empty($isbn) || empty($genre) || $quantity < 1) {
        $_SESSION['error'] = "All fields are required, and quantity must be at least 1.";
        header("Location: edit.php?id=$bookId");
        exit();
    }


    $newAvailable = $book['available_copies'] + ($quantity - $book['quantity']);
    if ($newAvailable < 0) {
        $_SESSION['error'] = "Available quantity cannot be negative.";
        header("Location: edit.php?id=$bookId");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE books SET 
                              title = ?, author = ?, isbn = ?, genre = ?, 
                              quantity = ?, available_copies = ?, published_year = ?
                              WHERE book_id = ?");
        $stmt->execute([$title, $author, $isbn, $genre, $quantity, $newAvailable, $published_year, $bookId]);

        $_SESSION['success'] = "Book updated successfully!";
        header('Location: list.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating book: " . $e->getMessage();
        header("Location: edit.php?id=$bookId");
        exit();
    }
}
?>

<div class="container">
    <h1>Edit Book</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?= $book['quantity'] ?>" required>
        </div>

        <div class="form-group">
            <label for="published_year">Published Year</label>
            <input type="number" id="published_year" name="published_year" min="1000" max="<?= date('Y') ?>" value="<?= htmlspecialchars($book['published_year']) ?>">
        </div>

        <button type="submit" class="btn">Update Book</button>
        <a href="list.php" class="btn">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
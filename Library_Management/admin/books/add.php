<?php
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: ../../login.php');
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
        header('Location: add.php');
        exit();
    }

    try {

        $stmt = $pdo->prepare("INSERT INTO books 
                              (title, author, isbn, genre, quantity, available_copies, published_year) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $isbn, $genre, $quantity, $quantity, $published_year]);

        $_SESSION['success'] = "Book added successfully!";
        header('Location: list.php');
        exit();
    } catch (PDOException $e) {

        if ($e->getCode() === '23000') { // SQLSTATE 23000: Integrity constraint violation
            $_SESSION['error'] = "A book with this ISBN already exists.";
        } else {
            $_SESSION['error'] = "Error adding book: " . $e->getMessage();
        }
        header('Location: add.php');
        exit();
    }
}
?>

<div class="container">
    <h1>Add New Book</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" required>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
        </div>

        <div class="form-group">
            <label for="published_year">Published Year</label>
            <input type="number" id="published_year" name="published_year" min="1000" max="<?= date('Y') ?>">
        </div>

        <button type="submit" class="btn">Add Book</button>
        <a href="list.php" class="btn">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
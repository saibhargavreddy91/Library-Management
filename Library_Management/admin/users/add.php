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
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);


    if (empty($username) || empty($password) || empty($name) || empty($email) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: add.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header('Location: add.php');
        exit();
    }

    try {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $pdo->prepare("INSERT INTO users 
                              (username, password_hash, name, email, role) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $name, $email, $role]);

        $_SESSION['success'] = "User added successfully!";
        header('Location: list.php');
        exit();
    } catch (PDOException $e) {

        if ($e->getCode() === '23000') { // SQLSTATE 23000: Integrity constraint violation
            $_SESSION['error'] = "A user with this username or email already exists.";
        } else {
            $_SESSION['error'] = "Error adding user: " . $e->getMessage();
        }
        header('Location: add.php');
        exit();
    }
}
?>

<div class="container">
    <h1>Add New User</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn">Add User</button>
        <a href="list.php" class="btn">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
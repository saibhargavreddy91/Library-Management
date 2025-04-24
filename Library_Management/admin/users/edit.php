<?php
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header('Location: list.php');
    exit();
}

$userId = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header('Location: list.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;

    if (empty($username) || empty($name) || empty($email) || empty($role)) {
        $_SESSION['error'] = "All fields except password are required.";
        header("Location: edit.php?id=$userId");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header("Location: edit.php?id=$userId");
        exit();
    }

    try {

        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET 
                                  username = ?, name = ?, email = ?, role = ?, password_hash = ?
                                  WHERE user_id = ?");
            $stmt->execute([$username, $name, $email, $role, $password, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET 
                                  username = ?, name = ?, email = ?, role = ?
                                  WHERE user_id = ?");
            $stmt->execute([$username, $name, $email, $role, $userId]);
        }

        $_SESSION['success'] = "User updated successfully!";
        header('Location: list.php');
        exit();
    } catch (PDOException $e) {
        
        if ($e->getCode() === '23000') { 
            $_SESSION['error'] = "A user with this username or email already exists.";
        } else {
            $_SESSION['error'] = "Error updating user: " . $e->getMessage();
        }
        header("Location: edit.php?id=$userId");
        exit();
    }
}
?>

<div class="container">
    <h1>Edit User</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password">
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn">Update User</button>
        <a href="list.php" class="btn">Cancel</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
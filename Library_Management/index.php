<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: users/dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/header.php'; ?>
<body>
    <div class="container text-center mt-5">
        <h1>Welcome to the Library Management System</h1>
        <p>Manage books, users, and loans efficiently.</p>
        <a href="login.php" class="btn btn-primary">Login</a>
    </div>
<?php require_once 'includes/footer.php'; ?>
</body>
</html>

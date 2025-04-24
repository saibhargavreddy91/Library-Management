<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../config/database.php';

requireAuth();
if (!isAdmin()) {
    header('Location: /login.php');
    exit();
}


$booksCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeLoans = $pdo->query("SELECT COUNT(*) FROM loans WHERE status = 'active'")->fetchColumn();

?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Books</h3>
            <p><?= $booksCount ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?= $usersCount ?></p>
        </div>
        <div class="stat-card">
            <h3>Active Loans</h3>
            <p><?= $activeLoans ?></p>
        </div>
    </div>

    <div class="dashboard-cards">
        <div class="card">
            <h2>Books Management</h2>
            <ul>
                <li><a href="books/add.php">Add New Book</a></li>
                <li><a href="books/list.php">View/Edit Books</a></li>
                <li><a href="books/active_loans.php">View Loans</a></li>
            </ul>
        </div>

        <div class="card">
            <h2>Users Management</h2>
            <ul>
                <li><a href="users/add.php">Add New User</a></li>
                <li><a href="users/list.php">View/Edit Users</a></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
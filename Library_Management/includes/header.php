<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library_Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Library_Management/assets/css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <h1>Library Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/">Library_Management</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li class="nav-item">
                                    <span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Library_Management/logout.php">Logout</a> <!-- Updated -->
                                </li>
                                <?php if (isAdmin()): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/Library_Management/admin/dashboard.php">Admin Dashboard</a> <!-- Updated -->
                                    </li>
                                <?php else: ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/Library_Management/users/dashboard.php">User Dashboard</a> <!-- Updated -->
                                    </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Library_Management/login.php">Login</a> <!-- Updated -->
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
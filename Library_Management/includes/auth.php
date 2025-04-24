<?php
session_start();
function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}
function isAdmin()
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser()
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

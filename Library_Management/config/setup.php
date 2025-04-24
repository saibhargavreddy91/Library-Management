<?php

try {
   
    $host = 'localhost'; 
    $username = 'root';
    $password = ''; 
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `library_management`");
    $pdo->exec("USE `library_management`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `user_id` INT NOT NULL AUTO_INCREMENT,
            `username` VARCHAR(50) NOT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `max_books_allowed` INT DEFAULT 5,
            `role` ENUM('user', 'admin') DEFAULT 'user',
            PRIMARY KEY (`user_id`),
            UNIQUE KEY `unique_username` (`username`)
        )
    ");

   
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `books` (
            `book_id` INT NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `author` VARCHAR(255) NOT NULL,
            `isbn` VARCHAR(20) DEFAULT NULL,
            `genre` VARCHAR(100) DEFAULT NULL,
            `quantity` INT DEFAULT 0,
            `available_copies` INT DEFAULT 0,
            `published_year` INT DEFAULT NULL,
            PRIMARY KEY (`book_id`),
            UNIQUE KEY `unique_isbn` (`isbn`)
        )
    ");


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `loans` (
            `loan_id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `book_id` INT NOT NULL,
            `loan_date` DATE NOT NULL,
            `due_date` DATE NOT NULL,
            `return_date` DATE DEFAULT NULL,
            `status` ENUM('active', 'returned') DEFAULT 'active',
            PRIMARY KEY (`loan_id`),
            KEY `fk_user_id` (`user_id`),
            KEY `fk_book_id` (`book_id`),
            CONSTRAINT `fk_loans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
            CONSTRAINT `fk_loans_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE
        )
    ");


    $pdo->exec("
        INSERT IGNORE INTO `users` (username, name, password_hash, email, role)
        VALUES ('admin', 'Administrator', '$2y$12$3WiWRsDspR/ESzaUh8IG5uhZH8Xu7Asy/TxQMaqRuXEzgeKXWzs56', 'admin@mail.com', 'admin')
    ");

    echo "Database and tables created successfully!";
} catch (PDOException $e) {
    echo "Error creating database or tables: " . $e->getMessage();
}

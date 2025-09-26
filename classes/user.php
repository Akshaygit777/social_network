<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, full_name, email, age FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    // User not found, log out
    session_destroy();
    header("Location: login.php");
    exit;
}

// Now you can use $user['full_name'], $user['email'], $user['age'] etc.

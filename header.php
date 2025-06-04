<?php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'tickets.php';

$auth = new Auth($pdo);
$ticketSystem = new TicketSystem($pdo);

$currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk Ticket System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticket-status-new { background-color: #d4edda; }
        .ticket-status-in-progress { background-color: #fff3cd; }
        .ticket-status-resolved { background-color: #cce5ff; }
        .ticket-status-closed { background-color: #f8d7da; }
        .internal-comment { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Helpdesk System</a>
            <div class="navbar-nav">
                <?php if ($currentUser): ?>
                    <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($currentUser['first_name']) ?></span>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
    
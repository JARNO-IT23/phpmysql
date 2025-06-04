<?php
require_once 'header.php';

if (!$currentUser) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticketSystem->createTicket(
            $currentUser['user_id'],
            $_POST['category'],
            $_POST['subject'],
            $_POST['description']
        );
        $_SESSION['message'] = 'Ticket created successfully!';
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<h2>Create New Ticket</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select class="form-select" id="category" name="category" required>
            <option value="IT">IT</option>
            <option value="HR">HR</option>
            <option value="Administration">Administration</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Ticket</button>
</form>

<?php require_once 'footer.php'; ?>
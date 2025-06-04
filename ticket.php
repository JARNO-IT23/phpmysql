<?php
require_once 'header.php';

if (!$currentUser) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ticketId = $_GET['id'];
$ticket = $ticketSystem->getTicket($ticketId, $currentUser['user_id'], $currentUser['is_employee']);
$comments = $ticketSystem->getComments($ticketId, $currentUser['user_id'], $currentUser['is_employee']);

if (!$ticket) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
        $ticketSystem->addComment(
            $ticketId,
            $currentUser['user_id'],
            $_POST['comment'],
            isset($_POST['is_internal']) && $currentUser['is_employee']
        );
        header("Location: ticket.php?id=$ticketId");
        exit();
    } elseif (isset($_POST['status'])) {
        try {
            $ticketSystem->updateTicketStatus(
                $ticketId,
                $_POST['status'],
                $currentUser['user_id'],
                $currentUser['is_employee']
            );
            header("Location: ticket.php?id=$ticketId");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<h2>Ticket #<?= $ticket['ticket_id'] ?>: <?= htmlspecialchars($ticket['subject']) ?></h2>

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <span>Category: <?= $ticket['category'] ?></span>
            <span>Status: <?= $ticket['status'] ?></span>
            <span>Created: <?= date('M j, Y g:i a', strtotime($ticket['created_at'])) ?></span>
        </div>
    </div>
    <div class="card-body">
        <p class="card-text"><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
    </div>
</div>

<h3>Comments</h3>
<div class="mb-4">
    <?php foreach ($comments as $comment): ?>
        <div class="card mb-2 <?= $comment['is_internal'] ? 'internal-comment' : '' ?>">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <span><?= htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) ?></span>
                    <span><?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?></span>
                </div>
                <?php if ($comment['is_internal']): ?>
                    <span class="badge bg-secondary">Internal Note</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h3>Add Comment</h3>
<form method="post">
    <div class="mb-3">
        <textarea class="form-control" name="comment" rows="3" required></textarea>
    </div>
    <?php if ($currentUser['is_employee']): ?>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_internal" name="is_internal">
            <label class="form-check-label" for="is_internal">Internal Note (not visible to user)</label>
        </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Submit Comment</button>
</form>

<?php if ($currentUser['is_employee'] || ($ticket['status'] == 'Resolved' && $ticket['user_id'] == $currentUser['user_id'])): ?>
    <hr>
    <h3>Update Status</h3>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <select class="form-select" name="status">
                <?php if ($currentUser['is_employee']): ?>
                    <option value="New" <?= $ticket['status'] == 'New' ? 'selected' : '' ?>>New</option>
                    <option value="In Progress" <?= $ticket['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= $ticket['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="Closed" <?= $ticket['status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
                <?php else: ?>
                    <option value="New">Reopen Ticket</option>
                <?php endif; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </form>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
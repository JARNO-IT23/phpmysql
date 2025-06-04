<?php
require_once 'header.php';

if (!$currentUser) {
    header("Location: login.php");
    exit();
}

$tickets = $ticketSystem->getTickets($currentUser['user_id'], $currentUser['is_employee']);
?>

<h2><?= $currentUser['is_employee'] ? 'All Tickets' : 'My Tickets' ?></h2>

<?php if ($currentUser['is_employee']): ?>
    <div class="mb-3">
        <a href="create_ticket.php" class="btn btn-primary">Create New Ticket</a>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr class="ticket-status-<?= strtolower(str_replace(' ', '-', $ticket['status'])) ?>">
                    <td><?= $ticket['ticket_id'] ?></td>
                    <td><?= htmlspecialchars($ticket['subject']) ?></td>
                    <td><?= $ticket['category'] ?></td>
                    <td><?= $ticket['status'] ?></td>
                    <td><?= date('M j, Y g:i a', strtotime($ticket['created_at'])) ?></td>
                    <td>
                        <a href="ticket.php?id=<?= $ticket['ticket_id'] ?>" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
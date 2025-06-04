<?php
require_once 'config.php';

class TicketSystem {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createTicket($userId, $category, $subject, $description) {
        $duplicateHash = md5($userId . strtolower($subject));
        $stmt = $this->pdo->prepare("SELECT ticket_id FROM tickets WHERE user_id = ? AND duplicate_hash = ? AND created_at > NOW() - INTERVAL 1 DAY");
        $stmt->execute([$userId, $duplicateHash]);
        
        if ($stmt->fetch()) {
            throw new Exception("A similar ticket was submitted recently.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO tickets (user_id, category, subject, description, duplicate_hash) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $category, $subject, $description, $duplicateHash]);
    }

    public function getTickets($userId, $isEmployee = false) {
        if ($isEmployee) {
            $stmt = $this->pdo->prepare("SELECT * FROM tickets ORDER BY created_at DESC");
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicket($ticketId, $userId, $isEmployee = false) {
        if ($isEmployee) {
            $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
            $stmt->execute([$ticketId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE ticket_id = ? AND user_id = ?");
            $stmt->execute([$ticketId, $userId]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTicketStatus($ticketId, $newStatus, $userId, $isEmployee) {
        $ticket = $this->getTicket($ticketId, $userId, $isEmployee);
        if (!$ticket) {
            throw new Exception("Ticket not found.");
        }

        if (!$isEmployee && !($newStatus == 'New' && $ticket['status'] == 'Resolved')) {
            throw new Exception("You can only reopen resolved tickets.");
        }

        if ($isEmployee && $newStatus == 'Closed' && $ticket['status'] != 'Resolved') {
            throw new Exception("Ticket must be resolved before closing.");
        }

        $stmt = $this->pdo->prepare("UPDATE tickets SET status = ? WHERE ticket_id = ?");
        return $stmt->execute([$newStatus, $ticketId]);
    }

    public function addComment($ticketId, $userId, $commentText, $isInternal = false) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (ticket_id, user_id, comment_text, is_internal) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$ticketId, $userId, $commentText, $isInternal]);
    }

    public function getComments($ticketId, $userId, $isEmployee) {
        if ($isEmployee) {
            $stmt = $this->pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM comments c JOIN users u ON c.user_id = u.user_id WHERE ticket_id = ? ORDER BY created_at ASC");
            $stmt->execute([$ticketId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM comments c JOIN users u ON c.user_id = u.user_id WHERE ticket_id = ? AND (c.is_internal = FALSE OR c.user_id = ?) ORDER BY created_at ASC");
            $stmt->execute([$ticketId, $userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
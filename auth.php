<?php
require_once 'config.php';

class Auth {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($firstName, $lastName, $personalId, $email, $password, $isEmployee = false) {
        // Check if email or personal ID already exists
        $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ? OR personal_id = ?");
        $stmt->execute([$email, $personalId]);
        if ($stmt->fetch()) {
            return false; // User already exists
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, personal_id, email, password_hash, is_employee) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$firstName, $lastName, $personalId, $email, $passwordHash, $isEmployee]);
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return false;
    }

    public function verifyEmail($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET email_verified = TRUE WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
?>
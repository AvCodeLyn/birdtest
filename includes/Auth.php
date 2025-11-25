<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    public function requireRole(string $role): void
    {
        $user = $this->currentUser();
        if (!$user || $user['role'] !== $role) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

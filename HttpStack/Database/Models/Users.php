<?php
namespace HttpStack\DataBase\Models;
use HttpStack\DataBase\DBConnect;

class Users
{
    protected DBConnect $db;
    protected string $table = 'users';

    public function __construct(DBConnect $db)
    {
        $this->db = $db;
    }

    // Signup: create a new user
    public function create(string $email, string $password, string $user_level = 'user'): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (email, password, user_level) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [$email, $hash, $user_level]);
    }

    // Login: verify user credentials
    public function authenticate(string $email, string $password): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $users = $this->db->query($sql, [$email]);
        if ($users && password_verify($password, $users[0]['password'])) {
            return $users[0];
        }
        return null;
    }

    // Admin: get all users
    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table}");
    }

    // Admin: update user level
    public function updateUserLevel(int $id, string $level): bool
    {
        $sql = "UPDATE {$this->table} SET user_level = ? WHERE id = ?";
        return $this->db->execute($sql, [$level, $id]);
    }

    // Admin: delete user
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
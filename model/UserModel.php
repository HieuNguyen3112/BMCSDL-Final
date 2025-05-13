<?php
// Bai02/model/UserModel.php

class UserModel {
    private $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // Tìm user theo username
    public function findByUsername(string $username): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM `user` WHERE `username` = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    // Tạo user mới (hash password)
    public function create(array $data): bool {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO `user` (username, password, firstname, lastname)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
          "ssss",
          $data['username'],
          $passwordHash,
          $data['firstname'],
          $data['lastname']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cập nhật last_login
    public function updateLastLogin(int $id): void {
        $stmt = $this->conn->prepare(
          "UPDATE `user` SET last_login = NOW() WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

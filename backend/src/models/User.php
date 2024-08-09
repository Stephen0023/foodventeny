<?php
require_once 'config/database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDatabaseConnection();
    }

    public function getAllUsers()
    {
        $query = "SELECT * FROM users";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createUser($data)
    {
        $query = "INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['password'], $data['user_type']);
        if ($stmt->execute()) {
            return $data['email'];
        }
        return false;
    }

    public function login($email, $password)
    {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Verify the password
        if ($password === $user['password']) {
            return [
                'success' => true,
                'user_id' => $user['id'],
                'username' => $user['name'],
                'email' => $user['email'],
                'user_type' => $user['user_type']
            ];
        } else {
            return ['success' => false, 'message' => 'Invalid email or password','passed_password' => $password,
            'stored_password' => $user['password']];
        }
    }

    public function updateUser($id, $data)
    {
        $query = "UPDATE users SET name = ?, email = ?, password = ?, user_type = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $data['name'], $data['email'], $data['password'], $data['user_type'], $id);
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

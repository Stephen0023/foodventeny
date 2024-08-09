<?php
require_once 'config/database.php';

class ApplicationType {
    private $conn;

    public function __construct() {
        $this->conn = getDatabaseConnection();
    }

    public function getAllApplicationTypes() {
        $query = "SELECT * FROM application_types";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getApplicationTypeById($id) {
        $query = "SELECT * FROM application_types WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createApplicationType($data) {
        $query = "INSERT INTO application_types (title, description, deadline, cover_photo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $data['title'], $data['description'], $data['deadline'], $data['cover_photo']);
        return $stmt->execute();
    }

    public function updateApplicationType($id, $data) {
        $query = "UPDATE application_types SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $data['name'], $data['description'], $id);
        return $stmt->execute();
    }

    public function deleteApplicationType($id) {
        $query = "DELETE FROM application_types WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

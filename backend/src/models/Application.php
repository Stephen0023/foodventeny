<?php
class Application implements CursorAble
{
    private $conn;
    private $results;
    private $pageSize;

    public function __construct($pageSize = 5)
    {
        $this->conn = getDatabaseConnection();
        $this->pageSize = $pageSize;
    }

    public function fetchNext($startingAfter, $filters = [])
    {
        $query = "SELECT applications.*, 
                     application_types.title as application_type_title, 
                     application_types.description as application_type_description, 
                     application_types.deadline as application_type_deadline, 
                     application_types.cover_photo as application_type_cover_photo, 
                     users.name as user_name, 
                     users.email as user_email 
              FROM applications 
              JOIN application_types ON applications.application_type_id = application_types.id 
              JOIN users ON applications.user_id = users.id 
              WHERE applications.id > ?";
        $params = [$startingAfter];
        $types = 'i';

        if (isset($filters['status'])) {
            $query .= " AND applications.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (isset($filters['type_id'])) {
            $query .= " AND applications.application_type_id = ?";
            $params[] = $filters['type_id'];
            $types .= 'i';
        }

        if (isset($filters['user_id'])) {
            $query .= " AND applications.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }

        $query .= " ORDER BY applications.id ASC LIMIT ?";
        $params[] = $this->pageSize;
        $types .= 'i';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $this->results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $this->results;
    }

    public function fetchPrev($startingBefore, $filters = [])
    {
        $query = "SELECT applications.*, 
                     application_types.title as application_type_title, 
                     application_types.description as application_type_description, 
                     application_types.deadline as application_type_deadline, 
                     application_types.cover_photo as application_type_cover_photo, 
                     users.name as user_name, 
                     users.email as user_email 
              FROM applications 
              JOIN application_types ON applications.application_type_id = application_types.id 
              JOIN users ON applications.user_id = users.id 
              WHERE applications.id < ?";
        $params = [$startingBefore];
        $types = 'i';

        if (isset($filters['status'])) {
            $query .= " AND applications.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (isset($filters['type_id'])) {
            $query .= " AND applications.application_type_id = ?";
            $params[] = $filters['type_id'];
            $types .= 'i';
        }

        if (isset($filters['user_id'])) {
            $query .= " AND applications.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }

        $query .= " ORDER BY applications.id DESC LIMIT ?";
        $params[] = $this->pageSize;
        $types .= 'i';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $this->results = array_reverse($stmt->get_result()->fetch_all(MYSQLI_ASSOC));

        return $this->results;
    }

    public function getPreviousLink($filters = [])
    {
        if (!$this->results || empty($this->results)) {
            return '';
        }

        // Ensure that the previous link is valid based on current filters
        if ($this->results[0]['id'] === (int)$this->getMinId($filters)) {
            return ''; // No previous data, return empty link
        }

        return '?starting_before=' . $this->results[0]['id'];
    }

    public function getNextLink($filters = [])
    {
        if (!$this->results || count($this->results) < $this->pageSize) {
            return '';
        }

        // Ensure that the next link is valid based on current filters
        if ($this->results[count($this->results) - 1]['id'] === (int)$this->getMaxId($filters)) {
            return ''; // No further data, return empty link
        }

        return '?starting_after=' . $this->results[count($this->results) - 1]['id'];

    }

    private function getMinId($filters = [])
    {
        
        $query = "SELECT MIN(id) as min_id FROM applications WHERE 1=1";
        $params = [];
        $types = '';

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (isset($filters['type_id'])) {
            $query .= " AND application_type_id = ?";
            $params[] = $filters['type_id'];
            $types .= 'i';
        }

        if (isset($filters['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }

        
        
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['min_id'] : null;

    }


    private function getMaxId($filters = [])
    {

        $query = "SELECT MAX(id) as max_id FROM applications WHERE 1=1";
        $params = [];
        $types = '';

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (isset($filters['type_id'])) {
            $query .= " AND application_type_id = ?";
            $params[] = $filters['type_id'];
            $types .= 'i';
        }

        if (isset($filters['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }

        $stmt = $this->conn->prepare($query);
       if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result ? $result['max_id'] : null;
    }

    public function getApplicationById($id)
    {
        $query = "SELECT applications.*, 
                     application_types.title as application_type_title, 
                     application_types.description as application_type_description, 
                     application_types.deadline as application_type_deadline, 
                     application_types.cover_photo as application_type_cover_photo, 
                     users.name as user_name, 
                     users.email as user_email 
              FROM applications 
              JOIN application_types ON applications.application_type_id = application_types.id 
              JOIN users ON applications.user_id = users.id 
              WHERE applications.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createApplication($data)
    {
        $status = isset($data['status']) ? $data['status'] : 'pending';
        $query = "INSERT INTO applications (user_id, application_type_id, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $data['user_id'], $data['application_type_id'], $status);
        return $stmt->execute();
    }

    public function updateApplication($id, $data)
    {
        $id = (int) $id;
        $query = "UPDATE applications SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si",  $data['status'], $id);
        return $stmt->execute();
    }

    public function deleteApplication($id)
    {
        $query = "DELETE FROM applications WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

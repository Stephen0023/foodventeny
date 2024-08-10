<?php
require_once 'models/Application.php';

class ApplicationController {
    private $applicationModel;

    public function __construct() {
        $this->applicationModel = new Application();
    }

    public function getApplications() {
        $startingAfter = $_GET['starting_after'] ?? 0;
        $startingBefore = $_GET['starting_before'] ?? null;
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'type_id' => $_GET['application_type_id'] ?? null,
            'user_id' => $_GET['user_id'] ?? null
        ];

        $applications = [];

        if ($startingAfter) {
            $applications = $this->applicationModel->fetchNext($startingAfter, $filters);
        } elseif ($startingBefore) {
            $applications = $this->applicationModel->fetchPrev($startingBefore, $filters);
        } else {
            $applications = $this->applicationModel->fetchNext(0, $filters); // Start from beginning if no cursor provided
        }

        $response = [
            'data' => $applications,
            'next' => $this->applicationModel->getNextLink($filters),
            'previous' => $this->applicationModel->getPreviousLink($filters)
        ];

        // $response = [
        //     'data' => $applications,
        //     'next' => $filters,
        //     'previous' => $filters
        // ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function getApplication($id) {
        $application = $this->applicationModel->getApplicationById($id);
        header('Content-Type: application/json');
        echo json_encode($application);
    }

    public function createApplication($data) {
        $result = $this->applicationModel->createApplication($data);
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }

    public function updateApplication($id, $data) {
        $result = $this->applicationModel->updateApplication($id, $data);
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }

    public function deleteApplication($id) {
        $result = $this->applicationModel->deleteApplication($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
}
?>

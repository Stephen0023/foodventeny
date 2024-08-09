<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'models/CursorAble.php';
require_once 'controllers/UserController.php';
require_once 'controllers/ApplicationController.php';
require_once 'controllers/ApplicationTypeController.php';



function route()
{
    // Get the requested URI and request method
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // Route based on URI and request method
    switch ($requestUri) {
            // Route for fetching all users
        case '/api/users':
            $userController = new UserController();
            switch ($requestMethod) {
                case 'GET':
                    $userController->getUsers();
                    break;
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $userController->createUser($data);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

        case '/api/users/login':
            $userController = new UserController();
            if ($requestMethod === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $userController->loginUser($data);
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
            }
            break;

            // Route for fetching a single user by ID
        case preg_match('/^\/api\/users\/(\d+)$/', $requestUri, $matches) ? true : false:
            $userId = $matches[1];
            $userController = new UserController();
            switch ($requestMethod) {
                case 'GET':
                    $userController->getUser($userId);
                    break;
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $userController->updateUser($userId, $data);
                    break;
                case 'DELETE':
                    $userController->deleteUser($userId);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

            // Route for fetching all applications
        case '/api/applications':
            $applicationController = new ApplicationController();
            switch ($requestMethod) {
                case 'GET':
                    $applicationController->getApplications();
                    break;
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $applicationController->createApplication($data);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

            // Route for fetching, updating, or deleting a single application by ID
        case preg_match('/^\/api\/applications\/(\d+)$/', $requestUri, $matches) ? true : false:
            $applicationId = $matches[1];
            $applicationController = new ApplicationController();
            switch ($requestMethod) {
                case 'GET':
                    $applicationController->getApplication($applicationId);
                    break;
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $applicationController->updateApplication($applicationId, $data);
                    break;
                case 'DELETE':
                    $applicationController->deleteApplication($applicationId);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

            // Route for fetching all application types
        case '/api/application-types':
            $applicationTypeController = new ApplicationTypeController();
            switch ($requestMethod) {
                case 'GET':
                    $applicationTypeController->getApplicationTypes();
                    break;
                case 'POST':
                    // $data = json_decode(file_get_contents('php://input'), true);
                    $data = [
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'deadline' => $_POST['deadline'],
                        'cover_photo' => $_FILES['cover_photo'] 
                    ];
                    // echo json_encode(['success' => true, 'message' => $data]);
                    $applicationTypeController->createApplicationType($data);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

            // Route for fetching, updating, or deleting a single application type by ID
        case preg_match('/^\/api\/application-types\/(\d+)$/', $requestUri, $matches) ? true : false:
            $applicationTypeId = $matches[1];
            $applicationTypeController = new ApplicationTypeController();
            switch ($requestMethod) {
                case 'GET':
                    $applicationTypeController->getApplicationType($applicationTypeId);
                    break;
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $applicationTypeController->updateApplicationType($applicationTypeId, $data);
                    break;
                case 'DELETE':
                    $applicationTypeController->deleteApplicationType($applicationTypeId);
                    break;
                default:
                    http_response_code(405); // Method Not Allowed
                    echo json_encode(['error' => 'Method Not Allowed', 'url' => $requestUri, 'method' => $requestMethod]);
                    break;
            }
            break;

        default:
            $message = "Route not found: " . $requestUri . " with method: " . $requestMethod;
            error_log($message);
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => $message, 'url' => $requestUri, 'method' => $requestMethod]);
            break;
    }
}

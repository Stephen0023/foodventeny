<?php
require_once 'models/User.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getUsers()
    {
        $users = $this->userModel->getAllUsers();
        echo json_encode($users);
    }

    public function getUser($id)
    {
        header('Content-Type: application/json');
        $user = $this->userModel->getUserById($id);
        echo json_encode($user);
    }

    public function getUserByEmail($email)
    {
        header('Content-Type: application/json');
        $user = $this->userModel->getUserByEmail($email);
        echo json_encode($user);
    }

    public function createUser($data)
    {
        $email = $this->userModel->createUser($data);
        if ($email) {

            $loginResult = $this->userModel->login($email, $data['password']);

            if ($loginResult['success']) {
                session_start();
                $_SESSION['user_id'] = $loginResult['user_id'];
                $_SESSION['username'] = $loginResult['username'];
                $_SESSION['user_type'] = $loginResult['user_type'];
            }

            echo json_encode($loginResult);
        } else {
            echo json_encode(['success' => false, 'message' => 'User creation failed']);
        }
    }

    public function loginUser($data)
    {
        $email = $data['email'];
        $password = $data['password'];

        $result = $this->userModel->login($email, $password);

        if ($result['success']) {
            session_start();
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['user_type'] = $result['user_type'];
        }

        echo json_encode($result);
    }

    public function updateUser($id, $data)
    {
        header('Content-Type: application/json');
        $result = $this->userModel->updateUser($id, $data);
        echo json_encode(['success' => $result]);
    }

    public function deleteUser($id)
    {
        header('Content-Type: application/json');
        $result = $this->userModel->deleteUser($id);
        echo json_encode(['success' => $result]);
    }
}

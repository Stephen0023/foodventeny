<?php
session_start();

require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $userData = $user->getUserByEmail($email);

    if ($userData && password_verify($password, $userData['password'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_type'] = $userData['user_type'];

    } else {
        echo "Invalid email or password.";
    }
}
?>

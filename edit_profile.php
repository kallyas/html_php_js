<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$username;
$email;
$password;
$phone;
$errors = [];

require_once('db_config.php');

//$sql_profiles = "INSERT INTO profiles";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['tel'];
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = "username is required";
    }

    if (empty($email)) {
        $errors[] = "email is required";
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($_POST['tel'])) {
        $errors[] = "Phone number is required";
    } else {
        $phone = $_POST['tel'];
        if (!preg_match("/^\+(?:[0-9] ?){6,12}[0-9]$/", $phone)) {
            $errors[] = $phone . ' is not a valid phone number';
        }
    }

    if (empty($password)) {
        $errors[] = "password is required";
    }

    $sql_users =  "UPDATE SET username = '$username',
                email = '$email', phone = '$phone', password = password_hash($password, PASSWORD_DEFAULT)
                WHERE user_id = $user_id";

    $statement = $pdo->prepare($sql_users);

    if (empty($errors)) {
        try {
            $statement->execute();
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }
        header('Location: profile.php');
    }
}

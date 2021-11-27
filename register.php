<?php
session_start();
//check if already logged in!
if (isset($_SESSION['username'])) {
    header('Location: index.php');
}

$username = "";
$email = "";
$password = "";
$phone = "";
$id = "";
$errors = [];

try {
    //require db connection
    require_once('db_config.php');
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

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

    $sql = "INSERT INTO users(username, email, phone, password) 
    values(:username, :email, :phone, :password)";

    $sql_profiles = "INSERT INTO profiles(profile_user)
    VALUES(:profile_user)";

    $statement = $pdo->prepare($sql);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':phone', $phone);
    $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));

    $stmt = $pdo->prepare($sql_profiles);

    if (empty($errors)) {
        try {
            $statement->execute();
            $id = $pdo->lastInsertId();
            $stmt->bindValue(':profile_user', $id);
            $stmt->execute();
            header('Location: login.php');
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | PHP APP</title>
    <link rel="stylesheet" href="./css/semantic.min.css">
</head>

<body class="ui container" style="margin-top: 50px;">
    <main class="ui raised very padded text container segment">
        <?php if ($errors) : ?>
            <div class="ui form error" style="margin-bottom: 10px;">
                <div class="ui error message">
                    <?php foreach ($errors as $err) : ?>
                        <ul class="list">
                            <li><?php echo $err; ?></li>
                        </ul>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-container">
            <h2 class="ui dividing header">Create your Account here</h2>
            <form action="" method="post" class="ui form">
                <div class="field">
                    <input type="text" name="username" value="<?php echo $username; ?>" placeholder="username">
                </div>
                <div class="field">
                    <input type="email" name="email" value="<?php echo $email; ?>" placeholder="email">
                </div>
                <div class="field">
                    <input type="tel" name="tel" placeholder="+256770000000" value="<?php echo $phone; ?>" pattern="+(?:[0-9] ?){6,12}[0-9]">
                </div>
                <div class="field">
                    <input type="password" name="password" value="<?php echo $password; ?>" placeholder="password">
                </div>
                <input type="submit" value="submit" class="ui primary basic button">
            </form>
        </div>
    </main>
    <div class="ui center aligned basic segment">
        <div class="ui horizontal divider" style="margin-top: 50px;">
            Or
        </div>
        <div class="ui teal labeled icon button" onclick="window.location.href = 'login.php'">
            Login
            <i class="add icon"></i>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js" integrity="sha512-dqw6X88iGgZlTsONxZK9ePmJEFrmHwpuMrsUChjAw1mRUhUITE5QU9pkcSox+ynfLhL15Sv2al5A0LVyDCmtUw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
</body>

</html>
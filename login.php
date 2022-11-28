<?php
session_start();

//check if already logged in!
if (isset($_SESSION['username'])) {
    header('Location: index.php');
}

$username = '';
$password = '';
$errors = [];

$sql = "SELECT * FROM users WHERE username = :username";

try {
    //require db connection
    require_once('db_config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (empty($username)) {
            $errors[] = "username is required!";
        }

        if (empty($password)) {
            $errors[] = "password is required";
        }

        $statement = $pdo->prepare($sql);

        if (empty($errors)) {
            $statement->execute(array(
                'username' => $username
            ));
            $row = $statement->fetch(PDO::FETCH_ASSOC);


            if ($statement->rowCount() > 0) {
                if ($username == $row['username'] && password_verify($password, $row['password'])) {
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['user_id'] = $row['id'];

                    header("Location: index.php");
                } else {
                    $errors[] = "The username or password you entered is wrong!";
                }
            } else {
                $errors[] = "No such user found in our database!";
            }
        }
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PHP App</title>
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
            <h2 class="ui dividing header">Login into your account</h2>
            <form action="" method="post" class="ui form">
                <div class="field">
                    <input type="text" name="username" value="<?php echo $username; ?>" placeholder="username">
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
        <div class="ui teal labeled icon button" onclick="window.location.href = 'register.php'">
            Register
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
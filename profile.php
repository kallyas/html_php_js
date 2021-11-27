<?php
//require db connection
require_once('db_config.php');
session_start();

//check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

//get user id
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}


$success;
$failure;

// get success status
if (isset($_GET['status']) == true) {
    if ($_GET['status'] == 'success') {
        $success = true;
    }
}

// get failure status
if (isset($_GET['status']) == true) {
    if ($_GET['status'] == 'failure') {
        $failure = true;
    }
}


$errors = [];
$img = "https://afribary.com/authors/tumuhirwe-iden/photo";
$username = $_SESSION['username'];
$email;
$file_path;
$password;
$phone;
$sql = "SELECT * from users inner join profiles on profiles.profile_user = users.user_id WHERE username = '$username'";

$statement = $pdo->prepare($sql);
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $file_path = "images/" . $_FILES["profile_img"]["name"];

    // upload image
    move_uploaded_file($_FILES["profile_img"]["tmp_name"], $file_path);

    // validate form data
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

    // init update query
    $update = "UPDATE users SET username = '$username',
    email = '$email', phone = '$phone', password = '$password'
    WHERE user_id = $user_id";

    $update_profile = "UPDATE profiles SET profile_img = '$file_path'
    WHERE profile_user = $user_id";

    // execute update query
    $statement = $pdo->prepare($update);
    $stmt = $pdo->prepare($update_profile);

    if (empty($errors)) {
        try {
            $statement->execute();
            if (!empty($_FILES['profile_img']['name'])) {
                $stmt->execute();
            }
            $_SESSION['username'] = $username;
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }
        header('Location: profile.php?status=success');
    } else {
        header('Location: profile.php?status=failure');
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | PHP APP</title>
    <link rel="stylesheet" href="./css/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
</head>

<body class="container" style="margin-top: 50px;">
    <main class="ui two column doubling stackable grid container ">
        <div class="four column row">
            <div class="left floated column">
                <h2><?php echo $username; ?></h2>
                <div class="container">
                    <img class="ui medium circular image" height="50" width="50" src="<?php echo $row['profile_img'] ?? $img; ?>">
                </div>
            </div>
            <div class="eight wide column">
                <?php if (isset($success)) : ?>
                    <div class="ui positive message">
                        <i class="close icon"></i>
                        <div class="header">
                            Success!
                        </div>
                        <p>your profile details have been updated successfully!.</p>
                    </div>
                <?php endif; ?>
                <?php if(isset($failure)) : ?>
                    <div class="ui negative message">
                        <i class="close icon"></i>
                        <div class="header">
                            Failure!
                        </div>
                        <p>your profile details could not be updated!.</p>
                    </div>
                <?php endif; ?>
                <h3>Personal Information</h3>
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
                    <h2 class="ui dividing header"></h2>
                    <form action="" method="post" class="ui form" enctype="multipart/form-data">
                        <div class="field">
                            <input type="text" name="username" value="<?php echo $row['username']; ?>" placeholder="username">
                        </div>
                        <div class="field">
                            <input type="email" name="email" value="<?php echo $row['email']; ?>" placeholder="email">
                        </div>
                        <div class="field">
                            <input type="tel" name="tel" placeholder="+256770000000" value="<?php echo $row['phone']; ?>" pattern="+(?:[0-9] ?){6,12}[0-9]">
                        </div>
                        <div class="field">
                            <input type="password" name="password" value="<?php echo $row['password']; ?>" placeholder="password" readonly>
                        </div>
                        <div class="field">
                            <input type="file" name="profile_img">
                        </div>
                        <input type="submit" value="submit" class="ui primary basic button">
                    </form>
                </div>
            </div>
            <div class="right floated column">
                Navigate Back to Home Page
                <a href="index.php">Home</a>
            </div>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js" integrity="sha512-dqw6X88iGgZlTsONxZK9ePmJEFrmHwpuMrsUChjAw1mRUhUITE5QU9pkcSox+ynfLhL15Sv2al5A0LVyDCmtUw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.message .close')
            .on('click', function () {
                $(this)
                    .closest('.message')
                    .transition('fade');
            });
    </script>
</body>

</html>
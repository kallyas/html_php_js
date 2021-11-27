<?php
session_start();

// check if user is logged in!
if (isset($_SESSION['username'])) {
    echo '<h2> Welcome ' . $_SESSION['username'] . '</h2>';
    echo '<br /><br /><a href="logout.php">Logout</a>';
    echo '<br /><br /><a href="profile.php">Profile</a>';
} else {
    header('Location: login.php');
}

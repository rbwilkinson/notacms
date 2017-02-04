<?php

session_start();

if (isset($_POST['login'])) {
    //Login uses database
    $user = strtolower(trim($_POST['username']));
    $password = $_POST['password'];
    $errors = array();

    $db = new SQLite3('site.db') or die('Unable to open database');
    $result = $db->query("SELECT username, password FROM users WHERE rowid = 1 AND username = '$user' ") or die('Query failed');
    while ($row = $result->fetchArray()) {
        echo $admin = $row['username'];
        echo $pwhash = $row['password'];
    }

    if (password_verify($password, $pwhash)) {
        echo ''; die();
    } else {
        echo 'failed'; die();
    }
    
    if ($user == '' || $user != $admin) { 
        $errors['user'] = '';
    }
    if (empty($errors)) {
        $_SESSION['user'] = $user;
        if ($user != '') {
            $state = 'user';
        }
    } else {
        echo '<p class="error">Please fill in your correct ';
        if (isset($errors['user']))
            echo 'username';
        if (count($errors) == 2)
            echo ' and ';
        if (isset($errors['pass']))
            echo 'password';
        echo '.</p>', "\n";
        die();
    }
}


header("Location: redirect.php?session=$state");
die();


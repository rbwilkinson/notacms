<?php

session_start();

if ($_REQUEST['session'] == 'user') {
    $user = $_SESSION['user'];
    header('Location: index.php?session=1&success=yes');
    die();
}


if ($_REQUEST['logout'] == 'yes') {
    header('Location: index.php?session=0');
    die();
}

if (isset($_REQUEST['success'])) {
    header('Location: index.php?session=1&success=yestext');
    die();
}




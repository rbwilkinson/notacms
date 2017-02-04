<?php
//** antiCMS **//

session_start();
// Set default timezone
date_default_timezone_set('UTC');

if (!file_exists($file_db)) {
    $db = new SQLite3('site.db') or die('Unable to open database');
    $query = <<<EOD
    CREATE TABLE IF NOT EXISTS users (
    username STRING,
    password STRING);
EOD;
    $db->exec($query) or die('Create user table failed');
    $username = 'admin';
    $password = 'password';
    $pwhash = password_hash($password, PASSWORD_DEFAULT);
    $query = <<<EOD
    INSERT INTO users VALUES ( '$username', '$pwhash' )
EOD;
    $db->exec($query) or die("Unable to add user $user");

    $query = <<<EOD
    CREATE TABLE IF NOT EXISTS general (
    company STRING,
    slogan STRING,
    phone STRING,
    email STRING,
    address1 STRING,
    address2 STRING,
    postcode STRING,
    latlong STRING,
    fb STRING,
    twit STRING,
    color1 STRING,
    color2 STRING);        
EOD;
    $db->exec($query) or die('Create general table failed');

    $company = 'Your Place';
    $slogan = 'we are a great company';
    $phone = '0123456789';
    $email = 'you@yourplace.com';
    $address1 = '333 High Street';
    $address2 = 'London';
    $postcode = 'EC1';
    $latlong = '51.50295, -0.09406';
    $fb = '';
    $twit = '';
    $color1 = 'whitesmoke';
    $color2 = 'pink';

    $query = <<<EOD
  INSERT INTO general VALUES ( '$company', '$slogan', '$phone', '$email', '$address1', '$address2', '$postcode', '$latlong', '$fb', '$twit', '$color1', '$color2'  )
EOD;
    $db->exec($query) or die("Unable to add data");
}

$db = new SQLite3('site.db') or die('Unable to open database');
$result = $db->query('SELECT * FROM general WHERE rowid = 1') or die('Query failed');
while ($row = $result->fetchArray()) {
    $company = $row['company'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>NotaCMS <?php echo " - $company; " ?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="refresh" content="900;url=?action=logout&time=expired" />


        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

        <script type="text/javascript" src="assets/jquery-te-1.4.0.min.js" charset="utf-8"></script>
        <script src="https://unpkg.com/leaflet@1.0.2/dist/leaflet.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.2/dist/leaflet.css" />
        <link type="text/css" rel="stylesheet" href="assets/jquery-te-1.4.0.css" />
        <link rel="stylesheet" href="style.css" type="text/css" />

        <script src='assets/spectrum.js'></script>
        <link rel='stylesheet' href='assets/spectrum.css' />

        <script>
            if (typeof window.history.pushState == 'function') {
                window.history.pushState({}, "Hide", "https://otgb.net/view/edit/index.php");
            }
        </script>


        <script>
            $(function () {
                $("#dialog").dialog({
                    autoOpen: false,
                    draggable: false,
                    resizable: false,
                    width: 320,
                    height: 70,
                    show: {
                        effect: 'blind',
                        duration: 20
                    },
                    hide: {
                        effect: 'fade',
                        delay: 1000,
                        duration: 2000
                    },
                    open: function () {
                        $(this).dialog('close');
                    },
                    close: function () {
                        $(this).dialog('destroy');
                    }
                });

                $(".ui-dialog-titlebar").remove();

                // Finally open the dialog! The open function above will run once
                // the dialog has opened. It runs the close function! After it has
                // faded out the dialog is destroyed
                $("#dialog").dialog("open");
            });

        </script>

    </head>
    <body>
        <?php
        ///**** LOGIN 
        if (isset($_POST['login'])) {
            //Login uses database
            $user = strtolower(trim($_POST['username']));
            $user = htmlspecialchars($user);
            $pass = htmlspecialchars($_POST['password']);
            $errors = array();

            $db = new SQLite3('site.db') or die('Unable to open database');
            $result = $db->query("SELECT * FROM users WHERE rowid = 1 ") or die('Query failed');
            while ($row = $result->fetchArray()) {
                $admin = $row['username'];
                $pwhash = $row['password'];
            }

            if (!password_verify($pass, $pwhash)) {
                $errors['pass'] = '';
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
            }
        }

        if (isset($_SESSION['user']) OR ( $_GET[session]) == 1) {
            $user = $_SESSION['user'];
            $value = 'online';
            setcookie("status", $value, time() + 3600);  /* expire in 1 hour */
            ?>

            <div id="wrapper">
                <div id="headertext">
                    <div class="left">
                        <a href="index.php"><img src="images/toadlogo.png" width="50" /></a>
                    </div>
                    <div class="left">

                        <h1>NotaCMS - <?php echo $company;?></h1> 
                        <?php
                        if (isset($_SESSION['user'])) {
                            $user = $_SESSION['user'];
                            ?>
                            You are logged in as <strong><?php echo $user; ?></strong>.
                            <p class="white"><a href="?credentials=editlogin">Edit Login</a></p>
                        <?php } ?>
                    </div>
                    <div class="right" style="padding-top: 10px;">  
                        <p class="r logout"><a href="?action=logout">Logout</a></p>
                        <p class="viewlink"><a href="../" target="_blank">View Site</a></p>
                    </div>
                </div>
                <div id="main">

                    <?php
                    if (isset($_GET['success'])) {
                        if ($_GET['success'] == 'yescreds') {
                            ?>
                            <div id="dialog" style="display: none;"><p>Successfully changed Login details.</p></div>
                            <?php
                        }
                        if ($_GET['success'] == 'yesgen') {
                            ?>
                            <div id="dialog" style="display: none;"><p>Successfully saved General details.</p></div>
                            <?php
                        }

                        if ($_GET['success'] == 'yestext') {
                            ?>
                            <div id="dialog" style="display: none;"><p>Successfully saved Main Text.</p></div>
                            <?php
                        }
                        $_GET['success'] = '';
                    }

                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == 'pwfail') {
                            echo '<div id="stats"><p>Passwords are not identical details.</p></div>';
                        }
                        $_GET['error'] = '';
                    }



                    if (isset($_POST['editcreds'])) {

                        echo $username = htmlspecialchars($_POST['username']);
                        echo $password1 = htmlspecialchars($_POST['password1']);
                        echo $password2 = htmlspecialchars($_POST['password2']);
                        if ($password1 != $password2) {
                            header('Location: index.php?error=pwfail');
                            die();
                        } elseif ($password1 == $password2) {
                            $pwhash = password_hash($password1, PASSWORD_DEFAULT);
                            $db = new SQLite3('site.db') or die('Unable to open database');
                            $db->exec(" UPDATE users SET username = '$username', password = '$pwhash' ");
                            header('Location: index.php?success=yes');
                            die();
                        }
                    }
                    ?>

                    <?php
                    if (isset($_GET['credentials'])) {
                        if ($_GET['credentials'] == 'editlogin') {
                            echo '<h1>Edit Credentials</h1>';
                            if ($_GET['error'] == 'pwfail') {
                                '<div id="test"><p><h3 style="color: red;">Passwords did not match</h3></p></div>';
                            }
                            ?>
                            <form method="post" action="" id="login"> 
                                <p>
                                    <label for="user">Username:</label><input type="text" name="username" id="username" value="" required/>
                                </p>
                                <p>
                                    <label for="pass">Password:</label><input type="password" name="password1" id="password1" value="" required/><br />
                                    <label for="pass">Confirm Password:</label><input type="password" name="password2" id="password2" value="" required />
                                </p>
                                <p>
                                    <button type="submit" name="editcreds">Submit</button>
                                </p>
                            </form>
                            <?php
                            die();
                        }
                    }
                    ?>

                    <?php
                    //************* LOGOUT here
                    if (empty($_POST) && isset($_GET['action'])) {
                        $action = $_GET['action'];
                        switch ($action) {
                            case 'logout':
                                session_unset();
                                session_destroy();
                                setcookie("status", '', time() - 3600);
                                break;
                        }
                        header('Location: redirect.php?logout=yes');
                        die();
                    }
                    ?>

                    <?php
                    if (isset($_POST['edit'])) {
                        if ($_POST['homecontent']) {
                            if (file_put_contents('text/main.txt', $_POST['homecontent']) !== FALSE) {
                                header('Location: redirect.php?success=yestext');
                            }
                        }

                        if ($_POST['menucontent']) {
                            if (file_put_contents('text/more.txt', $_POST['menucontent']) !== FALSE) {
                                header('Location: redirect.php?success=yestext');
                            }
                        }

                        if ($_POST['hourscontent']) {
                            if (file_put_contents('text/hours.txt', $_POST['hourscontent']) !== FALSE) {
                                header('Location: redirect.php?success=yestext');
                            }
                        }

                        if (isset($_POST['general'])) {
                            $company = htmlspecialchars($_POST['company']);
                            $company = strip_tags($company);
                            $slogan = htmlspecialchars($_POST['slogan']);
                            $slogan = strip_tags($slogan);
                            $phone = htmlspecialchars($_POST['phone']);
                            $phone = strip_tags($phone);
                            $email = htmlspecialchars($_POST['email']);
                            $email = strip_tags($email);
                            $address1 = htmlspecialchars($_POST['address1']);
                            $address1 = strip_tags($address1);
                            $address2 = htmlspecialchars($_POST['address2']);
                            $address2 = strip_tags($address2);
                            $postcode = htmlspecialchars($_POST['postcode']);
                            $postcode = strip_tags($postcode);
                            $latlong = htmlspecialchars($_POST['latlong']);
                            $latlong = strip_tags($latlong);
                            $fb = htmlspecialchars($_POST['fb']);
                            $fb = strip_tags($fb);
                            $twit = htmlspecialchars($_POST['twit']);
                            $twit = strip_tags($twit);
                            $color1 = htmlspecialchars($_POST['color1']);
                            $color1 = strip_tags($color1);
                            $color2 = htmlspecialchars($_POST['color2']);
                            $color2 = strip_tags($color2);

                            $db = new SQLite3('site.db') or die('Unable to open database');

                            $db->exec(" UPDATE general SET company = '$company', slogan = '$slogan', phone = '$phone', email = '$email', address1 = '$address1', address2 = '$address2', postcode = '$postcode', latlong = '$latlong', fb = '$fb', twit = '$twit', color1 = '$color1', color2 = '$color2' WHERE rowid = 1 ") or die("Unable to add data");

                            header('Location: index.php?success=yesgen');
                        }
                    }

                    $main = file_get_contents('text/main.txt');
                    $more = file_get_contents('text/more.txt');
                    $hours = file_get_contents('text/hours.txt');
                    ?>

                    <div id="tabs">
                        <ul>
                            <li><a href="#tab-1"><span>General</span></a></li>
                            <li><a href="#tab-2"><span>Main Text</span></a></li>
                            <li><a href="#tab-3"><span>Images</span></a></li>
                            <li><a href="#tab-4"><span>Locator</span></a></li>
                        </ul>

                        <div id="tab-1">

                            <div class="content">
                                <?php
                                $db = new SQLite3('site.db') or die('Unable to open database');
                                $result = $db->query('SELECT * FROM general WHERE rowid = 1') or die('Query failed');
                                while ($row = $result->fetchArray()) {
                                    $company = $row['company'];
                                    $slogan = $row['slogan'];
                                    $phone = $row['phone'];
                                    $email = $row['email'];
                                    $address1 = $row['address1'];
                                    $address2 = $row['address2'];
                                    $postcode = $row['postcode'];
                                    $latlong = $row['latlong'];
                                    $fb = $row['fb'];
                                    $twit = $row['twit'];
                                    $color1 = $row['color1'];
                                    $color2 = $row['color2'];
                                }
                                ?>

                                <h1>General Information</h1>
                                <form action='index.php' method='post' id='addgeneral'>
                                    <label>Company:</label><input type='text' name='company' id='company' value='<?php echo $company; ?>' ></input><br />
                                    <label>Slogan:</label><input type='text' name='slogan' id='slogan' value='<?php echo $slogan; ?>' ></input><br />
                                    <label>Phone:</label><input type='text' name='phone' id='phone' value='<?php echo $phone; ?>' ></input><br />
                                    <label>Email:</label><input type='text' name='email' id='email' value='<?php echo $email; ?>' ></input><br />
                                    <label>Address 1:</label><input type='text' name='address1' id='address1' value='<?php echo $address1; ?>' ></input><br />
                                    <label>Address 2:</label><input type='text' name='address2' id='address2' value='<?php echo $address2; ?>' ></input><br />
                                    <label>Post Code:</label><input type='text' name='postcode' id='postcode' value='<?php echo $postcode; ?>' ></input><br />
                                    <label>Lat/Long:</label><input type='text' name='latlong' id='latlong' value='<?php echo $latlong; ?>' ></input><br />
                                    <label>Facebook:</label><input type='text' name='fb' id='fb' value='<?php echo $fb; ?>' ></input><br />
                                    <label>Twitter:</label><input type='text' name='twit' id='twit' value='<?php echo $twit; ?>' ></input><br />
                                    <label>Primary color:</label><input type='text' name='color1' class="basic1" id='colorpicker-popup' value='<?php echo $color1; ?>' /><em class='basic1-log'></em><br />
                                    <label>Secondary color:</label><input type='text' name='color2' class="basic2" id='colorpicker-popup' value='<?php echo $color2; ?>' /><em class='basic2-log'></em><br />

                                    <input type='hidden' name='general' value='' />    
                                    <p><button type="submit" name="edit">Save changes</button></p>
                                </form>
                            </div>

                        </div>


                        <div id="tab-2">
                            <div class="content">
                                <form method="post" action="index.php">
                                    Main Text<br /><textarea class="jqte-test" name="homecontent" id="homecontent" rows="7" cols="65"><?php echo $main; ?></textarea><br />
                                    Menu Text<br /><textarea class="jqte-test" name="menucontent" id="menucontent" rows="7" cols="65"><?php echo $more; ?></textarea><br />
                                    Hours of Operation<br /><textarea class="jqte-test" name="hourscontent" id="hourscontent" rows="7" cols="65"><?php echo $hours; ?></textarea>

                                    <p><button type="submit" name="edit">Save changes</button></p>
                                </form>
                            </div>
                        </div>

                        <div id="tab-3">
                            <div class="content">
                                <?php
                                if (isset($_FILES['image'])) {
                                    $errors = array();
                                    $file_name = $_FILES['image']['name'];
                                    $file_size = $_FILES['image']['size'];
                                    $file_tmp = $_FILES['image']['tmp_name'];
                                    $file_type = $_FILES['image']['type'];
                                    $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                                    $extensions = array("jpeg", "jpg", "png", "JPG");

                                    if (in_array($file_ext, $extensions) === false) {
                                        $errors[] = "extension allowed, please choose a JPEG or PNG file.";
                                    }

                                    if ($file_size > 500715) {
                                        $errors[] = 'File size must be under 500kb ';
                                    }

                                    if (isset($_POST['logo'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/logo." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    } elseif (isset($_POST['img1'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/img1." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    } elseif (isset($_POST['img2'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/img2." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    } elseif (isset($_POST['img3'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/img3." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    } elseif (isset($_POST['img4'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/img4." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    } elseif (isset($_POST['geo'])) {
                                        if (empty($errors) == true) {
                                            $filename = "images/geo." . $file_ext;
                                            move_uploaded_file($file_tmp, $filename);
                                            chmod($filename, 0755);
                                            header('Location: index.php?success=image');
                                        } else {
                                            header("Location: index.php?error=$errors");
                                        }
                                    }
                                }
                                ?>

                                <?php
                                if (isset($_GET['delete'])) {
                                    if ($_GET['delete'] == 'logo') {
                                        if (file_exists('images/logo.png')) {
                                            $file = 'images/logo.png';
                                        } elseif (file_exists('images/logo.jpeg')) {
                                            $file = 'images/logo.jpeg';
                                        } elseif (file_exists('images/logo.jpg')) {
                                            $file = 'images/logo.jpg';
                                        }
                                        unlink($file);
                                    }
                                    if ($_GET['delete'] == 'img1') {
                                        if (file_exists('images/img1.png')) {
                                            $file = 'images/img1.png';
                                        } elseif (file_exists('images/img1.jpeg')) {
                                            $file = 'images/img1.jpeg';
                                        } elseif (file_exists('images/img1.jpg')) {
                                            $file = 'images/img1.jpg';
                                        }
                                        unlink($file);
                                    }
                                    if ($_GET['delete'] == 'img2') {
                                        if (file_exists('images/img2.png')) {
                                            $file = 'images/img2.png';
                                        } elseif (file_exists('images/img2.jpeg')) {
                                            $file = 'images/img2.jpeg';
                                        } elseif (file_exists('images/img2.jpg')) {
                                            $file = 'images/img2.jpg';
                                        }
                                        unlink($file);
                                    }
                                    if ($_GET['delete'] == 'img3') {
                                        if (file_exists('images/img3.png')) {
                                            $file = 'images/img3.png';
                                        } elseif (file_exists('images/img3.jpeg')) {
                                            $file = 'images/img3.jpeg';
                                        } elseif (file_exists('images/img3.jpg')) {
                                            $file = 'images/img3.jpg';
                                        }
                                        unlink($file);
                                    }
                                    if ($_GET['delete'] == 'img4') {
                                        if (file_exists('images/img4.png')) {
                                            $file = 'images/img4.png';
                                        } elseif (file_exists('images/img4.jpeg')) {
                                            $file = 'images/img4.jpeg';
                                        } elseif (file_exists('images/img4.jpg')) {
                                            $file = 'images/img4.jpg';
                                        }
                                        unlink($file);
                                    }
                                    if ($_GET['delete'] == 'geo') {
                                        if (file_exists('images/geo.png')) {
                                            $file = 'images/geo.png';
                                        } elseif (file_exists('images/geo.jpeg')) {
                                            $file = 'images/geo.jpeg';
                                        } elseif (file_exists('images/geo.jpg')) {
                                            $file = 'images/geo.jpg';
                                        }
                                        unlink($file);
                                    }
                                }
                                ?>

                                <div class="picrow" style="background-color: #f3f3f3">
                                    <?php
                                    if (file_exists('images/logo.png')) {
                                        $file = 'images/logo.png';
                                    } elseif (file_exists('images/logo.jpeg')) {
                                        $file = 'images/logo.jpeg';
                                    } elseif (file_exists('images/logo.jpg')) {
                                        $file = 'images/logo.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=logo' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Logo
                                            <input type="file" name="image" />
                                            <input type="hidden" name="logo" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>

                                </div>
                                <div class="picrow">
                                    <?php
                                    if (file_exists('images/img1.png')) {
                                        $file = 'images/img1.png';
                                    } elseif (file_exists('images/img1.jpeg')) {
                                        $file = 'images/img1.jpeg';
                                    } elseif (file_exists('images/img1.jpg')) {
                                        $file = 'images/img1.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=img1' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>

                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Image 1
                                            <input type="file" name="image" />
                                            <input type="hidden" name="img1" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>
                                </div>
                                <div class="picrow">
                                    <?php
                                    if (file_exists('images/img2.png')) {
                                        $file = 'images/img2.png';
                                    } elseif (file_exists('images/img2.jpeg')) {
                                        $file = 'images/img2.jpeg';
                                    } elseif (file_exists('images/img2.jpg')) {
                                        $file = 'images/img2.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=img2' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>

                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Image 2
                                            <input type="file" name="image" />
                                            <input type="hidden" name="img2" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>
                                </div>
                                <div class="picrow">
                                    <?php
                                    if (file_exists('images/img3.png')) {
                                        $file = 'images/img3.png';
                                    } elseif (file_exists('images/img3.jpeg')) {
                                        $file = 'images/img3.jpeg';
                                    } elseif (file_exists('images/img3.jpg')) {
                                        $file = 'images/img3.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=img3' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>

                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Image 3
                                            <input type="file" name="image" />
                                            <input type="hidden" name="img3" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>
                                </div>
                                <div class="picrow">
                                    <?php
                                    if (file_exists('images/img4.png')) {
                                        $file = 'images/img4.png';
                                    } elseif (file_exists('images/img4.jpeg')) {
                                        $file = 'images/img4.jpeg';
                                    } elseif (file_exists('images/img4.jpg')) {
                                        $file = 'images/img4.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=img4' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>

                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Image 4
                                            <input type="file" name="image" />
                                            <input type="hidden" name="img4" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>
                                </div>
                                <div class="picrow">
                                    <?php
                                    if (file_exists('images/geo.png')) {
                                        $file = 'images/geo.png';
                                    } elseif (file_exists('images/geo.jpeg')) {
                                        $file = 'images/geo.jpeg';
                                    } elseif (file_exists('images/geo.jpg')) {
                                        $file = 'images/geo.jpg';
                                    } else {
                                        $file = '';
                                    }
                                    if ($file != '') {
                                        echo "<img src='$file' width='50'> File: ../edit/$file <a href='index.php?delete=geo' class='imgdel'>Delete?</a>";
                                    }
                                    if ($file == '') {
                                        ?>

                                        <form action="" method="POST" enctype="multipart/form-data">
                                            Add Map Image
                                            <input type="file" name="image" />
                                            <input type="hidden" name="geo" />
                                            <input type="submit"/>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div id="tab-4">
                            Scroll and Click map to find coordinates.<br /> Copy and paste into <b>latlong</b> on General tab form.<br />
                            <div id="mapid" style="width: 100%; height: 400px;"></div>
                            <script>

                                var mymap = L.map('mapid').setView([51.505, -0.09], 13);

                                L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
                                    maxZoom: 18,
                                    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
                                            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                                            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
                                    id: 'mapbox.streets'
                                }).addTo(mymap);

                                var popup = L.popup();

                                function onMapClick(e) {
                                    popup
                                            .setLatLng(e.latlng)
                                            .setContent(e.latlng.toString())
                                            .openOn(mymap);
                                }

                                mymap.on('click', onMapClick);

                            </script>
                        </div>

                    </div>


                <?php } else { ?>
                    <div id='wrapper'>
                        <div class='signbox'>
                            <div class='l'>
                                <p><b>NotaCMS - <?php echo $company; ?></b></p>
                                <img src='images/toadlogo.png'>
                            </div>

                            <div class='r' style="width: 40%;">
                                <form method="post" action="" id="login"> 
                                    <p>
                                        <label for="user">Username:</label><input type="text" name="username" id="username" value="" required/>
                                    </p>
                                    <p>
                                        <label for="pass">Password:</label><input type="password" name="password" id="password" value="" required/>
                                    </p>
                                    <p>
                                        <button type="submit" name="login">Login</button>
                                    </p>
                                </form>
                            </div>
                            <div style='clear: both; text-align: center;'>Created by
                                <a href='http://otgb.net' target='_blank'>OTGB.net</a>
                                <p><i>'the world is open'</i></p>
                            </div>
                        </div>
                    </div><!-- /signbox -->
                <?php } ?>
            </div>
        </div>


        <script>
            function setCookie(cname, cvalue, exdays)
            {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toGMTString();
                document.cookie = cname + "=" + cvalue + "; " + expires;
            }

            function getCookie(cname)
            {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++)
                {
                    var c = ca[i].trim();
                    if (c.indexOf(name) == 0)
                        return c.substring(name.length, c.length);
                }
                return "";
            }

            function checkCookie()
            {
                var user = getCookie("username");
                if (user != "")
                {

                    return user;
                } else
                {
                    return false;

                }
            }

            jQuery(document).ready(function () {
                if (checkCookie()) {
                    $("#tabs").tabs({active: checkCookie()});
                } else {
                    $("#tabs").tabs({active: 0});
                }
                jQuery("#tabs ul li a").click(function () {
                    var status = jQuery(this).attr("href");
                    setCookie("username", parseInt(status.replace("#tab-", "")) - 1, 365);

                });


            });
        </script>

        <script>
            $("#stats p").delay(1000).animate({"opacity": "0"}, 1000);
        </script>

        <script>
            $('.jqte-test').jqte();

            // settings of status
            var jqteStatus = true;
            $(".status").click(function ()
            {
                jqteStatus = jqteStatus ? false : true;
                $('.jqte-test').jqte({"status": jqteStatus})
            });
        </script>



        <?php
        $db = new SQLite3('site.db') or die('Unable to open database');
        $result = $db->query('SELECT color1, color2 FROM general WHERE rowid = 1') or die('Query failed');
        while ($row = $result->fetchArray()) {
            $color1 = $row['color1'];
            $color2 = $row['color2'];
        }
        ?>

        <script>
            $(".basic1").spectrum({
                preferredFormat: "hex",
                showInput: true,
                allowEmpty: true,
                color: "<?php echo $color1; ?>",
                change: function (color) {
                    $(".basic1-log").text("change called: " + color.toHexString());

                }
            });
        </script>

        <script>
            $(".basic2").spectrum({
                preferredFormat: "hex",
                color: "<?php echo $color2; ?>",
                showInput: true,
                allowEmpty: true,
                change: function (color) {
                    $(".basic2-log").text("change called: " + color.toHexString());
                }
            });
        </script>
    </body>
</html>

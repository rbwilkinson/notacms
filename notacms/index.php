<div style='width: 100%; margin: auto; background-color: lightskyblue'>
    <div style='width: 80%; margin: auto; background-color: white; padding: 20px;'>
        <div style='width: 50%; margin: auto; margin-top; 30px; text-align: center;'>
            <h4>NotaCMS - Open Technology :: GB</h4>
            <i>'the world is open'</i>
            <h6>Read the code for comments and to derive the data source.</h6>

        </div>
        <?php
## NotaCMS data access details


        $db = new SQLite3('edit/site.db') or die('Unable to open database');
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
        echo "<h3 style='color: navy;'>Defaults from database:</h3>";
        echo $company . '<br />';
        echo $slogan . '<br />';
        echo $phone . '<br />';
        echo $email . '<br />';
        echo $address1 . '<br /.';
        echo $address2 . '<br />';
        echo $postcode . '<br />';
        echo $latlong . '<br />';
        echo $fb . ' facebook<br />';
        echo $twit . ' twitter<br />';
        echo $color1 . '<br />';
        echo $color2 . '<br />';

        echo "<h3 style='color: navy;'>From <b>edit/text folder:</b></h3>";
        echo file_get_contents('edit/text/main.txt');
        echo file_get_contents('edit/text/more.txt');
        echo file_get_contents('edit/text/hours.txt');
        ?>

        <div>There is a maximum of 6 images. They can be jpg,png,jpeg or JPG
            and are located here:

            edit/images/logo.*<br />
            edit/images/img1.*<br />
            edit/images/img2.*<br />
            edit/images/img3.*<br />
            edit/images/img4.*<br />
            edit/images/geo.*<br />
        </div>
        <div><br />
            Images can be accessed via following the example. <br />We preserve image types during the upload and therefor we 
            perform a conditional to display the image.<br />
            <?php
            if (file_exists('edit/images/logo.png')) {
                $file = 'edit/images/logo.png';
            } elseif (file_exists('edit/images/logo.jpeg')) {
                $file = 'edit/images/logo.jpeg';
            } elseif (file_exists('edit/images/logo.jpg')) {
                $file = 'edit/images/logo.jpg';
            }
            if ($file) {
                echo "<img src='$file' style='width: 100px; display: inline;' />";
            } else {
                echo "<img src='edit/images/no_photo.png' style='width: 100px; display: inline;' />";
            }
            ?>
             <?php
            if (file_exists('edit/images/img1.png')) {
                $file = 'edit/images/img1.png';
            } elseif (file_exists('edit/images/img1.jpeg')) {
                $file = 'edit/images/img1.jpeg';
            } elseif (file_exists('edit/images/img1.jpg')) {
                $file = 'edit/images/img1.jpg';
            }
            if ($file) {
                echo "<img src='$file' style='width: 100px; display: inline;' />";
            } else {
                echo "<img src='edit/images/no_photo.png' style='width: 100px; display: inline;' />";
            }
            ?>
        </div>
        <div>
            We hope you enjoy using the NotaCMS.<br />
            Good Luck
        </div>

    </div>
</div>










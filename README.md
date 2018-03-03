In order to make sure it all works, please follow the steps below:

1) In your phpMyAdmin/SQL server admin, run the following SQL code in your SQL tab:

----------- COPY BELOW -----------

CREATE TABLE `hb_mb` (
  `mb_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mb_code` varchar(32) NOT NULL DEFAULT '',
  `mb_username` varchar(16) NOT NULL,
  `mb_emailaddress` varchar(100) NOT NULL DEFAULT '',
  `mb_password` varchar(32) NOT NULL DEFAULT '',
  `mb_datetimejoin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mb_lastlogin` datetime NOT NULL,
  `mb_passresetcode` varchar(32) NOT NULL DEFAULT '',
  `mb_logincode` varchar(32) NOT NULL,
  `mb_activate` varchar(1) NOT NULL,
  `mb_deactivate` varchar(1) NOT NULL,
  `mb_ban` varchar(1) NOT NULL,
  PRIMARY KEY (mb_id)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

----------- COPY ABOVE -----------

2) Upload the files.

3) Go to the file "config-hm.php" and change the variables inside the speech marks "YOURSQLUSERNAME", "YOURSQLPASSWORD", "YOURSQLDATABASENAME" to match your SQL database.

4) Go to "register.php and look the following line:

$message.="confirmation.php?code=$confcode";

5) Change the line above in "register.php" to include your domain name path i.e.:

$message.="http://somfic.dx.am/confirmation.php?code=$confcode";

6) Go to "forgottenpassword.php and look for the following line:

$message.="resetpassword.php?code=$forgotemailresetcode";

7) Change the line above in "forgottenpassword.php" to include your domain name path i.e.:

$message.="http://somfic.dx.am/resetpassword.php?code=$forgotemailresetcode";

8) To change the page you visit once you've logged in, find the following code in "login.php" and change index.php to whatever link you want:

echo "<script type='text/javascript'> document.location = 'index.php'; </script>";

10) Voilà!

<?php
//checks cookies to make sure they are logged in
if(isset($_COOKIE['ID_my_site']))
{
$past = time() - 100;
//this makes the time in the past to destroy the cookie
setcookie("ID_my_site", "gone", $past);
setcookie("Key_my_site", "gone", $past);
header("Location: login.php");}
else {
header("Location: login.php");}
?>

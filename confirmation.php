<?php

include('config-hm.php');
$tbl_name='hb_mb';

$error="";

//Checks if there is a login cookie
if(isset($_COOKIE['ID_my_site']))

//if there is, it logs you in and directs you to the members page
{

   $cookusername = $_COOKIE['ID_my_site'];
   $cookpass = $_COOKIE['Key_my_site'];
   $cookchecksql = "SELECT * FROM hb_mb WHERE mb_username = '$cookusername'";
   $cookcheckrs=mysqli_query($link, $cookchecksql);

   while($info = mysqli_fetch_array($cookcheckrs))
   {
      if ($cookpass != md5($info['mb_logincode'].$salt))
      {
         $accepted = '0';
         echo "<script type='text/javascript'> document.location = 'logout.php'; </script>";
         exit;
      } else {
         $accepted = '1';
         $error .= "You are already logged in. Click <a href=logout.php>here</a> to log out.";
      }
   }
}

?>



<!DOCTYPE html>
<html>
  <head>
        <title>HelloBank</title>
     <link rel="stylesheet" type="text/css" href="style.css">
     <link href="https://fonts.googleapis.com/css?family=Merienda" rel="stylesheet">
    <meta charset="UTF-8">
<style class="cp-pen-styles">body
{
  font-family: Arial, Sans-serif;
}
.errorTxt li{
  font-weight: bold;
  list-style-type: none;
  color: red; 
  list-style: none;
}
</style>

  </head>
  <body>
    <div class="LoginHolder">
      <div class="whitebox register">
        <h1>HelloBank</h1>
        <h2>Confirm Your Account</h2>

<?php

// Passkey that got from link
$passkey=$_GET['code'];

if ($error =="") {

   if ($passkey !="") {

      $sql1 = "SELECT * FROM $tbl_name WHERE mb_logincode = '$passkey'";
      if ($result1=mysqli_query($link, $sql1)) {
         $count=mysqli_num_rows($result1);
         if ($count == 1) {

            // Random confirmation code
            $confcode=md5(uniqid(rand()));
            // checks if the c_c is in use
            if (!get_magic_quotes_gpc()) {
               $confcode = addslashes($confcode);
            }

            $confcodechecksql = "SELECT mb_logincode FROM $tbl_name WHERE mb_logincode = '$confcode'";
            if ($confcodecheckrs=mysqli_query($link, $confcodechecksql)) {
               $confcodecheckcount=mysqli_num_rows($confcodecheckrs);
               if ($confcodecheckcount != 0) {
                  $confcode = $passkey;
               }
            }

            $sql2 = "UPDATE $tbl_name SET mb_activate='1', mb_logincode='$confcode' WHERE mb_logincode = '$passkey'";
            if (mysqli_query($link, $sql2)) {
               echo ('Your account has been activated. Click <a href="login.php">here</a> to login.');
            } else {
               echo "There was an error. Please refresh the page and try again.";
            }

         } else {
            echo ('The confirmation code used has not been found. Your account may have already been activated. Please click <a href="login.php">here</a> to login or contact a moderator for more support.');
         }
      }
   } else {
      echo "No code has been supplied. Please go back and try again.";
   }

} else {
   echo $error;
}
?>

          <div class="spacer"></div>

      </div>
      <div class="formlinks">
        <div class="loginLink"><a href="login.php">Login</a></div>
        <div class="Password"><a href="forgottenpassword.php">Forgot password</a></div>
      </div>
    </div>

  </body>
</html>

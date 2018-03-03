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

if ($_SERVER['REQUEST_METHOD'] == "POST"){

   $suser = $_POST['username'];
   $spass = $_POST['password'];

   //if the login form is submitted
   if ($suser && $spass) { // if all form items have been submitted


      // checks it against the database

      if (!get_magic_quotes_gpc()) {
         $suser = addslashes($suser);
      }

      $checksql = "SELECT * FROM $tbl_name WHERE mb_username = '".$suser."'";
      $checkrs = mysqli_query($link, $checksql);

      //Gives error if user dosen't exist
      $checkcount = mysqli_num_rows($checkrs);
      if ($checkcount == 0) {
         $error .= "The details you have entered are incorrect, please try again. ";
      }

      while($rs = mysqli_fetch_array($checkrs))
      {
         $checkactivate = $rs['mb_activate'];
         $checkdeactivate = $rs['mb_deactivate'];
         $checkbanned = $rs['mb_ban'];
         $scode = md5($rs['mb_logincode'].$salt);

         if ($checkactivate == 0) {
            $error .= "You have not confirmed your email address with us. Please check your email or contact us if you have any further issues. ";
         } elseif ($checkdeactivate == 1) {
            $error .= "You have deactivated your account. Please use the forgotten password option to reactivate your account. ";
         } elseif ($checkbanned == 1) {
            $error .= "Your account has been banned.";
         } else {
            $spass = stripslashes($spass);
            $rs['mb_password'] = stripslashes($rs['mb_password']);
            $spass = md5($spass);

            //gives error if the password is wrong
            if ($spass != $rs['mb_password']) {
               $error .= "The details you have entered are incorrect, please try again. ";
            } else {
               // if login is ok then we add a cookie
               $suser = stripslashes($suser);
               $hour = time() + 7200;
               setcookie("ID_my_site", $suser, $hour);
               setcookie("Key_my_site", $scode, $hour); 

               $lastlogin = date('Y-m-d H:i:s');
               $lastloginsql = "UPDATE $tbl_name SET mb_lastlogin='$lastlogin' WHERE mb_username = '$suser'"; 
               $lastloginrs = mysqli_query($link, $lastloginsql);

               //then redirect them to the members area
               echo "<script type='text/javascript'> document.location = 'index.php'; </script>";

               exit();
            }
         }
      }

   } else {

      // if they are not logged in
      $error .=  "You did not fill in a required field. ";
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
  </head>
  <body>
    <div class="LoginHolder">
      <div class="whitebox login">
        <h1>HelloBank</h1>
        <form action="login.php" method="post" name="myForm" id="myForm">

<?php if($error !="") { ?>
<div><p style="color:red;"><?php echo $error; ?></p></div>
<?php } ?>

          <input type="text" name="username" placeholder="username" id="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'];?>">
          <img src="user.png">
          <input type="password" name="password" placeholder="password" id="password" value="<?php if (isset($_POST['password'])) echo $_POST['password'];?>">
          <img src="pass.png">
          <div class="spacer"></div>
          <input type="button" id="btnsubmit" name="btnsubmit" onclick="myFunction()">
          <input type="submit" id="formsubmit" name="formsubmit" value="1" style="display:none;">
        </form>
      </div>
      <div class="formlinks">
        <div class="Account"><a href="register.php">Create an account</a></div>
        <div class="Password"><a href="forgottenpassword.php">Forgot password</a></div>
      </div>
    </div>

<script>
function myFunction() {
    document.getElementById("myForm").submit();
}
</script>

  </body>
</html>

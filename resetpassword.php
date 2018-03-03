<?php

include('config-hm.php');
$tbl_name='hb_mb';
$resetkey=stripslashes($_GET['code']);

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
      <div class="whitebox login">
        <h1>HelloBank</h1>
        <h2>Reset Password</h2>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST"){


   foreach($_POST as $k => $v) {
      $v = trim($v);
      $$k = $v;
   }

   // create empty error variable
   $error = "";

   // check the data in required fields
   if (($_POST['email'] == "") ||
      ($_POST['password'] == "") ||
      ($_POST['confirmpassword'] == "")) {
         $error = "Please fill in all the required fields.<br>";
   }

   // validate emailaddress
   if (((strpos($_POST['email'], "@") === FALSE) ||
       (strpos($_POST['email'], ".") === FALSE) ||
       (strpos($_POST['email'], " ") != FALSE) ||
       (strpos($_POST['email'], "@") > strrpos($_POST['email'], ".")))
   && ($_POST['email'] > "")) {
      $error .= "- Please enter a valid email address.<br>";
   }

   // check password is more than 8 chars long
   if (strlen($_POST['password']) < 8 ){
      $error .="- Your password must be between 8 and 32 characters in length.<br>";
   }

   // check confirmedpassword is more than 32 chars long
   if (strlen($_POST['confirmpassword']) > 32 ){
      $error .="- You have confirmed a password that must be between 8 and 32 characters in length.<br>";
   }

   // this makes sure both passwords entered match
   if ($_POST['password'] != $_POST['confirmpassword']) {
      $error .="- The passwords you have provided do not match. Please try again.<br>";
   }

   // here we encrypt the password and add slashes if needed
   $_POST['password'] = md5($_POST['password']);
   if (!get_magic_quotes_gpc()) {
      $_POST['password'] = addslashes($_POST['password']);
      $_POST['email'] = addslashes($_POST['email']);
   }

   // if all data is there, build query
   if ($error =="") {

      $resetpasspass = $_POST['password'];
      $resetpassemail = $_POST['email'];
      $resetpasskey = $resetkey;

      // Random confirmation code
      $newcode=md5(uniqid(rand()));
      // checks if the c_c is in use
      if (!get_magic_quotes_gpc()) {
         $newcode = addslashes($newcode);
      }

      $newcodechecksql = "SELECT mb_passresetcode FROM $tbl_name WHERE mb_passresetcode = '$newcode'";
      if ($newcodecheckrs=mysqli_query($link, $newcodechecksql)) {
         $newcodecheckcount=mysqli_num_rows($newcodecheckrs);
         if ($newcodecheckcount == 0) {
            $newresetcode = $newcode;
         } else { $newresetcode = $resetpasskey; }
      }

      $resetchecksql = "SELECT * FROM $tbl_name WHERE mb_passresetcode = '$resetpasskey' AND mb_emailaddress='$resetpassemail'";
      if ($resetcheckrs=mysqli_query($link, $resetchecksql)) {
         $resetcheckcount=mysqli_num_rows($resetcheckrs);
         if ($resetcheckcount != 0) {

            // perform update
            $finalresetsql = "UPDATE $tbl_name SET mb_password='$resetpasspass', mb_passresetcode='$newresetcode', mb_deactivate='0' WHERE mb_emailaddress = '$resetpassemail' AND mb_passresetcode = '$resetpasskey'";
            if (mysqli_query($link, $finalresetsql)) {
               echo ('Your password has successfully been updated. Click <a href="login.php">here</a> to login.');
            } else {
               echo ('There was an error. Please try resetting your password again or contact a moderator.');
            }

         } else { echo "We are unable to reset your password as the info you entered appears to be incorrect."; }
      }

   }

   echo "<div class=\"spacer\"></div>";

} else {

   if ($error =="") {

      if($resetkey != ""){

?>


        <form action="resetpassword.php?code=<?php echo $resetkey; ?>" method="post" name="myForm" id="myForm">
        <p>Please enter your email address below to receive a link to reset your password.</p>

        <div class="errorTxt"><ul style="  border: 0px solid red;"></ul></div>

          <input type="text" name="email" placeholder="email" id="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>">
          <img src="email.png">
          <input type="password" name="password" placeholder="password" id="password" value="<?php if (isset($_POST['password'])) echo $_POST['password'];?>">
          <img src="pass.png">
          <input type="password" name="confirmpassword" placeholder="confirm password" id="confirmpassword" value="<?php if (isset($_POST['confirmpassword'])) echo $_POST['confirmpassword'];?>">
          <img src="pass.png">
          <div class="spacer"></div>
          <input type="button" id="btnsubmit" name="btnsubmit" onclick="myFunction()">
          <input type="submit" id="formsubmit" name="formsubmit" value="1" style="display:none;">
        </form>
<?php
      } else {
         echo "No code has been entered and therefore this form is invalid. Please check your email for a reset code.";
         echo "<div class=\"spacer\"></div>";
      }
   } else {
      echo "You are already logged in.<div class=\"spacer\"></div>";
   }
}
?>

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
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.min.js"></script>
<script>
$(function () {

jQuery.validator.addMethod("lettersspaceonly", function(value, element) 
{
return this.optional(element) || /^[a-z," "]+$/i.test(value);
}, "Letters and spaces only please")

jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[A-Za-z0-9_]+$/i.test(value);
}, "Letters only please"); 

    $('#myForm').validate({

       /*onkeyup: false,
       onclick: false,
       onfocusout: false,*/

        rules: {
            email: {
                required: true,
                email: true,
            },
            'password': {
                required: true,
                minlength: 8,
                maxlength: 32
            },
            'confirmpassword': {
                required: true,
                minlength: 8,
                maxlength: 32,
                equalTo: "#password"
            }

        },
        messages: {
            email: { required: 'Email - This field is required', email: 'Email - Please provide a valid email', remote: 'Email - This has already been previously registered. Please reset your password here' },
            password: { required: 'Password - This field is required', minlength: 'Password - This must be at least 8 characters', maxlength: 'Password - Your password is too long.' },
            confirmpassword: { required: 'Confirm Password - This field is required', equalTo: 'Confirm Password - Your passwords do not match', minlength: 'Confirm Password - This must be at least 8 characters', maxlength: 'Confirm Password - Your password is too long.' }
        },

        submitHandler: function(form) {
            // do other things for a valid form
            form.submit()
        },

        errorElement : 'div',
        errorContainer: ".errorTxt",
        errorLabelContainer: '.errorTxt ul',
        wrapper: "li"


    });
});
//@ sourceURL=pen.js
</script>

  </body>
</html>

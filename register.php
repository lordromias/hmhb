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
        <h2>Register</h2>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST"){

   // table name
   if ($_POST) {
      foreach($_POST as $k => $v) {
         $v = trim($v);
         $$k = $v;
      }

      // create empty error variable
      $error = "";

      // check the data in required fields
      if (($_POST['username'] == "") ||
         ($_POST['email'] == "") ||
         ($_POST['password'] == "") ||
         ($_POST['confirmpassword'] == "")) {
         $error = "Please fill in all the required fields. <br>";
      }

      // validate emailaddress
      if (((strpos($_POST['email'], "@") === FALSE) ||
         (strpos($_POST['email'], ".") === FALSE) ||
         (strpos($_POST['email'], " ") != FALSE) ||
         (strpos($_POST['email'], "@") > strrpos($_POST['email'], ".")))
         && ($_POST['email'] > "")) {
         $error .= "Please enter a valid email address. <br>";
      }

      // checks if the email address is in use
      if (!get_magic_quotes_gpc()) {
         $_POST['email'] = addslashes($_POST['email']);
      }

      $emailcheck = $_POST['email'];
      $echecksql = "SELECT mb_emailaddress FROM $tbl_name WHERE mb_emailaddress = '$emailcheck'";

      if ($echeckrs=mysqli_query($link, $echecksql)) {
         $echeckcount=mysqli_num_rows($echeckrs);
         if ($echeckcount != 0) {
            $error .= "The email address you have provided is already registered. <br>";
         }
      }


      //checks username is right format
      $unameregex = $_POST['username'];
      if (!preg_match("/^[A-Za-z0-9_]*$/",$unameregex)) {
        $error .= "The username must only be letters, numbers or an underscore. <br>"; 
      }


      // checks if the username is in use
      if (!get_magic_quotes_gpc()) {
         $_POST['username'] = addslashes($_POST['username']);
      }

      $ucheck = $_POST['username'];
      $uchecksql = "SELECT mb_username FROM $tbl_name WHERE mb_username = '$ucheck'";

      if ($ucheckrs=mysqli_query($link, $uchecksql)) {
         $ucheckcount=mysqli_num_rows($ucheckrs);
         if ($ucheckcount != 0) {
            $error .= "<p>The username you have provided is already registered. <br>";
         }
      }




      // Random code
      $code=md5(uniqid(rand()));
      // checks if the c_c is in use
      if (!get_magic_quotes_gpc()) {
         $code = addslashes($code);
      }

      $codechecksql = "SELECT mb_code FROM $tbl_name WHERE mb_code = '$code'";
      if ($codecheckrs=mysqli_query($link, $codechecksql)) {
         $codecheckcount=mysqli_num_rows($codecheckrs);
         if ($codecheckcount != 0) {
            $error .= "There was an error processing the form. <br>";
         }
      }

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
            $error .= "There was an error processing the form. <br>";
         }
      }

      // Random reset code
      $resetcode=md5(uniqid(rand()));
      // checks if the c_c is in use
      if (!get_magic_quotes_gpc()) {
         $resetcode = addslashes($resetcode);
      }

      $resetcodechecksql = "SELECT mb_passresetcode FROM $tbl_name WHERE mb_passresetcode = '$resetcode'";
      if ($resetcodecheckrs=mysqli_query($link, $resetcodechecksql)) {
         $resetcodecheckcount=mysqli_num_rows($resetcodecheckrs);
         if ($resetcodecheckcount != 0) {
            $error .= "There was an error processing the form. <br>";
         }
      }


      // check password is more than 8 chars long
      if (strlen($_POST['password']) < 8 ){
         $error .="Your password must be between 8 and 32 characters in length. <br>";
      }

      // check confirmedpassword is more than 32 chars long
      if (strlen($_POST['confirmpassword']) > 32 ){
         $error .="You have confirmed a password that must be between 8 and 32 characters in length. <br>";
      }

      // this makes sure both passwords entered match
      if ($_POST['password'] != $_POST['confirmpassword']) {
         $error .="The passwords you have provided do not match. <br>";
      }

      // here we encrypt the password and add slashes if needed
      $_POST['password'] = md5($_POST['password']);
      if (!get_magic_quotes_gpc()) {
         $_POST['password'] = addslashes($_POST['password']);
         $_POST['email'] = addslashes($_POST['email']);
      }

      // if all data is there, build query
      if ($error =="") {

         $usr_joindate = date('Y-m-d H:i:s');

         $sql = "INSERT INTO $tbl_name
         (mb_code, mb_username, mb_emailaddress, mb_password, mb_activate, mb_deactivate, mb_ban, mb_datetimejoin, mb_passresetcode, mb_logincode)
         VALUES ('$code', '$_POST[username]', '$_POST[email]', '$_POST[password]', '0', '0', '0', '$usr_joindate', '$resetcode', '$confcode')";
         $result=mysqli_query($link, $sql);

         // if sucessfully inserted data into database, send confirmation link to email
         if($result){

         // ---------------- SEND MAIL FORM ----------------

            $emailaddress = $_POST[email];
            $e_username = $_POST[username];

            // send e-mail to ...
            $to=$emailaddress;

            // Your subject
            $subject="HelloBank Email Validation";

            // From
            $header="from: No Reply <HelloBank>";

            // Your message
            $message="Hi $e_username, \r\n";
            $message.=" \r\n";
            $message.="Thank you for registering your HelloBank account! \r\n";
            $message.=" \r\n";
            $message.="In order to activate your HelloBank profile, please confirm your membership by clicking on the confirmation link below to activate this service: \r\n";
            $message.=" \r\n";
            $message.="confirmation.php?code=$confcode";
            $message.=" \r\n";
            $message.=" \r\n";
            $message.="Many thanks, \r\n";
            $message.="HelloBank \r\n";
            $message.="-- \r\n";

            // send email
            $sentmail = mail($to,$subject,$message,$header);

         }

         // if not found
         else {
            echo "There was an error with the form. <br>";
         }

         // if your email succesfully sent
         if($sentmail){
            echo "Thank you for joining HelloBank. A confirmation link has been sent to your email address in order to confirm your registration. <br>";
         }
         else {
            echo "Cannot send Confirmation link to your e-mail address. <br>";
         }
      }
   
      // print error or success messagesont

      echo "<p>".$error."Please click back to try again. <br></p><div class=\"spacer\"></div>";

   }

} else {

?>

        <form action="register.php" method="post" name="myForm" id="myForm">

<div class="errorTxt"><ul style="  border: 0px solid red;"></ul></div>

<?php if($error !="") { ?>
<div><p style="color:red;"><?php echo $error; ?></p></div>
<?php } ?>

          <input class="reg_spacing" type="text" name="username" placeholder="username" id="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'];?>">
          <img src="user.png">
          <input class="reg_spacing" type="text" name="email" placeholder="email" id="username" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>">
          <img src="email.png">
          <input class="reg_spacing" type="password" name="password" placeholder="password" id="password" value="<?php if (isset($_POST['password'])) echo $_POST['password'];?>">
          <img src="pass.png">
          <input class="reg_spacing" type="password" name="confirmpassword" placeholder="confirm password" id="confirmpassword" value="<?php if (isset($_POST['confirmpassword'])) echo $_POST['confirmpassword'];?>">
          <img src="pass.png">
          <div class="spacer"></div>
          <input type="button" id="btnsubmit" name="btnsubmit" onclick="myFunction()">
          <input type="submit" id="formsubmit" name="formsubmit" value="1" style="display:none;">
        </form>

<?php

}

?>

      </div>
      <div class="formlinks">
        <div class="loginLink"><a href="login.php">Login</a></div>
        <div class="Password"><a href="forgottenpassword.php">Forgot password</a></div>
      </div>
    </div>

<!-- Javascript files -->

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
            username: {
                required: true,
                lettersonly: true,
                minlength: 3,
                maxlength: 16
            },
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
            username: { required: 'Username - This field is required', lettersonly: 'Username - Special characters are not permitted', minlength: 'Username - Please enter at least 3 characters in length', maxlength: 'Username - Please enter a username that is maximum 16 characters long.' },
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

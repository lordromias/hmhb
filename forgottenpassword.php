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
      <div class="whitebox login">
        <h1>HelloBank</h1>
        <h2>Forgotten Password</h2>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST"){

   $forgotemail = addslashes($_POST['email']);

   $forgotemailsql = "SELECT * FROM $tbl_name WHERE mb_emailaddress = '$forgotemail'";
   if ($forgotemailrs=mysqli_query($link, $forgotemailsql)) {
      $forgotemailcount=mysqli_num_rows($forgotemailrs);

      if (mysqli_num_rows($forgotemailrs) > 0) {
         echo "A reset link has been sent to your email address.";
         while($forgotemailrow = mysqli_fetch_assoc($forgotemailrs)) {

            $forgotemailresetcode = $forgotemailrow["mb_passresetcode"];
            $forgotemailusername = $forgotemailrow["mb_username"];
            $forgotemailemail = $forgotemailrow["mb_emailaddress"];

            // ---------------- SEND MAIL FORM ----------------

            // send e-mail to ...
            $to=$forgotemailemail;

            // Your subject
            $subject="HelloBank Password Reset";

            // From
            $header="from: No Reply <HelloBank>";

            // Your message
            $message="Hi $forgotemailusername, \r\n";
            $message.=" \r\n";
            $message.="A request has been made via the HelloBank website to reset your password. If you requested this, please click the link below to reset your password: \r\n";
            $message.=" \r\n";
            $message.="resetpassword.php?code=$forgotemailresetcode";
            $message.=" \r\n";
            $message.=" \r\n";
            $message.="Many thanks, \r\n";
            $message.="HelloBank \r\n";
            $message.="-- \r\n";

            // send email
            $sentmail = mail($to,$subject,$message,$header);

         }
      } else { echo "We are sorry there was either an error with your submission or the email address no longer exists on our records."; }
   }

   echo "<br /><br /><br /><br /><br /><br /><br /><br /><br />";

} else {

   if ($error =="") {

?>


        <form action="forgottenpassword.php" method="post" name="myForm" id="myForm">
        <p>Please enter your email address below to receive a link to reset your password.</p>

        <div class="errorTxt"><ul style="  border: 0px solid red;"></ul></div>

          <input type="text" name="email" placeholder="email" id="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>">
          <img src="email.png">
          <div class="spacer"></div>
          <input type="button" id="btnsubmit" name="btnsubmit" onclick="myFunction()">
          <input type="submit" id="formsubmit" name="formsubmit" value="1" style="display:none;">
        </form>

<?php
   } else {
      echo $error;
?>
          <div class="spacer"></div>
<?php
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
            }

        },
        messages: {
            email: { required: 'Email - This field is required', email: 'Email - Please provide a valid email', remote: 'Email - This has already been previously registered. Please reset your password here' }
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

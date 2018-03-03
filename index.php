<?php

include('config-hm.php');
$tbl_name='hb_mb';
$accepted = '0';

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
         $display_username=$info['mb_username'];
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
  </head>
  <body>
    <div class="LoginHolder">
      <div class="whitebox">
        <h1>HelloBank</h1>

        <?php if($accepted > 0) { ?>

           <p>Welcome <?php echo $display_username; ?>!</p>
           <p>Click <a href=logout.php>here</a> to log out.</p>
        <?php } else { ?>

           <p>You are not logged in.</p>
           <p>Click <a href=login.php>here</a> to log in.</p>

        <?php } ?>
          <div class="spacer"></div>
      </div>
    </div>

<script>
function myFunction() {
    document.getElementById("myForm").submit();
}
</script>

  </body>
</html>

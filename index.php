
<!DOCTYPE html>
<html>
  <head>
     <link rel="stylesheet" type="text/css" href="login.css">
     <link href="https://fonts.googleapis.com/css?family=Merienda" rel="stylesheet">
     <script src="login.php"></script>
    <meta charset="UTF-8">
  </head>
  <body>
    <div class="LoginHolder">
      <div class="Login">
        <h1>HelloBank</h1>
        <form>
          <input type="text" name="Username" placeholder="Username" id="Username">
          <img src="user.png">
          <input type="password" name="Password" placeholder="Password" id="Password">
          <img src="pass.png">
          <input type="button" name="Sumbit" onclick="checkUser()">
        </form>
      </div>
      <div class="Account"><a href="url">Create an account</a></div>
      <div class="Password"><a href="url">Forgot password</a></div>
    </div>
  </body>
</html>

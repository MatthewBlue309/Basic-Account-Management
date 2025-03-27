<?php 
   session_start();

   if(isset($_SESSION["email"])) 
      header("Location: client.html"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="icon" type="image/x-icon" href="\images\icon.ico">
   <link rel="stylesheet" href="css.css">
   <script src="handler.js"></script>
</head>
<body id="body">
   <form id="login-form" class="container" action="server.php" method="POST" novalidate>
      <input type="hidden" name="form-type" value="login">  <!-- For the server to recognize what form is this -->
      <a id="logo" href="">Account<br>Management</a>

      <div class="input-container">
         <input type="text" id="email" class="input-field" name="email" placeholder=" " required>
         <label for="email" class="input-label">Email address</label>
      </div>
      <div class="input-container">
         <input type="password" id="pass" class="input-field" name="pass" placeholder=" " required>
         <label for="pass" class="input-label">Password</label>
         <button type="button" class="show-password" onclick="toggleShowHide()">
            <img src="/Midterm/images/eye-closed.png" alt="" style="width: 16px;">
         </button>
      </div>
      <div id="errorMessage" style="display: none"></div>
      <button id="submit-btn" type="submit">Log in</button>
      
      <p id="forgot-password" onclick="passwordRecover()">You forgot your password?<p>
      
      <div class="or-container">
         <div class="or-line"></div>
         <div class="or-text">OR</div>
         <div class="or-line"></div>
      </div>

      <a id="sign-up" href="signup.html">Sign up</p>
   </form>
</body>
</html>
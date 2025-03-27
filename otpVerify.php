<?php
  session_start();
  
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["form-type"]) && $_POST['form-type'] == "otpVerify") {
      $otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];
      if(isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {
        unset($_SESSION['otp']);
        header('Location: client.html');
        exit();
      } else {
        echo "
          <script>
            window.location.href = 'otpVerify.php';
            alert('Invalid OTP!');
          </script>";
      }
    }
    exit();
  }
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verify</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="icon" type="image/x-icon" href="\images\icon.ico">
  <link rel="stylesheet" href="css.css">
  <script src="handler.js"></script>
</head>
<body id="body">
  <form id="otpForm" action="" method="POST">
    <a id="logo" href="">Account<br>Management</a>
    <h3 id="otpMessage">OTP sent to '<span id="e"></span>'<br>Check your gmail and input the OTP code</h3>
    <div class="otp-container">
      <input type="hidden" name="form-type" value="otpVerify">  <!-- For the server to recognize what form is this -->
      <input type="text" name="otp1" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required autofocus>
      <input type="text" name="otp2" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required>
      <input type="text" name="otp3" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required>
      <input type="text" name="otp4" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required>
      <input type="text" name="otp5" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required>
      <input type="text" name="otp6" class="otp-input" maxlength="1" onkeyup="movePointer(this, event)" required>
    </div>
  </form>
</body>
</html>
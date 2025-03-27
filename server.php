<?php
   session_start();
   header('Content-Type: application/json');
//Setup library for PHPMailer
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;
   require "vendor/autoload.php";

//Setup connection with MySQL database
   $user = 'root';
   $pass = '';
   $dbname = 'user';
   $conn = new mysqli('localhost', $user, $pass) or die("Connection to server database failed!");
   //Create database if it does not exist
   $create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
   if ($conn-> query($create_db) === FALSE) {
      echo "Error creating database: " . $conn->error;
      exit();
   }
   //Select the database
   $db_conn = new mysqli('localhost', $user, $pass, $dbname);
   $table = "account";
   //Create if table account does not exist 
   $check_table = $db_conn-> query("SHOW TABLES LIKE '$table'");
   
   if ($check_table-> num_rows == 0) {
      $create_table = "CREATE TABLE $table (
         ID INT AUTO_INCREMENT PRIMARY KEY,
         Name VARCHAR(255) NOT NULL,
         Email VARCHAR(255) UNIQUE NOT NULL,
         Username VARCHAR(255) NOT NULL,
         Password VARCHAR(255) NOT NULL,
         TwoFA INT
      )";
  
      if ($db_conn-> query($create_table) === TRUE) {
         header("Location: login.php");
      } else {
         echo "Error creating table: " . $conn->error;
      }
   }

      
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST['form-type']) && $_POST['form-type'] == 'login') {
         handleLogin();
      } else 
      if(isset($_POST['form-type']) && $_POST['form-type'] == 'signup') {
         handleSignup();
      } else 
      if(isset($_POST['add2FA_to_email'])) {
         add2FA();
      }
   }
   function add2FA() {
      unset($_SESSION['email']);
      global $db_conn;

      $email = $_POST['add2FA_to_email'];
      $add = "UPDATE account SET TwoFA='1' WHERE Email='$email'";

      if($db_conn-> query($add) === TRUE) {
         echo json_encode(["success" => 'true']);
         exit();
      } else {
        echo json_encode(["success" => "false", "message" => "Update failed: " . $db_conn-> error]);
        exit();
      }
   }
   function handleSignup() {
      global $db_conn;

      $name = '';
      $username = '';
      $email = '';
      $pass = '';
      $twoFA = 0;
      $error = '';

   //Check if all input field are filled
      //Full name checking
      if(!isset($_POST['name']) || empty($_POST['name'])) {
         $error = ['error' => 'name', 'message' => 'Please input your full name!'];
         echo json_encode($error);
         exit();
      } else { 
         $name = $_POST['name'];
      }
      //Username checking
      if(!isset($_POST['username']) || empty($_POST['username'])) {
         $error = ['error' => 'username', 'message' => 'Please input your username!'];
         echo json_encode($error);
         exit();
      } else {
         $username = $_POST['username'];
      }
      //Email checking
      if(!isset($_POST['email']) || empty($_POST['email'])) {
         $error = ['error' => 'email', 'message' => 'Please input your email!'];
         echo json_encode($error);
         exit();
      } else
      if(!str_contains($_POST['email'], "@")) {
         $error = ["error"=> "email", "message"=> "Invalid email! Email must include '@'"];
         echo json_encode($error);
         exit();
      } else {
         $email = $_POST['email'];
      }
      //Password checking
      if(!isset($_POST['pass']) || empty($_POST['pass'])) {
         $error = ['error' => 'pass', 'message' => 'Please input your password!'];
         echo json_encode($error);
         exit();
      } else
      if(strlen($_POST['pass']) < 6) {
         $error = ['error'=> 'pass', 'message'=> 'Invalid password! Password must contain at least 6 character'];
         echo json_encode($error);
         exit();
      } else {
         $pass = $_POST['pass'];
      }
      

   //Secure the data before inserting into the database
      $name = mysqli_real_escape_string($db_conn, $name);
      $username = mysqli_real_escape_string($db_conn, $username);
      $email = mysqli_real_escape_string($db_conn, $email);
      $pass = mysqli_real_escape_string($db_conn, $pass);
      $twoFA = mysqli_real_escape_string($db_conn, $twoFA);

      //$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

   //Check if username or email is already exists
      $check_username = "SELECT * FROM account WHERE Username='$username'";
      $check_email = "SELECT * FROM account WHERE Email='$email'";
   
      if($db_conn-> query($check_username)->num_rows > 0) {
         $error = ['error' => 'username', 'message'=> 'Username already exists!'];
         echo json_encode($error);
         exit();
      } else 
      if($db_conn-> query($check_email)-> num_rows > 0) { 
         $error = ['error' => 'email', 'message'=> 'Email already exists!'];
         echo json_encode($error);
         exit();
      }
   //After all conditions check, store account in database
      $create_account = "INSERT INTO account(Name, Username, Email, Password, TwoFA) VALUES ('$name', '$username', '$email', '$pass', '$twoFA')";
      if($db_conn-> query($create_account) === TRUE) {
         $error = ['error' => 'none', 'message'=> 'signup', 'accEmail' => $email];
         echo json_encode($error);
         exit();
      } else {
         $error = ['error' => 'createFailed', 'message'=> 'Cannot create account. Please try again later!'];
         echo json_encode($error);
         exit();
      }
   }

   function handleLogin() {
      if($_POST['email'] == "admin@123" && $_POST['pass'] == "123") {
         $error = ["error" => "none", "message" => "admin"];
         echo json_encode($error);
         exit();
      }
      global $db_conn;

      $email = '';
      $pass = '';
      $error = '';

      //Check if all input field are filled
      if(!isset($_POST['email']) || empty($_POST['email'])) {
         $error = ["error" => "email", "message" => "Please input your email!"];
         echo json_encode($error);
         exit();
      } else {
         $email = $_POST['email'];
      }
      if(!isset($_POST['pass']) || empty($_POST['pass'])) {
         $error = ['error' => 'pass', 'message' => 'Please input your password!'];
         echo json_encode($error);
         exit();
      } else {
         $pass = $_POST['pass'];
      }
      if(!empty($error)) {
         echo $error;
         exit();
      }
      
      $check_user = "SELECT * FROM account WHERE Email='$email'";
      $sql = $db_conn->query($check_user);

      if($sql-> num_rows > 0) {
         //Convert sql into an account object
         $account = $sql-> fetch_assoc();

         //Check if the password is correct
         if($pass == $account['Password']) {
            //Check if the account have 2FA
            if($account['TwoFA'] == 1) {
               mail_otp($email);
               $error = ['error'=> 'none', 'message'=> 'login', 'twoFA' => "yes", "email" => $account['Email']];
               echo json_encode($error);
               $_SESSION['email'] = $email;
            } else {
               $error = ['error'=> 'none', 'message'=> 'login', 'twoFA' => "no", "email" => $account['Email']];
               echo json_encode($error);
               $_SESSION['email'] = $email;
               exit();
            }
         } else {
            $error = ['error' => 'pass', 'message' => 'Incorrect password!'];
            echo json_encode($error);
            exit();
         }
      } else {
         //The account has never been created
         $error = ['error' => 'email', 'message' => 'Account not exists. Please sign up first!'];
         echo json_encode($error);
         exit();
      }
   }

   function mail_otp($to) {
      $otp = rand(100000, 999999);
      $_SESSION['otp'] = $otp; //Store otp code in a session for later check

      $mail = new PHPMailer(true);

      try {
         $mail-> isSMTP();
         $mail-> Host = "smtp.gmail.com";
         $mail-> SMTPAuth = true;
         $mail-> Username = "hoangminhthang309@gmail.com";
         $mail-> Password = "wudo ftiu bggl ildx";
         $mail-> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail-> Port = 587;

         $mail-> setFrom("hoangminhthang309@gmail.com");
         $mail-> addAddress($to);
         $mail-> isHTML(true);
         $mail-> Subject = "Account Management sent OTP";
         $mail-> Body = "Your OTP code is : $otp. Use this code to verify your login.\nPlease DO NOT share the code!";

         $mail -> send();
      } catch (Exception $e) {
         echo "Message could not be sent, Error: " . $mail->ErrorInfo; 
      }
   }
   $db_conn-> close();
?>
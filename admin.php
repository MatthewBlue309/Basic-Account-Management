<?php
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
   $table = "SELECT * FROM account";
   $result = $db_conn-> query($table);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/x-icon" href="\images\icon.ico">
   <link rel="stylesheet" href="css.css">
   <script src="response.js"></script>
   <title>Admin</title>
</head>
<body id="ad-body">

   <p id="ad-logo">Account <br> Management</p>
   <div id="line-follow"></div>
   <table id="acc-table">
      <tr>
         <th>ID</th>
         <th>Full name</th>
         <th>Email</th>
         <th>Username</th>
         <th>Password</th>
         <th>2FA</th>
      </tr>
      <?php
      if($result->num_rows > 0) {
         while ($row = $result->fetch_assoc()) {
            echo "<tr>
                     <td>{$row['ID']}</td>
                     <td>{$row['Name']}</td>
                     <td>{$row['Email']}</td>
                     <td>{$row['Username']}</td>
                     <td>{$row['Password']}</td>
                     <td>" . ($row['TwoFA'] == 1 ? 'Enabled' : 'Disabled') . "</td>
                  </tr>";
         }
      }
      ?>
   </table>
</body>
</html>
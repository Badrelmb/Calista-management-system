<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
 header('Location: login.php');
 exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>초아's Management System</title>
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
 <link href="style.css" rel="stylesheet">
</head>

<body>
 <div class="header">
  <h1><a href="index.php"> 초아's Management System <img src="images/heart2.png" alt="Heart"></a></h1>

 </div>
 <div class="container">
  <div class="main-buttons">
   <a href="items.php" class="btn btn-primary">Items</a>
   <a href="stats.php" class="btn btn-info">View Stats</a>
  </div>
 </div>
 <div class="footer">
  Made by the love of your life "Badr"
 </div>
 <script src="js/index.js"></script>
</body>

</html>
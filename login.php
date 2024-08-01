<?php
session_start();
require 'config.php'; // Include the configuration file

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 $unique_id = $_POST['unique_id'];
 $password = $_POST['password'];

 if ($unique_id == UNIQUE_ID && $password == PASSWORD) {
  $_SESSION['loggedin'] = true;
  header('Location: index.php');
  exit();
 } else {
  $error = 'Invalid unique ID or password.';
 }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Login</title>
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
 <link href="style.css" rel="stylesheet">
</head>

<body>
 <div class="header">
  <h1> 초아's Management System</h1>
  <img src="heart.png" alt="Heart">
 </div>
 <div class="container">
  <h2 class="mt-5">Login</h2>
  <?php if ($error): ?>
   <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>
  <form action="login.php" method="POST" class="mt-3 mb-3">
   <div class="form-group">
    <label for="unique_id">Unique ID</label>
    <input type="text" class="form-control" id="unique_id" name="unique_id" required>
   </div>
   <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" required>
   </div>
   <button type="submit" class="btn btn-primary">Login</button>
  </form>
 </div>
</body>

</html>
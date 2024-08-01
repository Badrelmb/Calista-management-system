<?php
session_start();
require 'config.php'; // Include the configuration file

$error_message = '';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connect to the database
  $mysqli = new mysqli("localhost", "root", "", "sales_management");

  // Check connection
  if ($mysqli->connect_error) {
    $error_message = "Connection failed: " . $mysqli->connect_error;
  } else {
    // Initialize form data variables
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $name_brand = isset($_POST['name_brand']) ? $_POST['name_brand'] : '';
    $date_of_purchase = isset($_POST['date_of_purchase']) ? $_POST['date_of_purchase'] : '';
    $price_of_purchase = isset($_POST['price_of_purchase']) ? $_POST['price_of_purchase'] : '';

    // Handle file upload
    $photos = [];
    if (isset($_FILES['photos']) && count($_FILES['photos']['name']) > 0) {
      foreach ($_FILES['photos']['name'] as $key => $name) {
        if ($_FILES['photos']['error'][$key] == 0) {
          $target_dir = "uploads/";
          $unique_name = uniqid() . "_" . basename($name);
          $target_file = $target_dir . $unique_name;
          $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

          // Check if image file is an actual image or fake image
          $check = getimagesize($_FILES["photos"]["tmp_name"][$key]);
          if ($check === false) {
            $error_message = "File is not an image.";
            break;
          }

          // Check file size (limit to 5MB)
          if ($_FILES["photos"]["size"][$key] > 5000000) {
            $error_message = "Sorry, your file is too large.";
            break;
          }

          // Allow certain file formats
          if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            break;
          }

          // Attempt to move the uploaded file
          if (move_uploaded_file($_FILES["photos"]["tmp_name"][$key], $target_file)) {
            $photos[] = $target_file;
          } else {
            $error_message = "Sorry, there was an error uploading your file.";
            break;
          }
        }
      }
    } else {
      $error_message = "Photos are required.";
    }

    if (empty($error_message)) {
      // Convert photos array to JSON
      $photos_json = json_encode($photos);

      // Prepare and bind
      $stmt = $mysqli->prepare("INSERT INTO items (category, name_brand, photos, date_of_purchase, price_of_purchase) VALUES (?, ?, ?, ?, ?)");

      // Check if the statement was prepared successfully
      if ($stmt === false) {
        $error_message = "MySQL prepare statement error: " . $mysqli->error;
      } else {
        $stmt->bind_param("ssssd", $category, $name_brand, $photos_json, $date_of_purchase, $price_of_purchase);

        // Execute statement
        if ($stmt->execute() === false) {
          $error_message = "MySQL execute statement error: " . $stmt->error;
        } else {
          header("Location: items.php");
          exit();
        }

        $stmt->close();
      }
    }

    $mysqli->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Item</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <h2>Add Item</h2>
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>
    <form action="add.php" method="POST" enctype="multipart/form-data" class="mt-3 mb-3">
      <div class="form-group">
        <label for="category">Category</label>
        <select class="form-control" id="category" name="category" required>
          <option value="Jewelry">Jewelry</option>
          <option value="Toys">Toys</option>
          <option value="Accessories">Accessories</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="name_brand">Name/Brand</label>
        <input type="text" class="form-control" id="name_brand" name="name_brand" required>
      </div>
      <div class="form-group">
        <label for="photos">Photos</label>
        <input type="file" class="form-control-file" id="photos" name="photos[]" multiple required>
      </div>
      <div class="form-group">
        <label for="date_of_purchase">Date of Purchase</label>
        <input type="date" class="form-control" id="date_of_purchase" name="date_of_purchase" required>
      </div>
      <div class="form-group">
        <label for="price_of_purchase">Price of Purchase</label>
        <input type="number" step="0.01" class="form-control" id="price_of_purchase" name="price_of_purchase" required
          placeholder="₩">
      </div>
      <button type="submit" class="btn btn-primary">Add Item</button>
    </form>
  </div>
</body>

</html>
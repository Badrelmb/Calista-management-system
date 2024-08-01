<?php
// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "sales_management");

// Check connection
if ($mysqli->connect_error) {
 die("Connection failed: " . $mysqli->connect_error);
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 // Get form data
 $category = $_POST['category'];
 $name_brand = $_POST['name_brand'];
 $date_of_purchase = $_POST['date_of_purchase'];
 $price_of_purchase = $_POST['price_of_purchase'];
 $selling_price = $_POST['selling_price'];

 // Handle file upload if a new file is uploaded
 if (!empty($_FILES['photo']['name'])) {
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["photo"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["photo"]["tmp_name"]);
  if ($check === false) {
   die("File is not an image.");
  }

  // Check if file already exists
  if (file_exists($target_file)) {
   die("Sorry, file already exists.");
  }

  // Check file size (limit to 5MB)
  if ($_FILES["photo"]["size"] > 5000000) {
   die("Sorry, your file is too large.");
  }

  // Allow certain file formats
  if (
   $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
   && $imageFileType != "gif"
  ) {
   die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
  }

  // Attempt to move the uploaded file
  if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
   die("Sorry, there was an error uploading your file: " . error_get_last()['message']);
  }

  $photo = $target_file;
 } else {
  $photo = $_POST['existing_photo'];
 }

 // Update the database
 $stmt = $mysqli->prepare("UPDATE items SET category=?, name_brand=?, photo=?, date_of_purchase=?, price_of_purchase=?, selling_price=? WHERE id=?");
 $stmt->bind_param("ssssddi", $category, $name_brand, $photo, $date_of_purchase, $price_of_purchase, $selling_price, $id);

 if ($stmt->execute()) {
  echo "Record updated successfully";
 } else {
  echo "Error: " . $stmt->error;
 }

 // Close the connection
 $stmt->close();
 $mysqli->close();

 // Redirect back to index.php
 header("Location: index.php");
 exit();
} else {
 // Fetch the item data
 $result = $mysqli->query("SELECT * FROM items WHERE id=$id");
 $item = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Edit Item</title>
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
 <div class="container">
  <h1 class="mt-5">Edit Item</h1>
  <form action="edit.php?id=<?= $id ?>" method="POST" class="mt-3 mb-3" enctype="multipart/form-data">
   <div class="form-group">
    <label for="category">Category</label>
    <select class="form-control" id="category" name="category" required>
     <option value="Jewelry" <?= $item['category'] == 'Jewelry' ? 'selected' : '' ?>>Jewelry</option>
     <option value="Toys" <?= $item['category'] == 'Toys' ? 'selected' : '' ?>>Toys</option>
     <option value="Accessories" <?= $item['category'] == 'Accessories' ? 'selected' : '' ?>>Accessories</option>
     <option value="Other" <?= $item['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
   </div>
   <div class="form-group">
    <label for="name_brand">Name/Brand</label>
    <input type="text" class="form-control" id="name_brand" name="name_brand" value="<?= $item['name_brand'] ?>"
     required>
   </div>
   <div class="form-group">
    <label for="photo">Photo</label>
    <input type="file" class="form-control-file" id="photo" name="photo">
    <img src="<?= $item['photo'] ?>" alt="Current photo" style="max-width: 200px; margin-top: 10px;">
    <input type="hidden" name="existing_photo" value="<?= $item['photo'] ?>">
   </div>
   <div class="form-group">
    <label for="date_of_purchase">Date of Purchase</label>
    <input type="date" class="form-control" id="date_of_purchase" name="date_of_purchase"
     value="<?= $item['date_of_purchase'] ?>" required>
   </div>
   <div class="form-group">
    <label for="price_of_purchase">Price of Purchase</label>
    <input type="number" step="0.01" class="form-control" id="price_of_purchase" name="price_of_purchase"
     value="<?= $item['price_of_purchase'] ?>" required placeholder="₩">
   </div>
   <div class="form-group">
    <label for="selling_price">Selling Price</label>
    <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price"
     value="<?= $item['selling_price'] ?>" placeholder="₩">
   </div>
   <button type="submit" class="btn btn-primary">Update Item</button>
  </form>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
 </div>
</body>

</html>
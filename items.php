<?php
session_start();
require 'config.php'; // Include the configuration file

$error_message = '';

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "sales_management");

// Check connection
if ($mysqli->connect_error) {
  $error_message = "Connection failed: " . $mysqli->connect_error;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>초아's Management System - My Items</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">

</head>

<body>
  <div class="header">
    <h1><a href="index.php">초아's Management System<img src="images/heart2.png" alt="Heart" class="heart-icon"></a></h1>

  </div>
  <div class=" container">
    <h2>My Items</h2>

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


    <h2>Item List</h2>
    <!-- Search and Filter -->
    <div class="form-inline mb-3">
      <select class="form-control mr-2" id="categoryFilter">
        <option value="all">All Categories</option>
        <option value="Jewelry">Jewelry</option>
        <option value="Toys">Toys</option>
        <option value="Accessories">Accessories</option>
        <option value="Other">Other</option>
      </select>
      <input type="text" class="form-control" id="searchInput" placeholder="Search by Name/Brand">
      <button type="button" class="btn btn-primary" onclick="filterTable()">Search</button>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Category</th>
            <th>Name/Brand</th>
            <th>Photo</th>
            <th>Date of Purchase</th>
            <th>Price of Purchase</th>
            <th>Selling Price</th>
            <th>Profit</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="itemList">
          <?php
          $result = $mysqli->query("SELECT * FROM items");
          while ($row = $result->fetch_assoc()):
            $photos = isset($row['photos']) ? json_decode($row['photos'], true) : [];
            $first_photo = isset($photos[0]) ? $photos[0] : 'default_photo.png';
            ?>
            <tr>
              <td><?= $row['category'] ?></td>
              <td><?= $row['name_brand'] ?></td>
              <td><img src="<?= $first_photo ?>" alt="<?= $row['name_brand'] ?>" style="width: 50px;"
                  onclick="openLightbox(<?= htmlspecialchars(json_encode($photos)) ?>)"></td>
              <td><?= $row['date_of_purchase'] ?></td>
              <td>₩<?= number_format($row['price_of_purchase'], 2) ?></td>
              <td>₩<?= number_format($row['selling_price'], 2) ?></td>
              <td>
                ₩<?php if ($row['selling_price'])
                  echo number_format($row['selling_price'] - $row['price_of_purchase'], 2); ?>
              </td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                <?php if (!$row['selling_price']): ?>
                  <form action="sell.php?id=<?= $row['id'] ?>" method="POST" style="display:inline;">
                    <input type="number" step="0.01" name="selling_price" required placeholder="₩">
                    <button type="submit" class="btn btn-success">Sell</button>
                  </form>
                <?php endif; ?>
                <form action="delete.php?id=<?= $row['id'] ?>" method="POST" style="display:inline;"
                  onsubmit="return confirmDelete();">
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <a href="stats.php" class="btn btn-primary">View Stats</a>

  </div>
  <div class="footer">
    Made by the love of your life "Badr"
  </div>

  <!-- Modal Container -->
  <div id="lightbox" class="modal">
    <div class="modal-content-container">
      <span class="close" onclick="closeLightbox()">&times;</span>
      <img class="modal-content" id="lightbox-img">
      <a class="prev" onclick="changePhoto(-1)">&#10094;</a>
      <a class="next" onclick="changePhoto(1)">&#10095;</a>
    </div>
  </div>
  <script src="js/lightbox.js"></script>
  <script src="js/index.js"></script>
</body>

</html>
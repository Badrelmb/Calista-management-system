<?php
// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "sales_management");

// Check connection
if ($mysqli->connect_error) {
 die("Connection failed: " . $mysqli->connect_error);
}

// Get item ID and selling price from the form
$id = $_GET['id'];
$selling_price = $_POST['selling_price'];
$date_of_sale = date('Y-m-d');

// Prepare and bind
$stmt = $mysqli->prepare("UPDATE items SET selling_price = ?, date_of_sale = ? WHERE id = ?");
$stmt->bind_param("dsi", $selling_price, $date_of_sale, $id);

// Execute the query
if ($stmt->execute()) {
 echo "Item marked as sold successfully";
} else {
 echo "Error: " . $stmt->error;
}

// Close the connection
$stmt->close();
$mysqli->close();

// Redirect back to index.php
header("Location: index.php");
exit();
?>
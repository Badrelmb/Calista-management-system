<?php
session_start();
require 'config.php'; // Include the configuration file

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
 header('Location: login.php');
 exit();
}

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "sales_management");

// Check connection
if ($mysqli->connect_error) {
 die("Connection failed: " . $mysqli->connect_error);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch the item to get the photos
$query = "SELECT photos FROM items WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($photos_json);
$stmt->fetch();
$stmt->close();

if ($photos_json) {
 $photos = json_decode($photos_json, true);
 foreach ($photos as $photo) {
  if (file_exists($photo)) {
   unlink($photo);
  }
 }
}

// Delete the item from the database
$query = "DELETE FROM items WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$mysqli->close();

header("Location: items.php");
exit();
?>